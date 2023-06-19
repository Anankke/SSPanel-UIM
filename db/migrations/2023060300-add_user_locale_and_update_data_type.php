<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        DB::getPdo()->exec("
        ALTER TABLE user ADD COLUMN IF NOT EXISTS `locale` varchar(16) NOT NULL DEFAULT 'zh-TW' COMMENT '显示语言';
        ALTER TABLE user MODIFY COLUMN `user_name` varchar(255) NOT NULL DEFAULT '' COMMENT '用户名';
        ALTER TABLE user MODIFY COLUMN `email` varchar(255) NOT NULL COMMENT 'Email';
        ALTER TABLE user MODIFY COLUMN `pass` varchar(255) NOT NULL COMMENT '登录密码';
        ALTER TABLE user MODIFY COLUMN `passwd` varchar(255) NOT NULL COMMENT '节点密码';
        ALTER TABLE user MODIFY COLUMN `t` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '最后使用时间';
        ALTER TABLE user MODIFY COLUMN `u` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '账户当前上传流量';
        ALTER TABLE user MODIFY COLUMN `d` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '账户当前下载流量';
        ALTER TABLE user MODIFY COLUMN `transfer_today` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '账户今日所用流量';
        ALTER TABLE user MODIFY COLUMN `transfer_total` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '账户累计使用流量';
        ALTER TABLE user MODIFY COLUMN `transfer_enable` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '账户当前可用流量';
        ALTER TABLE user MODIFY COLUMN `last_detect_ban_time` datetime NOT NULL DEFAULT '1989-06-04 00:05:00' COMMENT '最后一次被封禁的时间';
        ALTER TABLE user MODIFY COLUMN `all_detect_number` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '累计违规次数';
        ALTER TABLE user MODIFY COLUMN `last_check_in_time` int(11) unsigned DEFAULT 0 COMMENT '最后签到时间';
        ALTER TABLE user MODIFY COLUMN `reg_date` datetime NOT NULL DEFAULT '1989-06-04 00:05:00' COMMENT '注册时间';
        ALTER TABLE user MODIFY COLUMN `invite_num` int(11) NOT NULL DEFAULT 0 COMMENT '可用邀请次数';
        ALTER TABLE user MODIFY COLUMN `money` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '账户余额';
        ALTER TABLE user MODIFY COLUMN `ref_by` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '邀请人ID';
        ALTER TABLE user MODIFY COLUMN `method` varchar(255) NOT NULL DEFAULT 'aes-128-gcm' COMMENT 'Shadowsocks加密方式';
        ALTER TABLE user MODIFY COLUMN `reg_ip` varchar(255) NOT NULL DEFAULT '127.0.0.1' COMMENT '注册IP';
        ALTER TABLE user MODIFY COLUMN `is_admin` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否管理员';
        ALTER TABLE user MODIFY COLUMN `im_type` smallint(6) unsigned NOT NULL DEFAULT 1 COMMENT '联系方式类型';
        UPDATE user SET im_value = '' WHERE im_value IS NULL;
        ALTER TABLE user MODIFY COLUMN `im_value` varchar(255) NOT NULL DEFAULT '' COMMENT '联系方式';
        ALTER TABLE user MODIFY COLUMN `class` smallint(6) unsigned NOT NULL DEFAULT 0 COMMENT '等级';
        ALTER TABLE user MODIFY COLUMN `class_expire` datetime NOT NULL DEFAULT '1989-06-04 00:05:00' COMMENT '等级过期时间';
        ALTER TABLE user MODIFY COLUMN `expire_in` datetime NOT NULL DEFAULT '2199-01-01 00:00:00' COMMENT '账户过期时间';
        ALTER TABLE user MODIFY COLUMN `theme` varchar(255) NOT NULL DEFAULT 'tabler' COMMENT '网站主题';
        ALTER TABLE user MODIFY COLUMN `ga_token` varchar(255) NOT NULL DEFAULT '' COMMENT 'GA密钥';
        ALTER TABLE user MODIFY COLUMN `ga_enable` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT 'GA开关';
        ALTER TABLE user MODIFY COLUMN `remark` text NOT NULL DEFAULT '' COMMENT '备注';
        ALTER TABLE user MODIFY COLUMN `node_group` smallint(6) unsigned NOT NULL DEFAULT 0 COMMENT '节点分组';
        ALTER TABLE user MODIFY COLUMN `is_banned` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否封禁';
        ALTER TABLE user MODIFY COLUMN `banned_reason` varchar(255) NOT NULL DEFAULT '' COMMENT '封禁理由';
        UPDATE user SET telegram_id = 0 WHERE telegram_id IS NULL;
        ALTER TABLE user MODIFY COLUMN `telegram_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT 'Telegram ID';
        ALTER TABLE user MODIFY COLUMN `expire_notified` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '过期提醒';
        ALTER TABLE user MODIFY COLUMN `traffic_notified` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '流量提醒';
        ALTER TABLE user MODIFY COLUMN `forbidden_ip` varchar(255) NOT NULL DEFAULT '' COMMENT '禁止访问IP';
        ALTER TABLE user MODIFY COLUMN `forbidden_port` varchar(255) NOT NULL DEFAULT '' COMMENT '禁止访问端口';
        ALTER TABLE user MODIFY COLUMN `auto_reset_day` smallint(6) unsigned NOT NULL DEFAULT 0 COMMENT '自动重置流量日';
        ALTER TABLE user MODIFY COLUMN `auto_reset_bandwidth` decimal(12,2) unsigned NOT NULL DEFAULT 0.00 COMMENT '自动重置流量';
        ALTER TABLE user MODIFY COLUMN `use_new_shop` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否启用新商店';
        ALTER TABLE user MODIFY COLUMN `is_dark_mode` tinyint(1) NOT NULL DEFAULT 0;
        ALTER TABLE user DROP KEY IF EXISTS `user_name`;
        ALTER TABLE user ADD UNIQUE KEY IF NOT EXISTS `api_token` (`api_token`);
        ALTER TABLE user ADD KEY IF NOT EXISTS `is_admin` (`is_admin`);
        ALTER TABLE user ADD KEY IF NOT EXISTS `is_banned` (`is_banned`);
        ALTER TABLE user CHANGE COLUMN IF EXISTS `sendDailyMail` `daily_mail_enable` tinyint(1) NOT NULL DEFAULT 0 COMMENT '每日报告开关';
        ");

        return 2023060300;
    }

    public function down(): int
    {
        DB::getPdo()->exec('ALTER TABLE user DROP COLUMN IF EXISTS `locale`;');

        return 2023053000;
    }
};
