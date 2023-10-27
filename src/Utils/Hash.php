<?php

declare(strict_types=1);

namespace App\Utils;

use function hash;
use function in_array;
use function md5;
use function password_hash;
use function password_verify;
use function substr;
use const PASSWORD_ARGON2I;
use const PASSWORD_ARGON2ID;
use const PASSWORD_BCRYPT;

final class Hash
{
    public static function cookieHash($pass, $expire_in): string
    {
        return substr(hash('sha3-256', $pass . $_ENV['key'] . $expire_in), 5, 45);
    }

    public static function ipHash($ip, $uid, $expire_in): string
    {
        return substr(hash('sha3-256', $ip . $_ENV['key'] . $uid . $expire_in), 5, 45);
    }

    public static function deviceHash($ua, $uid, $expire_in): string
    {
        return substr(hash('sha3-256', $ua . $_ENV['key'] . $uid . $expire_in), 5, 45);
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
            'sha3' => self::sha3WithSalt($pass),
            'argon2i' => password_hash($pass, PASSWORD_ARGON2I),
            'argon2id' => password_hash($pass, PASSWORD_ARGON2ID),
            default => password_hash($pass, PASSWORD_BCRYPT),
        };
    }

    public static function md5WithSalt($pwd): string
    {
        return md5($pwd . $_ENV['salt']);
    }

    public static function sha256WithSalt($pwd): string
    {
        return hash('sha256', $pwd . $_ENV['salt']);
    }

    public static function sha3WithSalt($pwd): string
    {
        return hash('sha3-256', $pwd . $_ENV['salt']);
    }
}
