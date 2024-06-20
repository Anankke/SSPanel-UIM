<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        DB::getPdo()->exec("
            ALTER TABLE node ADD COLUMN IF NOT EXISTS `is_dynamic_rate` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否启用动态流量倍率';
            ALTER TABLE node ADD COLUMN IF NOT EXISTS `dynamic_rate_config` longtext NOT NULL DEFAULT '{}' COMMENT '动态流量倍率配置' CHECK (json_valid(`custom_config`));
        ");

        return 2023102200;
    }

    public function down(): int
    {
        DB::getPdo()->exec('
            ALTER TABLE node DROP COLUMN IF EXISTS `is_dynamic_rate`;
            ALTER TABLE node DROP COLUMN IF EXISTS `dynamic_rate_config`;
        ');

        return 2023082000;
    }
};
