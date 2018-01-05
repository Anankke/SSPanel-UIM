<?php

namespace App\Services\Auth;

abstract class Base
{
    abstract public function login($uid, $time);
    abstract public function logout();
    abstract public function getUser();
}
