<?php

namespace App\Services;

use Illuminate\Database\Capsule\Manager as Capsule;

class Boot
{
    public static function setDebug()
    {
        // debug
        if (Config::get('debug') == "true") {
            define("DEBUG", true);
        }
        View::$beginTime = microtime(true);
    }

    public static function setVersion($version)
    {
        $System_Config['version'] = $version;
    }

    public static function setTimezone()
    {
        // config time zone
        date_default_timezone_set(Config::get('timeZone'));
    }

    public static function bootDb()
    {
        // Init Eloquent ORM Connection
        $capsule = new Capsule;
        $capsule->addConnection(Config::getDbConfig(), 'default');
        if (Config::get('enable_radius')=='true') {
            $capsule->addConnection(Config::getRadiusDbConfig(), 'radius');
        }
        $capsule->bootEloquent();

        View::$connection = $capsule->getDatabaseManager();
        $capsule->getDatabaseManager()->connection('default')->enableQueryLog();
    }
}
