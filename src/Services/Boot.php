<?php

declare(strict_types=1);

namespace App\Services;

use Exception;
use function date_default_timezone_set;
use function microtime;
use function Sentry\init;
use const PHP_EOL;

final class Boot
{
    public static function setTime(): void
    {
        date_default_timezone_set($_ENV['timeZone']);
        View::$beginTime = microtime(true);
    }

    public static function bootDb(): void
    {
        try {
            DB::init();
        } catch (Exception $e) {
            echo 'Database Error';

            if ($_ENV['debug']) {
                echo PHP_EOL . 'Reason: ' . $e->getMessage();
            }

            exit(1);
        }
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
