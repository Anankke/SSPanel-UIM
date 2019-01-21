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
            case 'argon2i':
                return self::argon2iWithSalt($str);
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
    public static function argon2iWithSalt($pwd)
    {
        $salt = Config::get('salt');
        return hash('sha256',password_hash($pwd.$salt, PASSWORD_ARGON2I));
        //由于直接Argon2字符超过64位数据库装不下，懒得去改数据库了，所有再套一层sha256在外面缩短下长度
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
