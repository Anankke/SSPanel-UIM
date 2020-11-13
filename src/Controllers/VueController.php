<?php

namespace App\Controllers;

use App\Models\{
    Ann,
    Code,
    Node,
    User,
    Shop,
    Relay,
    Payback,
    InviteCode
};
use App\Utils\{
    URL,
    Tools,
    Geetest,
    TelegramSessionManager,
    Cookie
};
use App\Services\{
    Auth,
    Config
};
use Slim\Http\{Request, Response};
use Psr\Http\Message\ResponseInterface;

class VueController extends BaseController
{
    public function getGlobalConfig($request, $response, $args)
    {
        $GtSdk = null;
        $recaptcha_sitekey = null;
        $user = $this->user;
        if ($_ENV['captcha_provider'] != '') {
            switch ($_ENV['captcha_provider']) {
                case 'recaptcha':
                    $recaptcha_sitekey = $_ENV['recaptcha_sitekey'];
                    break;
                case 'geetest':
                    $uid = time() . random_int(1, 10000);
                    $GtSdk = Geetest::get($uid);
                    break;
            }
        }

        if ($_ENV['enable_telegram'] == true) {
            $login_text = TelegramSessionManager::add_login_session();
            $login = explode('|', $login_text);
            $login_token = $login[0];
            $login_number = $login[1];
        } else {
            $login_token = '';
            $login_number = '';
        }
        $themes = Tools::getDir(BASE_PATH . '/resources/views');

        $res['globalConfig'] = array(
            'geetest_html'            => $GtSdk,
            'login_token'             => $login_token,
            'login_number'            => $login_number,
            'telegram_bot'            => $_ENV['telegram_bot'],
            'enable_logincaptcha'     => $_ENV['enable_login_captcha'],
            'enable_regcaptcha'       => $_ENV['enable_reg_captcha'],
            'enable_checkin_captcha'  => $_ENV['enable_checkin_captcha'],
            'base_url'                => $_ENV['baseUrl'],
            'recaptcha_sitekey'       => $recaptcha_sitekey,
            'captcha_provider'        => $_ENV['captcha_provider'],
            'jump_delay'              => $_ENV['jump_delay'],
            'register_mode'           => Config::getconfig('Register.string.Mode'),
            'enable_email_verify'     => Config::getconfig('Register.bool.Enable_email_verify'),
            'appName'                 => $_ENV['appName'],
            'dateY'                   => date('Y'),
            'isLogin'                 => $user->isLogin,
            'enable_telegram'         => $_ENV['enable_telegram'],
            'enable_mylivechat'       => $_ENV['enable_mylivechat'],
            'enable_flag'             => $_ENV['enable_flag'],
            'enable_ticket'           => $_ENV['enable_ticket'],
            'payment_type'            => $_ENV['payment_system'],
            'mylivechat_id'           => $_ENV['mylivechat_id'],
            'enable_kill'             => $_ENV['enable_kill'],
            'subscribeLog'            => $_ENV['subscribeLog'],
            'subscribeLog_show'       => $_ENV['subscribeLog_show'],
            'themes'                  => $themes,
            'use_new_telegram_bot'    => $_ENV['use_new_telegram_bot']
        );

        $res['ret'] = 1;

        return $response->getBody()->write(json_encode($res));
    }

    public function vuelogout($request, $response, $args)
    {
        Auth::logout();
        $res['ret'] = 1;
        return $response->getBody()->write(json_encode($res));
    }

