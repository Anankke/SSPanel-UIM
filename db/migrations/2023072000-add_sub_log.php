<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        DB::getPdo()->exec("
            CREATE TABLE IF NOT EXISTS `subscribe_log` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID',
                `user_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '用户ID',
                `type` varchar(255) NOT NULL DEFAULT '' COMMENT '获取的订阅类型',
                `request_ip` varchar(255) NOT NULL DEFAULT '' COMMENT '请求IP',
                `request_user_agent` varchar(255) NOT NULL DEFAULT '' COMMENT '请求UA信息',
                `request_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '请求时间',
                PRIMARY KEY (`id`),
                KEY `user_id` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            DROP TABLE IF EXISTS `user_subscribe_log`;
        ");

        return 2023072000;
    }

    public function down(): int
    {
        DB::getPdo()->exec("
            CREATE TABLE IF NOT EXISTS `user_subscribe_log` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID',
                `user_name` varchar(255) NOT NULL DEFAULT '' COMMENT '用户名',
                `user_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '用户ID',
                `email` varchar(255) NOT NULL DEFAULT '' COMMENT '用户邮箱',
                `subscribe_type` varchar(255) NOT NULL DEFAULT '' COMMENT '获取的订阅类型',
                `request_ip` varchar(255) NOT NULL DEFAULT '' COMMENT '请求IP',
                `request_time` timestamp NOT NULL DEFAULT '1989-06-04 00:05:00' COMMENT '请求时间',
                `request_user_agent` varchar(255) NOT NULL DEFAULT '' COMMENT '请求UA信息',
                PRIMARY KEY (`id`),
                KEY `user_id` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            DROP TABLE IF EXISTS `subscribe_log`;
        ");

        return 2023071700;
    }
};
