<?php

namespace App\Services;

class Auth
{
    protected $driver;

    public function __construct()
    {
    }

    private static function getDriver()
    {
        return Factory::createAuth();
    }

    public static function login($uid, $time)
    {
        self::getDriver()->login($uid, $time);
    }

    public static function getUser()
    {
        return self::getDriver()->getUser();
    }

    public static function logout()
    {
        self::getDriver()->logout();
    }
}