    public function getUserInfo($request, $response, $args)
    {
        $user = $this->user;

        if (!$user->isLogin) {
            $res['ret'] = -1;
            return $response->getBody()->write(json_encode($res));
        }

        $pre_user = URL::cloneUser($user);
        $user->ssr_url_all = URL::get_NewAllUrl($pre_user, ['type' => 'ssr']);
        $user->ssr_url_all_mu = URL::get_NewAllUrl($pre_user, ['type' => 'ssr', 'is_mu' => 1]);
        $user->ss_url_all = URL::get_NewAllUrl($pre_user, ['type' => 'ss']);
        $ssinfo = URL::getSSConnectInfo($pre_user);
        $user->vmess_url_all = URL::getAllVMessUrl($user);
        $user->isAbleToCheckin = $user->isAbleToCheckin();
        $ssr_sub_token = LinkController::GenerateSSRSubCode($this->user->id);
        $GtSdk = null;
        $recaptcha_sitekey = null;
        if ($_ENV['captcha_provider'] != '') {
            switch ($_ENV['captcha_provider']) {
                case 'recaptcha':
                    $recaptcha_sitekey = $_ENV['recaptcha_sitekey'];
                    break;
                case 'geetest':
                    $uid = time() . random_int(1, 10000);
                    $GtSdk = Geetest::get($uid);
                    break;
            }
        }
        $can_backtoadmin = 0;
        if (Cookie::get('old_uid') && Cookie::get('old_email') && Cookie::get('old_key') && Cookie::get('old_ip') && Cookie::get('old_expire_in') && Cookie::get('old_local')) {
            $can_backtoadmin = 1;
        }
        $Ann = Ann::orderBy('date', 'desc')->first();
        $display_ios_class = $_ENV['display_ios_class'];
        $display_ios_topup = $_ENV['display_ios_topup'];
        $ios_account = null;
        $ios_password = null;
        $show_ios_account = false;
        if ($user->class >= $display_ios_class && $user->get_top_up() >= $display_ios_topup || $user->is_admin) {
            $ios_account = $_ENV['ios_account'];
            $ios_password = $_ENV['ios_password'];
            $show_ios_account = true;
        }
        $mergeSub = $_ENV['mergeSub'];
        $subUrl = $_ENV['subUrl'];
        $baseUrl = $_ENV['baseUrl'];
        $user['online_ip_count'] = $user->online_ip_count();
        $bind_token = TelegramSessionManager::add_bind_session($this->user);
        $subInfo = LinkController::getSubinfo($this->user, 0);
        $url_subinfo = array();
        foreach ($subInfo as $key => $value) {
            $url_subinfo[$key] = urlencode($value);
        }

        $res['info'] = array(
            'user' => $user,
            'ssrSubToken' => $ssr_sub_token,
            'displayIosClass' => $display_ios_class,
            'display_ios_topup' => $display_ios_topup,
            'show_ios_account' => $show_ios_account,
            'iosAccount' => $ios_account,
            'iosPassword' => $ios_password,
            'mergeSub' => $mergeSub,
            'subUrl' => $subUrl,
            'subInfo' => $subInfo,
            'url_subinfo' => $url_subinfo,
            'baseUrl' => $baseUrl,
            'can_backtoadmin' => $can_backtoadmin,
            'ann' => $Ann,
            'recaptchaSitekey' => $recaptcha_sitekey,
            'GtSdk' => $GtSdk,
            'GaUrl' => $user->getGAurl(),
            'bind_token' => $bind_token,
            'gravatar' => $user->gravatar
        );

        $res['ret'] = 1;

        return $response->getBody()->write(json_encode($res));
    }

    public function telegramReset($request, $response, $args)
    {
        $user = $this->user;
        $user->telegram_id = 0;
        $user->save();
        $res['ret'] = 1;
        $res['msg'] = '解绑成功';
        return $response->getBody()->write(json_encode($res));
    }

    public function getUserInviteInfo($request, $response, $args)
    {
        $user = $this->user;

        if (!$user->isLogin) {
            $res['ret'] = -1;
            return $response->getBody()->write(json_encode($res));
        }

        $code = InviteCode::where('user_id', $user->id)->first();
        if ($code == null) {
            $user->addInviteCode();
            $code = InviteCode::where('user_id', $user->id)->first();
        }

        $pageNum = $request->getParam('current');

        $paybacks = Payback::where('ref_by', $user->id)->orderBy('id', 'desc')->paginate(15, ['*'], 'page', $pageNum);
        if (!$paybacks_sum = Payback::where('ref_by', $user->id)->sum('ref_get')) {
            $paybacks_sum = 0;
        }
        $paybacks->setPath('/#/user/panel');
        foreach ($paybacks as $payback)
        {
            $payback['user_name'] = $payback->user() != null ? $payback->user()->user_name : '已注销';
        };

        $res['inviteInfo'] = array(
            'code'              => $code,
            'paybacks'          => $paybacks,
            'paybacks_sum'      => $paybacks_sum,
            'invite_num'        => $user->invite_num,
            'invitePrice'       => $_ENV['invite_price'],
            'customPrice'       => $_ENV['custom_invite_price'],
            'invite_gift'       => $_ENV['invite_gift'],
            'invite_get_money'  => (int) Config::getconfig('Register.string.defaultInvite_get_money'),
            'code_payback'      => $_ENV['code_payback'],
        );

        $res['ret'] = 1;

        return $response->getBody()->write(json_encode($res));
    }

