<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;

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
    public static function getUser(): User
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

    private static function getDriver(): Auth\Cookie
    {
        return Factory::createAuth();
    }
}
