<?php

//Thanks to http://blog.csdn.net/jollyjumper/article/details/9823047

namespace App\Controllers;

use App\Models\Link;
use App\Models\User;
use App\Models\Smartline;
use App\Utils\ConfRender;
use App\Utils\Tools;
use App\Utils\URL;
use App\Services\Config;

/**
 *  HomeController
 */
class LinkController extends BaseController
{
    public static function GenerateRandomLink()
    {
        for ($i = 0; $i < 10; $i++) {
            $token = Tools::genRandomChar(16);
            $Elink = Link::where('token', '=', $token)->first();
            if ($Elink == null) {
                return $token;
            }
        }

        return "couldn't alloc token";
    }

    public static function GenerateSSRSubCode($userid, $without_mu)
    {
        $Elink = Link::where('type', '=', 11)->where('userid', '=', $userid)->where('geo', $without_mu)->first();
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
        $token = $args['token'];

        //$builder->getPhrase();
        $Elink = Link::where('token', '=', $token)->first();
        if ($Elink == null) {
            return null;
        }

        if ($Elink->type != 11) {
            return null;
        }

        $user = User::where('id', $Elink->userid)->first();
        if ($user == null) {
            return null;
        }

        $mu = 0;
        if (isset($request->getQueryParams()['mu'])) {
            $mu = (int)$request->getQueryParams()['mu'];
        }

        $newResponse = $response->withHeader('Content-type', ' application/octet-stream; charset=utf-8')->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate')->withHeader('Content-Disposition', ' attachment; filename=' . $token . '.txt');
        $newResponse->getBody()->write(self::GetSSRSub(User::where('id', '=', $Elink->userid)->first(), $mu));
        return $newResponse;
    }

    public const V2RYA_MU = 2;
    public const SSD_MU = 3;
    public const CLASH_MU = 4;

    public static function GetSSRSub($user, $mu = 0)
    {
        if ($mu == 0 || $mu == 1) {
            return Tools::base64_url_encode(URL::getAllUrl($user, $mu, 0));
        }

        if ($mu == self::V2RYA_MU) {
            return Tools::base64_url_encode(URL::getAllVMessUrl($user));
        }

        if ($mu == self::SSD_MU) {
            return URL::getAllSSDUrl($user);
        }

        if ($mu == self::CLASH_MU) {
            // Clash
            return self::GetClash($user);
        }
    }

    public static function GetClash($user)
    {
        $confs = [];
        $proxy_confs = [];
        // ss
        $items = array_merge(URL::getAllItems($user, 0, 1), URL::getAllItems($user, 1, 1));
        foreach ($items as $item) {
            $sss = [
                'name' => $item['remark'],
                'type' => 'ss',
                'server' => $item['address'],
                'port' => $item['port'],
                'cipher' => $item['method'],
                'password' => $item['passwd'],
            ];
            if ($item['obfs'] != 'plain') {
                switch ($item['obfs']) {
                    case 'simple_obfs_http':
                        $sss['plugin'] = 'obfs';
                        $sss['plugin-opts']['mode'] = 'http';
                        break;
                    case 'simple_obfs_tls':
                        $sss['plugin'] = 'obfs';
                        $sss['plugin-opts']['mode'] = 'tls';
                        break;
                    case 'v2ray':
                        $sss['plugin'] = 'v2ray-plugin';
                        $sss['plugin-opts']['mode'] = 'websocket';
                        if (strpos($item['obfs_param'], 'security=tls')) {
                            $sss['plugin-opts']['tls'] = true;
                        }
                        $sss['plugin-opts']['host'] = $user->getMuMd5();
                        $sss['plugin-opts']['path'] = $item['path'];
                        break;
                }
                if ($item['obfs'] != 'v2ray') {
                    if ($item['obfs_param'] != '') {
                        $sss['plugin-opts']['host'] = $item['obfs_param'];
                    } elseif ($user->obfs_param != '') {
                        $sss['plugin-opts']['host'] = $user->obfs_param;
                    } else {
                        $sss['plugin-opts']['host'] = 'wns.windows.com';
                    }
                }
            }
            $proxy_confs[] = $sss;
            $confs[] = $sss;
        }
        // v2
        $items = URL::getAllVMessUrl($user, 1);
        foreach ($items as $item) {
            if (in_array($item['net'], array('kcp', 'http', 'quic'))) {
                continue;
            }
            $v2rays = [
                'name' => $item['ps'],
                'type' => 'vmess',
                'server' => $item['add'],
                'port' => $item['port'],
                'uuid' => $item['id'],
                'alterId' => $item['aid'],
                'cipher' => 'auto',
            ];
            if ($item['net'] == 'ws') {
                $v2rays['network'] = 'ws';
                $v2rays['ws-path'] = $item['path'];
                if ($item['tls'] == 'tls') {
                    $v2rays['tls'] = true;
                }
                if ($item['host'] != '') {
                    $v2rays['ws-headers']['Host'] = $item['host'];
                }
            } elseif ($item['net'] == 'tls') {
                $v2rays['tls'] = true;
            }
            $proxy_confs[] = $v2rays;
            $confs[] = $v2rays;
        }
        $render = ConfRender::getTemplateRender();
        $render->assign('user', $user)
            ->assign('confs', $confs)
            ->assign(
                'proxies',
                array_map(
                    static function ($conf) {
                        return $conf['name'];
                    },
                    $proxy_confs
                )
            );
        return $render->fetch('clash.tpl');
    }

