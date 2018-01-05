<?php

namespace App\Services;

use App\Services\Auth\Cookie;
use App\Services\Auth\Redis;
use App\Services\Auth\JwtToken;
use App\Services\Token\DB;
use App\Services\Token\Dynamodb;

class Factory
{
    public static function createAuth()
    {
        $method = Config::get('authDriver');
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

    public static function createCache()
    {
    }

    public static function createMail()
    {
    }

    public static function createTokenStorage()
    {
        switch (Config::get('tokenDriver')) {
            case 'db':
                return new DB();
            case 'dynamodb':
                return new Dynamodb();
            default:
                return new DB();
        }
    }
}
