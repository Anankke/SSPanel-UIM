<?php

namespace App\Controllers;

use App\Models\{
    User,
    LoginIp,
    InviteCode,
    EmailVerify
};
use App\Utils\{
    GA,
    Hash,
    Check,
    Tools,
    Radius,
    Geetest,
    TelegramSessionManager
};
use App\Services\{
    Auth,
    Mail,
    Config
};
use voku\helper\AntiXSS;
use Exception;

/**
 *  AuthController
 */
class AuthController extends BaseController
{
    public function login()
    {
        $GtSdk = null;
        $recaptcha_sitekey = null;
        if ($_ENV['enable_login_captcha'] === true) {
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

        if ($_ENV['enable_telegram'] === true) {
            $login_text = TelegramSessionManager::add_login_session();
            $login = explode('|', $login_text);
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
            ->assign('telegram_bot', $_ENV['telegram_bot'])
            ->assign('base_url', $_ENV['baseUrl'])
            ->assign('recaptcha_sitekey', $recaptcha_sitekey)
            ->display('auth/login.tpl');
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

    public function loginHandle($request, $response, $args)
    {
        // $data = $request->post('sdf');
        $email = $request->getParam('email');
        $email = trim($email);
        $email = strtolower($email);
        $passwd = $request->getParam('passwd');
        $code = $request->getParam('code');
        $rememberMe = $request->getParam('remember_me');

        if ($_ENV['enable_login_captcha'] === true) {
            switch ($_ENV['captcha_provider']) {
                case 'recaptcha':
                    $recaptcha = $request->getParam('recaptcha');
                    if ($recaptcha == '') {
                        $ret = false;
                    } else {
                        $json = file_get_contents('https://recaptcha.net/recaptcha/api/siteverify?secret=' . $_ENV['recaptcha_secret'] . '&response=' . $recaptcha);
                        $ret = json_decode($json)->success;
                    }
                    break;
                case 'geetest':
                    $ret = Geetest::verify($request->getParam('geetest_challenge'), $request->getParam('geetest_validate'), $request->getParam('geetest_seccode'));
                    break;
            }
            if (!$ret) {
                $res['ret'] = 0;
                $res['msg'] = '系统无法接受您的验证结果，请刷新页面后重试。';
                return $response->getBody()->write(json_encode($res));
            }
        }

        // Handle Login
        $user = User::where('email', '=', $email)->first();

        if ($user == null) {
            $rs['ret'] = 0;
            $rs['msg'] = '邮箱不存在';
            return $response->getBody()->write(json_encode($rs));
        }

        if (!Hash::checkPassword($user->pass, $passwd)) {
            $rs['ret'] = 0;
            $rs['msg'] = '邮箱或者密码错误';


            $loginIP = new LoginIp();
            $loginIP->ip = $_SERVER['REMOTE_ADDR'];
            $loginIP->userid = $user->id;
            $loginIP->datetime = time();
            $loginIP->type = 1;
            $loginIP->save();

            return $response->getBody()->write(json_encode($rs));
        }

        $time = 3600 * 24;
        if ($rememberMe) {
            $time = 3600 * 24 * ($_ENV['rememberMeDuration'] ?: 7);
        }

        if ($user->ga_enable == 1) {
            $ga = new GA();
            $rcode = $ga->verifyCode($user->ga_token, $code);

            if (!$rcode) {
                $res['ret'] = 0;
                $res['msg'] = '两步验证码错误，如果您是丢失了生成器或者错误地设置了这个选项，您可以尝试重置密码，即可取消这个选项。';
                return $response->getBody()->write(json_encode($res));
            }
        }

        Auth::login($user->id, $time);
        $rs['ret'] = 1;
        $rs['msg'] = '登录成功';

        $loginIP = new LoginIp();
        $loginIP->ip = $_SERVER['REMOTE_ADDR'];
        $loginIP->userid = $user->id;
        $loginIP->datetime = time();
        $loginIP->type = 0;
        $loginIP->save();

        return $response->getBody()->write(json_encode($rs));
    }

    public function qrcode_loginHandle($request, $response, $args)
    {
        // $data = $request->post('sdf');
        $token = $request->getParam('token');
        $number = $request->getParam('number');

        $ret = TelegramSessionManager::step2_verify_login_session($token, $number);
        if (!$ret) {
            $res['ret'] = 0;
            $res['msg'] = '此令牌无法被使用。';
            return $response->getBody()->write(json_encode($res));
        }


        // Handle Login
        $user = User::where('id', '=', $ret)->first();
        // @todo
        $time = 3600 * 24;

        Auth::login($user->id, $time);
        $rs['ret'] = 1;
        $rs['msg'] = '登录成功';

        $this->logUserIp($user->id, $_SERVER['REMOTE_ADDR']);

        return $response->getBody()->write(json_encode($rs));
    }

    private function logUserIp($id, $ip)
    {
        $loginip = new LoginIp();
        $loginip->ip = $ip;
        $loginip->userid = $id;
        $loginip->datetime = time();
        $loginip->type = 0;
        $loginip->save();
    }

    public function register($request, $response, $next)
    {
        $ary = $request->getQueryParams();
        $code = '';
        if (isset($ary['code'])) {
            $antiXss = new AntiXSS();
            $code = $antiXss->xss_clean($ary['code']);
        }

        $GtSdk = null;
        $recaptcha_sitekey = null;
        if ($_ENV['enable_reg_captcha'] === true) {
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

        if ($_ENV['enable_telegram'] === true) {
            $login_text = TelegramSessionManager::add_login_session();
            $login = explode('|', $login_text);
            $login_token = $login[0];
            $login_number = $login[1];
        } else {
            $login_token = '';
            $login_number = '';
        }

        return $this->view()
            ->assign('geetest_html', $GtSdk)
            ->assign('enable_email_verify', Config::getconfig('Register.bool.Enable_email_verify'))
            ->assign('code', $code)
            ->assign('recaptcha_sitekey', $recaptcha_sitekey)
            ->assign('telegram_bot', $_ENV['telegram_bot'])
            ->assign('base_url', $_ENV['baseUrl'])
            ->assign('login_token', $login_token)
            ->assign('login_number', $login_number)
            ->display('auth/register.tpl');
    }

    public function sendVerify($request, $response, $next)
    {
        if (Config::getconfig('Register.bool.Enable_email_verify')) {
            $email = $request->getParam('email');
            $email = trim($email);

            if ($email == '') {
                $res['ret'] = 0;
                $res['msg'] = '未填写邮箱';
                return $response->getBody()->write(json_encode($res));
            }

            // check email format
            if (!Check::isEmailLegal($email)) {
                $res['ret'] = 0;
                $res['msg'] = '邮箱无效';
                return $response->getBody()->write(json_encode($res));
            }

            $user = User::where('email', '=', $email)->first();
            if ($user != null) {
                $res['ret'] = 0;
                $res['msg'] = '此邮箱已经注册';
                return $response->getBody()->write(json_encode($res));
            }

            $ipcount = EmailVerify::where('ip', '=', $_SERVER['REMOTE_ADDR'])->where('expire_in', '>', time())->count();
            if ($ipcount >= (int) Config::getconfig('Register.string.Email_verify_iplimit')) {
                $res['ret'] = 0;
                $res['msg'] = '此IP请求次数过多';
                return $response->getBody()->write(json_encode($res));
            }


            $mailcount = EmailVerify::where('email', '=', $email)->where('expire_in', '>', time())->count();
            if ($mailcount >= 3) {
                $res['ret'] = 0;
                $res['msg'] = '此邮箱请求次数过多';
                return $response->getBody()->write(json_encode($res));
            }

            $code = Tools::genRandomNum(6);

            $ev = new EmailVerify();
            $ev->expire_in = time() + (int) Config::getconfig('Register.string.Email_verify_ttl');
            $ev->ip = $_SERVER['REMOTE_ADDR'];
            $ev->email = $email;
            $ev->code = $code;
            $ev->save();

            $subject = $_ENV['appName'] . '- 验证邮件';

            try {
                Mail::send($email, $subject, 'auth/verify.tpl', [
                    'code' => $code, 'expire' => date('Y-m-d H:i:s', time() + (int) Config::getconfig('Register.string.Email_verify_ttl'))
                ], [
                    //BASE_PATH.'/public/assets/email/styles.css'
                ]);
            } catch (Exception $e) {
                $res['ret'] = 1;
                $res['msg'] = '邮件发送失败，请联系网站管理员。';
                return $response->getBody()->write(json_encode($res));
            }

            $res['ret'] = 1;
            $res['msg'] = '验证码发送成功，请查收邮件。';
            return $response->getBody()->write(json_encode($res));
        }
        $res['ret'] = 0;
        return $response->getBody()->write(json_encode($res));
    }

    public function register_helper($name, $email, $passwd, $code, $imtype, $imvalue, $telegram_id)
    {
        if (Config::getconfig('Register.string.Mode') === 'close') {
            $res['ret'] = 0;
            $res['msg'] = '未开放注册。';
            return $res;
        }

        //dumplin：1、邀请人等级为0则邀请码不可用；2、邀请人invite_num为可邀请次数，填负数则为无限
        $c = InviteCode::where('code', $code)->first();
        if ($c == null) {
            if (Config::getconfig('Register.string.Mode') === 'invite') {
                $res['ret'] = 0;
                $res['msg'] = '邀请码无效';
                return $res;
            }
        } elseif ($c->user_id != 0) {
            $gift_user = User::where('id', '=', $c->user_id)->first();
            if ($gift_user == null) {
                $res['ret'] = 0;
                $res['msg'] = '邀请人不存在';
                return $res;
            }

            if ($gift_user->class == 0) {
                $res['ret'] = 0;
                $res['msg'] = '邀请人不是VIP';
                return $res;
            }

            if ($gift_user->invite_num == 0) {
                $res['ret'] = 0;
                $res['msg'] = '邀请人可用邀请次数为0';
                return $res;
            }
        }

        // do reg user
        $user                       = new User();

        $antiXss                    = new AntiXSS();

        $user->user_name            = $antiXss->xss_clean($name);
        $user->email                = $email;
        $user->pass                 = Hash::passwordHash($passwd);
        $user->passwd               = Tools::genRandomChar(6);
        $user->port                 = Tools::getAvPort();
        $user->t                    = 0;
        $user->u                    = 0;
        $user->d                    = 0;
        $user->method               = Config::getconfig('Register.string.defaultMethod');
        $user->protocol             = Config::getconfig('Register.string.defaultProtocol');
        $user->protocol_param       = Config::getconfig('Register.string.defaultProtocol_param');
        $user->obfs                 = Config::getconfig('Register.string.defaultObfs');
        $user->obfs_param           = Config::getconfig('Register.string.defaultObfs_param');
        $user->forbidden_ip         = $_ENV['reg_forbidden_ip'];
        $user->forbidden_port       = $_ENV['reg_forbidden_port'];
        $user->im_type              = $imtype;
        $user->im_value             = $antiXss->xss_clean($imvalue);
        $user->transfer_enable      = Tools::toGB((int) Config::getconfig('Register.string.defaultTraffic'));
        $user->invite_num           = (int) Config::getconfig('Register.string.defaultInviteNum');
        $user->auto_reset_day       = $_ENV['reg_auto_reset_day'];
        $user->auto_reset_bandwidth = $_ENV['reg_auto_reset_bandwidth'];
        $user->money                = 0;

        //dumplin：填写邀请人，写入邀请奖励
        $user->ref_by = 0;
        if (($c != null) && $c->user_id != 0) {
            $gift_user = User::where('id', '=', $c->user_id)->first();
            $user->ref_by = $c->user_id;
            $user->money = (int) Config::getconfig('Register.string.defaultInvite_get_money');
            $gift_user->transfer_enable += $_ENV['invite_gift'] * 1024 * 1024 * 1024;
            --$gift_user->invite_num;
            $gift_user->save();
        }
        if ($telegram_id) {
            $user->telegram_id = $telegram_id;
        }

        $user->class_expire     = date('Y-m-d H:i:s', time() + (int) Config::getconfig('Register.string.defaultClass_expire') * 3600);
        $user->class            = (int) Config::getconfig('Register.string.defaultClass');
        $user->node_connector   = (int) Config::getconfig('Register.string.defaultConn');
        $user->node_speedlimit  = (int) Config::getconfig('Register.string.defaultSpeedlimit');
        $user->expire_in        = date('Y-m-d H:i:s', time() + (int) Config::getconfig('Register.string.defaultExpire_in') * 86400);
        $user->reg_date         = date('Y-m-d H:i:s');
        $user->reg_ip           = $_SERVER['REMOTE_ADDR'];
        $user->plan             = 'A';
        $user->theme            = $_ENV['theme'];

        $groups                 = explode(',', $_ENV['ramdom_group']);

        $user->node_group       = $groups[array_rand($groups)];

        $ga = new GA();
        $secret = $ga->createSecret();

        $user->ga_token = $secret;
        $user->ga_enable = 0;

        if ($user->save()) {
            Auth::login($user->id, 3600);
            $this->logUserIp($user->id, $_SERVER['REMOTE_ADDR']);

            $res['ret'] = 1;
            $res['msg'] = '注册成功！正在进入登录界面';

            Radius::Add($user, $user->passwd);
            return $res;
        }
        $res['ret'] = 0;
        $res['msg'] = '未知错误';
        return $res;
    }

    public function registerHandle($request, $response)
    {
        if (Config::getconfig('Register.string.Mode') === 'close') {
            $res['ret'] = 0;
            $res['msg'] = '未开放注册。';
            return $response->getBody()->write(json_encode($res));
        }

        $name = $request->getParam('name');
        $email = $request->getParam('email');
        $email = trim($email);
        $email = strtolower($email);
        $passwd = $request->getParam('passwd');
        $repasswd = $request->getParam('repasswd');
        $code = $request->getParam('code');
        $code = trim($code);
        $imtype = $request->getParam('imtype');
        $emailcode = $request->getParam('emailcode');
        $emailcode = trim($emailcode);

        // 前端传入参数为wechat, 后续作为 im_value使用，变量改名为 im_value
        $imvalue = $request->getParam('wechat');
        $imvalue = trim($imvalue);

        if ($_ENV['enable_reg_captcha'] === true) {
            switch ($_ENV['captcha_provider']) {
                case 'recaptcha':
                    $recaptcha = $request->getParam('recaptcha');
                    if ($recaptcha == '') {
                        $ret = false;
                    } else {
                        $json = file_get_contents('https://recaptcha.net/recaptcha/api/siteverify?secret=' . $_ENV['recaptcha_secret'] . '&response=' . $recaptcha);
                        $ret = json_decode($json)->success;
                    }
                    break;
                case 'geetest':
                    $ret = Geetest::verify($request->getParam('geetest_challenge'), $request->getParam('geetest_validate'), $request->getParam('geetest_seccode'));
                    break;
            }
            if (!$ret) {
                $res['ret'] = 0;
                $res['msg'] = '系统无法接受您的验证结果，请刷新页面后重试。';
                return $response->getBody()->write(json_encode($res));
            }
        }

        // check email format
        if (!Check::isEmailLegal($email)) {
            $res['ret'] = 0;
            $res['msg'] = '邮箱无效';
            return $response->getBody()->write(json_encode($res));
        }
        // check email
        $user = User::where('email', $email)->first();
        if ($user != null) {
            $res['ret'] = 0;
            $res['msg'] = '邮箱已经被注册了';
            return $response->getBody()->write(json_encode($res));
        }

        if (Config::getconfig('Register.bool.Enable_email_verify')) {
            $mailcount = EmailVerify::where('email', '=', $email)->where('code', '=', $emailcode)->where('expire_in', '>', time())->first();
            if ($mailcount == null) {
                $res['ret'] = 0;
                $res['msg'] = '您的邮箱验证码不正确';
                return $response->getBody()->write(json_encode($res));
            }
        }

        // check pwd length
        if (strlen($passwd) < 8) {
            $res['ret'] = 0;
            $res['msg'] = '密码请大于8位';
            return $response->getBody()->write(json_encode($res));
        }

        // check pwd re
        if ($passwd != $repasswd) {
            $res['ret'] = 0;
            $res['msg'] = '两次密码输入不符';
            return $response->getBody()->write(json_encode($res));
        }

        if ($imtype == '' || $imvalue == '') {
            $res['ret'] = 0;
            $res['msg'] = '请填上你的联络方式';
            return $response->getBody()->write(json_encode($res));
        }

        $user = User::where('im_value', $imvalue)->where('im_type', $imtype)->first();
        if ($user != null) {
            $res['ret'] = 0;
            $res['msg'] = '此联络方式已注册';
            return $response->getBody()->write(json_encode($res));
        }
        if (Config::getconfig('Register.bool.Enable_email_verify')) {
            EmailVerify::where('email', '=', $email)->delete();
        }
        $res = $this->register_helper($name, $email, $passwd, $code, $imtype, $imvalue, 0);
        return $response->getBody()->write(json_encode($res));
    }

    public function logout($request, $response, $next)
    {
        Auth::logout();
        return $response->withStatus(302)->withHeader('Location', '/auth/login');
    }

    public function qrcode_check($request, $response, $args)
    {
        $token = $request->getParam('token');
        $number = $request->getParam('number');
        $user = Auth::getUser();
        if ($user->isLogin) {
            $res['ret'] = 0;
            return $response->getBody()->write(json_encode($res));
        }

        if ($_ENV['enable_telegram'] === true) {
            $ret = TelegramSessionManager::check_login_session($token, $number);
            $res['ret'] = $ret;
            return $response->getBody()->write(json_encode($res));
        }

        $res['ret'] = 0;
        return $response->getBody()->write(json_encode($res));
    }

    public function telegram_oauth($request, $response, $args)
    {
        if ($_ENV['enable_telegram'] === true) {
            $auth_data = $request->getQueryParams();
            if ($this->telegram_oauth_check($auth_data) === true) { // Looks good, proceed.
                $telegram_id = $auth_data['id'];
                $user = User::query()->where('telegram_id', $telegram_id)->firstOrFail(); // Welcome Back :)
                if ($user == null) {
                    return $this->view()->assign('title', '您需要先进行邮箱注册后绑定Telegram才能使用授权登录')->assign('message', '很抱歉带来的不便，请重新试试')->assign('redirect', '/auth/login')->display('telegram_error.tpl');
                }
                Auth::login($user->id, 3600);
                $this->logUserIp($user->id, $_SERVER['REMOTE_ADDR']);

                // 登陆成功！
                return $this->view()->assign('title', '登录成功')->assign('message', '正在前往仪表盘')->assign('redirect', '/user')->display('telegram_success.tpl');
            }
            // 验证失败
            return $this->view()->assign('title', '登陆超时或非法构造信息')->assign('message', '很抱歉带来的不便，请重新试试')->assign('redirect', '/auth/login')->display('telegram_error.tpl');
        }
        return $response->withRedirect('/404');
    }

    private function telegram_oauth_check($auth_data)
    {
        $check_hash = $auth_data['hash'];
        $bot_token = $_ENV['telegram_token'];
        unset($auth_data['hash']);
        $data_check_arr = [];
        foreach ($auth_data as $key => $value) {
            $data_check_arr[] = $key . '=' . $value;
        }
        sort($data_check_arr);
        $data_check_string = implode("\n", $data_check_arr);
        $secret_key = hash('sha256', $bot_token, true);
        $hash = hash_hmac('sha256', $data_check_string, $secret_key);
        if (strcmp($hash, $check_hash) !== 0) {
            return false; // Bad Data :(
        }

        if ((time() - $auth_data['auth_date']) > 300) { // Expire @ 5mins
            return false;
        }

        return true; // Good to Go
    }
}
