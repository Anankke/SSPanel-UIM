<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Models\Node;
use App\Utils;
use App\Utils\Hash;
use App\Services\Config;

class Cookie extends Base
{
    public function login($uid, $time)
    {
        $user = User::find($uid);
        $key = Hash::cookieHash($user->pass);
        $expire_in = $time+time();
        Utils\Cookie::set([
            "uid" => $uid,
            "email" => $user->email,
            "key" => $key,
            "ip" => md5($_SERVER["REMOTE_ADDR"].Config::get('key').$uid.$expire_in),
            "expire_in" => $expire_in
        ], $expire_in);
    }

    public function getUser()
    {
        $uid = Utils\Cookie::get('uid');
        $key = Utils\Cookie::get('key');
        $ip = Utils\Cookie::get('ip');
        
        $expire_in = Utils\Cookie::get('expire_in');
        
        if ($uid == null) {
            $user = new User();
            $user->isLogin = false;
            return $user;
        }
        
        $nodes=Node::where("node_ip", "=", $_SERVER["REMOTE_ADDR"])->first();
        if ($ip != md5($_SERVER["REMOTE_ADDR"].Config::get('key').$uid.$expire_in) && $nodes==null && Config::get('enable_login_bind_ip')=='true') {
            $user = new User();
            $user->isLogin = false;
            return $user;
        }
        
        if ($expire_in<time()) {
            $user = new User();
            $user->isLogin = false;
            return $user;
        }

        $user = User::find($uid);
        if ($user == null) {
            $user = new User();
            $user->isLogin = false;
            return $user;
        }

        if (Hash::cookieHash($user->pass) != $key) {
            $user->isLogin = false;
            return $user;
        }
        $user->isLogin = true;
        return $user;
    }

    public function logout()
    {
        $time = time() - 1000;
        Utils\Cookie::set([
            "uid" => null,
            "email" => null,
            "key" => null
        ], $time);
    }
}
