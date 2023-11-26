<?php

declare(strict_types=1);

namespace App\Services;

use Redis;
use RedisException;

final class Cache
{
    /**
     * @throws RedisException
     */
    public function initRedis(): Redis
    {
        $redis = new Redis();
        $redis->connect($_ENV['redis_host'], $_ENV['redis_port']);

        if ($_ENV['redis_password'] !== '') {
            $redis->auth($_ENV['redis_password']);
        }

        return $redis;
    }

    public static function getRedisConfig(): array
    {
        return [
            'host' => $_ENV['redis_host'],
            'port' => $_ENV['redis_port'],
            'connectTimeout' => $_ENV['redis_timeout'],
            'auth' => [$_ENV['redis_username'], $_ENV['redis_password']],
            'ssl' => ['verify_peer' => $_ENV['redis_ssl']],
        ];
    }
}
