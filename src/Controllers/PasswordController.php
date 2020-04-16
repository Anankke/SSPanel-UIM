<?php

namespace App\Controllers;

use App\Models\{
    User,
    PasswordReset
};
use App\Utils\Hash;
use App\Services\Password;

/***
 * Class Password
 * @package App\Controllers
 * 密码重置
 */
class PasswordController extends BaseController
{
    public function reset()
    {
        return $this->view()->display('password/reset.tpl');
    }

    public function handleReset($request, $response, $args)
    {
        $email = $request->getParam('email');
        // check limit

        // send email
        $user = User::where('email', $email)->first();
        if ($user == null) {
            $rs['ret'] = 0;
            $rs['msg'] = '此邮箱不存在.';
            return $response->getBody()->write(json_encode($rs));
        }
        $rs['ret'] = 1;
        $rs['msg'] = '重置邮件已经发送,请检查邮箱.';
        if (Password::sendResetEmail($email)) {
            $res['msg'] = '邮件发送失败，请联系网站管理员。';
        }

        return $response->getBody()->write(json_encode($rs));
    }

    public function token($request, $response, $args)
    {
        $token = $args['token'];
        return $this->view()->assign('token', $token)->display('password/token.tpl');
    }

    public function handleToken($request, $response, $args)
    {
        $tokenStr = $args['token'];
        $password = $request->getParam('password');
        $repasswd = $request->getParam('repasswd');

        if ($password != $repasswd) {
            $res['ret'] = 0;
            $res['msg'] = '两次输入不符合';
            return $response->getBody()->write(json_encode($res));
        }

        if (strlen($password) < 8) {
            $res['ret'] = 0;
            $res['msg'] = '密码太短啦';
            return $response->getBody()->write(json_encode($res));
        }

        // check token
        $token = PasswordReset::where('token', $tokenStr)->where('expire_time', '>', time())->orderBy('id', 'desc')->first();
        if ($token == null) {
            $rs['ret'] = 0;
            $rs['msg'] = '链接已经失效，请重新获取';
            return $response->getBody()->write(json_encode($rs));
        }

        $user = User::where('email', $token->email)->first();
        if ($user == null) {
            $rs['ret'] = 0;
            $rs['msg'] = '链接已经失效，请重新获取';
            return $response->getBody()->write(json_encode($rs));
        }

        // reset password
        $hashPassword = Hash::passwordHash($password);
        $user->pass = $hashPassword;
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

        return $response->write(json_encode($rs));
    }
}
