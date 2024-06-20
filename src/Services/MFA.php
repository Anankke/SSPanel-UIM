<?php

declare(strict_types=1);

namespace App\Services;

use Exception;
use Vectorface\GoogleAuthenticator;

final class MFA
{
    /**
     * @throws Exception
     */
    public static function generateGaToken(): string
    {
        $ga = new GoogleAuthenticator();
        return $ga->createSecret();
    }

    public static function verifyGa($user, string $code): bool
    {
        $ga = new GoogleAuthenticator();
        return $ga->verifyCode($user->ga_token, $code);
    }

    public static function getGaUrl($user): string
    {
        return 'otpauth://totp/' .
            rawurlencode($_ENV['appName'] . ' (' . $user->email . ')') . '?secret=' . $user->ga_token;
    }
}
