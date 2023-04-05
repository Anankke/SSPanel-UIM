<?php

declare(strict_types=1);

namespace App\Services;

use function Sentry\init;

final class Boot
{
    public static function setTime(): void
    {
        date_default_timezone_set($_ENV['timeZone']);
        View::$beginTime = microtime(true);
    }

    public static function bootDb(): void
    {
        DB::init();
    }

    public static function bootSentry(): void
    {
        if ($_ENV['sentry_dsn'] !== '') {
            init([
                'dsn' => $_ENV['sentry_dsn'],
            ]);
        }
    }
}
