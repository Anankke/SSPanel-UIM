<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        DB::getPdo()->exec("
            ALTER TABLE invoice ADD COLUMN IF NOT EXISTS `type` varchar(255) NOT NULL DEFAULT 'product' COMMENT '类型';
            ALTER TABLE invoice ADD KEY IF NOT EXISTS `type` (`type`);
            ALTER TABLE invoice MODIFY COLUMN `user_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '归属用户ID';
            ALTER TABLE invoice MODIFY COLUMN `order_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '订单ID';
            ALTER TABLE invoice MODIFY COLUMN `content` longtext NOT NULL DEFAULT '{}' COMMENT '账单内容' CHECK (json_valid(`content`));
            ALTER TABLE invoice MODIFY COLUMN `price` double unsigned NOT NULL DEFAULT 0 COMMENT '账单金额';
            ALTER TABLE invoice MODIFY COLUMN `status` varchar(255) NOT NULL DEFAULT '' COMMENT '账单状态';
            ALTER TABLE invoice MODIFY COLUMN `create_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间';
            ALTER TABLE invoice MODIFY COLUMN `update_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '更新时间';
            ALTER TABLE invoice MODIFY COLUMN `pay_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '支付时间';
            ALTER TABLE `order` ADD KEY IF NOT EXISTS `product_type` (`product_type`);
            ALTER TABLE node MODIFY COLUMN `traffic_rate` double unsigned NOT NULL DEFAULT 1 COMMENT '流量倍率';
        ");

        return 2024040500;
    }

    public function down(): int
    {
        DB::getPdo()->exec('
            ALTER TABLE invoice DROP COLUMN IF EXISTS `type`;
            ALTER TABLE invoice DROP KEY IF EXISTS `type`;
        ');

        return 2024031700;
    }
};
