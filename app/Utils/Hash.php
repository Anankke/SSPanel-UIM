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
                return self::argon2i($str);
                break;
            case 'bcypt':
                return self::bcypt($str);
                break;
            default:
                return self::md5WithSalt($str);
        }
    }

    public static function cookieHash($str)
    {
        return substr(hash('sha256', $str . Config::get('key')), 5, 45);
    }

    public static function md5WithSalt($pwd)
    {
        $salt = Config::get('salt');
        return md5($pwd . $salt);
    }

    public static function sha256WithSalt($pwd)
    {
        $salt = Config::get('salt');
        return hash('sha256', $pwd . $salt);
    }

    public static function argon2i($pwd)
    {
        $salt = Config::get('salt');
        if ($salt == "") return password_hash($pwd, PASSWORD_ARGON2I);
        else return password_hash($pwd, PASSWORD_ARGON2I, ['salt' => $salt]);
    }

    public static function bcypt($pwd)
    {
        $salt = Config::get('salt');
        if ($salt == "") return password_hash($pwd, PASSWORD_BCRYPT);
        else return password_hash($pwd, PASSWORD_BCRYPT, ['salt' => $salt]);
    }

    public static function checkPassword($hashedPassword, $password)
    {
        if (in_array(Config::get('pwdMethod'), ['bcypt', 'argon2i'])) return password_verify($password, $hashedPassword);
        else return ($hashedPassword == self::passwordHash($password));
    }
}
