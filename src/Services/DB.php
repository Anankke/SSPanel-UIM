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
            $db->addConnection(Config::getDbConfig());
            $db->getConnection()->getPdo();
        } catch (Exception $e) {
            die('Could not connect to main database: ' . $e->getMessage());
        }

        $db->setAsGlobal();
        $db->bootEloquent();

        View::$connection = $db->getDatabaseManager();
        $db->getDatabaseManager()->connection('default')->enableQueryLog();
    }
}
