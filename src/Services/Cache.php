<?php

declare(strict_types=1);

namespace App\Services;

use Redis;

final class Cache
{
    public function initRedis(): Redis
    {
        return new Redis(self::getRedisConfig());
    }

    public static function getRedisConfig(): array
    {
        $config = [
            'host' => $_ENV['redis_host'],
            'port' => $_ENV['redis_port'],
            'connectTimeout' => $_ENV['redis_connect_timeout'],
            'readTimeout' => $_ENV['redis_read_timeout'],
            'database' => $_ENV['redis_db'] ?? 0,
        ];

        if ($_ENV['redis_username'] !== '') {
            $config['auth']['user'] = $_ENV['redis_username'];
        }

        if ($_ENV['redis_password'] !== '') {
            $config['auth']['pass'] = $_ENV['redis_password'];
        }

        if ($_ENV['redis_ssl']) {
            $config['ssl'] = $_ENV['redis_ssl_context'];
        }

        return $config;
    }
}
