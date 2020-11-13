#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\Models\Link;
use App\Services\Boot;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

require __DIR__ . '/../app/predefine.php';
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/.config.php';

Boot::setTime();
Boot::bootDb();

echo PHP_EOL;

try {
    Link::where('type', '<>', 11)->delete();
    echo '所有类型非 11 的订阅链接已全部清理.' . PHP_EOL;
} catch (\Throwable $th) {
    $th->getMessage();
}

try {
    Capsule::schema()->table(
        'link',
        function (Blueprint $table) {
            $table->dropColumn(
                [
                    'type',
                    'address',
                    'port',
                    'ios',
                    'isp',
                    'geo',
                    'method',
                ]
            );
        }
    );
    echo 'link 表无用的字段已全部清理.' . PHP_EOL;
} catch (\Throwable $th) {
    $th->getMessage();
}

exit(0);
