<?php

declare(strict_types=1);

namespace App\Services;

use RateLimit\Exception\LimitExceeded;
use RateLimit\Rate;
use RateLimit\RedisRateLimiter;
use RedisException;

final class RateLimit
{
    /**
     * @throws RedisException
     */
    public static function checkIPLimit(string $request_ip): bool
    {
        $ip_limiter = new RedisRateLimiter(
            Rate::perMinute($_ENV['rate_limit_ip']),
            Cache::initRedis()
        );

        try {
            $ip_limiter->limit($request_ip);
        } catch (LimitExceeded $e) {
            return false;
        }

        return true;
    }

    /**
     * @throws RedisException
     */
    public static function checkSubLimit(string $sub_token): bool
    {
        $sub_limiter = new RedisRateLimiter(
            Rate::perMinute($_ENV['rate_limit_sub']),
            Cache::initRedis()
        );

        try {
            $sub_limiter->limit($sub_token);
        } catch (LimitExceeded $e) {
            return false;
        }

        return true;
    }

    /**
     * @throws RedisException
     */
    public static function checkWebAPILimit(string $web_api_token): bool
    {
        $webapi_limiter = new RedisRateLimiter(
            Rate::perMinute($_ENV['rate_limit_webapi']),
            Cache::initRedis()
        );

        try {
            $webapi_limiter->limit($web_api_token);
        } catch (LimitExceeded $e) {
            return false;
        }

        return true;
    }

    /**
     * @throws RedisException
     */
    public static function checkUserAPILimit(string $user_api_token): bool
    {
        $user_api_limiter = new RedisRateLimiter(
            Rate::perMinute($_ENV['rate_limit_user_api']),
            Cache::initRedis()
        );

        try {
            $user_api_limiter->limit($user_api_token);
        } catch (LimitExceeded $e) {
            return false;
        }

        return true;
    }

    /**
     * @throws RedisException
     */
    public static function checkAdminAPILimit(string $admin_api_token): bool
    {
        $admin_api_limiter = new RedisRateLimiter(
            Rate::perMinute($_ENV['rate_limit_admin_api']),
            Cache::initRedis()
        );

        try {
            $admin_api_limiter->limit($admin_api_token);
        } catch (LimitExceeded $e) {
            return false;
        }

        return true;
    }
}
