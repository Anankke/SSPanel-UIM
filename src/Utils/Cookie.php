<?php

declare(strict_types=1);

namespace App\Utils;

final class Cookie
{
    public static function set(array $arg, int $time): void
    {
        foreach ($arg as $key => $value) {
            setcookie($key, $value, [
                'expires' => $time,
                'path' => '/',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
        }
    }

    public static function setWithDomain(array $arg, int $time, string $domain): void
    {
        foreach ($arg as $key => $value) {
            setcookie($key, $value, [
                'expires' => $time,
                'path' => '/',
                'domain' => $domain,
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
        }
    }

    public static function get(string $key): string
    {
        return $_COOKIE[$key] ?? '';
    }
}
