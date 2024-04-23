<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        DB::getPdo()->exec("
            CREATE TABLE IF NOT EXISTS `syslog` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID',
                `user_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '触发用户',
                `ip` varchar(255) NOT NULL DEFAULT '' COMMENT '触发IP',
                `message` varchar(1024) NOT NULL DEFAULT '' COMMENT '日志内容',
                `level` tinyint(3) unsigned NOT NULL DEFAULT 100 COMMENT '日志等级',
                `context` longtext NOT NULL DEFAULT '{}' COMMENT '日志内容' CHECK (json_valid(`context`)),
                `channel` varchar(255) NOT NULL DEFAULT '' COMMENT '日志类别',
                `datetime` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '记录时间',
                PRIMARY KEY (`id`),
                KEY `user_id` (`user_id`),
                KEY `ip` (`ip`),
                KEY `message` (`message`),
                KEY `level` (`level`),
                KEY `channel` (`channel`),
                KEY `datetime` (`datetime`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        return 2024041000;
    }

    public function down(): int
    {
        DB::getPdo()->exec('
            DROP TABLE IF EXISTS `syslog`;
        ');

        return 2024040500;
    }
};
