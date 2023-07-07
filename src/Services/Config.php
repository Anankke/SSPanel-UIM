<?php

declare(strict_types=1);

namespace App\Services;

// Config is singleton instance store all config
final class Config
{
    private function __construct()
    {
    }

    public static function getPublicConfig(): array
    {
        return [
            'appName' => $_ENV['appName'],
            'baseUrl' => $_ENV['baseUrl'],

            'enable_checkin' => $_ENV['enable_checkin'],
            'checkinMin' => $_ENV['checkinMin'],
            'checkinMax' => $_ENV['checkinMax'],

            'jump_delay' => $_ENV['jump_delay'],
            'enable_analytics_code' => $_ENV['enable_analytics_code'],

            'enable_kill' => $_ENV['enable_kill'],
            'enable_change_email' => $_ENV['enable_change_email'],

            'enable_telegram' => $_ENV['enable_telegram'],
            'telegram_bot' => $_ENV['telegram_bot'],

            'subscribeLog' => $_ENV['subscribeLog'],
            'subscribeLog_keep_days' => $_ENV['subscribeLog_keep_days'],

            'enable_r2_client_download' => $_ENV['enable_r2_client_download'],
        ];
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

    public static function getDbConfig(): array
    {
        return [
            'driver' => $_ENV['db_driver'],
            'host' => $_ENV['db_host'],
            'unix_socket' => $_ENV['db_socket'],
            'database' => $_ENV['db_database'],
            'username' => $_ENV['db_username'],
            'password' => $_ENV['db_password'],
            'charset' => $_ENV['db_charset'],
            'collation' => $_ENV['db_collation'],
            'prefix' => $_ENV['db_prefix'],
            'port' => $_ENV['db_port'],
        ];
    }

    public static function getSupportParam($type): array
    {
        return match ($type) {
            'ss_aead_method' => [
                'aes-128-gcm',
                'aes-192-gcm',
                'aes-256-gcm',
                'chacha20-ietf-poly1305',
                'xchacha20-ietf-poly1305',
            ],
            'ss_obfs' => [
                'simple_obfs_http',
                'simple_obfs_http_compatible',
                'simple_obfs_tls',
                'simple_obfs_tls_compatible',
            ],
            'ss_2022' => [
                '2022-blake3-aes-128-gcm',
                '2022-blake3-aes-256-gcm',
                '2022-blake3-chacha20-poly1305',
            ],
            default => [
                'aes-128-gcm',
                'aes-192-gcm',
                'aes-256-gcm',
                'chacha20-ietf-poly1305',
                'xchacha20-ietf-poly1305',
                'none',
                'plain',
                '2022-blake3-aes-128-gcm',
                '2022-blake3-aes-256-gcm',
                '2022-blake3-chacha20-poly1305',
            ],
        };
    }
}
