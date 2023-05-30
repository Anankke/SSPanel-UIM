<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        DB::getPdo()->exec(
            "CREATE TABLE `announcement` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `date` datetime DEFAULT NULL,
                `content` text DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `config` (
                `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
                `item` varchar(255) DEFAULT NULL COMMENT '项',
                `value` varchar(2048) DEFAULT NULL,
                `class` varchar(255) DEFAULT 'default' COMMENT '配置分类',
                `is_public` int(11) DEFAULT 0 COMMENT '是否为公共参数',
                `type` varchar(255) DEFAULT NULL COMMENT '值类型',
                `default` varchar(255) DEFAULT NULL COMMENT '默认值',
                `mark` varchar(255) DEFAULT NULL COMMENT '备注',
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `detect_ban_log` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `user_name` varchar(255) DEFAULT NULL COMMENT '用户名',
                `user_id` bigint(20) unsigned DEFAULT NULL COMMENT '用户 ID',
                `email` varchar(255) DEFAULT NULL COMMENT '用户邮箱',
                `detect_number` int(11) DEFAULT NULL COMMENT '本次违规次数',
                `ban_time` int(11) DEFAULT NULL COMMENT '本次封禁时长',
                `start_time` bigint(20) DEFAULT NULL COMMENT '统计开始时间',
                `end_time` bigint(20) DEFAULT NULL COMMENT '统计结束时间',
                `all_detect_number` int(11) DEFAULT NULL COMMENT '累计违规次数',
                PRIMARY KEY (`id`),
                KEY `user_id` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `detect_list` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `name` varchar(255) DEFAULT NULL,
                `text` varchar(255) DEFAULT NULL,
                `regex` varchar(255) DEFAULT NULL,
                `type` int(11) DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `detect_log` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `user_id` bigint(20) unsigned DEFAULT NULL,
                `list_id` bigint(20) unsigned DEFAULT NULL,
                `datetime` bigint(20) unsigned DEFAULT NULL,
                `node_id` int(11) DEFAULT NULL,
                `status` int(11) DEFAULT 0,
                PRIMARY KEY (`id`),
                KEY `user_id` (`user_id`),
                KEY `node_id` (`node_id`),
                KEY `list_id` (`list_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `docs` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `date` datetime DEFAULT NULL,
                `title` varchar(255) DEFAULT NULL,
                `content` varchar(255) DEFAULT NULL,
                `markdown` varchar(255) DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `email_queue` (
                `id` bigint(20) NOT NULL AUTO_INCREMENT,
                `to_email` varchar(255) DEFAULT NULL,
                `subject` varchar(255) DEFAULT NULL,
                `template` varchar(255) DEFAULT NULL,
                `array` longtext DEFAULT NULL,
                `time` int(11) DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `email_verify` (
                `id` bigint(20) NOT NULL AUTO_INCREMENT,
                `email` varchar(255) DEFAULT NULL,
                `ip` varchar(255) DEFAULT NULL,
                `code` varchar(255) DEFAULT NULL,
                `expire_in` bigint(20) DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `gift_card` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `card` text DEFAULT NULL COMMENT '卡号',
                `balance` int(11) DEFAULT NULL COMMENT '余额',
                `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
                `status` int(11) DEFAULT NULL COMMENT '使用状态',
                `use_time` int(11) DEFAULT NULL COMMENT '使用时间',
                `use_user` int(11) DEFAULT NULL COMMENT '使用用户',
                PRIMARY KEY (`id`),
                KEY `id` (`id`),
                KEY `status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `invoice` (
                `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '账单ID',
                `user_id` int(11) DEFAULT NULL COMMENT '归属用户',
                `order_id` int(11) DEFAULT NULL COMMENT '订单ID',
                `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '账单内容' CHECK (json_valid(`content`)),
                `price` double DEFAULT NULL COMMENT '账单金额',
                `status` varchar(255) DEFAULT NULL COMMENT '账单状态',
                `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
                `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
                `pay_time` int(11) DEFAULT NULL COMMENT '支付时间',
                PRIMARY KEY (`id`),
                KEY `id` (`id`),
                KEY `user_id` (`user_id`),
                KEY `order_id` (`order_id`),
                KEY `status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `link` (
                `id` bigint(20) NOT NULL AUTO_INCREMENT,
                `token` varchar(255) DEFAULT NULL,
                `userid` bigint(20) unsigned DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `token` (`token`),
                UNIQUE KEY `userid` (`userid`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `login_ip` (
                `id` bigint(20) NOT NULL AUTO_INCREMENT,
                `userid` bigint(20) unsigned DEFAULT NULL,
                `ip` varchar(255) DEFAULT NULL,
                `datetime` bigint(20) DEFAULT NULL,
                `type` int(11) DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `userid` (`userid`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `node` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(255) DEFAULT NULL,
                `type` int(11) DEFAULT NULL,
                `server` varchar(255) DEFAULT NULL,
                `custom_config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT '{}' COMMENT '自定义配置' CHECK (json_valid(`custom_config`)),
                `info` text DEFAULT '',
                `status` varchar(255) DEFAULT '',
                `sort` int(11) DEFAULT NULL,
                `traffic_rate` float DEFAULT 1,
                `node_class` int(11) DEFAULT 0,
                `node_speedlimit` double NOT NULL DEFAULT 0 COMMENT '节点限速',
                `node_connector` int(11) DEFAULT 0,
                `node_bandwidth` bigint(20) DEFAULT 0,
                `node_bandwidth_limit` bigint(20) DEFAULT 0,
                `bandwidthlimit_resetday` int(11) DEFAULT 0,
                `node_heartbeat` bigint(20) DEFAULT 0,
                `online_user` int(11) DEFAULT 0 COMMENT '节点在线用户',
                `node_ip` varchar(255) DEFAULT NULL,
                `node_group` int(11) DEFAULT 0,
                `mu_only` tinyint(1) DEFAULT 0,
                `online` tinyint(1) DEFAULT 1,
                `gfw_block` tinyint(1) DEFAULT 0,
                `password` varchar(255) DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            
            CREATE TABLE `online_log` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `user_id` INT UNSIGNED NOT NULL,
                `ip` INET6 NOT NULL,
                `node_id` INT UNSIGNED NOT NULL,
                `first_time` INT UNSIGNED NOT NULL,
                `last_time` INT UNSIGNED NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY (`user_id`, `ip`),
                KEY (`last_time`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `order` (
                `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '订单ID',
                `user_id` int(11) DEFAULT NULL COMMENT '提交用户',
                `product_id` int(11) DEFAULT NULL COMMENT '商品ID',
                `product_type` varchar(255) DEFAULT NULL COMMENT '商品类型',
                `product_name` varchar(255) DEFAULT NULL COMMENT '商品名称',
                `product_content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '商品内容' CHECK (json_valid(`product_content`)),
                `coupon` varchar(255) DEFAULT NULL COMMENT '订单优惠码',
                `price` double DEFAULT NULL COMMENT '订单金额',
                `status` varchar(255) DEFAULT NULL COMMENT '订单状态',
                `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
                `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
                PRIMARY KEY (`id`),
                KEY `id` (`id`),
                KEY `user_id` (`user_id`),
                KEY `product_id` (`product_id`),
                KEY `status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `payback` (
                `id` bigint(20) NOT NULL AUTO_INCREMENT,
                `total` decimal(12,2) DEFAULT NULL,
                `userid` bigint(20) DEFAULT NULL,
                `ref_by` bigint(20) DEFAULT NULL,
                `ref_get` decimal(12,2) DEFAULT NULL,
                `datetime` bigint(20) DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `paylist` (
                `id` bigint(20) NOT NULL AUTO_INCREMENT,
                `userid` bigint(20) unsigned DEFAULT NULL,
                `total` decimal(12,2) DEFAULT NULL,
                `status` int(11) DEFAULT 0,
                `invoice_id` int(11) DEFAULT 0,
                `tradeno` varchar(255) DEFAULT NULL,
                `gateway` varchar(255) NOT NULL DEFAULT '',
                `datetime` bigint(20) DEFAULT 0,
                PRIMARY KEY (`id`),
                KEY `userid` (`userid`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `product` (
                `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '商品ID',
                `type` varchar(255) DEFAULT NULL COMMENT '类型',
                `name` varchar(255) DEFAULT NULL COMMENT '名称',
                `price` double DEFAULT NULL COMMENT '售价',
                `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '内容' CHECK (json_valid(`content`)),
                `limit` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '购买限制' CHECK (json_valid(`limit`)),
                `status` int(11) DEFAULT NULL COMMENT '销售状态',
                `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
                `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
                `sale_count` int(11) DEFAULT NULL COMMENT '累计销售数',
                `stock` int(11) DEFAULT NULL COMMENT '库存',
                PRIMARY KEY (`id`),
                KEY `id` (`id`),
                KEY `type` (`type`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `stream_media` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `node_id` int(11) DEFAULT NULL COMMENT '节点id',
                `result` text DEFAULT NULL COMMENT '检测结果',
                `created_at` int(11) DEFAULT NULL COMMENT '创建时间',
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `telegram_session` (
                `id` bigint(20) NOT NULL AUTO_INCREMENT,
                `user_id` bigint(20) DEFAULT NULL,
                `type` int(11) DEFAULT NULL,
                `session_content` varchar(255) DEFAULT NULL,
                `datetime` bigint(20) DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `ticket` (
                `id` bigint(20) NOT NULL AUTO_INCREMENT,
                `title` varchar(255) DEFAULT NULL,
                `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT '' COMMENT '工单内容' CHECK (json_valid(`content`)),
                `userid` bigint(20) DEFAULT NULL,
                `datetime` bigint(20) DEFAULT NULL,
                `status` varchar(255) DEFAULT '' COMMENT '工单状态',
                `type` varchar(255) DEFAULT 'other' COMMENT '工单类型',
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `user` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
                `user_name` varchar(255) DEFAULT NULL COMMENT '用户名',
                `email` varchar(255) DEFAULT NULL COMMENT 'E-Mail',
                `pass` varchar(255) DEFAULT NULL COMMENT '登录密码',
                `passwd` varchar(255) DEFAULT NULL COMMENT '节点密码',
                `uuid` char(36) NOT NULL COMMENT 'UUID',
                `t` bigint(20) unsigned DEFAULT 0 COMMENT '最后使用时间',
                `u` bigint(20) unsigned DEFAULT 0 COMMENT '账户当前上传流量',
                `d` bigint(20) unsigned DEFAULT 0 COMMENT '账户当前下载流量',
                `transfer_today` bigint(20) unsigned DEFAULT 0 COMMENT '账户今日所用流量',
                `transfer_total` bigint(20) unsigned DEFAULT 0 COMMENT '账户累计使用流量',
                `transfer_enable` bigint(20) unsigned DEFAULT 0 COMMENT '账户当前可用流量',
                `port` smallint(6) unsigned NOT NULL COMMENT '端口',
                `last_detect_ban_time` datetime DEFAULT '1989-06-04 00:05:00' COMMENT '最后一次被封禁的时间',
                `all_detect_number` int(11) DEFAULT 0 COMMENT '累计违规次数',
                `last_check_in_time` bigint(20) unsigned DEFAULT 0 COMMENT '最后签到时间',
                `reg_date` datetime DEFAULT NULL COMMENT '注册时间',
                `invite_num` int(11) DEFAULT 0 COMMENT '可用邀请次数',
                `money` decimal(10,2) NOT NULL DEFAULT 0.00,
                `ref_by` bigint(20) unsigned DEFAULT 0 COMMENT '邀请人ID',
                `method` varchar(255) DEFAULT 'aes-128-gcm' COMMENT 'Shadowsocks加密方式',
                `reg_ip` varchar(255) DEFAULT '127.0.0.1' COMMENT '注册IP',
                `node_speedlimit` double NOT NULL DEFAULT 0 COMMENT '用户限速',
                `node_iplimit` smallint(6) unsigned NOT NULL DEFAULT 0 COMMENT '同时可连接IP数',
                `node_connector` int(11) DEFAULT 0 COMMENT '同时可使用连接数',
                `is_admin` tinyint(1) DEFAULT 0 COMMENT '是否管理员',
                `im_type` int(11) DEFAULT 1 COMMENT '联系方式类型',
                `im_value` varchar(255) DEFAULT '' COMMENT '联系方式',
                `sendDailyMail` tinyint(1) DEFAULT 0 COMMENT '每日报告开关',
                `class` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '等级',
                `class_expire` datetime DEFAULT '1989-06-04 00:05:00' COMMENT '等级过期时间',
                `expire_in` datetime DEFAULT '2199-01-01 00:00:00',
                `theme` varchar(255) DEFAULT NULL COMMENT '网站主题',
                `ga_token` varchar(255) DEFAULT NULL,
                `ga_enable` int(11) DEFAULT 0,
                `remark` text DEFAULT '' COMMENT '备注',
                `node_group` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '节点分组',
                `is_banned` int(11) DEFAULT 0 COMMENT '是否封禁',
                `banned_reason` varchar(255) DEFAULT '' COMMENT '封禁理由',
                `telegram_id` bigint(20) DEFAULT 0,
                `expire_notified` tinyint(1) DEFAULT 0,
                `traffic_notified` tinyint(1) DEFAULT 0,
                `forbidden_ip` varchar(255) DEFAULT '',
                `forbidden_port` varchar(255) DEFAULT '',
                `auto_reset_day` int(11) DEFAULT 0,
                `auto_reset_bandwidth` decimal(12,2) DEFAULT 0.00,
                `api_token` char(36) NOT NULL DEFAULT '' COMMENT 'API 密钥',
                `use_new_shop` smallint(6) NOT NULL DEFAULT 0 COMMENT '是否启用新商店',
                `is_dark_mode` int(11) DEFAULT 0,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uuid` (`uuid`),
                UNIQUE KEY `email` (`email`),
                UNIQUE KEY `ga_token` (`ga_token`),
                KEY `user_name` (`user_name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `user_coupon` (
                `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '优惠码ID',
                `code` varchar(255) DEFAULT NULL COMMENT '优惠码',
                `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '优惠码内容' CHECK (json_valid(`content`)),
                `limit` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT '优惠码限制' CHECK (json_valid(`limit`)),
                `use_count` int(11) NOT NULL DEFAULT 0 COMMENT '累计使用次数',
                `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
                `expire_time` int(11) DEFAULT NULL COMMENT '过期时间',
                PRIMARY KEY (`id`),
                KEY `id` (`id`),
                KEY `code` (`code`),
                KEY `expire_time` (`expire_time`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `user_hourly_usage` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `user_id` bigint(20) unsigned DEFAULT NULL,
                `traffic` bigint(20) DEFAULT NULL,
                `hourly_usage` bigint(20) DEFAULT NULL,
                `datetime` int(11) DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `user_id` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `user_invite_code` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `code` varchar(255) DEFAULT NULL,
                `user_id` bigint(20) unsigned DEFAULT NULL,
                `created_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                `updated_at` timestamp NULL DEFAULT '1989-06-04 00:05:00',
                PRIMARY KEY (`id`),
                UNIQUE KEY `code` (`code`),
                UNIQUE KEY `user_id` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            
            CREATE TABLE `user_money_log` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `user_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '用户ID',
                `before` decimal(10,2) NOT NULL DEFAULT 0 COMMENT '用户变动前账户余额',
                `after` decimal(10,2) NOT NULL DEFAULT 0 COMMENT '用户变动后账户余额',
                `amount` decimal(10,2) NOT NULL DEFAULT 0 COMMENT '变动总额',
                `remark` text NOT NULL DEFAULT '' COMMENT '备注',
                `create_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
                PRIMARY KEY (`id`),
                KEY `user_id` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `user_password_reset` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `email` varchar(255) DEFAULT NULL,
                `token` varchar(255) DEFAULT NULL,
                `init_time` int(11) DEFAULT NULL,
                `expire_time` int(11) DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `user_subscribe_log` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `user_name` varchar(255) DEFAULT NULL COMMENT '用户名',
                `user_id` bigint(20) unsigned DEFAULT NULL COMMENT '用户 ID',
                `email` varchar(255) DEFAULT NULL COMMENT '用户邮箱',
                `subscribe_type` varchar(255) DEFAULT NULL COMMENT '获取的订阅类型',
                `request_ip` varchar(255) DEFAULT NULL COMMENT '请求 IP',
                `request_time` datetime DEFAULT NULL COMMENT '请求时间',
                `request_user_agent` text DEFAULT NULL COMMENT '请求 UA 信息',
                PRIMARY KEY (`id`),
                KEY `user_id` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
        );

        return 2023020100;
    }

    public function down(): int
    {
        echo "No reverse operation for initial migration\n";

        return 2023020100;
    }
};
