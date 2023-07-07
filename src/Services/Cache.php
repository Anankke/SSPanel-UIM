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
    public static function initRedis(): Redis
    {
        $redis = new Redis();
        $redis->connect($_ENV['redis_host'], $_ENV['redis_port']);

        if ($_ENV['redis_password'] !== '') {
            $redis->auth($_ENV['redis_password']);
        }

        return $redis;
    }
}
