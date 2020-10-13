<?php

//Thanks to http://blog.csdn.net/jollyjumper/article/details/9823047

namespace App\Controllers;

use App\Models\{
    Link,
    User,
    UserSubscribeLog
};
use App\Utils\{
    URL,
    Tools,
    AppURI,
    ConfRender
};
use voku\helper\AntiXSS;
use Psr\Http\Message\ResponseInterface;

/**
 *  LinkController
 */
class LinkController extends BaseController
{
    public static function GenerateRandomLink()
    {
        for ($i = 0; $i < 10; $i++) {
            $token = Tools::genRandomChar(16);
            $Elink = Link::where('token', $token)->first();
            if ($Elink == null) {
                return $token;
            }
        }

        return "couldn't alloc token";
    }

    public static function GenerateSSRSubCode($userid, $without_mu)
    {
        $Elink = Link::where('type', 11)->where('userid', $userid)->where('geo', $without_mu)->first();
        if ($Elink != null) {
            return $Elink->token;
        }
        $NLink = new Link();
        $NLink->type = 11;
        $NLink->address = '';
        $NLink->port = 0;
        $NLink->ios = 0;
        $NLink->geo = $without_mu;
        $NLink->method = '';
        $NLink->userid = $userid;
        $NLink->token = self::GenerateRandomLink();
        $NLink->save();

        return $NLink->token;
    }

    public static function GetContent($request, $response, $args)
    {
        if (!$_ENV['Subscribe']) {
            return null;
        }

        $token = $args['token'];

        //$builder->getPhrase();
        $Elink = Link::where('type', 11)->where('token', $token)->first();
        if ($Elink == null) {
            return null;
        }

        $user = User::where('id', $Elink->userid)->first();
        if ($user == null) {
            return null;
        }

        $opts = $request->getQueryParams();

        // 筛选节点部分
        $Rule['type'] = (isset($opts['type']) ? trim($opts['type']) : 'all');
        $Rule['is_mu'] = ($_ENV['mergeSub'] === true ? 1 : 0);
        if (isset($opts['mu'])) $Rule['is_mu'] = (int) $opts['mu'];

        if (isset($opts['class'])) {
            $class = trim(urldecode($opts['class']));
            $Rule['content']['class'] = array_map(
                function ($item) {
                    return (int) $item;
                },
                explode('-', $class)
            );
        }

        if (isset($opts['noclass'])) {
            $noclass = trim(urldecode($opts['noclass']));
            $Rule['content']['noclass'] = array_map(
                function ($item) {
                    return (int) $item;
                },
                explode('-', $noclass)
            );
        }

        if (isset($opts['regex'])) {
            $Rule['content']['regex'] = trim(urldecode($opts['regex']));
        }

        // Emoji
        $Rule['emoji'] = $_ENV['add_emoji_to_node_name'];
        if (isset($opts['emoji'])) {
            $Rule['emoji'] = (bool) $opts['emoji'];
        }

        // 显示流量以及到期时间等
        $Rule['extend'] = $_ENV['enable_sub_extend'];
        if (isset($opts['extend'])) {
            $Rule['extend'] = (bool) $opts['extend'];
        }

        // 兼容原版
        if (isset($opts['mu'])) {
            $mu = (int) $opts['mu'];
            switch ($mu) {
                case 0:
                    $opts['sub'] = 1;
                    break;
                case 1:
                    $opts['sub'] = 1;
                    break;
                case 2:
                    $opts['sub'] = 3;
                    break;
                case 3:
                    $opts['ssd'] = 1;//deprecated
                    break;
                case 4:
                    $opts['clash'] = 1;
                    break;
            }
        }

        // 订阅类型
        $subscribe_type = '';

        $getBody = '';

        $sub_type_array = ['list', 'clash', 'surge', 'surfboard', 'quantumult', 'quantumultx', 'sub'];
        foreach ($sub_type_array as $key) {
            if (isset($opts[$key])) {
                $query_value = $opts[$key];
                if ($query_value != '0' && $query_value != '') {

                    // 兼容代码开始
                    if ($key == 'sub' && $query_value > 4) {
                        $query_value = 1;
                    }
                    // 兼容代码结束

                    if ($key == 'list') {
                        $SubscribeExtend = self::getSubscribeExtend($query_value);
                    } else {
                        $SubscribeExtend = self::getSubscribeExtend($key, $query_value);
                    }
                    $filename = $SubscribeExtend['filename'] . '_' . time() . '.' . $SubscribeExtend['suffix'];
                    $subscribe_type = $SubscribeExtend['filename'];

                    $class = ('get' . $SubscribeExtend['class']);
                    $content = self::$class($user, $query_value, $opts, $Rule);
                    $getBody = self::getBody(
                        $user,
                        $response,
                        $content,
                        $filename
                    );
                    break;
                }
                continue;
            }
        }

        // 记录订阅日志
        if ($_ENV['subscribeLog'] === true) {
            self::Subscribe_log($user, $subscribe_type, $request->getHeaderLine('User-Agent'));
        }

        return $getBody;
    }

