<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Setting;
use App\Models\User;
use App\Services\Cache;
use App\Services\Captcha;
use App\Services\Password;
use App\Services\RateLimit;
use App\Utils\Hash;
use App\Utils\ResponseHelper;
use Exception;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use RedisException;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use voku\helper\AntiXSS;
use function strlen;

/*
 * Class Password
 *
 * @package App\Controllers
 * 密码重置
 */
final class PasswordController extends BaseController
{
    /**
     * @throws Exception
     */
    public function reset(ServerRequest $request, Response $response, array $args): Response|ResponseInterface
    {
        $captcha = [];

        if (Setting::obtain('enable_reset_password_captcha')) {
            $captcha = Captcha::generate();
        }

        return $response->write(
            $this->view()
                ->assign('captcha', $captcha)
                ->fetch('password/reset.tpl')
        );
    }

    /**
     * @throws RedisException
     */
    public function handleReset(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        if (Setting::obtain('enable_reset_password_captcha')) {
            $ret = Captcha::verify($request->getParams());
            if (! $ret) {
                return ResponseHelper::error($response, '系统无法接受你的验证结果，请刷新页面后重试');
            }
        }

        $antiXss = new AntiXSS();
        $email = strtolower($antiXss->xss_clean($request->getParam('email')));

        if ($email === '') {
            return ResponseHelper::error($response, '未填写邮箱');
        }

        if (! RateLimit::checkEmailIpLimit($request->getServerParam('REMOTE_ADDR')) ||
            ! RateLimit::checkEmailAddressLimit($email)
        ) {
            return ResponseHelper::error($response, '你的请求过于频繁，请稍后再试');
        }

        $user = User::where('email', $email)->first();
        $msg = '如果你的账户存在于我们的数据库中，那么重置密码的链接将会发送到你账户所对应的邮箱。';

        if ($user !== null) {
            try {
                Password::sendResetEmail($email);
            } catch (ClientExceptionInterface|RedisException $e) {
                $msg = '邮件发送失败，请联系网站管理员。';
            }
        }

        return ResponseHelper::successfully($response, $msg);
    }

    /**
     * @throws Exception
     */
    public function token(ServerRequest $request, Response $response, array $args)
    {
        $antiXss = new AntiXSS();
        $token = $antiXss->xss_clean($args['token']);
        $redis = Cache::initRedis();
        $email = $redis->get($token);

        if (! $email) {
            return $response->withStatus(302)->withHeader('Location', '/password/reset');
        }

        return $response->write(
            $this->view()->fetch('password/token.tpl')
        );
    }

    /**
     * @throws RedisException
     */
    public function handleToken(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $antiXss = new AntiXSS();
        $token = $antiXss->xss_clean($args['token']);
        $password = $request->getParam('password');
        $repasswd = $request->getParam('repasswd');

        if ($password !== $repasswd) {
            return ResponseHelper::error($response, '两次输入不符合');
        }

        if (strlen($password) < 8) {
            return ResponseHelper::error($response, '密码过短');
        }

        $redis = Cache::initRedis();
        $email = $redis->get($token);

        if (! $email) {
            return ResponseHelper::error($response, '链接无效');
        }

        $user = User::where('email', $email)->first();
        if ($user === null) {
            return ResponseHelper::error($response, '链接无效');
        }

        // reset password
        $hashPassword = Hash::passwordHash($password);
        $user->pass = $hashPassword;

        if (! $user->save()) {
            return ResponseHelper::error($response, '重置失败，请重试');
        }

        if (Setting::obtain('enable_forced_replacement')) {
            $user->cleanLink();
        }

        $redis->del($token);

        return ResponseHelper::successfully($response, '重置成功');
    }
}
