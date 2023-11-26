<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Config;
use App\Utils\Tools;
use Psr\Http\Client\ClientExceptionInterface;
use RedisException;

final class Password
{
    /**
     * @throws ClientExceptionInterface
     * @throws RedisException
     */
    public static function sendResetEmail($email): void
    {
        $redis = (new Cache())->initRedis();
        $token = Tools::genRandomChar(64);

        $redis->setex('password_reset:' . $token, Config::obtain('email_password_reset_ttl'), $email);

        $subject = $_ENV['appName'] . '-重置密码';
        $resetUrl = $_ENV['baseUrl'] . '/password/token/' . $token;

        Mail::send(
            $email,
            $subject,
            'password_reset.tpl',
            [
                'resetUrl' => $resetUrl,
            ]
        );
    }
}