    /**
     * 获取订阅类型的文件名
     *
     * @param string      $type  订阅类型
     * @param string|null $value 值
     *
     * @return array
     */
    public static function getSubscribeExtend($type, $value = null)
    {
        switch ($type) {
            case 'ss':
                $return = [
                    'filename' => 'SS',
                    'suffix'   => 'txt',
                    'class'    => 'Sub'
                ];
                break;
            case 'ssa':
                $return = [
                    'filename' => 'SSA',
                    'suffix'   => 'json',
                    'class'    => 'Lists'
                ];
                break;
            case 'ssr':
                $return = [
                    'filename' => 'SSR',
                    'suffix'   => 'txt',
                    'class'    => 'Sub'
                ];
                break;
            case 'sub':
                $strArray = [
                    1 => 'ssr',
                    2 => 'ss',
                    3 => 'v2rayn',
                    4 => 'trojan',
                ];
                $str = (!in_array($value, $strArray) ? $strArray[$value] : $strArray[1]);
                $return = self::getSubscribeExtend($str);
                break;
            case 'clash':
                if ($value !== null) {
                    if ((int) $value == 2) {
                        $return = self::getSubscribeExtend('clashr');
                        $return['class'] = 'Clash';
                    } else {
                        $return = self::getSubscribeExtend('clash');
                        $return['class'] = 'Clash';
                    }
                } else {
                    $return = [
                        'filename' => 'Clash',
                        'suffix'   => 'yaml',
                        'class'    => 'Lists'
                    ];
                }
                break;
            case 'surge':
                if ($value !== null) {
                    $return = [
                        'filename' => 'Surge',
                        'suffix'   => 'conf',
                        'class'    => 'Surge'
                    ];
                    $return['filename'] .= $value;
                } else {
                    $return = [
                        'filename' => 'SurgeList',
                        'suffix'   => 'list',
                        'class'    => 'Lists'
                    ];
                }
                break;
            case 'clashr':
                $return = [
                    'filename' => 'ClashR',
                    'suffix'   => 'yaml',
                    'class'    => 'Lists'
                ];
                break;
            case 'v2rayn':
                $return = [
                    'filename' => 'V2RayN',
                    'suffix'   => 'txt',
                    'class'    => 'Sub'
                ];
                break;
            case 'kitsunebi':
                $return = [
                    'filename' => 'Kitsunebi',
                    'suffix'   => 'txt',
                    'class'    => 'Lists'
                ];
                break;
            case 'surfboard':
                $return = [
                    'filename' => 'Surfboard',
                    'suffix'   => 'conf',
                    'class'    => 'Surfboard'
                ];
                break;
            case 'quantumult':
                if ($value !== null) {
                    if ((int) $value == 2) {
                        $return = self::getSubscribeExtend('quantumult_sub');
                    } else {
                        $return = self::getSubscribeExtend('quantumult_conf');
                    }
                } else {
                    $return = [
                        'filename' => 'Quantumult',
                        'suffix'   => 'conf',
                        'class'    => 'Lists'
                    ];
                }
                break;
            case 'quantumultx':
                $return = [
                    'filename' => 'QuantumultX',
                    'suffix'   => 'txt',
                    'class'    => 'Lists'
                ];
                if ($value !== null) {
                    $return['class'] = 'QuantumultX';
                }
                break;
            case 'shadowrocket':
                $return = [
                    'filename' => 'Shadowrocket',
                    'suffix'   => 'txt',
                    'class'    => 'Lists'
                ];
                break;
            case 'clash_provider':
                $return = [
                    'filename' => 'ClashProvider',
                    'suffix'   => 'yaml',
                    'class'    => 'Lists'
                ];
                break;
            case 'clashr_provider':
                $return = [
                    'filename' => 'ClashRProvider',
                    'suffix'   => 'yaml',
                    'class'    => 'Lists'
                ];
                break;
            case 'quantumult_sub':
                $return = [
                    'filename' => 'QuantumultSub',
                    'suffix'   => 'conf',
                    'class'    => 'Quantumult'
                ];
                break;
            case 'quantumult_conf':
                $return = [
                    'filename' => 'QuantumultConf',
                    'suffix'   => 'conf',
                    'class'    => 'Quantumult'
                ];
                break;
            default:
                $return = [
                    'filename' => 'UndefinedNode',
                    'suffix'   => 'txt',
                    'class'    => 'Sub'
                ];
                break;
        }
        return $return;
    }

