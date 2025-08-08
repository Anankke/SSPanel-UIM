<?php

declare(strict_types=1);

use App\Services\Cache;

beforeEach(function () {
    $this->originalEnv = $_ENV;
});

afterEach(function () {
    $_ENV = $this->originalEnv;
});

it('gets redis config with all parameters set', function () {
    $_ENV['redis_host'] = 'localhost';
    $_ENV['redis_port'] = '6379';
    $_ENV['redis_db'] = '0';
    $_ENV['redis_connect_timeout'] = '1.0';
    $_ENV['redis_read_timeout'] = '1.0';
    $_ENV['redis_username'] = 'username';
    $_ENV['redis_password'] = 'password';
    $_ENV['redis_ssl'] = true;
    $_ENV['redis_ssl_context'] = [];

    $expected = [
        'host' => 'localhost',
        'port' => '6379',
        'database' => '0',
        'connectTimeout' => '1.0',
        'readTimeout' => '1.0',
        'auth' => [
            'user' => 'username',
            'pass' => 'password',
        ],
        'ssl' => [],
    ];

    $result = Cache::getRedisConfig();
    expect($result)->toMatchArray($expected);
});

it('gets redis config with optional parameters not set', function () {
    $_ENV['redis_host'] = 'localhost';
    $_ENV['redis_port'] = '6379';
    $_ENV['redis_db'] = '0';
    $_ENV['redis_connect_timeout'] = '1.0';
    $_ENV['redis_read_timeout'] = '1.0';
    $_ENV['redis_username'] = '';
    $_ENV['redis_password'] = '';
    $_ENV['redis_ssl'] = false;

    $expected = [
        'host' => 'localhost',
        'port' => '6379',
        'database' => '0',
        'connectTimeout' => '1.0',
        'readTimeout' => '1.0',
    ];

    $result = Cache::getRedisConfig();
    expect($result)->toMatchArray($expected);
});
