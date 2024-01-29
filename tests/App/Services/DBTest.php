<?php

declare(strict_types=1);

namespace App\Services;

use PHPUnit\Framework\TestCase;

class DBTest extends TestCase
{
    /**
     * @covers App\Services\DB::getConfig
     */
    public function testGetConfig()
    {
        // Scenario 1: All parameters are set
        $_ENV['db_driver'] = 'mysql';
        $_ENV['db_host'] = 'localhost';
        $_ENV['db_socket'] = '/tmp/mysql.sock';
        $_ENV['db_database'] = 'test_db';
        $_ENV['db_username'] = 'username';
        $_ENV['db_password'] = 'password';
        $_ENV['db_charset'] = 'utf8';
        $_ENV['db_collation'] = 'utf8_unicode_ci';
        $_ENV['db_prefix'] = '';
        $_ENV['db_port'] = '3306';

        $expected1 = [
            'driver' => 'mysql',
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

        $result1 = DB::getConfig();
        $this->assertEquals($expected1, $result1);

        // Scenario 2: Missing parameters
        $_ENV['db_socket'] = '';
        $_ENV['db_port'] = '';

        $expected2 = [
            'driver' => 'mysql',
            'host' => 'localhost',
            'unix_socket' => '',
            'database' => 'test_db',
            'username' => 'username',
            'password' => 'password',
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'port' => '',
        ];

        $result2 = DB::getConfig();
        $this->assertEquals($expected2, $result2);
    }
}