    /**
     * 记录订阅日志
     *
     * @param User   $user 用户
     * @param string $type 订阅类型
     * @param string $ua   UA
     *
     * @return void
     */
    private static function Subscribe_log($user, $type, $ua)
    {
        $log                     = new UserSubscribeLog();
        $log->user_name          = $user->user_name;
        $log->user_id            = $user->id;
        $log->email              = $user->email;
        $log->subscribe_type     = $type;
        $log->request_ip         = $_SERVER['REMOTE_ADDR'];
        $log->request_time       = date('Y-m-d H:i:s');
        $antiXss                 = new AntiXSS();
        $log->request_user_agent = $antiXss->xss_clean($ua);
        $log->save();
    }

    /**
     * 响应内容
     *
     * @param User   $user
     * @param array  $response
     * @param string $content  订阅内容
     * @param string $filename 文件名
     */
    public static function getBody($user, $response, $content, $filename): ResponseInterface
    {
        $response = $response
            ->withHeader(
                'Content-type',
                ' application/octet-stream; charset=utf-8'
            )
            ->withHeader(
                'Cache-Control',
                'no-store, no-cache, must-revalidate'
            )
            ->withHeader(
                'Content-Disposition',
                ' attachment; filename=' . $filename
            )
            ->withHeader(
                'Subscription-Userinfo',
                (' upload=' . $user->u
                    . '; download=' . $user->d
                    . '; total=' . $user->transfer_enable
                    . '; expire=' . strtotime($user->class_expire))
            );

        return $response->write($content);
    }

    /**
     * 订阅链接汇总
     *
     * @param User $user 用户
     * @param int  $int  当前用户访问的订阅类型
     *
     * @return array
     */
    public static function getSubinfo($user, $int = 0)
    {
        if ($int == 0) {
            $int = '';
        }
        $userapiUrl = $_ENV['subUrl'] . self::GenerateSSRSubCode($user->id, 0);
        $return_info = [
            'link'            => '',
            // sub
            'ss'              => '?sub=2',
            'ssr'             => '?sub=1',
            'v2ray'           => '?sub=3',
            'trojan'          => '?sub=4',
            // apps
            'ssa'             => '?list=ssa',
            'clash'           => '?clash=1',
            'clash_provider'  => '?list=clash',
            'clashr'          => '?clash=2',
            'clashr_provider' => '?list=clashr',
            'surge'           => '?surge=' . $int,
            'surge_node'      => '?list=surge',
            'surge2'          => '?surge=2',
            'surge3'          => '?surge=3',
            'surge4'          => '?surge=4',
            'surfboard'       => '?surfboard=1',
            'quantumult'      => '?quantumult=' . $int,
            'quantumult_v2'   => '?list=quantumult',
            'quantumult_sub'  => '?quantumult=2',
            'quantumult_conf' => '?quantumult=3',
            'quantumultx'     => '?list=quantumultx',
            'shadowrocket'    => '?list=shadowrocket',
            'kitsunebi'       => '?list=kitsunebi'
        ];

        return array_map(
            function ($item) use ($userapiUrl) {
                return ($userapiUrl . $item);
            },
            $return_info
        );
    }

