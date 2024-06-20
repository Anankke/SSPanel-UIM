<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Config;
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
use function strlen;

final class PasswordController extends BaseController
{
    /**
     * @throws Exception
     */
    public function reset(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        $captcha = [];

        if (Config::obtain('enable_reset_password_captcha')) {
            $captcha = Captcha::generate();
        }

        return $response->write(
            $this->view()
                ->assign('captcha', $captcha)
                ->fetch('password/reset.tpl')
        );
    }

    public function handleReset(ServerRequest $request, Response $response, array $args): ResponseInterface
    {
        if (Config::obtain('enable_reset_password_captcha')) {
            $ret = Captcha::verify($request->getParams());

            if (! $ret) {
                return ResponseHelper::error($response, '系统无法接受你的验证结果，请刷新页面后重试');
            }
        }

        $email = strtolower($this->antiXss->xss_clean($request->getParam('email')));

        if ($email === '') {
            return ResponseHelper::error($response, '未填写邮箱');
        }

        if (! (new RateLimit())->checkRateLimit('email_request_ip', $request->getServerParam('REMOTE_ADDR')) ||
            ! (new RateLimit())->checkRateLimit('email_request_address', $email)
        ) {
            return ResponseHelper::error($response, '你的请求过于频繁，请稍后再试');
        }

        $user = (new User())->where('email', $email)->first();
        $msg = '如果你的账户存在于我们的数据库中，那么重置密码的链接将会发送到你账户所对应的邮箱';

        if ($user !== null) {
            try {
                Password::sendResetEmail($email);
            } catch (ClientExceptionInterface|RedisException) {
                $msg = '邮件发送失败';
            }
        }

        return ResponseHelper::success($response, $msg);
    }

    /**
     * @throws Exception
     */
    public function token(ServerRequest $request, Response $response, array $args)
    {
        $token = $this->antiXss->xss_clean($args['token']);
        $redis = (new Cache())->initRedis();
        $email = $redis->get('password_reset:' . $token);

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
        $token = $this->antiXss->xss_clean($args['token']);
        $password = $request->getParam('password');
        $confirm_password = $request->getParam('confirm_password');

        if ($password !== $confirm_password) {
            return ResponseHelper::error($response, '两次输入不符合');
        }

        if (strlen($password) < 8) {
            return ResponseHelper::error($response, '密码过短');
        }

        $redis = (new Cache())->initRedis();
        $email = $redis->get('password_reset:' . $token);

        if (! $email) {
            return ResponseHelper::error($response, '链接无效');
        }

        $user = (new User())->where('email', $email)->first();

        if ($user === null) {
            return ResponseHelper::error($response, '链接无效');
        }

        // reset password
        $hashPassword = Hash::passwordHash($password);
        $user->pass = $hashPassword;

        if (! $user->save()) {
            return ResponseHelper::error($response, '重置失败，请重试');
        }

        if (Config::obtain('enable_forced_replacement')) {
            $user->removeLink();
        }

        $redis->del('password_reset:' . $token);

        return ResponseHelper::success($response, '重置成功');
    }
}
