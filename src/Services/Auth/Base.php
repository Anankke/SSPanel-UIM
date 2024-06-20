<?php

declare(strict_types=1);

namespace App\Services\Auth;

use App\Models\User;

abstract class Base
{
    abstract public function login($uid, $time): void;

    abstract public function logout(): void;

    abstract public function getUser(): User;
}
