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

class VueController extends BaseController {

    private $user;

    public function __construct()
    {
        $this->user = Auth::getUser();
    }

    public function getGlobalConfig($request, $response, $args) {
        $GtSdk = null;
        $recaptcha_sitekey = null;
        $user = $this->user;
        if (Config::get('captcha_provider') != ''){
            switch(Config::get('captcha_provider'))
            {
                case 'recaptcha':
                    $recaptcha_sitekey = Config::get('recaptcha_sitekey');
                    break;
                case 'geetest':
                    $uid = time().rand(1, 10000) ;
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

        $res['globalConfig'] = array(
            "geetest_html" => $GtSdk,
            "login_token" => $login_token,
            "login_number" => $login_number,
            "telegram_bot" => Config::get('telegram_bot'),
            "enable_logincaptcha" => Config::get('enable_login_captcha'),
            "enable_regcaptcha" => Config::get('enable_reg_captcha'),
            "base_url" => Config::get('enable_reg_captcha'),
            "recaptcha_sitekey" => $recaptcha_sitekey,
            "captcha_provider" => Config::get('captcha_provider'),
            "jump_delay" => Config::get('jump_delay'),
            "register_mode" => Config::get('register_mode'),
            "enable_email_verify" => Config::get('enable_email_verify'),
            "appName" => Config::get('appName'),
            "dateY" => date("Y"),
            "isLogin" => $user->isLogin,
        );

        $res['ret'] = 1;

        return $response->getBody()->write(json_encode($res));
    }

}