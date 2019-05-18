<?php

namespace App\Controllers;

use App\Models\InviteCode;
use App\Models\User;
use App\Models\Code;
use App\Models\Payback;
use App\Models\Paylist;
use App\Services\Auth;
use App\Services\Config;
use App\Services\Payment;
use App\Utils\AliPay;
use App\Utils\Tools;
use App\Utils\Telegram;
use App\Utils\Tuling;
use App\Utils\TelegramSessionManager;
use App\Utils\QRcode;
use App\Utils\Pay;
use App\Utils\TelegramProcess;
use App\Utils\Spay_tool;
use App\Utils\Geetest;

/**
 *  HomeController
 */
class HomeController extends BaseController
{
    public function index()
    {
        $GtSdk = null;
        $recaptcha_sitekey = null;
        if (Config::get('captcha_provider') != '') {
            switch (Config::get('captcha_provider')) {
                case 'recaptcha':
                    $recaptcha_sitekey = Config::get('recaptcha_sitekey');
                    break;
                case 'geetest':
                    $uid = time() . rand(1, 10000);
                    $GtSdk = Geetest::get($uid);
                    break;
            }
        }

        if (Config::get('enable_telegram') == 'true') {
            $login_text = TelegramSessionManager::add_login_session();
            $login = explode("|", $login_text);
            $login_token = $login[0];
            $login_number = $login[1];
        } else {
            $login_token = '';
            $login_number = '';
        }

        return $this->view()
            ->assign('geetest_html', $GtSdk)
            ->assign('login_token', $login_token)
            ->assign('login_number', $login_number)
            ->assign('telegram_bot', Config::get('telegram_bot'))
            ->assign('enable_logincaptcha', Config::get('enable_login_captcha'))
            ->assign('enable_regcaptcha', Config::get('enable_reg_captcha'))
            ->assign('base_url', Config::get('baseUrl'))
            ->assign('recaptcha_sitekey', $recaptcha_sitekey)
            ->display('index.tpl');
    }

    public function indexold()
    {
        return $this->view()->display('indexold.tpl');
    }

    public function code()
    {
        $codes = InviteCode::where('user_id', '=', '0')->take(10)->get();
        return $this->view()->assign('codes', $codes)->display('code.tpl');
    }

    public function down()
    {
    }

    public function tos()
    {
        return $this->view()->display('tos.tpl');
    }

    public function staff()
    {
        return $this->view()->display('staff.tpl');
    }

    public function telegram($request, $response, $args)
    {
        $token = "";
        if (isset($request->getQueryParams()["token"])) {
            $token = $request->getQueryParams()["token"];
        }

        if ($token == Config::get('telegram_request_token')) {
            TelegramProcess::process();
        } else {
            echo("不正确请求！");
        }
    }

    public function page404($request, $response, $args)
    {
        return $this->view()->display('404.tpl');
    }

    public function page405($request, $response, $args)
    {
        return $this->view()->display('405.tpl');
    }

    public function page500($request, $response, $args)
    {
        return $this->view()->display('500.tpl');
    }

    public function getOrderList($request, $response, $args)
    {
        $key = $request->getParam('key');
        if (!$key || $key != Config::get('key')) {
            $res['ret'] = 0;
            $res['msg'] = "错误";
            return $response->getBody()->write(json_encode($res));
        }
        return $response->getBody()->write(json_encode(['data' => AliPay::getList()]));
    }

    public function setOrder($request, $response, $args)
    {
        $key = $request->getParam('key');
        $sn = $request->getParam('sn');
        $url = $request->getParam('url');
        if (!$key || $key != Config::get('key')) {
            $res['ret'] = 0;
            $res['msg'] = "错误";
            return $response->getBody()->write(json_encode($res));
        }
        return $response->getBody()->write(json_encode(['res' => AliPay::setOrder($sn, $url)]));
    }
}
