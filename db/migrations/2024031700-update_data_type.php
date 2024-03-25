<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        DB::getPdo()->exec("
            ALTER TABLE announcement MODIFY COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '公告ID';
            ALTER TABLE announcement MODIFY COLUMN `content` longtext NOT NULL DEFAULT '' COMMENT '公告内容';
            ALTER TABLE config MODIFY COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '配置ID';
            ALTER TABLE config MODIFY COLUMN `item` varchar(255) NOT NULL DEFAULT '' COMMENT '配置项';
            ALTER TABLE config MODIFY COLUMN `value` varchar(2048) NOT NULL DEFAULT '' COMMENT '配置值';
            ALTER TABLE config MODIFY COLUMN `class` varchar(16) NOT NULL DEFAULT '' COMMENT '配置类别';
            ALTER TABLE config MODIFY COLUMN `is_public` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否为公共参数';
            ALTER TABLE config MODIFY COLUMN `type` varchar(16) NOT NULL DEFAULT '' COMMENT '配置值类型';
            ALTER TABLE config MODIFY COLUMN `default` varchar(2048) NOT NULL DEFAULT '' COMMENT '默认值';
            ALTER TABLE config MODIFY COLUMN `mark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注';
            ALTER TABLE detect_ban_log MODIFY COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '封禁记录ID';
            ALTER TABLE invoice MODIFY COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '账单ID';
            ALTER TABLE link MODIFY COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID';
            ALTER TABLE subscribe_log MODIFY COLUMN `request_user_agent` varchar(1024) NOT NULL DEFAULT '' COMMENT '请求UA';
            ALTER TABLE subscribe_log ADD KEY IF NOT EXISTS `request_ip` (`request_ip`);
            ALTER TABLE subscribe_log ADD KEY IF NOT EXISTS `request_time` (`request_time`);
            ALTER TABLE subscribe_log ADD KEY IF NOT EXISTS `request_user_agent` (`request_user_agent`);
            ALTER TABLE user_coupon MODIFY COLUMN `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '优惠码ID';
            ALTER TABLE user_coupon DROP KEY IF EXISTS `code`;
            ALTER TABLE user_coupon ADD UNIQUE KEY IF NOT EXISTS `code` (`code`);
        ");

        return 2024031700;
    }

    public function down(): int
    {
        return 2024031700;
    }
};