    public function getUserShops($request, $response, $args)
    {
        $user = $this->user;

        if (!$user->isLogin) {
            $res['ret'] = -1;
            return $response->getBody()->write(json_encode($res));
        }

        $shops = Shop::where('status', 1)->orderBy('name')->get();

        $res['arr'] = array(
            'shops' => $shops,
        );
        $res['ret'] = 1;

        return $response->getBody()->write(json_encode($res));
    }

    public function getAllResourse($request, $response, $args)
    {
        $user = $this->user;

        if (!$user->isLogin) {
            $res['ret'] = -1;
            return $response->getBody()->write(json_encode($res));
        }

        $res['resourse'] = array(
            'money' => $user->money,
            'class' => $user->class,
            'class_expire' => $user->class_expire,
            'expire_in' => $user->expire_in,
            'online_ip_count' => $user->online_ip_count(),
            'node_speedlimit' => $user->node_speedlimit,
            'node_connector' => $user->node_connector,
        );
        $res['ret'] = 1;

        return $response->getBody()->write(json_encode($res));
    }

    public function getNewSubToken($request, $response, $args)
    {
        $user = $this->user;

        if (!$user->isLogin) {
            $res['ret'] = -1;
            return $response->getBody()->write(json_encode($res));
        }

        $user->clean_link();
        $ssr_sub_token = LinkController::GenerateSSRSubCode($this->user->id);

        $res['arr'] = array(
            'ssr_sub_token' => $ssr_sub_token,
        );

        $res['ret'] = 1;

        return $response->getBody()->write(json_encode($res));
    }

    public function getNewInviteCode($request, $response, $args)
    {
        $user = $this->user;

        if (!$user->isLogin) {
            $res['ret'] = -1;
            return $response->getBody()->write(json_encode($res));
        }

        $user->clear_inviteCodes();
        $code = InviteCode::where('user_id', $this->user->id)->first();
        if ($code == null) {
            $this->user->addInviteCode();
            $code = InviteCode::where('user_id', $this->user->id)->first();
        }

        $res['arr'] = array(
            'code' => $code,
        );

        $res['ret'] = 1;

        return $response->getBody()->write(json_encode($res));
    }

    public function getTransfer($request, $response, $args)
    {
        $user = $this->user;

        if (!$user->isLogin) {
            $res['ret'] = -1;
            return $response->getBody()->write(json_encode($res));
        }

        $res['arr'] = array(
            'todayUsedTraffic' => $user->TodayusedTraffic(),
            'lastUsedTraffic' => $user->LastusedTraffic(),
            'unUsedTraffic' => $user->unusedTraffic(),
        );

        $res['ret'] = 1;

        return $response->getBody()->write(json_encode($res));
    }

    public function getCaptcha($request, $response, $args)
    {
        $GtSdk = null;
        $recaptcha_sitekey = null;
        if ($_ENV['captcha_provider'] != '') {
            switch ($_ENV['captcha_provider']) {
                case 'recaptcha':
                    $recaptcha_sitekey = $_ENV['recaptcha_sitekey'];
                    $res['recaptchaKey'] = $recaptcha_sitekey;
                    break;
                case 'geetest':
                    $uid = time() . random_int(1, 10000);
                    $GtSdk = Geetest::get($uid);
                    $res['GtSdk'] = $GtSdk;
                    break;
            }
        }

        $res['respon'] = 1;
        return $response->getBody()->write(json_encode($res));
    }

