<?php

namespace App\Controllers;

use App\Models\{
    User,
    PasswordReset
};
use App\Utils\Hash;
use App\Services\Password;
use Slim\Http\{
    Request,
    Response
};

/***
 * Class Password
 * @package App\Controllers
 * 密码重置
 */
class PasswordController extends BaseController
{
    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function reset($request, $response, $args)
    {
        return $response->write(
            $this->view()->display('password/reset.tpl')
        );
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function handleReset($request, $response, $args)
    {
        $email = strtolower($request->getParam('email'));
        $user  = User::where('email', $email)->first();
        if ($user == null) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '此邮箱不存在'
            ]);
        }
        if (Password::sendResetEmail($email)) {
            $msg = '重置邮件已经发送,请检查邮箱.';
        } else {
            $msg = '邮件发送失败，请联系网站管理员。';
        }
        return $response->withJson([
            'ret' => 1,
            'msg' => $msg
        ]);
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function token($request, $response, $args)
    {
        return $response->write(
            $this->view()->assign('token', $args['token'])->display('password/token.tpl')
        );
    }

    /**
     * @param Request   $request
     * @param Response  $response
     * @param array     $args
     */
    public function handleToken($request, $response, $args)
    {
        $tokenStr = $args['token'];
        $password = $request->getParam('password');
        $repasswd = $request->getParam('repasswd');

        if ($password != $repasswd) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '两次输入不符合'
            ]);
        }

        if (strlen($password) < 8) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '密码太短啦'
            ]);
        }

        // check token
        $token = PasswordReset::where('token', $tokenStr)->where('expire_time', '>', time())->orderBy('id', 'desc')->first();
        if ($token == null) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '链接已经失效，请重新获取'
            ]);
        }
        /** @var PasswordReset $token */
        $user = $token->getUser();
        if ($user == null) {
            return $response->withJson([
                'ret' => 0,
                'msg' => '链接已经失效，请重新获取'
            ]);
        }

        // reset password
        $hashPassword    = Hash::passwordHash($password);
        $user->pass      = $hashPassword;
        $user->ga_enable = 0;

        if (!$user->save()) {
            $rs['ret'] = 0;
            $rs['msg'] = '重置失败，请重试';
        } else {
            $rs['ret'] = 1;
            $rs['msg'] = '重置成功';
            $user->clean_link();

            // 禁止链接多次使用
            $token->expire_time = time();
            $token->save();
        }

        return $response->withJson($rs);
    }
}
