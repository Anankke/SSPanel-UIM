<?php

declare(strict_types=1);

namespace App\Utils;

final class Cookie
{
    /*

    setcookie(
        string $name,
        string $value = "",
        int $expires = 0,
        string $path = "",
        string $domain = "",
        bool $secure = false,
        bool $httponly = false
    ): bool

    PHP 7.3.0 起有效的签名

    setcookie(string $name, string $value = "", array $options = []): bool

    https://www.php.net/manual/zh/function.setcookie.php

    */

    public static function set($arg, $time): void
    {
        foreach ($arg as $key => $value) {
            setcookie((string) $key, (string) $value, (int) $time, '/');
        }
    }

    public static function setwithdomain($arg, $time, $domain): void
    {
        foreach ($arg as $key => $value) {
            setcookie((string) $key, (string) $value, (int) $time, '/', (string) $domain);
        }
    }

    public static function get($key)
    {
        return $_COOKIE[$key] ?? '';
    }
}
