<?php

declare(strict_types=1);

namespace App\Services;

use PHPUnit\Framework\TestCase;

class CacheTest extends TestCase
{
    /**
     * @covers App\Services\Cache::getRedisConfig
     */
    public function testGetRedisConfig()
    {
        // Scenario 1: All parameters are set
        $_ENV['redis_host'] = 'localhost';
        $_ENV['redis_port'] = '6379';
        $_ENV['redis_connect_timeout'] = '1.0';
        $_ENV['redis_read_timeout'] = '1.0';
        $_ENV['redis_username'] = 'username';
        $_ENV['redis_password'] = 'password';
        $_ENV['redis_ssl'] = true;
        $_ENV['redis_ssl_context'] = [];

        $expected1 = [
            'host' => 'localhost',
            'port' => '6379',
            'connectTimeout' => '1.0',
            'readTimeout' => '1.0',
            'auth' => [
                'user' => 'username',
                'pass' => 'password',
            ],
            'ssl' => [],
        ];

        $result1 = Cache::getRedisConfig();
        $this->assertEquals($expected1, $result1);

        // Scenario 2: Optional parameters are not set
        $_ENV['redis_username'] = '';
        $_ENV['redis_password'] = '';
        $_ENV['redis_ssl'] = false;

        $expected2 = [
            'host' => 'localhost',
            'port' => '6379',
            'connectTimeout' => '1.0',
            'readTimeout' => '1.0',
        ];

        $result2 = Cache::getRedisConfig();
        $this->assertEquals($expected2, $result2);
    }
}
