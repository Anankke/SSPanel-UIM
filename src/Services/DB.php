<?php

declare(strict_types=1);

namespace App\Services;

use Exception;
use Illuminate\Database\Capsule\Manager;
use const PHP_EOL;

final class DB extends Manager
{
    public static function init(): void
    {
        $db = new DB();

        try {
            $db->addConnection(self::getConfig());
            $db->getConnection()->getPdo();
        } catch (Exception $e) {
            if ($_ENV['debug']) {
                die('Databse Error' . PHP_EOL . 'Reason: ' . $e->getMessage());
            }

            die('Databse Error');
        }

        $db->setAsGlobal();
        $db->bootEloquent();

        View::$connection = $db->getDatabaseManager();
        $db->getDatabaseManager()->connection('default')->enableQueryLog();
    }

    public static function getConfig(): array
    {
        if ($_ENV['enable_db_rw_split']) {
            return [
                'driver' => 'mariadb',
                'read' => [
                    'host' => $_ENV['read_db_hosts'],
                ],
                'write' => [
                    'host' => $_ENV['write_db_host'],
                ],
                'sticky' => true,
                'database' => $_ENV['db_database'],
                'username' => $_ENV['db_username'],
                'password' => $_ENV['db_password'],
                'charset' => $_ENV['db_charset'],
                'collation' => $_ENV['db_collation'],
                'prefix' => $_ENV['db_prefix'],
                'port' => $_ENV['db_port'],
            ];
        }

        return [
            'driver' => 'mariadb',
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