    public static function getListItem($item, $list)
    {
        $return = null;
        switch ($list) {
            case 'ss':
                $return = AppURI::getItemUrl($item, 1);
                break;
            case 'ssr':
                $return = AppURI::getItemUrl($item, 0);
                break;
            case 'ssa':
                $return = AppURI::getSSJSON($item);
                break;
            case 'surge':
                $return = AppURI::getSurgeURI($item, 3);
                break;
            case 'clash':
                $return = AppURI::getClashURI($item);
                break;
            case 'clashr':
                $return = AppURI::getClashURI($item, true);
                break;
            case 'v2rayn':
                $return = AppURI::getV2RayNURI($item);
                break;
            case 'trojan':
                $return = AppURI::getTrojanURI($item);
                break;
            case 'kitsunebi':
                $return = AppURI::getKitsunebiURI($item);
                break;
            case 'quantumult':
                $return = AppURI::getQuantumultURI($item, true);
                break;
            case 'quantumultx':
                $return = AppURI::getQuantumultXURI($item);
                break;
            case 'shadowrocket':
                $return = AppURI::getShadowrocketURI($item);
                break;
        }
        return $return;
    }

    public static function getLists($user, $list, $opts, $Rule)
    {
        $list = strtolower($list);
        if ($list == 'ssa') {
            $Rule['type'] = 'ss';
        }
        if ($list == 'quantumult') {
            $Rule['type'] = 'vmess';
        }
        if ($list == 'shadowrocket') {
            // Shadowrocket 自带 emoji
            $Rule['emoji'] = false;
        }
        $items = URL::getNew_AllItems($user, $Rule);
        $return = [];
        if ($Rule['extend'] === true) {
            switch ($list) {
                case 'ssa':
                case 'clash':
                case 'clashr':
                    $return = array_merge($return, self::getListExtend($user, $list));
                    break;
                default:
                    $return[] = implode(PHP_EOL, self::getListExtend($user, $list));
                    break;
            }
        }
        foreach ($items as $item) {
            $out = self::getListItem($item, $list);
            if ($out != null) {
                $return[] = $out;
            }
        }
        switch ($list) {
            case 'ssa':
                return json_encode($return, 320);
                break;
            case 'clash':
            case 'clashr':
                return \Symfony\Component\Yaml\Yaml::dump(['proxies' => $return], 4, 2);
            case 'kitsunebi':
            case 'quantumult':
            case 'shadowrocket':
                return base64_encode(implode(PHP_EOL, $return));
            default:
                return implode(PHP_EOL, $return);
        }
    }

    public static function getListExtend($user, $list)
    {
        $return = [];
        $info_array = (count($_ENV['sub_message']) != 0 ? (array) $_ENV['sub_message'] : []);
        if (strtotime($user->expire_in) > time()) {
            if ($user->transfer_enable == 0) {
                $unusedTraffic = '剩余流量：0';
            } else {
                $unusedTraffic = '剩余流量：' . $user->unusedTraffic();
            }
            $expire_in = '过期时间：';
            if ($user->class_expire != '1989-06-04 00:05:00') {
                $userClassExpire = explode(' ', $user->class_expire);
                $expire_in .= $userClassExpire[0];
            } else {
                $expire_in .= '无限期';
            }
        } else {
            $unusedTraffic  = '账户已过期，请续费后使用';
            $expire_in      = '账户已过期，请续费后使用';
        }
        if (!in_array($list, ['quantumult', 'quantumultx', 'shadowrocket'])) {
            $info_array[] = $unusedTraffic;
            $info_array[] = $expire_in;
        }
        $baseUrl = explode('//', $_ENV['baseUrl'])[1];
        $Extend = [
            'remark'          => '',
            'type'            => '',
            'add'             => $baseUrl,
            'address'         => $baseUrl,
            'port'            => 10086,
            'method'          => 'chacha20-ietf',
            'passwd'          => $user->passwd,
            'id'              => $user->getUuid(),
            'aid'             => 0,
            'net'             => 'tcp',
            'headerType'      => 'none',
            'host'            => '',
            'path'            => '/',
            'tls'             => '',
            'protocol'        => 'origin',
            'protocol_param'  => '',
            'obfs'            => 'plain',
            'obfs_param'      => '',
            'group'           => $_ENV['appName']
        ];
        if ($list == 'shadowrocket') {
            $return[] = ('STATUS=' . $unusedTraffic . '.♥.' . $expire_in . PHP_EOL . 'REMARKS=' . $_ENV['appName']);
        }
        foreach ($info_array as $remark) {
            $Extend['remark'] = $remark;
            if (in_array($list, ['kitsunebi', 'quantumult', 'v2rayn'])) {
                $Extend['type'] = 'vmess';
                $out = self::getListItem($Extend, $list);
            } elseif ($list == 'trojan') {
                $Extend['type'] = 'trojan';
                $out = self::getListItem($Extend, $list);
            } elseif ($list == 'ssr') {
                $Extend['type'] = 'ssr';
                $out = self::getListItem($Extend, $list);
            } else {
                $Extend['type'] = 'ss';
                $out = self::getListItem($Extend, $list);
            }
            if ($out !== null) $return[] = $out;
        }
        return $return;
    }

