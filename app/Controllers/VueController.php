<?php

namespace App\Controllers;

use App\Models\InviteCode;
use App\Models\User;
use App\Models\Code;
use App\Models\Payback;
use App\Models\Paylist;
use App\Models\Ann;
use App\Models\Shop;
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

use voku\helper\AntiXSS;

use App\Utils\URL;
use App\Models\Ip;
use App\Models\Node;

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
            "enable_checkin_captcha" => Config::get('enable_checkin_captcha'),
            "base_url" => Config::get('baseUrl'),
            "recaptcha_sitekey" => $recaptcha_sitekey,
            "captcha_provider" => Config::get('captcha_provider'),
            "jump_delay" => Config::get('jump_delay'),
            "register_mode" => Config::get('register_mode'),
            "enable_email_verify" => Config::get('enable_email_verify'),
            "appName" => Config::get('appName'),
            "dateY" => date("Y"),
            "isLogin" => $user->isLogin,
            "enable_telegram" => Config::get('enable_telegram'),
            "enable_mylivechat" => Config::get('enable_mylivechat'),
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

    public function getUserInfo($request, $response, $args) {
        $user = $this->user;

        if (!$user->isLogin) {
            $res['ret'] = -1;
            return $response->getBody()->write(json_encode($res));
        }

        $pre_user = URL::cloneUser($user);
        $user->ssr_url_all = URL::getAllUrl($pre_user, 0, 0);
        $user->ssr_url_all_mu = URL::getAllUrl($pre_user, 1, 0);
        $user->ss_url_all = URL::getAllUrl($pre_user, 0, 2);
        $ssinfo = URL::getSSConnectInfo($pre_user);
        $user->ssd_url_all = URL::getAllSSDUrl($ssinfo);
        $user->isAbleToCheckin = $user->isAbleToCheckin();
        $ssr_sub_token = LinkController::GenerateSSRSubCode($this->user->id, 0);
        $GtSdk = null;
        $recaptcha_sitekey = null;
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
        $Ann = Ann::orderBy('date', 'desc')->first();
        $display_ios_class = Config::get('display_ios_class');
        $ios_account = Config::get('ios_account');
        $ios_password = Config::get('ios_password');
        $mergeSub = Config::get('mergeSub');
        $subUrl = Config::get('subUrl');
        $baseUrl = Config::get('baseUrl');
        $user['online_ip_count'] = $user->online_ip_count();

        $res['info'] = array(
            "user" => $user,
            "ssrSubToken" => $ssr_sub_token,
            "displayIosClass" => $display_ios_class,
            "iosAccount" => $ios_account,
            "iosPassword" => $ios_password,
            "mergeSub" => $mergeSub,
            "subUrl" => $subUrl,
            "baseUrl" => $baseUrl,
            "ann" => $Ann,
            "recaptchaSitekey" => $recaptcha_sitekey,
            "GtSdk" => $GtSdk,
        );

        $res['ret'] = 1;

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
        
        $paybacks = Payback::where("ref_by", $user->id)->orderBy("id", "desc")->paginate(15, ['*'], 'page', $pageNum);
        if (!$paybacks_sum = Payback::where("ref_by", $user->id)->sum('ref_get')) {
            $paybacks_sum = 0;
        }
        $paybacks->setPath('/#/user/panel');

        $res['inviteInfo'] = array(
            "code" => $code,
            "paybacks" => $paybacks,
            "paybacks_sum" => $paybacks_sum,
            "invitePrice" => Config::get('invite_price'),
            "customPrice" => Config::get('custom_invite_price'),
            "invite_gift" => Config::get('invite_gift'),
            "invite_get_money" => Config::get('invite_get_money'),
            "code_payback" => Config::get('code_payback'),
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
        
        $shops = Shop::where("status", 1)->orderBy("name")->get();
        
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
            "money" => $user->money,
            "class" => $user->class,
            "class_expire" => $user->class_expire,
            "expire_in" => $user->expire_in,
            "online_ip_count" => $user->online_ip_count(),
            "node_speedlimit" => $user->node_speedlimit,
            "node_connector" => $user->node_connector,
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
        $ssr_sub_token = LinkController::GenerateSSRSubCode($this->user->id, 0);

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
            "code" => $code,
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
            "todayUsedTraffic" => $user->TodayusedTraffic(),
            "lastUsedTraffic" => $user->LastusedTraffic(),
            "unUsedTraffic" => $user->unusedTraffic(),
        );

        $res['ret'] = 1;
        
        return $response->getBody()->write(json_encode($res));
    }

    public function getCaptcha($request, $response, $args) {
        $GtSdk = null;
        $recaptcha_sitekey = null;
        if (Config::get('captcha_provider') != ''){
            switch(Config::get('captcha_provider'))
            {
                case 'recaptcha':
                    $recaptcha_sitekey = Config::get('recaptcha_sitekey');
                    $res['recaptchaKey'] = $recaptcha_sitekey;
                    break;
                case 'geetest':
                    $uid = time().rand(1, 10000) ;
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
}