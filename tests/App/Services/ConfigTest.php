<?php

declare(strict_types=1);

namespace App\Services;

use PHPUnit\Framework\TestCase;
use App\Services\Config;

class ConfigTest extends TestCase
{
    /**
     * @covers App\Services\Config::getPublicConfig
     */
    public function testGetPublicConfig(): void
    {
        $_ENV = [
            'appName' => 'My App',
            'baseUrl' => 'https://example.com',
            'enable_checkin' => true,
            'checkinMin' => 10,
            'checkinMax' => 20,
            'jump_delay' => 5,
            'enable_analytics_code' => false,
            'enable_kill' => true,
            'enable_change_email' => false,
            'enable_telegram' => true,
            'telegram_bot' => 'my_bot',
            'subscribeLog' => true,
            'subscribeLog_keep_days' => 30,
            'enable_r2_client_download' => true,
        ];

        $mockEnv = [
            'appName' => 'My App',
            'baseUrl' => 'https://example.com',
            'enable_checkin' => true,
            'checkinMin' => 10,
            'checkinMax' => 20,
            'jump_delay' => 5,
            'enable_analytics_code' => false,
            'enable_kill' => true,
            'enable_change_email' => false,
            'enable_telegram' => true,
            'telegram_bot' => 'my_bot',
            'subscribeLog' => true,
            'subscribeLog_keep_days' => 30,
            'enable_r2_client_download' => true,
        ];

        $config = Config::getPublicConfig();

        $this->assertSame($mockEnv['appName'], $config['appName']);
        $this->assertSame($mockEnv['baseUrl'], $config['baseUrl']);
        $this->assertSame($mockEnv['enable_checkin'], $config['enable_checkin']);
        $this->assertSame($mockEnv['checkinMin'], $config['checkinMin']);
        $this->assertSame($mockEnv['checkinMax'], $config['checkinMax']);
        $this->assertSame($mockEnv['jump_delay'], $config['jump_delay']);
        $this->assertSame($mockEnv['enable_analytics_code'], $config['enable_analytics_code']);
        $this->assertSame($mockEnv['enable_kill'], $config['enable_kill']);
        $this->assertSame($mockEnv['enable_change_email'], $config['enable_change_email']);
        $this->assertSame($mockEnv['enable_telegram'], $config['enable_telegram']);
        $this->assertSame($mockEnv['telegram_bot'], $config['telegram_bot']);
        $this->assertSame($mockEnv['subscribeLog'], $config['subscribeLog']);
        $this->assertSame($mockEnv['subscribeLog_keep_days'], $config['subscribeLog_keep_days']);
        $this->assertSame($mockEnv['enable_r2_client_download'], $config['enable_r2_client_download']);
    }

    /**
     * @covers App\Services\Config::getRedisConfig
     */
    public function testGetRedisConfig(): void
    {
        $_ENV = [
            'redis_host' => 'localhost',
            'redis_port' => 6379,
            'redis_timeout' => 10,
            'redis_username' => 'myuser',
            'redis_password' => 'mypassword',
            'redis_ssl' => false,
        ];

        $mockEnv = [
            'redis_host' => 'localhost',
            'redis_port' => 6379,
            'redis_timeout' => 10,
            'redis_username' => 'myuser',
            'redis_password' => 'mypassword',
            'redis_ssl' => false,
        ];

        $config = Config::getRedisConfig();

        $this->assertSame($mockEnv['redis_host'], $config['host']);
        $this->assertSame($mockEnv['redis_port'], $config['port']);
        $this->assertSame($mockEnv['redis_timeout'], $config['connectTimeout']);
        $this->assertSame([$mockEnv['redis_username'], $mockEnv['redis_password']], $config['auth']);
        $this->assertSame(['verify_peer' => $mockEnv['redis_ssl']], $config['ssl']);
    }

    /**
     * @covers App\Services\Config::getDbConfig
     */
    public function testGetDbConfig(): void
    {
        $_ENV = [
            'db_driver' => 'mysql',
            'db_host' => 'localhost',
            'db_socket' => '/var/run/mysqld/mysqld.sock',
            'db_database' => 'mydb',
            'db_username' => 'myuser',
            'db_password' => 'mypassword',
            'db_charset' => 'utf8mb4',
            'db_collation' => 'utf8mb4_unicode_ci',
            'db_prefix' => '',
            'db_port' => 3306,
        ];

        $mockEnv = [
            'db_driver' => 'mysql',
            'db_host' => 'localhost',
            'db_socket' => '/var/run/mysqld/mysqld.sock',
            'db_database' => 'mydb',
            'db_username' => 'myuser',
            'db_password' => 'mypassword',
            'db_charset' => 'utf8mb4',
            'db_collation' => 'utf8mb4_unicode_ci',
            'db_prefix' => '',
            'db_port' => 3306,
        ];

        $config = Config::getDbConfig();

        $this->assertSame($mockEnv['db_driver'], $config['driver']);
        $this->assertSame($mockEnv['db_host'], $config['host']);
        $this->assertSame($mockEnv['db_socket'], $config['unix_socket']);
        $this->assertSame($mockEnv['db_database'], $config['database']);
        $this->assertSame($mockEnv['db_username'], $config['username']);
        $this->assertSame($mockEnv['db_password'], $config['password']);
        $this->assertSame($mockEnv['db_charset'], $config['charset']);
        $this->assertSame($mockEnv['db_collation'], $config['collation']);
        $this->assertSame($mockEnv['db_prefix'], $config['prefix']);
        $this->assertSame($mockEnv['db_port'], $config['port']);
    }

    /**
     * @covers App\Services\Config::getSupportParam
     */
    public function testGetSupportParam(): void
    {
        $params = Config::getSupportParam('ss_aead_method');

        $this->assertIsArray($params);
        $this->assertContains('aes-128-gcm', $params);
        $this->assertContains('aes-192-gcm', $params);
        $this->assertContains('aes-256-gcm', $params);
        $this->assertContains('chacha20-ietf-poly1305', $params);
        $this->assertContains('xchacha20-ietf-poly1305', $params);
    }
}

