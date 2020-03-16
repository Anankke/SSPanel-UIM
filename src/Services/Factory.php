<?php

namespace App\Services;

use App\Services\Auth\Cookie;
use App\Services\Auth\Redis;
use App\Services\Auth\JwtToken;

class Factory
{
    public static function createAuth()
    {
        $method = $_ENV['authDriver'];
        switch ($method) {
            case 'cookie':
                return new Cookie();
            case 'redis':
                return new Redis();
            case 'jwt':
                return new JwtToken();
        }
        return new Redis();
    }
}