    /**
     * Surge 配置
     *
     * @param User  $user  用户
     * @param int   $surge 订阅类型
     * @param array $opts  request
     * @param array $Rule  节点筛选规则
     *
     * @return string
     */
    public static function getSurge($user, $surge, $opts, $Rule)
    {
        $subInfo = self::getSubinfo($user, $surge);
        $userapiUrl = $subInfo['surge'];
        if ($surge != 4) {
            $Rule['type'] = 'ss';
        }
        $items = URL::getNew_AllItems($user, $Rule);
        $Nodes = [];
        $All_Proxy = '';
        foreach ($items as $item) {
            $out = AppURI::getSurgeURI($item, $surge);
            if ($out !== null) {
                $Nodes[] = $item;
                $All_Proxy .= $out . PHP_EOL;
            }
        }
        $variable = ($surge == 2 ? 'Surge2_Profiles' : 'Surge_Profiles');
        if (isset($opts['profiles']) && in_array($opts['profiles'], array_keys($_ENV[$variable]))) {
            $Profiles = $opts['profiles'];
            $userapiUrl .= ('&profiles=' . $Profiles);
        } else {
            $Profiles = ($surge == 2 ? $_ENV['Surge2_DefaultProfiles'] : $_ENV['Surge_DefaultProfiles']);
        }

        return ConfController::getSurgeConfs($user, $All_Proxy, $Nodes, $_ENV[$variable][$Profiles]);
    }

    /**
     * Quantumult 配置
     *
     * @param User  $user       用户
     * @param int   $quantumult 订阅类型
     * @param array $opts       request
     * @param array $Rule       节点筛选规则
     *
     * @return string
     */
    public static function getQuantumult($user, $quantumult, $opts, $Rule)
    {
        switch ($quantumult) {
            case 2:
                $subUrl = self::getSubinfo($user, 0);
                $str = [
                    '[SERVER]',
                    '',
                    '[SOURCE]',
                    $_ENV['appName'] . ', server ,' . $subUrl['ssr'] . ', false, true, false',
                    $_ENV['appName'] . '_ss, server ,' . $subUrl['ss'] . ', false, true, false',
                    $_ENV['appName'] . '_VMess, server ,' . $subUrl['quantumult_v2'] . ', false, true, false',
                    'Hackl0us Rules, filter, https://raw.githubusercontent.com/Hackl0us/Surge-Rule-Snippets/master/LAZY_RULES/Quantumult.conf, true',
                    '',
                    '[DNS]',
                    'system, 119.29.29.29, 223.6.6.6, 114.114.114.114',
                    '',
                    '[STATE]',
                    'STATE,AUTO'
                ];
                return implode(PHP_EOL, $str);
                break;
            case 3:
                $items = URL::getNew_AllItems($user, $Rule);
                break;
            default:
                return self::getLists($user, 'quantumult', $opts, $Rule);
                break;
        }

        $All_Proxy          = '';
        $All_Proxy_name     = '';
        $BackChina_name     = '';
        foreach ($items as $item) {
            $out = AppURI::getQuantumultURI($item);
            if ($out !== null) {
                $All_Proxy .= $out . PHP_EOL;
                if (strpos($item['remark'], '回国') || strpos($item['remark'], 'China')) {
                    $BackChina_name .= "\n" . $item['remark'];
                } else {
                    $All_Proxy_name .= "\n" . $item['remark'];
                }
            }
        }
        $ProxyGroups = [
            'proxy_group'       => base64_encode("🍃 Proxy  :  static, 🏃 Auto\n🏃 Auto\n🚀 Direct\n" . $All_Proxy_name),
            'domestic_group'    => base64_encode("🍂 Domestic  :  static, 🚀 Direct\n🚀 Direct\n🍃 Proxy\n" . $BackChina_name),
            'others_group'      => base64_encode("☁️ Others  :   static, 🍃 Proxy\n🚀 Direct\n🍃 Proxy"),
            'direct_group'      => base64_encode("🚀 Direct : static, DIRECT\nDIRECT"),
            'apple_group'       => base64_encode("🍎 Only  :  static, 🚀 Direct\n🚀 Direct\n🍃 Proxy"),
            'auto_group'        => base64_encode("🏃 Auto  :  auto\n" . $All_Proxy_name),
        ];
        $render = ConfRender::getTemplateRender();
        $render->assign('All_Proxy', $All_Proxy)->assign('ProxyGroups', $ProxyGroups);

        return $render->fetch('quantumult/quantumult.tpl');
    }

