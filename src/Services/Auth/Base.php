<?php

declare(strict_types=1);

namespace App\Services\Auth;

abstract class Base
{
    abstract public function login($uid, $time): void;

    abstract public function logout(): void;

    abstract public function getUser(): void;
}
