<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\InviteCode;
use App\Models\Setting;
use App\Models\User;
use App\Services\Auth;
use App\Services\Cache;
use App\Services\Captcha;
use App\Services\Mail;
use App\Services\RateLimit;
use App\Utils\Cookie;
use App\Utils\Hash;
use App\Utils\ResponseHelper;
use App\Utils\Tools;
use Exception;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;
use RedisException;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Vectorface\GoogleAuthenticator;
use voku\helper\AntiXSS;
use function array_rand;
use function date;
use function explode;
use function strlen;
use function strtolower;
use function time;
use function trim;

/**
 *  AuthController
 */
final class AuthController extends BaseController
{
    /**
     * @throws Exception
     */
    public function login(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $captcha = [];

        if (Setting::obtain('enable_login_captcha')) {
            $captcha = Captcha::generate();
        }

        return $response->write($this->view()
            ->assign('base_url', $_ENV['baseUrl'])
            ->assign('captcha', $captcha)
            ->fetch('auth/login.tpl'));
    }

    public function loginHandle(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        if (Setting::obtain('enable_login_captcha')) {
            $ret = Captcha::verify($request->getParams());
            if (! $ret) {
                return $response->withJson([
                    'ret' => 0,
                    'msg' => '系统无法接受你的验证结果，请刷新页面后重试。',
                ]);
            }
        }

        $code = $request->getParam('code');
        $passwd = $request->getParam('passwd');
        $rememberMe = $request->getParam('remember_me');
        $email = strtolower(trim($request->getParam('email')));
        $redir = Cookie::get('redir') ?? '/user';

        $user = User::where('email', $email)->first();

        if ($user === null) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '邮箱或者密码错误',
            ]);
        }

        if (! Hash::checkPassword($user->pass, $passwd)) {
            // 记录登录失败
            $user->collectLoginIP($_SERVER['REMOTE_ADDR'], 1);

            return $response->withJson([
                'ret' => 0,
                'msg' => '邮箱或者密码错误',
            ]);
        }

        if ($user->ga_enable === 1) {
            if (strlen($code) !== 6) {
                // 记录登录失败
                $user->collectLoginIP($_SERVER['REMOTE_ADDR'], 1);

                return $response->withJson([
                    'ret' => 0,
                    'msg' => '两步验证码错误',
                ]);
            }

            $ga = new GoogleAuthenticator();
            $rcode = $ga->verifyCode($user->ga_token, $code);

            if (! $rcode) {
                // 记录登录失败
                $user->collectLoginIP($_SERVER['REMOTE_ADDR'], 1);

                return $response->withJson([
                    'ret' => 0,
                    'msg' => '两步验证码错误',
                ]);
            }
        }

        $time = 3600 * 24;
        if ($rememberMe) {
            $time = 3600 * 24 * ($_ENV['rememberMeDuration'] ?: 7);
        }

        Auth::login($user->id, $time);
        // 记录登录成功
        $user->collectLoginIP($_SERVER['REMOTE_ADDR']);

        return $response->withJson([
            'ret' => 1,
            'msg' => '登录成功',
            'redir' => $redir,
        ]);
    }

    /**
     * @throws Exception
     */
    public function register(ServerRequest $request, Response $response, $next): Response|ResponseInterface
    {
        $captcha = [];

        if (Setting::obtain('enable_reg_captcha')) {
            $captcha = Captcha::generate();
        }

        $ary = $request->getQueryParams();
        $code = '';
        if (isset($ary['code'])) {
            $antiXss = new AntiXSS();
            $code = $antiXss->xss_clean($ary['code']);
        }

        return $response->write($this->view()
            ->assign('code', $code)
            ->assign('base_url', $_ENV['baseUrl'])
            ->assign('captcha', $captcha)
            ->fetch('auth/register.tpl'));
    }

    /**
     * @throws RedisException
     */
    public function sendVerify(ServerRequest $request, Response $response, $next): Response|ResponseInterface
    {
        if (Setting::obtain('reg_email_verify')) {
            $antiXss = new AntiXSS();
            $email = strtolower(trim($antiXss->xss_clean($request->getParam('email'))));

            if ($email === '') {
                return ResponseHelper::error($response, '未填写邮箱');
            }

            // check email format
            $check_res = Tools::isEmailLegal($email);
            if ($check_res['ret'] === 0) {
                return $response->withJson($check_res);
            }

            if (! RateLimit::checkEmailIpLimit($request->getServerParam('REMOTE_ADDR')) ||
                ! RateLimit::checkEmailAddressLimit($email)
            ) {
                return ResponseHelper::error($response, '你的请求过于频繁，请稍后再试');
            }

            $user = User::where('email', $email)->first();

            if ($user !== null) {
                return ResponseHelper::error($response, '此邮箱已经注册');
            }

            $code = Tools::genRandomChar(6);
            $redis = Cache::initRedis();
            $redis->setex($code, Setting::obtain('email_verify_code_ttl'), $email);

            try {
                Mail::send(
                    $email,
                    $_ENV['appName'] . '- 验证邮件',
                    'verify_code.tpl',
                    [
                        'code' => $code,
                        'expire' => date('Y-m-d H:i:s', time() + Setting::obtain('email_verify_code_ttl')),
                    ]
                );
            } catch (Exception|ClientExceptionInterface $e) {
                return ResponseHelper::error($response, '邮件发送失败，请联系网站管理员。');
            }

            return ResponseHelper::successfully($response, '验证码发送成功，请查收邮件。');
        }

        return ResponseHelper::error($response, '站点未启用邮件验证');
    }

    /**
     * @param Response $response
     * @param $name
     * @param $email
     * @param $passwd
     * @param $code
     * @param $imtype
     * @param $imvalue
     * @param $telegram_id
     * @param $money
     * @param $is_admin_reg
     *
     * @return ResponseInterface
     *
     * @throws Exception
     */
    public static function registerHelper(
        Response $response,
        $name,
        $email,
        $passwd,
        $code,
        $imtype,
        $imvalue,
        $telegram_id,
        $money,
        $is_admin_reg
    ): ResponseInterface {
        $redir = Cookie::get('redir') ?? '/user';
        $configs = Setting::getClass('register');
        // do reg user
        $user = new User();

        $user->user_name = $name;
        $user->email = $email;
        $user->remark = '';
        $user->pass = Hash::passwordHash($passwd);
        $user->passwd = Tools::genRandomChar(16);
        $user->uuid = Uuid::uuid4();
        $user->api_token = Uuid::uuid4();
        $user->port = Tools::getAvPort();
        $user->u = 0;
        $user->d = 0;
        $user->method = $configs['sign_up_for_method'];
        $user->forbidden_ip = Setting::obtain('reg_forbidden_ip');
        $user->forbidden_port = Setting::obtain('reg_forbidden_port');
        $user->im_type = $imtype;
        $user->im_value = $imvalue;
        $user->telegram_id = $telegram_id;

        $user->transfer_enable = Tools::toGB($configs['sign_up_for_free_traffic']);
        $user->invite_num = $configs['sign_up_for_invitation_codes'];
        $user->auto_reset_day = Setting::obtain('free_user_reset_day');
        $user->auto_reset_bandwidth = Setting::obtain('free_user_reset_bandwidth');
        $user->daily_mail_enable = $configs['sign_up_for_daily_report'];

        if ($money > 0) {
            $user->money = $money;
        } else {
            $user->money = 0;
        }

        $user->ref_by = 0;

        if ($code !== '') {
            $invite = InviteCode::where('code', $code)->first();
            $invite->reward();
            $user->ref_by = $invite->user_id;
            $user->money = Setting::obtain('invitation_to_register_balance_reward');
        }

        $ga = new GoogleAuthenticator();
        $secret = $ga->createSecret();
        $user->ga_token = $secret;
        $user->ga_enable = 0;

        $user->class_expire = date('Y-m-d H:i:s', time() + (int) $configs['sign_up_for_class_time'] * 86400);
        $user->class = $configs['sign_up_for_class'];
        $user->node_iplimit = $configs['connection_ip_limit'];
        $user->node_speedlimit = $configs['connection_rate_limit'];
        $user->expire_in = date('Y-m-d H:i:s', time() + (int) $configs['sign_up_for_free_time'] * 86400);
        $user->reg_date = date('Y-m-d H:i:s');
        $user->reg_ip = $_SERVER['REMOTE_ADDR'];
        $user->theme = $_ENV['theme'];
        $user->use_new_shop = 1;
        $random_group = Setting::obtain('random_group');

        if ($random_group === '') {
            $user->node_group = 0;
        } else {
            $user->node_group = $random_group[array_rand(explode(',', $random_group))];
        }

        if ($user->save() && ! $is_admin_reg) {
            Auth::login($user->id, 3600);
            $user->collectLoginIP($_SERVER['REMOTE_ADDR']);

            return $response->withJson([
                'ret' => 1,
                'msg' => '注册成功！正在进入登录界面',
                'redir' => $redir,
            ]);
        }

        return ResponseHelper::error($response, '未知错误');
    }

    /**
     * @throws Exception
     */
    public function registerHandle(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        if (Setting::obtain('reg_mode') === 'close') {
            return ResponseHelper::error($response, '未开放注册。');
        }

        if (Setting::obtain('enable_reg_captcha')) {
            $ret = Captcha::verify($request->getParams());
            if (! $ret) {
                return ResponseHelper::error($response, '系统无法接受你的验证结果，请刷新页面后重试。');
            }
        }

        $antiXss = new AntiXSS();

        $tos = $request->getParam('tos') === 'true' ? 1 : 0;
        $email = strtolower(trim($antiXss->xss_clean($request->getParam('email'))));
        $name = $antiXss->xss_clean($request->getParam('name'));
        $passwd = $request->getParam('passwd');
        $repasswd = $request->getParam('repasswd');
        $code = $antiXss->xss_clean(trim($request->getParam('code')));
        // Check TOS agreement
        if (! $tos) {
            return ResponseHelper::error($response, '请同意服务条款');
        }
        // Check Invite Code
        if ($code === '' && Setting::obtain('reg_mode') === 'invite') {
            return ResponseHelper::error($response, '邀请码不能为空');
        }

        if ($code !== '') {
            $user_invite = InviteCode::where('code', $code)->first();

            if ($user_invite === null) {
                return ResponseHelper::error($response, '邀请码无效');
            }

            $gift_user = User::where('id', $user_invite->user_id)->first();

            if ($gift_user === null || $gift_user->invite_num === 0) {
                return ResponseHelper::error($response, '邀请码无效');
            }
        }

        // Check IM
        if (Setting::obtain('enable_reg_im')) {
            $imtype = $antiXss->xss_clean($request->getParam('im_type'));
            $imvalue = $antiXss->xss_clean($request->getParam('im_value'));

            if ($imtype === '' || $imvalue === '') {
                return ResponseHelper::error($response, '请填上你的联络方式');
            }

            $user = User::where('im_value', $imvalue)->where('im_type', $imtype)->first();

            if ($user !== null) {
                return ResponseHelper::error($response, '此联络方式已注册');
            }
        } else {
            $imtype = 1;
            $imvalue = '';
        }

        // check email format
        $check_res = Tools::isEmailLegal($email);
        if ($check_res['ret'] === 0) {
            return $response->withJson($check_res);
        }
        // check email
        $user = User::where('email', $email)->first();
        if ($user !== null) {
            return ResponseHelper::error($response, '邮箱已经被注册了');
        }
        // check pwd length
        if (strlen($passwd) < 8) {
            return ResponseHelper::error($response, '密码请大于8位');
        }
        // check pwd re
        if ($passwd !== $repasswd) {
            return ResponseHelper::error($response, '两次密码输入不符');
        }

        if (Setting::obtain('reg_email_verify')) {
            $redis = Cache::initRedis();
            $email_verify_code = trim($antiXss->xss_clean($request->getParam('emailcode')));
            $email_verify = $redis->get($email_verify_code);

            if (! $email_verify) {
                return ResponseHelper::error($response, '你的邮箱验证码不正确');
            }

            $redis->del($email_verify_code);
        }

        return $this->registerHelper($response, $name, $email, $passwd, $code, $imtype, $imvalue, 0, 0, 0);
    }

    public function logout(ServerRequest $request, Response $response, $next): Response
    {
        Auth::logout();
        return $response->withStatus(302)
            ->withHeader('Location', '/auth/login');
    }
}
