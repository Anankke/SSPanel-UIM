<?php

namespace App\Controllers;

use App\Models\InviteCode;
use App\Utils\{
    Tools,
    Geetest,
    TelegramProcess,
    TelegramSessionManager,
    Telegram\Process
};
use App\Services\Auth;
use Slim\Http\{
    Request,
    Response
};
use Psr\Http\Message\ResponseInterface;

/**
 *  HomeController
 */
class HomeController extends BaseController
{
    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function index($request, $response, $args): ResponseInterface
    {
        if ($_ENV['newIndex'] === false && $_ENV['theme'] == 'material') {
            return $response->write($this->view()->fetch('indexold.tpl'));
        }

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

        if ($_ENV['enable_telegram'] == true) {
            $login_text = TelegramSessionManager::add_login_session();
            $login = explode('|', $login_text);
            $login_token = $login[0];
            $login_number = $login[1];
        } else {
            $login_token = '';
            $login_number = '';
        }

        return $response->write($this->view()
            ->assign('geetest_html', $GtSdk)
            ->assign('login_token', $login_token)
            ->assign('login_number', $login_number)
            ->assign('telegram_bot', $_ENV['telegram_bot'])
            ->assign('enable_logincaptcha', $_ENV['enable_login_captcha'])
            ->assign('enable_regcaptcha', $_ENV['enable_reg_captcha'])
            ->assign('base_url', $_ENV['baseUrl'])
            ->assign('recaptcha_sitekey', $recaptcha_sitekey)
            ->fetch('index.tpl'));
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function indexold($request, $response, $args): ResponseInterface
    {
        return $response->write($this->view()->fetch('indexold.tpl'));
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function code($request, $response, $args): ResponseInterface
    {
        $codes = InviteCode::where('user_id', '=', '0')->take(10)->get();
        return $response->write($this->view()->assign('codes', $codes)->fetch('code.tpl'));
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function tos($request, $response, $args): ResponseInterface
    {
        return $response->write($this->view()->fetch('tos.tpl'));
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function staff($request, $response, $args): ResponseInterface
    {
        return $response->write($this->view()->fetch('staff.tpl'));
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function telegram($request, $response, $args): ResponseInterface
    {
        $token = $request->getQueryParam('token');
        if ($token == $_ENV['telegram_request_token']) {
            if ($_ENV['use_new_telegram_bot']) {
                Process::index();
            } else {
                TelegramProcess::process();
            }
            $result = '1';
        } else {
            $result = '0';
        }
        return $response->write($result);
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function page404($request, $response, $args): ResponseInterface
    {
        return $response->write($this->view()->fetch('404.tpl'));
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function page405($request, $response, $args): ResponseInterface
    {
        return $response->write($this->view()->fetch('405.tpl'));
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function page500($request, $response, $args): ResponseInterface
    {
        return $response->write($this->view()->fetch('500.tpl'));
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function getOrderList($request, $response, $args): ResponseInterface
    {
        $key = $request->getParam('key');
        if (!$key || $key != $_ENV['key']) {
            $res['ret'] = 0;
            $res['msg'] = '错误';
            return $response->write(json_encode($res));
        }
        return $response->write(json_encode(['data' => AliPay::getList()]));
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function setOrder($request, $response, $args): ResponseInterface
    {
        $key = $request->getParam('key');
        $sn = $request->getParam('sn');
        $url = $request->getParam('url');
        if (!$key || $key != $_ENV['key']) {
            $res['ret'] = 0;
            $res['msg'] = '错误';
            return $response->write(json_encode($res));
        }
        return $response->write(json_encode(['res' => AliPay::setOrder($sn, $url)]));
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function getDocCenter($request, $response, $args): ResponseInterface
    {
        $user = Auth::getUser();
        if (!$user->isLogin && $_ENV['enable_documents'] === false) {
            $response = $response->withStatus(302)->withHeader('Location', '/');
            return $response;
        }
        return $response->write($this->view()->fetch('doc/index.tpl'));
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function getSubLink($request, $response, $args): string
    {
        $type = trim($request->getParam('type'));
        $user = Auth::getUser();
        if (!$user->isLogin) {
            return $msg = '!> ₍₍ ◝(・ω・)◟ ⁾⁾ 您没有登录噢，[点击此处登录](/auth/login \':ignore target=_blank\') 之后再刷新就阔以了啦';
        } else {
            $subInfo = LinkController::getSubinfo($user, 0);
            switch ($type) {
                case 'ssr':
                    $msg = [
                        '**订阅链接：**',
                        '```',
                        $subInfo['ssr'],
                        '```'
                    ];
                    break;
                case 'v2ray':
                    $msg = [
                        '**订阅链接：**',
                        '```',
                        $subInfo['v2ray'],
                        '```'
                    ];
                    break;
                case 'ssd':
                    $msg = [
                        '**订阅链接：**',
                        '```',
                        $subInfo['ssd'],
                        '```'
                    ];
                    break;
                case 'clash':
                    $msg = [
                        '**订阅链接：**[[点击下载配置]](' . $subInfo['clash'] . ')',
                        '```',
                        $subInfo['clash'],
                        '```'
                    ];
                    break;
                case 'surge':
                    $msg = [
                        '**Surge Version 2.x 托管配置链接：**[[iOS 点击此处一键添加]](surge:///install-config?url=' . urlencode($subInfo['surge2']) . ')',
                        '```',
                        $subInfo['surge2'],
                        '```',
                        '**Surge Version 3.x 托管配置链接：**[[iOS 点击此处一键添加]](surge3:///install-config?url=' . urlencode($subInfo['surge3']) . ')',
                        '```',
                        $subInfo['surge3'],
                        '```'
                    ];
                    break;
                case 'kitsunebi':
                    $msg = [
                        '**包含 ss、v2ray 的合并订阅链接：**',
                        '```',
                        $subInfo['kitsunebi'],
                        '```'
                    ];
                    break;
                case 'surfboard':
                    $msg = [
                        '**托管配置链接：**',
                        '```',
                        $subInfo['surfboard'],
                        '```'
                    ];
                    break;
                case 'quantumult_sub':
                    $msg = [
                        '**ssr 订阅链接：**[[iOS 点击此处一键添加]](quantumult://configuration?server=' . Tools::base64_url_encode($subInfo['ssr']) . ')',
                        '```',
                        $subInfo['ssr'],
                        '```',
                        '**V2ray 订阅链接：**[[iOS 点击此处一键添加]](quantumult://configuration?server=' . Tools::base64_url_encode($subInfo['quantumult_v2']) . ')',
                        '```',
                        $subInfo['quantumult_v2'],
                        '```'
                    ];
                    break;
                case 'quantumult_conf':
                    $msg = [
                        '**导入 ss、ssr、v2ray 以及分流规则的配置链接：**',
                        '```',
                        $subInfo['quantumult_sub'],
                        '```',
                        '**导入类似 Surge、Clash 使用自定义策略组的配置链接：**',
                        '```',
                        $subInfo['quantumult_conf'],
                        '```'
                    ];
                    break;
                case 'shadowrocket':
                    $msg = [
                        '**包含 ss、ssr、v2ray 的合并订阅链接：**[[iOS 点击此处一键添加]](sub://' . base64_encode($subInfo['shadowrocket']) . ')',
                        '```',
                        $subInfo['shadowrocket'],
                        '```'
                    ];
                    break;
                default:
                    if (in_array($type, $subInfo)) {
                        $msg = [
                            '```',
                            $subInfo[$type],
                            '```'
                        ];
                    } else {
                        $msg = [
                            '获取失败了呢...，请联系管理员。'
                        ];
                    }
                    break;
            }
        }
        return implode(PHP_EOL, $msg);
    }
}
