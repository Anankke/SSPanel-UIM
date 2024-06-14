<?php

declare(strict_types=1);

namespace App\Utils;

final class Cookie
{
    public static function set(array $arg, int $time): void
    {
        $developmentMode = self::isDevelopmentMode();
        foreach ($arg as $key => $value) {
            setcookie($key, $value, $time, path: '/', secure: ! $developmentMode, httponly: true);
        }
    }

    public static function setWithDomain(array $arg, int $time, string $domain): void
    {
        $developmentMode = self::isDevelopmentMode();
        foreach ($arg as $key => $value) {
            setcookie($key, $value, $time, path: '/', domain: $domain, secure: ! $developmentMode, httponly: true);
        }
    }

    public static function get(string $key): string
    {
        return $_COOKIE[$key] ?? '';
    }

    private static function isDevelopmentMode(): bool
    {
        return ($_ENV['debug'] || str_contains($_ENV['baseUrl'], '.test')) && str_contains($_ENV['baseUrl'], 'http://');
    }
}
