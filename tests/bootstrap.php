<?php

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));
define('TESTING', true);

require_once BASE_PATH . '/vendor/autoload.php';
if (file_exists(BASE_PATH . '/config/.config.test.php')) {
    require_once BASE_PATH . '/config/.config.test.php';
} elseif (file_exists(BASE_PATH . '/config/.config.php')) {
    // Fallback to main config but use test database
    require_once BASE_PATH . '/config/.config.php';
    $_ENV['db_database'] = 'sspanel_test';
    $_ENV['redis_db'] = 15;
} else {
    require_once BASE_PATH . '/config/.config.example.php';
    // Set test database for example config
    $_ENV['db_database'] = 'sspanel_test';
    $_ENV['redis_db'] = 15;
}

$_ENV['APP_ENV'] = 'testing';
error_reporting(E_ALL);
ini_set('display_errors', '1');
$directories = [
    BASE_PATH . '/storage/logs',
    BASE_PATH . '/storage/framework/smarty/compile',
    BASE_PATH . '/storage/framework/smarty/cache',
    BASE_PATH . '/.phpunit.cache',
    BASE_PATH . '/coverage',
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

require_once __DIR__ . '/helpers.php';

