<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Models\Node;
use App\Models\User;
use App\Utils\Cookie as CookieUtils;
use App\Utils\Hash;
use function strval;
use function time;

final class Cookie extends Base
{
    public function login($uid, $time): void
    {
        $user = User::find($uid);
        $expire_in = $time + time();

        CookieUtils::set([
            'uid' => strval($uid),
            'email' => $user->email,
            'key' => Hash::cookieHash($user->pass, $expire_in),
            'ip' => Hash::ipHash($_SERVER['REMOTE_ADDR'], $uid, $expire_in),
            'expire_in' => strval($expire_in),
        ], $expire_in);
    }

    public function getUser(): User
    {
        $uid = CookieUtils::get('uid');
        $email = CookieUtils::get('email');
        $key = CookieUtils::get('key');
        $ipHash = CookieUtils::get('ip');
        $expire_in = CookieUtils::get('expire_in');

        $user = new User();
        $user->isLogin = false;

        if ($uid === null) {
            return $user;
        }

        if ($expire_in < time()) {
            return $user;
        }

        if ($_ENV['enable_login_bind_ip']) {
            $nodes = Node::where('node_ip', $_SERVER['REMOTE_ADDR'])->first();

            if (($nodes === null) && $ipHash !== Hash::ipHash($_SERVER['REMOTE_ADDR'], $uid, $expire_in)) {
                return $user;
            }
        }

        $user = User::find($uid);

        if ($user === null) {
            $user = new User();
            $user->isLogin = false;
            return $user;
        }

        if ($user->email !== $email) {
            $user = new User();
            $user->isLogin = false;
        }

        if (Hash::cookieHash($user->pass, $expire_in) !== $key) {
            $user = new User();
            $user->isLogin = false;
            return $user;
        }

        $user->isLogin = true;

        return $user;
    }

    public function logout(): void
    {
        $time = time() - 1000;

        CookieUtils::set([
            'uid' => '',
            'email' => '',
            'key' => '',
            'ip' => '',
            'expire_in' => '',
        ], $time);
    }
}
