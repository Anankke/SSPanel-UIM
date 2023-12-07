<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        DB::getPdo()->exec("
            ALTER TABLE node ADD COLUMN IF NOT EXISTS `dynamic_rate_type` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '动态流量倍率计算方式';
        ");

        return 2023120700;
    }

    public function down(): int
    {
        DB::getPdo()->exec('
            ALTER TABLE node DROP COLUMN IF EXISTS `dynamic_rate_type`;
        ');

        return 2023111801;
    }
};
