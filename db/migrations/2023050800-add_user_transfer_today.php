<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        DB::getPdo()->exec('ALTER TABLE user DROP COLUMN IF EXISTS `last_day_t`;');
        DB::getPdo()->exec("ALTER TABLE user ADD COLUMN IF NOT EXISTS `transfer_today` bigint(20) unsigned DEFAULT 0 COMMENT '账户今日所用流量';");

        return 2023050800;
    }

    public function down(): int
    {
        DB::getPdo()->exec("ALTER TABLE user ADD COLUMN IF NOT EXISTS `last_day_t` bigint(20) DEFAULT 0 COMMENT '今天之前已使用的流量';");
        DB::getPdo()->exec('ALTER TABLE user DROP COLUMN IF EXISTS `transfer_today`;');

        return 2023032600;
    }
};
