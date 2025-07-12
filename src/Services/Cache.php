<?php

declare(strict_types=1);

namespace App\Services;

use Redis;

final class Cache
{
    public function initRedis(): Redis
    {
        $redis = new Redis();
        $config = self::getRedisConfig();

        $redis->connect(
            $config['host'],
            $config['port'],
            $config['connectTimeout']
        );

        $redis->setOption(Redis::OPT_READ_TIMEOUT, $config['readTimeout']);

        if (isset($config['auth'])) {
            if (isset($config['auth']['user']) && isset($config['auth']['pass'])) {
                $redis->auth([$config['auth']['user'], $config['auth']['pass']]);
            } elseif (isset($config['auth']['pass'])) {
                $redis->auth($config['auth']['pass']);
            }
        }

        return $redis;
    }
    public static function getRedisConfig(): array
    {
        $config = [
            'host' => $_ENV['redis_host'] ?? 'localhost',
            'port' => (int)($_ENV['redis_port'] ?? 6379),
            'connectTimeout' => (float)($_ENV['redis_connect_timeout'] ?? 2.0),
            'readTimeout' => (float)($_ENV['redis_read_timeout'] ?? 2.0),
        ];

        if (!empty($_ENV['redis_username'])) {
            $config['auth']['user'] = $_ENV['redis_username'];
        }

        if (!empty($_ENV['redis_password'])) {
            $config['auth']['pass'] = $_ENV['redis_password'];
        }

        if (filter_var($_ENV['redis_ssl'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
            $config['ssl'] = $_ENV['redis_ssl_context'] ?? [];
        }

        return $config;
    }
}