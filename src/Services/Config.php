<?php

declare(strict_types=1);

namespace App\Services;

final class Config
{
    public static function getViewConfig(): array
    {
        return [
            'appName' => $_ENV['appName'],
            'baseUrl' => $_ENV['baseUrl'],

            'enable_checkin' => $_ENV['enable_checkin'],
            'checkinMin' => $_ENV['checkinMin'],
            'checkinMax' => $_ENV['checkinMax'],

            'jump_delay' => $_ENV['jump_delay'],

            'enable_kill' => $_ENV['enable_kill'],
            'enable_change_email' => $_ENV['enable_change_email'],

            'enable_r2_client_download' => $_ENV['enable_r2_client_download'],

            'jsdelivr_url' => $_ENV['jsdelivr_url'],
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

    public static function getSsMethod($type): array
    {
        return match ($type) {
            'ss_obfs' => [
                'simple_obfs_http',
                'simple_obfs_http_compatible',
                'simple_obfs_tls',
                'simple_obfs_tls_compatible',
            ],
            default => [
                'aes-128-gcm',
                'aes-192-gcm',
                'aes-256-gcm',
                'chacha20-ietf-poly1305',
                'xchacha20-ietf-poly1305',
            ],
        };
    }
}
