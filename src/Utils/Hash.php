<?php

declare(strict_types=1);

namespace App\Utils;

use function hash;
use function in_array;
use function password_hash;

final class Hash
{
    public static function cookieHash($passHash, $expire_in): string
    {
        return substr(hash('sha256', $passHash . $_ENV['key'] . $expire_in), 5, 45);
    }

    public static function ipHash($ip, $uid, $expire_in): string
    {
        return substr(hash('sha256', $ip . $_ENV['key'] . $uid . $expire_in), 5, 45);
    }

    public static function checkPassword($hashedPassword, $password): bool
    {
        if (in_array($_ENV['pwdMethod'], ['bcrypt', 'argon2i', 'argon2id'])) {
            return password_verify($password, $hashedPassword);
        }
        return $hashedPassword === self::passwordHash($password);
    }

    public static function passwordHash($pass): string
    {
        $method = $_ENV['pwdMethod'];
        return match ($method) {
            'md5' => self::md5WithSalt($pass),
            'sha256' => self::sha256WithSalt($pass),
            'argon2i' => password_hash($pass, PASSWORD_ARGON2I),
            'argon2id' => password_hash($pass, PASSWORD_ARGON2ID),
            default => password_hash($pass, PASSWORD_BCRYPT),
        };
    }

    public static function md5WithSalt($pwd): string
    {
        $salt = $_ENV['salt'];
        return md5($pwd . $salt);
    }

    public static function sha256WithSalt($pwd): string
    {
        $salt = $_ENV['salt'];
        return hash('sha256', $pwd . $salt);
    }
}
