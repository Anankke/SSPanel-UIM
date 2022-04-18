<?php

declare(strict_types=1);

namespace App\Services;

use Firebase\JWT\JWT as JwtClient;

class Jwt
{
    public static function encode($input)
    {
        return JwtClient::encode($input, self::getKey());
    }

    public static function encode_withkey($input, $key)
    {
        return JwtClient::encode($input, $key);
    }

    public static function decodeArray($input)
    {
        return JWT::decode($input, self::getKey(), ['HS256']);
    }
    private static function getKey()
    {
        return $_ENV['key'];
    }
}
