<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Config;
use App\Models\InviteCode;
use App\Models\LoginIp;
use App\Models\User;
use App\Services\Auth;
use App\Services\Cache;
use App\Services\Captcha;
use App\Services\Filter;
use App\Services\Mail;
use App\Services\MFA;
use App\Services\RateLimit;
use App\Services\Reward;
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
use function array_rand;
use function date;
use function explode;
use function strlen;
use function strtolower;
use function time;
use function trim;

final class AuthController extends BaseController
{
    /**
     * @throws Exception
     */
    public function login(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $captcha = [];

        if (Config::obtain('enable_login_captcha')) {
            $captcha = Captcha::generate();
        }

        return $response->write($this->view()
            ->assign('base_url', $_ENV['baseUrl'])
            ->assign('captcha', $captcha)
            ->fetch('auth/login.tpl'));
    }

    public function loginHandle(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        if (Config::obtain('enable_login_captcha') && ! Captcha::verify($request->getParams())) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '系统无法接受你的验证结果，请刷新页面后重试。',
            ]);
        }

        $mfa_code = $this->antiXss->xss_clean($request->getParam('mfa_code'));
        $password = $request->getParam('password');
        $rememberMe = $request->getParam('remember_me') === 'true' ? 1 : 0;
        $email = strtolower(trim($this->antiXss->xss_clean($request->getParam('email'))));
        $redir = $this->antiXss->xss_clean(Cookie::get('redir')) ?? '/user';
        $user = (new User())->where('email', $email)->first();
        $loginIp = new LoginIp();

        if ($user === null) {
            $loginIp->collectLoginIP($_SERVER['REMOTE_ADDR'], 1);

            return $response->withJson([
                'ret' => 0,
                'msg' => '邮箱或者密码错误',
            ]);
        }

        if (! Hash::checkPassword($user->pass, $password)) {
            $loginIp->collectLoginIP($_SERVER['REMOTE_ADDR'], 1, $user->id);

            return $response->withJson([
                'ret' => 0,
                'msg' => '邮箱或者密码错误',
            ]);
        }

        if ($user->ga_enable && (strlen($mfa_code) !== 6 || ! MFA::verifyGa($user, $mfa_code))) {
            $loginIp->collectLoginIP($_SERVER['REMOTE_ADDR'], 1, $user->id);

            return $response->withJson([
                'ret' => 0,
                'msg' => '两步验证码错误',
            ]);
        }

        $time = 3600;

        if ($rememberMe) {
            $time = 86400 * ($_ENV['rememberMeDuration'] ?: 7);
        }

        Auth::login($user->id, $time);
        // 记录登录成功
        $loginIp->collectLoginIP($_SERVER['REMOTE_ADDR'], 0, $user->id);
        $user->last_login_time = time();
        $user->save();

        return $response->withHeader('HX-Redirect', $redir);
    }

    /**
     * @throws Exception
     */
    public function register(ServerRequest $request, Response $response, $next): ResponseInterface
    {
        $captcha = [];

        if (Config::obtain('enable_reg_captcha')) {
            $captcha = Captcha::generate();
        }

        $invite_code = $this->antiXss->xss_clean($request->getParam('code'));

        return $response->write(
            $this->view()
                ->assign('invite_code', $invite_code)
                ->assign('base_url', $_ENV['baseUrl'])
                ->assign('captcha', $captcha)
                ->fetch('auth/register.tpl')
        );
    }

    /**
     * @throws RedisException
     */
    public function sendVerify(ServerRequest $request, Response $response, $next): ResponseInterface
    {
        if (Config::obtain('reg_email_verify')) {
            $email = strtolower(trim($this->antiXss->xss_clean($request->getParam('email'))));

            if ($email === '') {
                return ResponseHelper::error($response, '未填写邮箱');
            }

            // check email format
            $email_check = Filter::checkEmailFilter($email);

            if (! $email_check) {
                return ResponseHelper::error($response, '无效的邮箱');
            }

            if (! (new RateLimit())->checkRateLimit('email_request_ip', $request->getServerParam('REMOTE_ADDR')) ||
                ! (new RateLimit())->checkRateLimit('email_request_address', $email)
            ) {
                return ResponseHelper::error($response, '你的请求过于频繁，请稍后再试');
            }

            $user = (new User())->where('email', $email)->first();

            if ($user !== null) {
                return ResponseHelper::error($response, '此邮箱已经注册');
            }

            $email_code = Tools::genRandomChar(6);
            $redis = (new Cache())->initRedis();
            $redis->setex('email_verify:' . $email_code, Config::obtain('email_verify_code_ttl'), $email);

            try {
                Mail::send(
                    $email,
                    $_ENV['appName'] . '- 验证邮件',
                    'verify_code.tpl',
                    [
                        'code' => $email_code,
                        'expire' => date('Y-m-d H:i:s', time() + Config::obtain('email_verify_code_ttl')),
                    ]
                );
            } catch (Exception|ClientExceptionInterface) {
                return ResponseHelper::error($response, '邮件发送失败，请联系网站管理员。');
            }

            return ResponseHelper::success($response, '验证码发送成功，请查收邮件。');
        }

        return ResponseHelper::error($response, '站点未启用邮件验证');
    }

    /**
     * @throws Exception
     */
    public function registerHelper(
        Response $response,
        $name,
        $email,
        $password,
        $invite_code,
        $imtype,
        $imvalue,
        $money,
        $is_admin_reg
    ): ResponseInterface {
        $redir = $this->antiXss->xss_clean(Cookie::get('redir')) ?? '/user';
        $configs = Config::getClass('reg');
        // do reg user
        $user = new User();

        $user->user_name = $name;
        $user->email = $email;
        $user->remark = '';
        $user->pass = Hash::passwordHash($password);
        $user->passwd = Tools::genRandomChar(16);
        $user->uuid = Uuid::uuid4();
        $user->api_token = Tools::genRandomChar(32);
        $user->port = Tools::getSsPort();
        $user->u = 0;
        $user->d = 0;
        $user->method = $configs['reg_method'];
        $user->im_type = $imtype;
        $user->im_value = $imvalue;
        $user->transfer_enable = Tools::gbToB($configs['reg_traffic']);
        $user->auto_reset_day = Config::obtain('free_user_reset_day');
        $user->auto_reset_bandwidth = Config::obtain('free_user_reset_bandwidth');
        $user->daily_mail_enable = $configs['reg_daily_report'];

        if ($money > 0) {
            $user->money = $money;
        } else {
            $user->money = 0;
        }

        $user->ref_by = 0;

        if ($invite_code !== '') {
            $invite = (new InviteCode())->where('code', $invite_code)->first();

            if ($invite !== null) {
                $user->ref_by = $invite->user_id;
            }
        }

        $user->ga_token = MFA::generateGaToken();
        $user->ga_enable = 0;
        $user->class = $configs['reg_class'];
        $user->class_expire = date('Y-m-d H:i:s', time() + (int) $configs['reg_class_time'] * 86400);
        $user->node_iplimit = $configs['reg_ip_limit'];
        $user->node_speedlimit = $configs['reg_speed_limit'];
        $user->reg_date = date('Y-m-d H:i:s');
        $user->reg_ip = $_SERVER['REMOTE_ADDR'];
        $user->theme = $_ENV['theme'];
        $user->locale = $_ENV['locale'];
        $random_group = Config::obtain('random_group');

        if ($random_group === '') {
            $user->node_group = 0;
        } else {
            $user->node_group = $random_group[array_rand(explode(',', $random_group))];
        }

        $user->last_login_time = time();

        if ($user->save() && ! $is_admin_reg) {
            if ($user->ref_by !== 0) {
                Reward::issueRegReward($user->id, $user->ref_by);
            }

            Auth::login($user->id, 3600);
            (new LoginIp())->collectLoginIP($_SERVER['REMOTE_ADDR'], 0, $user->id);

            return $response->withHeader('HX-Redirect', $redir);
        }

        return ResponseHelper::error($response, '未知错误');
    }

    /**
     * @throws RedisException
     * @throws Exception
     */
    public function registerHandle(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        if (Config::obtain('reg_mode') === 'close') {
            return ResponseHelper::error($response, '未开放注册。');
        }

        if (Config::obtain('enable_reg_captcha') && ! Captcha::verify($request->getParams())) {
            return ResponseHelper::error($response, '系统无法接受你的验证结果，请刷新页面后重试。');
        }

        $tos = $request->getParam('tos') === 'true' ? 1 : 0;
        $email = strtolower(trim($this->antiXss->xss_clean($request->getParam('email'))));
        $name = $this->antiXss->xss_clean($request->getParam('name'));
        $password = $request->getParam('password');
        $confirm_password = $request->getParam('confirm_password');
        $invite_code = $this->antiXss->xss_clean(trim($request->getParam('invite_code')));

        if (! $tos) {
            return ResponseHelper::error($response, '请同意服务条款');
        }

        if (strlen($password) < 8) {
            return ResponseHelper::error($response, '密码请大于8位');
        }

        if ($password !== $confirm_password) {
            return ResponseHelper::error($response, '两次密码输入不符');
        }

        if ($invite_code === '' && Config::obtain('reg_mode') === 'invite') {
            return ResponseHelper::error($response, '邀请码不能为空');
        }

        if ($invite_code !== '') {
            $invite = (new InviteCode())->where('code', $invite_code)->first();

            if ($invite === null) {
                return ResponseHelper::error($response, '邀请码无效');
            }

            $ref_user = (new User())->where('id', $invite->user_id)->first();

            if ($ref_user === null) {
                return ResponseHelper::error($response, '邀请码无效');
            }
        }

        $imtype = 0;
        $imvalue = '';

        // check email format
        $email_check = Filter::checkEmailFilter($email);

        if (! $email_check) {
            return ResponseHelper::error($response, '无效的邮箱');
        }
        // check email
        $user = (new User())->where('email', $email)->first();

        if ($user !== null) {
            return ResponseHelper::error($response, '无效的邮箱');
        }

        if (Config::obtain('reg_email_verify')) {
            $redis = (new Cache())->initRedis();
            $email_verify_code = trim($this->antiXss->xss_clean($request->getParam('emailcode')));
            $email_verify = $redis->get('email_verify:' . $email_verify_code);

            if (! $email_verify) {
                return ResponseHelper::error($response, '你的邮箱验证码不正确');
            }

            $redis->del('email_verify:' . $email_verify_code);
        }

        return $this->registerHelper($response, $name, $email, $password, $invite_code, $imtype, $imvalue, 0, 0);
    }

    public function logout(ServerRequest $request, Response $response, $next): Response
    {
        Auth::logout();

        return $response->withStatus(302)->withHeader('Location', '/auth/login');
    }
}
