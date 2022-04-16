<?php
namespace App\Controllers;

use App\Utils\GA;
use App\Utils\Hash;
use App\Utils\Tools;
use Ramsey\Uuid\Uuid;
use App\Services\Auth;
use App\Services\Mail;
use Slim\Http\Response;
use voku\helper\AntiXSS;
use App\Models\User;
use App\Models\Setting;
use App\Models\InviteCode;
use App\Models\EmailVerify;

class AuthController extends BaseController
{
    public function login($request, $response, $args)
    {
        return $this->view()->display('auth/login.tpl');
    }

    public function loginHandle($request, $response, $args)
    {
        $code = $request->getParam('code');
        $email = strtolower(trim($request->getParam('email')));
        $passwd = $request->getParam('passwd');
        $rememberMe = $request->getParam('remember_me');
        $user = User::where('email', $email)->first();

        try {
            if ($user == null) {
                throw new \Exception('没有找到这个邮箱');
            }
            if (!Hash::checkPassword($user->pass, $passwd)) {
                $user->collectLoginIP($_SERVER['REMOTE_ADDR'], 1);
                throw new \Exception('登录密码不正确');
            }
            if ($user->ga_enable == 1) {
                $ga = new GA();
                $rcode = $ga->verifyCode($user->ga_token, $code);
                if (!$rcode) {
                    throw new \Exception('两步验证码错误，如丢失密钥，请重置密码');
                }
            }

            $time = 3600 * 24;
            Auth::login($user->id, $time);
            $user->collectLoginIP($_SERVER['REMOTE_ADDR']);
        } catch (\Exception $e) {
            return $response->withJson([
                'ret' => 0,
                'msg' => $e->getMessage()
            ]);
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '登录成功，欢迎回来'
        ]);
    }

    public function sendVerify($request, $response, $next)
    {
        try {
            if (!Setting::obtain('reg_email_verify')) {
                throw new \Exception('不需要验证邮箱');
            }
            if (Setting::obtain('mail_driver') == 'none') {
                throw new \Exception('没有有效的发信配置');
            }
            $email = strtolower(trim($request->getParam('email')));
            if ($email == '') {
                throw new \Exception('请填写邮箱');
            }
            if (!Tools::emailCheck($email)) {
                throw new \Exception('邮箱格式不正确');
            }
            $user = User::where('email', $email)->first();
            if ($user != null) {
                throw new \Exception('此邮箱已注册');
            }
            $ipcount = EmailVerify::where('ip', $_SERVER['REMOTE_ADDR'])
            ->where('expire_in', '>', time())
            ->count();
            if ($ipcount >= Setting::obtain('email_verify_ip_limit')) {
                throw new \Exception('此IP请求次数过多');
            }
            $mailcount = EmailVerify::where('email', $email)
            ->where('expire_in', '>', time())
            ->count();
            if ($mailcount >= 3) {
                throw new \Exception('此邮箱请求次数过多');
            }

            $code = Tools::genRandomNum(6);
            $ev = new EmailVerify();
            $ev->expire_in = time() + Setting::obtain('email_verify_ttl');
            $ev->ip = $_SERVER['REMOTE_ADDR'];
            $ev->email = $email;
            $ev->code = $code;
            $ev->save();

            Mail::send($email, $_ENV['appName'] . ' - 验证邮件', 'auth/verify.tpl',
                [
                    'code' => $code,
                    'expire' => date('Y-m-d H:i:s', time() + Setting::obtain('email_verify_ttl'))
                ], []
            );
        } catch (\Exception $e) {
            return $response->withJson([
                'ret' => 0,
                'msg' => $e->getMessage()
            ]);
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '请查收验证码'
        ]);
    }

    public function register($request, $response, $next)
    {
        $anti_xss = new AntiXSS();
        $code = $anti_xss->xss_clean(trim($request->getParam('code')));

        return $this->view()
            ->assign('code', $code)
            ->assign('enable_email_verify', Setting::obtain('reg_email_verify'))
            ->display('auth/register.tpl');
    }

    public function registerHandle($request, $response, $args)
    {
        try {
            $tos = $request->getParam('tos');
            $name = trim($request->getParam('name'));
            $passwd = $request->getParam('passwd');
            $repasswd = $request->getParam('repasswd');
            $code = trim($request->getParam('code'));
            $emailcode = trim($request->getParam('emailcode'));
            $email = strtolower(trim($request->getParam('email')));
            $reg_mode = Setting::obtain('reg_mode');

            if ($tos == 'false') {
                throw new \Exception('请勾选同意服务条款与隐私政策');
            }
            if ($name == '') {
                throw new \Exception('请填写昵称');
            }
            if ($email == '') {
                throw new \Exception('请填写注册邮箱');
            }
            if (!Tools::emailCheck($email)) {
                throw new \Exception('邮箱格式不正确');
            }
            if (strlen($passwd) < 8) {
                throw new \Exception('密码长度不足8位');
            }
            if ($passwd != $repasswd) {
                throw new \Exception('两次输入的密码不相符');
            }
            if ($reg_mode == 'close') {
                throw new \Exception('未开放注册');
            }
            if ($reg_mode == 'invite' && $code == '') {
                throw new \Exception('仅开放邀请注册，请填写邀请码');
            }
            if ($reg_mode == 'invite') {
                $reg_invite_code = InviteCode::where('code', $code)->first();
                if ($reg_invite_code == null) {
                    throw new \Exception('没有找到这个邀请码');
                }
                $invite_user = User::where('id', $reg_invite_code->user_id)->first();
                if ($invite_user == null) {
                    throw new \Exception('邀请人不存在');
                }
                if ($invite_user->invite_num == 0) {
                    throw new \Exception('邀请码可用次数不足');
                }
            }
            if ($_ENV['enable_reg_im']) {
                $imtype = $request->getParam('im_type');
                $imvalue = $request->getParam('im_value');
                if ($imtype == '0' || $imtype != '1' && $imtype != '2' && $imtype != '4' && $imtype != '5' || $imvalue == '') {
                    throw new \Exception('选择社交软件并填写社交账户');
                }
                $imtype_exist = User::where('im_value', $imvalue)
                ->where('im_type', $imtype)
                ->first();
                if ($imtype_exist != null) {
                    throw new \Exception('此社交账户已被使用');
                }
            } else {
                $imtype = 1;
                $imvalue = '';
            }
            $user = User::where('email', $email)->first();
            if ($user != null) {
                throw new \Exception('此邮箱已注册');
            }
            if (Setting::obtain('reg_email_verify')) {
                $mailcount = EmailVerify::where('email', $email)
                ->where('code', $emailcode)
                ->where('expire_in', '>', time())
                ->first();
                if ($mailcount == null) {
                    throw new \Exception('邮箱验证码不正确');
                }
            }

            self::register_helper($name, $email, $passwd, $code, $imtype, $imvalue, 0);
        } catch (\Exception $e) {
            return $response->withJson([
                'ret' => 0,
                'msg' => $e->getMessage()
            ]);
        }

        if ($reg_mode == 'invite') {
            // 仅在仅允许邀请注册的情况下扣减邀请码次数
            $invite_user->invite_num -= 1;
            $invite_user->save();
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '注册成功'
        ]);
    }

    public function register_helper($name, $email, $passwd, $code, $imtype, $imvalue, $telegram_id)
    {
        $ga = new GA();
        $user = new User();
        $antiXss = new AntiXSS();

        $user->money = $_ENV['reg_money'];
        $user->email = $email;
        $user->im_type = ($imtype == '') ? 'none' : $imtype;
        $user->im_value = $antiXss->xss_clean($imvalue);
        $user->user_name = $antiXss->xss_clean($name);
        $user->port = Tools::getAvPort();
        $user->pass = Hash::passwordHash($passwd); // 登录密码
        $user->passwd = Tools::genRandomChar(16); // ss 连接密码
        $user->uuid = Uuid::uuid3(Uuid::NAMESPACE_DNS, $email . '|' . time());
        $user->t = 0;
        $user->u = 0;
        $user->d = 0;
        $user->transfer_enable = 0;
        $user->invite_num = $_ENV['reg_invite_num'];
        $user->auto_reset_day = 0;
        $user->auto_reset_bandwidth = 0;
        $user->sendDailyMail = 0; // 默认不发送
        $user->obfs = $_ENV['reg_obfs'];
        $user->method = $_ENV['reg_method'];
        $user->protocol = $_ENV['reg_protocol'];
        $user->obfs_param = $_ENV['reg_obfs_param'];
        $user->protocol_param = $_ENV['reg_protocol_param'];
        $user->forbidden_ip = $_ENV['reg_forbidden_ip'];
        $user->forbidden_port = $_ENV['reg_forbidden_port'];
        if ($telegram_id) {
            $user->telegram_id = $telegram_id;
        }
        $user->ga_enable = 0;
        $user->ga_token = $ga->createSecret();
        $user->node_connector = 0;
        $user->node_speedlimit = 0;
        $user->class = 0;
        $user->expire_in = date('Y-m-d H:i:s', time());
        $user->class_expire = date('Y-m-d H:i:s', time());
        $user->reg_date = date('Y-m-d H:i:s');
        $user->reg_ip = $_SERVER['REMOTE_ADDR'];
        $user->theme = $_ENV['theme'];
        $groups = explode(',', $_ENV['random_group']);
        $user->node_group = $groups[array_rand($groups)];
        $user->save();

        Auth::login($user->id, 3600);
        $user->collectLoginIP($_SERVER['REMOTE_ADDR']);
    }

    public function logout($request, $response, $next)
    {
        Auth::logout();
        return $response->withStatus(302)->withHeader('Location', '/auth/login');
    }
}