    public static function getSSPcConf($user)
    {
        $proxy = [];
        $items = URL::getAllItems($user, 0, 1);
        foreach ($items as $item) {
            $proxy_plugin = '';
            $proxy_plugin_opts = '';
            if ($item['obfs'] == 'v2ray' || in_array($item['obfs'], Config::getSupportParam('ss_obfs'))) {
                if ($item['obfs'] == 'v2ray') {
                    $proxy_plugin .= 'v2ray';
                } else {
                    $proxy_plugin .= 'obfs-local';
                }
                if (strpos($item['obfs'], 'http') !== false) {
                    $proxy_plugin_opts .= 'obfs=http';
                } elseif (strpos($item['obfs'], 'tls') !== false) {
                    $proxy_plugin_opts .= 'obfs=tls';
                } else {
                    $proxy_plugin_opts .= 'v2ray;' . $item['obfs_param'];
                }
                if ($item['obfs_param'] != '' && $item['obfs'] != 'v2ray') {
                    $proxy_plugin_opts .= ';obfs-host=' . $item['obfs_param'];
                }
            }
            $proxy[] = [
                'remarks' => $item['remark'],
                'server' => $item['address'],
                'server_port' => $item['port'],
                'method' => $item['method'],
                'password' => $item['passwd'],
                'timeout' => 5,
                'plugin' => $proxy_plugin,
                'plugin_opts' => $proxy_plugin_opts
            ];
        }
        $config = [
            'configs' => $proxy,
            'strategy' => null,
            'index' => 0,
            'global' => false,
            'enabled' => true,
            'shareOverLan' => false,
            'isDefault' => false,
            'localPort' => 1080,
            'portableMode' => true,
            'pacUrl' => null,
            'useOnlinePac' => false,
            'secureLocalPac' => true,
            'availabilityStatistics' => false,
            'autoCheckUpdate' => true,
            'checkPreRelease' => false,
            'isVerboseLogging' => false,
            'logViewer' => [
              'topMost' => false,
              'wrapText' => false,
              'toolbarShown' => false,
              'Font' => 'Consolas, 8pt',
              'BackgroundColor' => 'Black',
              'TextColor' => 'White'
            ],
            'proxy' => [
              'useProxy' => false,
              'proxyType' => 0,
              'proxyServer' => '',
              'proxyPort' => 0,
              'proxyTimeout' => 3
            ],
            'hotkey' => [
              'SwitchSystemProxy' => '',
              'SwitchSystemProxyMode' => '',
              'SwitchAllowLan' => '',
              'ShowLogs' => '',
              'ServerMoveUp' => '',
              'ServerMoveDown' => '',
              'RegHotkeysAtStartup' => false
            ]
        ];

        return json_encode($config, JSON_PRETTY_PRINT);
    }

    public static function getSSRPcConf($user)
    {
        $proxy = [];
        $items = URL::getAllItems($user, 0, 0);
        foreach ($items as $item) {
            $proxy[] = [
                'remarks' => $item['remark'],
                'server' => $item['address'],
                'server_port' => $item['port'],
                'method' => $item['method'],
                'obfs' => $item['obfs'],
                'obfsparam' => $item['obfs_param'],
                'remarks_base64' => base64_encode($item['remark']),
                'password' => $item['passwd'],
                'tcp_over_udp' => false,
                'udp_over_tcp' => false,
                'group' => Config::get('appName'),
                'protocol' => $item['protocol'],
                'protocolparam' => $item['protocol_param'],
                'obfs_udp' => false,
                'enable' => true
            ];
        }
        $config = [
            'configs' => $proxy,
            'index' => 0,
            'random' => true,
            'sysProxyMode' => 1,
            'shareOverLan' => false,
            'localPort' => 1080,
            'localAuthPassword' => Tools::genRandomChar(26),
            'dnsServer' => '',
            'reconnectTimes' => 2,
            'balanceAlgorithm' => 'LowException',
            'randomInGroup' => false,
            'TTL' => 0,
            'connectTimeout' => 5,
            'proxyRuleMode' => 2,
            'proxyEnable' => false,
            'pacDirectGoProxy' => false,
            'proxyType' => 0,
            'proxyHost' => '',
            'proxyPort' => 0,
            'proxyAuthUser' => '',
            'proxyAuthPass' => '',
            'proxyUserAgent' => '',
            'authUser' => '',
            'authPass' => '',
            'autoBan' => false,
            'sameHostForSameTarget' => false,
            'keepVisitTime' => 180,
            'isHideTips' => false,
            'nodeFeedAutoUpdate' => true,
            'serverSubscribes' => [
                [
                    'URL' => (Config::get('subUrl') . self::GenerateSSRSubCode($user->id, 0) . '?mu=0'),
                    'Group' => Config::get('appName'),
                    'LastUpdateTime' => 0
                ]
            ],
            'token' => [],
            'portMap' => []
        ];

        return json_encode($config, JSON_PRETTY_PRINT);
    }

