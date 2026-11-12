<?php

declare(strict_types=1);

// Settings the Docker image derives from the container environment. The site's
// /data/sspanel/.config.php requires this file, so defaults follow image upgrades.

require_once __DIR__ . '/env.php';
require __DIR__ . '/../config/.config.example.php';

$_ENV['key'] = sspanel_env('SSPANEL_KEY', $_ENV['key']);
$_ENV['muKey'] = sspanel_env('SSPANEL_MU_KEY', $_ENV['muKey']);

$_ENV['baseUrl'] = sspanel_env('SSPANEL_BASE_URL', $_ENV['baseUrl']);
$_ENV['webAPIUrl'] = $_ENV['baseUrl'];
$_ENV['subUrl'] = $_ENV['baseUrl'];

$_ENV['db_host'] = sspanel_env('SSPANEL_DB_HOST', 'mariadb');
$_ENV['db_socket'] = '';
$_ENV['db_port'] = sspanel_env('SSPANEL_DB_PORT', '3306');
$_ENV['db_database'] = sspanel_env('SSPANEL_DB_DATABASE', 'sspanel');
$_ENV['db_username'] = sspanel_env('SSPANEL_DB_USERNAME', 'sspanel');
$_ENV['db_password'] = sspanel_env('SSPANEL_DB_PASSWORD', '');

$_ENV['redis_host'] = sspanel_env('SSPANEL_REDIS_HOST', 'redis');
$_ENV['redis_port'] = (int) sspanel_env('SSPANEL_REDIS_PORT', '6379');
$_ENV['redis_password'] = sspanel_env('SSPANEL_REDIS_PASSWORD', '');
