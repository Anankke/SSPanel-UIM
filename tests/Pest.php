<?php

declare(strict_types=1);

/**
 * Pest PHP configuration file
 *
 * This file is loaded before each test run
 */

use Tests\SlimTestCase;
use Tests\TestCase;

// Base test case for all tests
uses(TestCase::class)->in('Unit');
uses(SlimTestCase::class)->in('Feature');

// Helper functions
function resetEnv(): void
{
    // Store original ENV values
    static $originalEnv = null;

    if ($originalEnv === null) {
        $originalEnv = $_ENV;
    }

    // Reset to original
    $_ENV = $originalEnv;
}

// Expectations
expect()->extend('toBeValidEmail', function () {
    return $this->toMatch('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/');
});

expect()->extend('toBeValidUuid', function () {
    return $this->toMatch('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i');
});

expect()->extend('toBeValidPort', function () {
    return $this->toBeInt()
        ->toBeGreaterThanOrEqual(1)
        ->toBeLessThanOrEqual(65535);
});

// Architecture tests
arch('controllers extend base controller')
    ->expect('App\Controllers')
    ->toExtend('App\Controllers\BaseController')
    ->ignoring('App\Controllers\BaseController');

arch('models extend eloquent')
    ->expect('App\Models')
    ->toExtend('App\Models\Model');

arch('no debug statements')
    ->expect(['App', 'Tests'])
    ->not->toUse(['dd', 'dump', 'var_dump', 'print_r']);

arch('strict types declaration')
    ->expect('App')
    ->toUseStrictTypes();

// Test datasets
dataset('invalid_emails', [
    'no-at-sign',
    '@no-local-part.com',
    'no-domain@',
    'spaces in@email.com',
    'double@@at.com',
]);

dataset('node_types', [
    ['shadowsocks', 14],
    ['shadowsocks_relay', 11],
    ['vmess', 1],
    ['trojan', 2],
]);
