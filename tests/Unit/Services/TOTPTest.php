<?php

use App\Services\MFA\TOTP;
use App\Models\User;

beforeEach(function () {
    $this->originalEnv = $_ENV;
});

afterEach(function () {
    $_ENV = $this->originalEnv;
});

describe('TOTP::generateGaToken', function () {
    it('generates valid token string', function () {
        $token = TOTP::generateGaToken();

        expect($token)
            ->toBeString()
            ->and(strlen($token))->toBeGreaterThan(0)
            ->and(strlen($token))->toBe(32); // TOTP now uses 32 chars
    });
});

describe('TOTP::getGaUrl', function () {
    it('generates valid Google Authenticator URL', function () {
        $_ENV['appName'] = 'Test';
        $user = new User();
        $user->email = 'test@example.com';
        $token = 'SECRET';
        
        $url = TOTP::getGaUrl($user, $token);

        expect($url)
            ->toContain('otpauth://totp/')
            ->toContain('Test:' . rawurlencode($user->email))
            ->toContain('issuer=' . rawurlencode('Test'))
            ->toContain('secret=' . $token);
    });
});

// Note: verifyGa is now part of the RegisterVerify flow
// which requires Redis and database setup - needs integration test
