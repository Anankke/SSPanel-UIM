<?php

declare(strict_types=1);

namespace App\Services;

final class MFA
{
    public static function getGAurl($user)
    {
        return 'otpauth://totp/' . urlencode($_ENV['appName'] . ' (' . $user->email . ')') . '?secret=' . $user->ga_token . '';
    }
}
