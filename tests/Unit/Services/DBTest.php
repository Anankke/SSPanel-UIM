<?php

declare(strict_types=1);

use App\Services\DB;

beforeEach(function () {
    $this->originalEnv = $_ENV;
});

afterEach(function () {
    $_ENV = $this->originalEnv;
});

it('gets config with read/write split enabled', function () {
    $_ENV['enable_db_rw_split'] = true;
    $_ENV['read_db_hosts'] = 'localhost';
    $_ENV['write_db_host'] = 'localhost';
    $_ENV['db_database'] = 'test_db';
    $_ENV['db_username'] = 'username';
    $_ENV['db_password'] = 'password';
    $_ENV['db_charset'] = 'utf8';
    $_ENV['db_collation'] = 'utf8_unicode_ci';
    $_ENV['db_prefix'] = '';
    $_ENV['db_port'] = '3306';

    $expected = [
        'driver' => 'mariadb',
        'read' => [
            'host' => 'localhost',
        ],
        'write' => [
            'host' => 'localhost',
        ],
        'sticky' => true,
        'database' => 'test_db',
        'username' => 'username',
        'password' => 'password',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix' => '',
        'port' => '3306',
    ];

    $result = DB::getConfig();
    expect($result)->toBe($expected);
});

it('gets config with read/write split disabled', function () {
    $_ENV['enable_db_rw_split'] = false;
    $_ENV['db_host'] = 'localhost';
    $_ENV['db_socket'] = '/tmp/mysql.sock';
    $_ENV['db_database'] = 'test_db';
    $_ENV['db_username'] = 'username';
    $_ENV['db_password'] = 'password';
    $_ENV['db_charset'] = 'utf8';
    $_ENV['db_collation'] = 'utf8_unicode_ci';
    $_ENV['db_prefix'] = '';
    $_ENV['db_port'] = '3306';

    $expected = [
        'driver' => 'mariadb',
        'host' => 'localhost',
        'unix_socket' => '/tmp/mysql.sock',
        'database' => 'test_db',
        'username' => 'username',
        'password' => 'password',
        'charset' => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix' => '',
        'port' => '3306',
    ];

    $result = DB::getConfig();
    expect($result)->toBe($expected);
});