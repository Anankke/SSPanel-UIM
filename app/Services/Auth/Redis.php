<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Models\Node;
use App\Services\RedisClient;
use App\Utils\Tools;
use App\Utils\Cookie;
use App\Services\Config;

class Redis extends Base
{
    private $client;

    public function __construct()
    {
        $client = new RedisClient();
        $this->client = $client;
    }

    public function getClient()
    {
        $client = new RedisClient();
        return $client;
    }

    public function login($uid, $time)
    {
        $sid = Tools::genSID();
        Cookie::set([
            'sid' => $sid
        ], $time+time());
        $value = $uid;
        $this->client->setex($sid, $time, $value);
        $this->client->setex($sid."ip", $time, $_SERVER["REMOTE_ADDR"]);
    }

    public function logout()
    {
        $sid = Cookie::get('sid');
        $this->client->del($sid);
    }

    public function getUser()
    {
        $sid = Cookie::get('sid');
        $value = $this->client->get($sid);
        
        $ip = $this->client->get($sid."ip");
        $nodes=Node::where("node_ip", "=", $_SERVER["REMOTE_ADDR"])->first();
        if ($ip != $_SERVER["REMOTE_ADDR"] && $nodes==null && Config::get('enable_login_bind_ip')=='true') {
            $user = new User();
            $user->isLogin = false;
            return $user;
        }
        
        if ($value == null) {
            $user = new User();
            $user->isLogin = false;
            return $user;
        }
        $uid = $value;
        $user =  User::find($uid);
        if ($user == null) {
            $user = new User();
            $user->isLogin = false;
            return $user;
        }
        $user->isLogin = true;
        return $user;
    }
}