    public static function getSSDPcConf($user)
    {
        $id = 1;
        $proxy = [];
        $items = URL::getAllItems($user, 0, 1);
        foreach ($items as $item) {
            $proxy_plugin = '';
            $proxy_plugin_opts = '';
            if ($item['obfs'] == 'v2ray' || in_array($item['obfs'], Config::getSupportParam('ss_obfs'))) {
                if ($item['obfs'] == 'v2ray') {
                    $proxy_plugin .= 'v2ray';
                } else {
                    $proxy_plugin .= 'simple-obfs';
                }
                if (strpos($item['obfs'], 'http') !== false) {
                    $proxy_plugin_opts .= 'obfs=http';
                } elseif (strpos($item['obfs'], 'tls') !== false) {
                    $proxy_plugin_opts .= 'obfs=tls';
                } else {
                    $proxy_plugin_opts .= 'v2ray;' . $item['obfs_param'];
                }
                if ($item['obfs_param'] != '' && $item['obfs'] != 'v2ray') {
                    $proxy_plugin_opts .= ';obfs-host=' . $item['obfs_param'];
                }
            }
            $proxy[] = [
                'remarks' => $item['remark'],
                'server' => $item['address'],
                'server_port' => $item['port'],
                'password' => $item['passwd'],
                'method' => $item['method'],
                'plugin' => $proxy_plugin,
                'plugin_opts' => $proxy_plugin_opts,
                'plugin_args' => '',
                'timeout' => 5,
                'id' => $id,
                'ratio' => $item['ratio'],
                'subscription_url' => (Config::get('subUrl') . self::GenerateSSRSubCode($user->id, 0) . '?mu=3')
            ];
            $id++;
        }
        $plugin = '';
        $plugin_opts = '';
        if ($user->obfs == 'v2ray' || in_array($user->obfs, Config::getSupportParam('ss_obfs'))) {
            if ($user->obfs == 'v2ray') {
                $plugin .= 'v2ray';
            } else {
                $plugin .= 'simple-obfs';
            }
            if (strpos($user->obfs, 'http') !== false) {
                $plugin_opts .= 'obfs=http';
            } elseif (strpos($user->obfs, 'tls') !== false) {
                $plugin_opts .= 'obfs=tls';
            } else {
                $plugin_opts .= 'v2ray;' . $user->obfs_param;
            }
            if ($user->obfs_param != '' && $user->obfs != 'v2ray') {
                $plugin_opts .= ';obfs-host=' . $user->obfs_param;
            }
        }
        $config = [
            'configs' => $proxy,
            'strategy' => null,
            'index' => 0,
            'global' => false,
            'enabled' => true,
            'shareOverLan' => false,
            'isDefault' => false,
            'localPort' => 1080,
            'portableMode' => true,
            'pacUrl' => null,
            'useOnlinePac' => false,
            'secureLocalPac' => true,
            'availabilityStatistics' => false,
            'autoCheckUpdate' => true,
            'checkPreRelease' => false,
            'isVerboseLogging' => false,
            'logViewer' => [
              'topMost' => false,
              'wrapText' => false,
              'toolbarShown' => false,
              'Font' => 'Consolas, 8pt',
              'BackgroundColor' => 'Black',
              'TextColor' => 'White'
            ],
            'proxy' => [
              'useProxy' => false,
              'proxyType' => 0,
              'proxyServer' => '',
              'proxyPort' => 0,
              'proxyTimeout' => 3
            ],
            'hotkey' => [
              'SwitchSystemProxy' => '',
              'SwitchSystemProxyMode' => '',
              'SwitchAllowLan' => '',
              'ShowLogs' => '',
              'ServerMoveUp' => '',
              'ServerMoveDown' => '',
              'RegHotkeysAtStartup' => false
            ],
            'subscriptions' => [
              [
                'airport' => Config::get('appName'),
                'encryption' => $user->method,
                'password' => $user->passwd,
                'port' => $user->port,
                'expiry' => $user->class_expire,
                'traffic_used' => Tools::flowToGB($user->u + $user->d),
                'traffic_total' => Tools::flowToGB($user->transfer_enable),
                'url' => (Config::get('subUrl') . self::GenerateSSRSubCode($user->id, 0) . '?mu=3'),
                'plugin' => $plugin,
                'plugin_options' => $plugin_opts,
                'plugin_arguments' => '',
                'use_proxy' => false
              ]
            ]
        ];

        return json_encode($config, JSON_PRETTY_PRINT);
    }
}
