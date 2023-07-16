<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        DB::getPdo()->exec('
            DROP TABLE IF EXISTS `stream_media`;
        ');

        return 2023071600;
    }

    public function down(): int
    {
        DB::getPdo()->exec(
            "CREATE TABLE IF NOT EXISTS `stream_media` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID',
                `node_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '节点ID',
                `result` text NOT NULL DEFAULT '' COMMENT '检测结果',
                `created_at` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
        );

        return 2023071000;
    }
};
