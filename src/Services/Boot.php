<?php

declare(strict_types=1);

namespace App\Services;

use Sentry;

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
        if (! isset($_ENV['sentry_dsn'])) {
            Sentry\init([
                'dsn' => $_ENV['sentry_dsn'],
                'prefixes' => [
                    realpath(__DIR__ . '/../../'),
                ],
                'in_app_exclude' => [
                    realpath(__DIR__ . '/../../vendor'),
                ],
            ]);
        }
    }
}