    public function getChargeLog($request, $response, $args)
    {
        $user = $this->user;

        if (!$user->isLogin) {
            $res['ret'] = -1;
            return $response->getBody()->write(json_encode($res));
        }

        $pageNum = $request->getParam('current');

        $codes = Code::where('type', '<>', '-2')->where('userid', '=', $user->id)->orderBy('id', 'desc')->paginate(15, ['*'], 'page', $pageNum);
        $codes->setPath('/#/user/code');

        $res['codes'] = $codes;
        $res['ret'] = 1;

        return $response->getBody()->write(json_encode($res));
    }

    public function getNodeList($request, $response, $args)
    {
        $user = Auth::getUser();

        if (!$this->user->isLogin) {
            $res['ret'] = -1;
            return $response->getBody()->write(json_encode($res));
        }

        $nodes = Node::where('type', 1)->orderBy('node_class')->orderBy('name')->get();
        $relay_rules = Relay::where('user_id', $this->user->id)->orwhere('user_id', 0)->orderBy('id', 'asc')->get();
        if (!Tools::is_protocol_relay($user)) {
            $relay_rules = array();
        }

        $array_nodes = array();
        $nodes_muport = array();

        foreach ($nodes as $node) {
            if ($node->node_group != $user->node_group && $node->node_group != 0) {
                continue;
            }
            if ($node->sort == 9) {
                $mu_user = User::where('port', '=', $node->server)->first();
                $mu_user->obfs_param = $this->user->getMuMd5();
                $nodes_muport[] = array('server' => $node, 'user' => $mu_user);
                continue;
            }
            $array_node = array();

            $array_node['id'] = $node->id;
            $array_node['class'] = $node->node_class;
            $array_node['name'] = $node->name;
            if ($this->user->class < $node->node_class) {
                $array_node['server'] = '***.***.***.***';
            } elseif ($node->sort == 13) {
                $server = Tools::ssv2Array($node->server);
                $array_node['server'] = $server['add'];
            } else {
                $array_node['server'] = $node->server;
            }

            $array_node['sort'] = $node->sort;
            $array_node['info'] = $node->info;
            $array_node['mu_only'] = $node->mu_only;
            $array_node['group'] = $node->node_group;

            $array_node['raw_node'] = $node;
            $regex = $_ENV['flag_regex'];
            $matches = array();
            preg_match($regex, $node->name, $matches);
            if (isset($matches[0])) {
                $array_node['flag'] = $matches[0] . '.png';
            } else {
                $array_node['flag'] = 'unknown.png';
            }

            $node_online = $node->isNodeOnline();
            if ($node_online === null) {
                $array_node['online'] = 0;
            } elseif ($node_online === true) {
                $array_node['online'] = 1;
            } elseif ($node_online === false) {
                $array_node['online'] = -1;
            }

            if (in_array($node->sort, array(0, 7, 8, 10, 11, 12, 13))) {
                $array_node['online_user'] = $node->getOnlineUserCount();
            } else {
                $array_node['online_user'] = -1;
            }

            $nodeLoad = $node->getNodeLoad();
            if (isset($nodeLoad[0]['load'])) {
                $array_node['latest_load'] = (explode(' ', $nodeLoad[0]['load']))[0] * 100;
            } else {
                $array_node['latest_load'] = -1;
            }

            $array_node['traffic_used'] = (int) Tools::flowToGB($node->node_bandwidth);
            $array_node['traffic_limit'] = (int) Tools::flowToGB($node->node_bandwidth_limit);
            if ($node->node_speedlimit == 0.0) {
                $array_node['bandwidth'] = 0;
            } elseif ($node->node_speedlimit >= 1024.00) {
                $array_node['bandwidth'] = round($node->node_speedlimit / 1024.00, 1) . 'Gbps';
            } else {
                $array_node['bandwidth'] = $node->node_speedlimit . 'Mbps';
            }

            $array_node['traffic_rate'] = $node->traffic_rate;
            $array_node['status'] = $node->status;

            $array_nodes[] = $array_node;
        }

        $res['nodeinfo'] = array(
            'nodes' => $array_nodes,
            'nodes_muport' => $nodes_muport,
            'relay_rules' => $relay_rules,
            'user' => $user,
            'tools' => new Tools(),
        );
        $res['ret'] = 1;

        return $response->getBody()->write(json_encode($res));
    }

