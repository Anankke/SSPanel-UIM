<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Services\Jwt;
use App\Utils;

class JwtToken extends Base
{
    public function login($uid, $time): void
    {
        $expireTime = time() + $time;
        $ary = [
            'uid' => $uid,
            'expire_time' => $expireTime,
        ];
        $decode = Jwt::encode($ary);
        Utils\Cookie::set([
            //"uid" => $uid,
            'token' => $decode,
        ], $expireTime);
    }

    public function logout(): void
    {
        Utils\Cookie::set([
            //"uid" => $uid,
            'token' => '',
        ], time() - 3600);
    }

    public function getUser(): void
    {
        $token = Utils\Cookie::get('token');
        $tokenInfo = Jwt::decodeArray($token);
    }
}
