<?php

namespace App\Utils;

use App\Services\Config;

class Hash
{
    public static function passwordHash($str)
    {
        $method = Config::get('pwdMethod');
        switch ($method) {
            case 'md5':
                return self::md5WithSalt($str);
                break;
            case 'sha256':
                return self::sha256WithSalt($str);
                break;
            default:
                return self::md5WithSalt($str);
        }
        return $str;
    }

    public static function cookieHash($str)
    {
        return  substr(hash('sha256', $str.Config::get('key')), 5, 45);
    }

    public static function md5WithSalt($pwd)
    {
        $salt = Config::get('salt');
        return md5($pwd.$salt);
    }

    public static function sha256WithSalt($pwd)
    {
        $salt = Config::get('salt');
        return hash('sha256', $pwd.$salt);
    }

    // @TODO
    public static function checkPassword($hashedPassword, $password)
    {
        $method = Config::get('pwdMethod');
        if ($hashedPassword == self::passwordHash($password)) {
            return true;
        }
        return false;
    }
}