    /**
     * @param Request   $requesr
     * @param Response  $response
     * @param array     $args
     */
    public function getNodeInfo($request, $response, $args): ResponseInterface
    {
        $user = $this->user;
        $id = $args['id'];
        $mu = $request->getQueryParam('ismu', 0);
        $relay_rule_id = $request->getQueryParam('relay_rule', 0);
        $node = Node::find($id);


        if ($node == null) {
            return $response->withJson([null]);
        }

        $ssr_item = $node->getItem($user, $mu, $relay_rule_id, 0);
        $ss_item = $node->getItem($user, $mu, $relay_rule_id, 1);

        switch ($node->sort) {
            case 0:
                if ((($user->class >= $node->node_class
                        && ($user->node_group == $node->node_group || $node->node_group == 0)) || $user->is_admin)
                    && ($node->node_bandwidth_limit == 0 || $node->node_bandwidth < $node->node_bandwidth_limit)
                ) {

                    $res = [
                        'ret' => 1,
                        'nodeInfo' => [
                            'node' => $node,
                            'user' => $user,
                            'mu' => $mu,
                            'relay_rule_id' => $relay_rule_id,
                            'URL' => URL::class,
                        ],
                    ];

                    if (URL::SSRCanConnect($user, $mu)) {
                        $res['ssrlink'] = URL::getItemUrl($ssr_item, 0);
                        $res['ssritem'] = $ssr_item;
                    }

                    if (URL::SSCanConnect($user, $mu)) {
                        $res['sslink'] = URL::getItemUrl($ss_item, 1);
                        $res['ssQrWin'] = URL::getItemUrl($ss_item, 2);
                        $res['ss_item'] = $ss_item;
                    }

                    return $response->withJson($res);
                }
                break;
            case 1:
                if (
                    $user->class >= $node->node_class
                    && ($user->node_group == $node->node_group || $node->node_group == 0)
                ) {
                    $email = $user->email;
                    $email = Radius::GetUserName($email);
                    $json_show = 'VPN 信息<br>地址：' . $node->server
                        . '<br>用户名：' . $email . '<br>密码：' . $this->user->passwd
                        . '<br>支持方式：' . $node->method . '<br>备注：' . $node->info;

                    return $response->write(
                        $this->view()->assign('json_show', $json_show)->fetch('user/nodeinfovpn.tpl')
                    );
                }
                break;
            case 2:
                if (
                    $user->class >= $node->node_class
                    && ($user->node_group == $node->node_group || $node->node_group == 0)
                ) {
                    $email = $user->email;
                    $email = Radius::GetUserName($email);
                    $json_show = 'SSH 信息<br>地址：' . $node->server
                        . '<br>用户名：' . $email . '<br>密码：' . $this->user->passwd
                        . '<br>支持方式：' . $node->method . '<br>备注：' . $node->info;

                    return $response->write(
                        $this->view()->assign('json_show', $json_show)->fetch('user/nodeinfossh.tpl')
                    );
                }
                break;
            case 5:
                if (
                    $user->class >= $node->node_class
                    && ($user->node_group == $node->node_group || $node->node_group == 0)
                ) {
                    $email = $user->email;
                    $email = Radius::GetUserName($email);

                    $json_show = 'Anyconnect 信息<br>地址：' . $node->server
                        . '<br>用户名：' . $email . '<br>密码：' . $this->user->passwd
                        . '<br>支持方式：' . $node->method . '<br>备注：' . $node->info;

                    return $response->write(
                        $this->view()->assign('json_show', $json_show)->fetch('user/nodeinfoanyconnect.tpl')
                    );
                }
                break;
            case 10:
                if ((($user->class >= $node->node_class
                        && ($user->node_group == $node->node_group || $node->node_group == 0)) || $user->is_admin)
                    && ($node->node_bandwidth_limit == 0 || $node->node_bandwidth < $node->node_bandwidth_limit)) {

                    $res = [
                        'ret' => 1,
                        'nodeInfo' => [
                            'node' => $node,
                            'user' => $user,
                            'mu' => $mu,
                            'relay_rule_id' => $relay_rule_id,
                            'URL' => URL::class,
                        ],
                    ];

                    if (URL::SSRCanConnect($user, $mu)) {
                        $res['ssrlink'] = URL::getItemUrl($ssr_item, 0);
                        $res['ssritem'] = $ssr_item;
                    }

                    if (URL::SSCanConnect($user, $mu)) {
                        $res['sslink'] = URL::getItemUrl($ss_item, 1);
                        $res['ssQrWin'] = URL::getItemUrl($ss_item, 2);
                        $res['ss_item'] = $ss_item;
                    }

                    return $response->withJson($res);
                }
                break;
            case 11:
                if ((($user->class >= $node->node_class
                        && ($user->node_group == $node->node_group || $node->node_group == 0)) || $user->is_admin)
                    && ($node->node_bandwidth_limit == 0 || $node->node_bandwidth < $node->node_bandwidth_limit)) {

                        $res = [
                            'ret' => 1,
                            'nodeInfo' => [
                                'node' => URL::getV2Url($user, $node, true),
                                'user' => $user,
                            ],
                            'vmessUrl' => URL::getV2Url($user, $node, false)
                        ];

                        return $response->withJson($res);
                }
                break;
            case 12:
                if ((($user->class >= $node->node_class
                        && ($user->node_group == $node->node_group || $node->node_group == 0)) || $user->is_admin)
                    && ($node->node_bandwidth_limit == 0 || $node->node_bandwidth < $node->node_bandwidth_limit)) {

                        $res = [
                            'ret' => 1,
                            'nodeInfo' => [
                                'node' => URL::getV2Url($user, $node, true),
                                'user' => $user,
                            ],
                            'vmessUrl' => URL::getV2Url($user, $node, false)
                        ];

                        return $response->withJson($res);
                }
                break;
            case 13:
                if ((($user->class >= $node->node_class
                        && ($user->node_group == $node->node_group || $node->node_group == 0)) || $user->is_admin)
                    && ($node->node_bandwidth_limit == 0 || $node->node_bandwidth < $node->node_bandwidth_limit)) {

                    $res = [
                        'ret' => 1,
                        'nodeInfo' => [
                            'node' => $node,
                            'user' => $user,
                            'mu' => $mu,
                            'relay_rule_id' => $relay_rule_id,
                            'URL' => URL::class,
                        ],
                    ];

                    if (URL::SSRCanConnect($user, $mu)) {
                        $res['ssrlink'] = URL::getItemUrl($ssr_item, 0);
                        $res['ssritem'] = $ssr_item;
                    }

                    if (URL::SSCanConnect($user, $mu)) {
                        $res['sslink'] = URL::getItemUrl($ss_item, 1);
                        $res['ssQrWin'] = URL::getItemUrl($ss_item, 2);
                        $res['ss_item'] = $ss_item;
                    }

                    return $response->withJson($res);
                }
                break;
        }

        // Default and judgement fail return
        return $response->withJson([
            'ret' => 0,
            'nodeInfo' => [
                'message' => ':)',
            ],
        ]);
    }

    public function getConnectSettings($request, $response, $args)
    {
        $config_service = new Config();

        $res['ret'] = 1;
        $res['methods'] = $config_service->getSupportParam('methods');
        $res['protocol'] = $config_service->getSupportParam('protocol');
        $res['obfs'] = $config_service->getSupportParam('obfs');
        $res['allow_none_protocol'] = $config_service->getSupportParam('allow_none_protocol');
        $res['relay_able_protocol'] = $config_service->getSupportParam('relay_able_protocol');
        $res['ss_aead_method'] = $config_service->getSupportParam('ss_aead_method');
        $res['ss_obfs'] = $config_service->getSupportParam('ss_obfs');
        $res['port_price'] = $_ENV['invite_gift'];
        $res['port_price_specify'] = $_ENV['port_price_specify'];
        $res['min_port'] = $_ENV['min_port'];
        $res['max_port'] = $_ENV['max_port'];

        return $response->withJson($res);
    }
}
