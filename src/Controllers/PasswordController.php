<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\PasswordReset;
use App\Models\User;
use App\Services\Password;
use App\Utils\Hash;
use App\Utils\ResponseHelper;
use Slim\Http\Request;
use Slim\Http\Response;

/*
 * Class Password
 *
 * @package App\Controllers
 * 密码重置
 */
final class PasswordController extends BaseController
{
    /**
     * @param array     $args
     */
    public function reset(Request $request, Response $response, array $args)
    {
        return $response->write(
            $this->view()->display('password/reset.tpl')
        );
    }

    /**
     * @param array     $args
     */
    public function handleReset(Request $request, Response $response, array $args)
    {
        $email = strtolower($request->getParam('email'));
        $user = User::where('email', $email)->first();
        if ($user === null) {
            return ResponseHelper::error($response, '此邮箱不存在');
        }
        if (Password::sendResetEmail($email)) {
            $msg = '重置邮件已经发送,请检查邮箱.';
        } else {
            $msg = '邮件发送失败，请联系网站管理员。';
        }
        return ResponseHelper::successfully($response, $msg);
    }

    /**
     * @param array     $args
     */
    public function token(Request $request, Response $response, array $args)
    {
        $token = PasswordReset::where('token', $args['token'])->where('expire_time', '>', time())->orderBy('id', 'desc')->first();
        if ($token === null) {
            return $response->withStatus(302)->withHeader('Location', '/password/reset');
        }
        return $response->write(
            $this->view()->display('password/token.tpl')
        );
    }

    /**
     * @param array     $args
     */
    public function handleToken(Request $request, Response $response, array $args)
    {
        $tokenStr = $args['token'];
        $password = $request->getParam('password');
        $repasswd = $request->getParam('repasswd');

        if ($password !== $repasswd) {
            return ResponseHelper::error($response, '两次输入不符合');
        }

        if (strlen($password) < 8) {
            return ResponseHelper::error($response, '密码太短啦');
        }

        /** @var PasswordReset $token */
        $token = PasswordReset::where('token', $tokenStr)->where('expire_time', '>', time())->orderBy('id', 'desc')->first();
        if ($token === null) {
            return ResponseHelper::error($response, '链接已经失效，请重新获取');
        }

        $user = $token->getUser();
        if ($user === null) {
            return ResponseHelper::error($response, '链接已经失效，请重新获取');
        }

        // reset password
        $hashPassword = Hash::passwordHash($password);
        $user->pass = $hashPassword;
        $user->ga_enable = 0;

        if (! $user->save()) {
            return ResponseHelper::error($response, '重置失败，请重试');
        }

        if ($_ENV['enable_forced_replacement'] === true) {
            $user->cleanLink();
        }

        // 禁止链接多次使用
        $token->expire_time = time();
        $token->save();

        return ResponseHelper::successfully($response, '重置成功');
    }
}
