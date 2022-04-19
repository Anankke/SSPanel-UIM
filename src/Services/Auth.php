<?php

declare(strict_types=1);

namespace App\Services;

final class Auth
{
    private $user;

    public static function login($uid, $time): void
    {
        self::getDriver()->login($uid, $time);
    }

    /**
     * Get current user(cached)
     */
    public static function getUser(): \App\Models\User
    {
        global $user;
        if ($user === null) {
            $user = self::getDriver()->getUser();
        }
        return $user;
    }

    public static function logout(): void
    {
        self::getDriver()->logout();
    }

    private static function getDriver()
    {
        return Factory::createAuth();
    }
}