    /**
     * QuantumultX 配置
     *
     * @param User  $user        用户
     * @param int   $quantumultx 订阅类型
     * @param array $opts        request
     * @param array $Rule        节点筛选规则
     *
     * @return string
     */
    public static function getQuantumultX($user, $quantumultx, $opts, $Rule)
    {
        return '';
    }

    /**
     * Surfboard 配置
     *
     * @param User  $user      用户
     * @param int   $surfboard 订阅类型
     * @param array $opts      request
     * @param array $Rule      节点筛选规则
     *
     * @return string
     */
    public static function getSurfboard($user, $surfboard, $opts, $Rule)
    {
        $subInfo = self::getSubinfo($user, 0);
        $userapiUrl = $subInfo['surfboard'];
        $Nodes = [];
        $All_Proxy = '';
        $items = URL::getNew_AllItems($user, $Rule);
        foreach ($items as $item) {
            $out = AppURI::getSurfboardURI($item);
            if ($out !== null) {
                $Nodes[] = $item;
                $All_Proxy .= $out . PHP_EOL;
            }
        }
        if (isset($opts['profiles']) && in_array($opts['profiles'], array_keys($_ENV['Surfboard_Profiles']))) {
            $Profiles = $opts['profiles'];
            $userapiUrl .= ('&profiles=' . $Profiles);
        } else {
            $Profiles = $_ENV['Surfboard_DefaultProfiles']; // 默认策略组
        }

        return ConfController::getSurgeConfs($user, $All_Proxy, $Nodes, $_ENV['Surfboard_Profiles'][$Profiles]);
    }

    /**
     * Clash 配置
     *
     * @param User  $user  用户
     * @param int   $clash 订阅类型
     * @param array $opts  request
     * @param array $Rule  节点筛选规则
     *
     * @return string
     */
    public static function getClash($user, $clash, $opts, $Rule)
    {
        $subInfo = self::getSubinfo($user, $clash);
        $userapiUrl = $subInfo['clash'];
        $ssr_support = ($clash == 2 ? true : false);
        $items = URL::getNew_AllItems($user, $Rule);
        $Proxys = [];
        foreach ($items as $item) {
            $Proxy = AppURI::getClashURI($item, $ssr_support);
            if ($Proxy !== null) {
                $Proxys[] = $Proxy;
            }
        }
        if (isset($opts['profiles']) && in_array($opts['profiles'], array_keys($_ENV['Clash_Profiles']))) {
            $Profiles = $opts['profiles'];
            $userapiUrl .= ('&profiles=' . $Profiles);
        } else {
            $Profiles = $_ENV['Clash_DefaultProfiles']; // 默认策略组
        }

        return ConfController::getClashConfs($user, $Proxys, $_ENV['Clash_Profiles'][$Profiles]);
    }

    /**
     * 通用订阅，ssr & v2rayn
     *
     * @param User   $user 用户
     * @param int    $sub  订阅类型
     * @param array  $opts request
     * @param array  $Rule 节点筛选规则
     *
     * @return string
     */
    public static function getSub($user, $sub, $opts, $Rule)
    {
        $return_url = '';
        switch ($sub) {
            case 2: // SS
                $Rule['type'] = 'ss';
                $getListExtend = $Rule['extend'] ? self::getListExtend($user, 'ss') : [];
                break;
            case 3: // V2
                $Rule['type'] = 'vmess';
                $getListExtend = $Rule['extend'] ? self::getListExtend($user, 'v2rayn') : [];
                break;
            case 4: // Trojan
                $Rule['type'] = 'trojan';
                $getListExtend = $Rule['extend'] ? self::getListExtend($user, 'trojan') : [];
                break;
            default: // SSR
                $Rule['type'] = 'ssr';
                $getListExtend = $Rule['extend'] ? self::getListExtend($user, 'ssr') : [];
                break;
        }
        if ($Rule['extend']) {
            $return_url .= implode(PHP_EOL, $getListExtend) . PHP_EOL;
        }
        $return_url .= URL::get_NewAllUrl($user, $Rule);
        return base64_encode($return_url);
    }
}
