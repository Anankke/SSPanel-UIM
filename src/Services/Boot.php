<?php

namespace App\Services;

use Illuminate\Database\Capsule\Manager as Capsule;

class Boot
{
    public static function setTime()
    {
        date_default_timezone_set($_ENV['timeZone']);
        View::$beginTime = microtime(true);
    }

    public static function bootDb()
    {
        // Init Eloquent ORM Connection
        $capsule = new Capsule();
        try {
            $capsule->addConnection(Config::getDbConfig());
            $capsule->getConnection()->getPdo();
        } catch (\Exception $e) {
            die('Could not connect to main database: ' . $e->getMessage());
        }

        $capsule->setAsGlobal();

        if ($_ENV['enable_radius'] === true) {
            try {
                $capsule->addConnection(Config::getRadiusDbConfig(), 'radius');
                $capsule->getConnection('radius')->getPdo();
            } catch (\Exception $e) {
                die('Could not connect to radius database: ' . $e->getMessage());
            }
        }
        
        $capsule->bootEloquent();

        View::$connection = $capsule->getDatabaseManager();
        $capsule->getDatabaseManager()->connection('default')->enableQueryLog();
    }
}
