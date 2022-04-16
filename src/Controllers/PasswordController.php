<?php
namespace App\Controllers;

use App\Models\PasswordReset;
use App\Models\User;
use App\Models\Setting;
use App\Services\Password;
use App\Utils\Hash;
use App\Utils\Tools;
use Slim\Http\Request;
use Slim\Http\Response;

class PasswordController extends BaseController
{
    public function reset($request, $response, $args)
    {
        return $response->write(
            $this->view()->display('password/reset.tpl')
        );
    }

    public function handleReset($request, $response, $args)
    {
        try {
            $email = strtolower($request->getParam('email'));
            $user = User::where('email', $email)->first();
            if ($user == null) {
                throw new \Exception('此邮箱不存在');
            }
            if (!Tools::emailCheck($email)) {
                throw new \Exception('邮箱格式不正确');
            }
            if (Setting::obtain('mail_driver') == 'none') {
                throw new \Exception('没有有效的发信配置');
            }
            Password::sendResetEmail($email);
        } catch (\Exception $e) {
            return $response->withJson([
                'ret' => 0,
                'msg' => $e->getMessage()
            ]);
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '已发送，请查收邮箱收件箱或垃圾箱'
        ]);
    }

    public function token($request, $response, $args)
    {
        $token = PasswordReset::where('token', $args['token'])
        ->where('expire_time', '>', time())
        ->orderBy('id', 'desc')
        ->first();

        if ($token == null) {
            return $response->withStatus(302)->withHeader('Location', '/password/reset');
        }

        return $response->write(
            $this->view()->display('password/token.tpl')
        );
    }

    public function handleToken($request, $response, $args)
    {
        $tokenStr = $args['token'];
        $password = $request->getParam('password');
        $repasswd = $request->getParam('repasswd');

        try {
            if (strlen($password) < 8) {
                throw new \Exception('密码长度不足8位');
            }
            if ($password != $repasswd) {
                throw new \Exception('两次输入不符合');
            }
            $token = PasswordReset::where('token', $tokenStr)
            ->where('expire_time', '>', time())
            ->orderBy('id', 'desc')
            ->first();
            if ($token == null) {
                throw new \Exception('链接已经失效，请重新获取');
            }
            $user = $token->getUser();
            if ($user == null) {
                throw new \Exception('链接已经失效，请重新获取');
            }

            $hashPassword = Hash::passwordHash($password);
            $user->pass = $hashPassword;
            $user->ga_enable = 0;
            $user->save();
            if ($_ENV['enable_forced_replacement'] == true) {
                $user->clean_link();
            }

            $token->expire_time = time();
            $token->save();
        } catch (\Exception $e) {
            return $response->withJson([
                'ret' => 0,
                'msg' => $e->getMessage()
            ]);
        }

        return $response->withJson([
            'ret' => 1,
            'msg' => '重置成功，请使用新密码登录'
        ]);
    }
}
