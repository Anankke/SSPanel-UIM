<?php

declare(strict_types=1);

namespace App\Services;

use Exception;
use Illuminate\Database\Capsule\Manager;

final class DB extends Manager
{
    public static function init(): void
    {
        $db = new DB();

        try {
            $db->addConnection(self::getConfig());
            $db->getConnection()->getPdo();
        } catch (Exception $e) {
            die('Could not connect to main database: ' . $e->getMessage());
        }

        $db->setAsGlobal();
        $db->bootEloquent();

        View::$connection = $db->getDatabaseManager();
        $db->getDatabaseManager()->connection('default')->enableQueryLog();
    }

    public static function getConfig(): array
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
}
