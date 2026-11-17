<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        DB::getPdo()->exec(
            "CREATE TABLE IF NOT EXISTS `nano_payment_claims` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID',
                `block_hash` char(64) NOT NULL COMMENT 'Nano 发送区块哈希',
                `tradeno` varchar(255) NOT NULL DEFAULT '' COMMENT '网关识别码',
                `claimed_at` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '入账时间',
                PRIMARY KEY (`id`),
                UNIQUE KEY `block_hash` (`block_hash`),
                KEY `tradeno` (`tradeno`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
        );

        return 2026111700;
    }

    public function down(): int
    {
        DB::getPdo()->exec('DROP TABLE IF EXISTS `nano_payment_claims`;');

        return 2026111699;
    }
};
