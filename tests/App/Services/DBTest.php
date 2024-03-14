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
        // Scenario 1: enable_db_rw_split is true
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

        $expected1 = [
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

        $result1 = DB::getConfig();
        $this->assertEquals($expected1, $result1);

        // Scenario 2: enable_db_rw_split is false
        $_ENV['enable_db_rw_split'] = false;
        $_ENV['db_host'] = 'localhost';
        $_ENV['db_socket'] = '/tmp/mysql.sock';

        $expected2 = [
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

        $result2 = DB::getConfig();
        $this->assertEquals($expected2, $result2);
    }
}
