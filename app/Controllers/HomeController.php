<?php

namespace App\Controllers;

use App\Models\InviteCode;
use App\Services\Config;
use App\Utils\AliPay;
use App\Utils\TelegramSessionManager;
use App\Utils\TelegramProcess;
use App\Utils\Geetest;
use Slim\Http\{Request, Response};
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
        $GtSdk = null;
        $recaptcha_sitekey = null;
        if (Config::get('captcha_provider') != '') {
            switch (Config::get('captcha_provider')) {
                case 'recaptcha':
                    $recaptcha_sitekey = Config::get('recaptcha_sitekey');
                    break;
                case 'geetest':
                    $uid = time() . random_int(1, 10000);
                    $GtSdk = Geetest::get($uid);
                    break;
            }
        }

        if (Config::get('enable_telegram') == 'true') {
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
            ->assign('telegram_bot', Config::get('telegram_bot'))
            ->assign('enable_logincaptcha', Config::get('enable_login_captcha'))
            ->assign('enable_regcaptcha', Config::get('enable_reg_captcha'))
            ->assign('base_url', Config::get('baseUrl'))
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
        if ($token == Config::get('telegram_request_token')) {
            TelegramProcess::process();
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
        if (!$key || $key != Config::get('key')) {
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
        if (!$key || $key != Config::get('key')) {
            $res['ret'] = 0;
            $res['msg'] = '错误';
            return $response->write(json_encode($res));
        }
        return $response->write(json_encode(['res' => AliPay::setOrder($sn, $url)]));
    }
}
