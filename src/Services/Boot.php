<?php

namespace App\Services;

use Illuminate\Database\Capsule\Manager as Capsule;

class Boot
{
    public static function setTime()
    {
        date_default_timezone_set(Config::get('timeZone'));
        View::$beginTime = microtime(true);
    }

    public static function bootDb()
    {
        // Init Eloquent ORM Connection
        $capsule = new Capsule();
        $capsule->addConnection(Config::getDbConfig());
        if (Config::get('enable_radius') == true) {
            $capsule->addConnection(Config::getRadiusDbConfig(), 'radius');
        }
        $capsule->bootEloquent();

        View::$connection = $capsule->getDatabaseManager();
        $capsule->getDatabaseManager()->connection('default')->enableQueryLog();
    }
}
