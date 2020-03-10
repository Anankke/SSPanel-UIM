<?php

namespace App\Utils;

use App\Services\Config;


class Hash
{
    public static function passwordHash($pass)
    {
        $method = $_ENV['pwdMethod'];
        switch ($method) {
            case 'md5':
                return self::md5WithSalt($pass);
                break;
            case 'sha256':
                return self::sha256WithSalt($pass);
                break;
            case 'bcrypt':
                return password_hash($pass, PASSWORD_BCRYPT);
                break;
            case 'argon2i':
                return password_hash($pass, PASSWORD_ARGON2I);
                break;
            case 'argon2id':
                return password_hash($pass, PASSWORD_ARGON2ID);
                break;

            default:
                return self::md5WithSalt($pass);
        }
    }

    public static function cookieHash($passHash, $expire_in)
    {
        return substr(hash('sha256', $passHash . $_ENV['key'] . $expire_in), 5, 45);
    }

    public static function md5WithSalt($pwd)
    {
        $salt = $_ENV['salt'];
        return md5($pwd . $salt);
    }

    public static function sha256WithSalt($pwd)
    {
        $salt = $_ENV['salt'];
        return hash('sha256', $pwd . $salt);
    }

    public static function checkPassword($hashedPassword, $password)
    {
        if (in_array($_ENV['pwdMethod'], ['bcrypt', 'argon2i', 'argon2id'])) {
            return password_verify($password, $hashedPassword);
        }
        return ($hashedPassword == self::passwordHash($password));
    }
}
