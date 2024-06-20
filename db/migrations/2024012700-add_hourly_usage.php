<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        DB::getPdo()->exec("
            CREATE TABLE IF NOT EXISTS `hourly_usage` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID',
                `user_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '用户ID',
                `date` date NOT NULL DEFAULT 0 COMMENT '记录日期',
                `usage` longtext NOT NULL DEFAULT '{}' COMMENT '流量用量' CHECK (json_valid(`usage`)),
                PRIMARY KEY (`id`),
                KEY `user_id` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            DROP TABLE IF EXISTS `user_hourly_usage`;
        ");

        return 2024012700;
    }

    public function down(): int
    {
        DB::getPdo()->exec("
            CREATE TABLE IF NOT EXISTS `user_hourly_usage` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID',
                `user_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '用户ID',
                `traffic` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '当前总流量',
                `hourly_usage` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '过去一小时流量',
                `datetime` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '记录时间',
                PRIMARY KEY (`id`),
                KEY `user_id` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            DROP TABLE IF EXISTS `hourly_usage`;
        ");

        return 2024012300;
    }
};
