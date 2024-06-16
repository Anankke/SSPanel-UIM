<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        DB::getPdo()->exec("
            ALTER TABLE invoice MODIFY COLUMN `price` decimal(12,2) unsigned NOT NULL DEFAULT 0 COMMENT '账单金额';
            ALTER TABLE node MODIFY COLUMN `traffic_rate` decimal(5,2) unsigned NOT NULL DEFAULT 1 COMMENT '流量倍率';
            ALTER TABLE node MODIFY COLUMN `node_speedlimit` smallint(6) unsigned NOT NULL DEFAULT 0 COMMENT '节点限速';
            ALTER TABLE `order` MODIFY COLUMN `price` decimal(12,2) unsigned NOT NULL DEFAULT 0 COMMENT '订单金额';
            ALTER TABLE paylist MODIFY COLUMN `tradeno` varchar(255) NOT NULL DEFAULT '' COMMENT '网关识别码';
            ALTER TABLE product MODIFY COLUMN `price` decimal(12,2) unsigned NOT NULL DEFAULT 0 COMMENT '售价';
            ALTER TABLE user MODIFY COLUMN `money` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT '账户余额';
            ALTER TABLE user MODIFY COLUMN `node_speedlimit` smallint(6) unsigned NOT NULL DEFAULT 0 COMMENT '用户限速';
            ALTER TABLE user MODIFY COLUMN `im_type` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '联系方式类型';
            ALTER TABLE user MODIFY COLUMN `contact_method` tinyint(3) unsigned NOT NULL DEFAULT 1 COMMENT '偏好的联系方式';
            ALTER TABLE user MODIFY COLUMN `daily_mail_enable` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '每日报告开关';
            ALTER TABLE user ADD KEY IF NOT EXISTS `contact_method` (`contact_method`);
            ALTER TABLE user ADD KEY IF NOT EXISTS `class` (`class`);
            ALTER TABLE user ADD KEY IF NOT EXISTS `class_expire` (`class_expire`);
            ALTER TABLE user ADD KEY IF NOT EXISTS `node_group` (`node_group`);
            ALTER TABLE user_money_log MODIFY COLUMN `before` decimal(12,2) NOT NULL DEFAULT 0 COMMENT '用户变动前账户余额';
            ALTER TABLE user_money_log MODIFY COLUMN `after` decimal(12,2) NOT NULL DEFAULT 0 COMMENT '用户变动后账户余额';
            ALTER TABLE user_money_log MODIFY COLUMN `amount` decimal(12,2) NOT NULL DEFAULT 0 COMMENT '变动总额';
        ");

        return 2024061600;
    }

    public function down(): int
    {
        return 2024061600;
    }
};
