<?php

namespace App\Services;

use Firebase\JWT\JWT as JwtClient;

class Jwt
{
    private static function getKey()
    {
        return Config::get('key');
    }

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
        $decoded = JWT::decode($input, self::getKey(), array('HS256'));
        return $decoded;
    }
}
