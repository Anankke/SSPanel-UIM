<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        DB::getPdo()->exec(
            "CREATE TABLE `announcement` (
                `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '公告ID',
                `date` datetime NOT NULL DEFAULT '1989-06-04 00:05:00' COMMENT '公告日期',
                `content` text NOT NULL DEFAULT '' COMMENT '公告内容',
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `config` (
                `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
                `item` varchar(255) DEFAULT NULL COMMENT '项',
                `value` varchar(2048) DEFAULT NULL COMMENT '值',
                `class` varchar(255) DEFAULT 'default' COMMENT '配置分类',
                `is_public` int(11) DEFAULT 0 COMMENT '是否为公共参数',
                `type` varchar(255) DEFAULT NULL COMMENT '值类型',
                `default` varchar(255) DEFAULT NULL COMMENT '默认值',
                `mark` varchar(255) DEFAULT NULL COMMENT '备注',
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `detect_ban_log` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID',
                `user_name` varchar(255) NOT NULL DEFAULT '' COMMENT '用户名',
                `user_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '用户ID',
                `email` varchar(255) NOT NULL DEFAULT '' COMMENT '用户邮箱',
                `detect_number` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '本次违规次数',
                `ban_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '本次封禁时长',
                `start_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '统计开始时间',
                `end_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '统计结束时间',
                `all_detect_number` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '累计违规次数',
                PRIMARY KEY (`id`),
                KEY `user_id` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `detect_list` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '审计规则ID',
                `name` varchar(255) NOT NULL DEFAULT '' COMMENT '规则名称',
                `text` varchar(255) NOT NULL DEFAULT '' COMMENT '规则名称',
                `regex` varchar(255) NOT NULL DEFAULT '' COMMENT '正则表达式',
                `type` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '规则类型',
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `detect_log` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID',
                `user_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '用户ID',
                `list_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '规则ID',
                `datetime` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '触发时间',
                `node_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '节点ID',
                `status` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '状态',
                PRIMARY KEY (`id`),
                KEY `user_id` (`user_id`),
                KEY `node_id` (`node_id`),
                KEY `list_id` (`list_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `docs` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `date` datetime NOT NULL DEFAULT '1989-06-04 00:05:00' COMMENT '文档日期',
                `title` varchar(255) NOT NULL DEFAULT '' COMMENT '文档标题',
                `content` longtext NOT NULL DEFAULT '' COMMENT '文档内容',
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `email_queue` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID',
                `to_email` varchar(255) NOT NULL DEFAULT '' COMMENT '收件人邮箱',
                `subject` varchar(255) NOT NULL DEFAULT '' COMMENT '邮件标题',
                `template` varchar(255) NOT NULL DEFAULT '' COMMENT '邮件模板',
                `array` longtext NOT NULL DEFAULT '{}' COMMENT '模板参数' CHECK (json_valid(`array`)),
                `time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '添加时间',
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `gift_card` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '礼品卡ID',
                `card` text NOT NULL DEFAULT '' COMMENT '卡号',
                `balance` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '余额',
                `create_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
                `status` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '使用状态',
                `use_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '使用时间',
                `use_user` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '使用用户',
                PRIMARY KEY (`id`),
                KEY `id` (`id`),
                KEY `status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `invoice` (
                `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '账单ID',
                `user_id` bigint(20) unsigned DEFAULT 0 COMMENT '归属用户',
                `order_id` bigint(20) unsigned DEFAULT 0 COMMENT '订单ID',
                `content` longtext DEFAULT '{}' COMMENT '账单内容' CHECK (json_valid(`content`)),
                `price` double unsigned DEFAULT 0 COMMENT '账单金额',
                `status` varchar(255) DEFAULT '' COMMENT '账单状态',
                `create_time` int(11) unsigned DEFAULT 0 COMMENT '创建时间',
                `update_time` int(11) unsigned DEFAULT 0 COMMENT '更新时间',
                `pay_time` int(11) unsigned DEFAULT 0 COMMENT '支付时间',
                PRIMARY KEY (`id`),
                KEY `id` (`id`),
                KEY `user_id` (`user_id`),
                KEY `order_id` (`order_id`),
                KEY `status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `link` (
                `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '记录ID',
                `token` varchar(255) NOT NULL DEFAULT '' COMMENT '订阅token',
                `userid` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '用户ID',
                PRIMARY KEY (`id`),
                UNIQUE KEY `token` (`token`),
                UNIQUE KEY `userid` (`userid`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `login_ip` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID',
                `userid` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '用户ID',
                `ip` varchar(255) NOT NULL DEFAULT '' COMMENT '登录IP',
                `datetime` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '登录时间',
                `type` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '登录类型',
                PRIMARY KEY (`id`),
                KEY `userid` (`userid`),
                KEY `type` (`type`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `node` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '节点ID',
                `name` varchar(255) NOT NULL DEFAULT '' COMMENT '节点名称',
                `type` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '节点显示',
                `server` varchar(255) NOT NULL DEFAULT '' COMMENT '节点地址',
                `custom_config` longtext NOT NULL DEFAULT '{}' COMMENT '自定义配置' CHECK (json_valid(`custom_config`)),
                `info` varchar(255) NOT NULL DEFAULT '' COMMENT '节点信息',
                `status` varchar(255) NOT NULL DEFAULT '' COMMENT '节点状态',
                `sort` tinyint(2) unsigned NOT NULL DEFAULT 14 COMMENT '节点类型',
                `traffic_rate` float unsigned NOT NULL DEFAULT 1 COMMENT '流量倍率',
                `node_class` smallint(5) unsigned NOT NULL DEFAULT 0 COMMENT '节点等级',
                `node_speedlimit` double unsigned NOT NULL DEFAULT 0 COMMENT '节点限速',
                `node_bandwidth` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '节点流量',
                `node_bandwidth_limit` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '节点流量限制',
                `bandwidthlimit_resetday` tinyint(2) unsigned NOT NULL DEFAULT 0 COMMENT '流量重置日',
                `node_heartbeat` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '节点心跳',
                `online_user` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '节点在线用户',
                `node_ip` varchar(255) NOT NULL DEFAULT '' COMMENT '节点IP',
                `node_group` smallint(5) unsigned NOT NULL DEFAULT 0 COMMENT '节点群组',
                `online` tinyint(1) NOT NULL DEFAULT 1 COMMENT '在线状态',
                `gfw_block` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否被GFW封锁',
                `password` varchar(255) NOT NULL DEFAULT '' COMMENT '后端连接密码',
                PRIMARY KEY (`id`),
                KEY `type` (`type`),
                KEY `sort` (`sort`),
                KEY `node_class` (`node_class`),
                KEY `node_group` (`node_group`),
                KEY `online` (`online`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            
            CREATE TABLE `online_log` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID',
                `user_id` bigint(20) unsigned NOT NULL COMMENT '用户ID',
                `ip` inet6 NOT NULL COMMENT 'IP地址',
                `node_id` int(11) unsigned NOT NULL COMMENT '节点ID',
                `first_time` int(11) unsigned NOT NULL COMMENT '首次在线时间',
                `last_time` int(11) unsigned NOT NULL COMMENT '最后在线时间',
                PRIMARY KEY (`id`),
                UNIQUE KEY (`user_id`, `ip`),
                KEY (`last_time`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `order` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '订单ID',
                `user_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '提交用户',
                `product_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '商品ID',
                `product_type` varchar(255) NOT NULL DEFAULT '' COMMENT '商品类型',
                `product_name` varchar(255) NOT NULL DEFAULT '' COMMENT '商品名称',
                `product_content` longtext NOT NULL DEFAULT '{}' COMMENT '商品内容' CHECK (json_valid(`product_content`)),
                `coupon` varchar(255) NOT NULL DEFAULT '' COMMENT '订单优惠码',
                `price` double unsigned NOT NULL DEFAULT 0 COMMENT '订单金额',
                `status` varchar(255) NOT NULL DEFAULT '' COMMENT '订单状态',
                `create_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
                `update_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '更新时间',
                PRIMARY KEY (`id`),
                KEY `id` (`id`),
                KEY `user_id` (`user_id`),
                KEY `product_id` (`product_id`),
                KEY `status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `payback` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID',
                `total` decimal(12,2) unsigned NOT NULL DEFAULT 0 COMMENT '总金额',
                `userid` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '用户ID',
                `ref_by` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '推荐人ID',
                `ref_get` decimal(12,2) unsigned NOT NULL DEFAULT 0 COMMENT '推荐人获得金额',
                `datetime` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `paylist` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID',
                `userid` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '用户ID',
                `total` decimal(12,2) NOT NULL DEFAULT 0 COMMENT '总金额',
                `status` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '状态',
                `invoice_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '账单ID',
                `tradeno` varchar(255) NOT NULL DEFAULT '' COMMENT '网关单号',
                `gateway` varchar(255) NOT NULL DEFAULT '' COMMENT '支付网关',
                `datetime` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
                PRIMARY KEY (`id`),
                KEY `userid` (`userid`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `product` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '商品ID',
                `type` varchar(255) NOT NULL DEFAULT 'tabp' COMMENT '类型',
                `name` varchar(255) NOT NULL DEFAULT '' COMMENT '名称',
                `price` double unsigned NOT NULL DEFAULT 0 COMMENT '售价',
                `content` longtext NOT NULL DEFAULT '{}' COMMENT '内容' CHECK (json_valid(`content`)),
                `limit` longtext NOT NULL DEFAULT '{}' COMMENT '购买限制' CHECK (json_valid(`limit`)),
                `status` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '销售状态',
                `create_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
                `update_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '更新时间',
                `sale_count` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '累计销售数',
                `stock` int(11) NOT NULL DEFAULT -1 COMMENT '库存',
                PRIMARY KEY (`id`),
                KEY `id` (`id`),
                KEY `type` (`type`),
                KEY `status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `stream_media` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID',
                `node_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '节点ID',
                `result` text NOT NULL DEFAULT '' COMMENT '检测结果',
                `created_at` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `ticket` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '工单ID',
                `title` varchar(255) NOT NULL DEFAULT '' COMMENT '工单标题',
                `content` longtext NOT NULL DEFAULT '{}' COMMENT '工单内容' CHECK (json_valid(`content`)),
                `userid` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '用户ID',
                `datetime` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
                `status` varchar(255) NOT NULL DEFAULT '' COMMENT '工单状态',
                `type` varchar(255) NOT NULL DEFAULT '' COMMENT '工单类型',
                PRIMARY KEY (`id`),
                KEY `userid` (`userid`),
                KEY `status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `user` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
                `user_name` varchar(255) NOT NULL DEFAULT '' COMMENT '用户名',
                `email` varchar(255) NOT NULL COMMENT 'E-Mail',
                `pass` varchar(255) NOT NULL COMMENT '登录密码',
                `passwd` varchar(255) NOT NULL COMMENT '节点密码',
                `uuid` char(36) NOT NULL COMMENT 'UUID',
                `u` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '账户当前上传流量',
                `d` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '账户当前下载流量',
                `transfer_today` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '账户今日所用流量',
                `transfer_total` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '账户累计使用流量',
                `transfer_enable` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '账户当前可用流量',
                `port` smallint(6) unsigned NOT NULL COMMENT '端口',
                `last_detect_ban_time` datetime NOT NULL DEFAULT '1989-06-04 00:05:00' COMMENT '最后一次被封禁的时间',
                `all_detect_number` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '累计违规次数',
                `last_use_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '最后使用时间',
                `last_check_in_time` int(11) unsigned DEFAULT 0 COMMENT '最后签到时间',
                `last_login_time` int(11) unsigned DEFAULT 0 COMMENT '最后登录时间',
                `reg_date` datetime NOT NULL DEFAULT '1989-06-04 00:05:00' COMMENT '注册时间',
                `invite_num` int(11) NOT NULL DEFAULT 0 COMMENT '可用邀请次数',
                `money` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '账户余额',
                `ref_by` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '邀请人ID',
                `method` varchar(255) NOT NULL DEFAULT 'aes-128-gcm' COMMENT 'Shadowsocks加密方式',
                `reg_ip` varchar(255) NOT NULL DEFAULT '127.0.0.1' COMMENT '注册IP',
                `node_speedlimit` double NOT NULL DEFAULT 0 COMMENT '用户限速',
                `node_iplimit` smallint(6) unsigned NOT NULL DEFAULT 0 COMMENT '同时可连接IP数',
                `is_admin` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否管理员',
                `im_type` smallint(6) unsigned NOT NULL DEFAULT 1 COMMENT '联系方式类型',
                `im_value` varchar(255) NOT NULL DEFAULT '' COMMENT '联系方式',
                `daily_mail_enable` tinyint(1) NOT NULL DEFAULT 0 COMMENT '每日报告开关',
                `class` smallint(5) unsigned NOT NULL DEFAULT 0 COMMENT '等级',
                `class_expire` datetime NOT NULL DEFAULT '1989-06-04 00:05:00' COMMENT '等级过期时间',
                `expire_in` datetime NOT NULL DEFAULT '2199-01-01 00:00:00' COMMENT '账户过期时间',
                `theme` varchar(255) NOT NULL DEFAULT 'tabler' COMMENT '网站主题',
                `ga_token` varchar(255) NOT NULL DEFAULT '' COMMENT 'GA密钥',
                `ga_enable` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT 'GA开关',
                `remark` text NOT NULL DEFAULT '' COMMENT '备注',
                `node_group` smallint(6) unsigned NOT NULL DEFAULT 0 COMMENT '节点分组',
                `is_banned` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否封禁',
                `banned_reason` varchar(255) NOT NULL DEFAULT '' COMMENT '封禁理由',
                `telegram_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT 'Telegram ID',
                `expire_notified` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '过期提醒',
                `traffic_notified` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '流量提醒',
                `forbidden_ip` varchar(255) NOT NULL DEFAULT '' COMMENT '禁止访问IP',
                `forbidden_port` varchar(255) NOT NULL DEFAULT '' COMMENT '禁止访问端口',
                `auto_reset_day` smallint(6) unsigned NOT NULL DEFAULT 0 COMMENT '自动重置流量日',
                `auto_reset_bandwidth` decimal(12,2) unsigned NOT NULL DEFAULT 0 COMMENT '自动重置流量',
                `api_token` char(36) NOT NULL DEFAULT '' COMMENT 'API 密钥',
                `use_new_shop` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否启用新商店',
                `is_dark_mode` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否启用暗黑模式',
                `is_inactive` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否处于闲置状态',
                `locale` varchar(16) NOT NULL DEFAULT 'zh-TW' COMMENT '显示语言',
                PRIMARY KEY (`id`),
                UNIQUE KEY `uuid` (`uuid`),
                UNIQUE KEY `email` (`email`),
                UNIQUE KEY `ga_token` (`ga_token`),
                UNIQUE KEY `api_token` (`api_token`),
                KEY `is_admin` (`is_admin`),
                KEY `is_banned` (`is_banned`),
                KEY `is_inactive` (`is_inactive`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `user_coupon` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '优惠码ID',
                `code` varchar(255) NOT NULL DEFAULT '' COMMENT '优惠码',
                `content` longtext NOT NULL DEFAULT '{}' COMMENT '优惠码内容' CHECK (json_valid(`content`)),
                `limit` longtext NOT NULL DEFAULT '{}' COMMENT '优惠码限制' CHECK (json_valid(`limit`)),
                `use_count` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '累计使用次数',
                `create_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
                `expire_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '过期时间',
                PRIMARY KEY (`id`),
                KEY `id` (`id`),
                KEY `code` (`code`),
                KEY `expire_time` (`expire_time`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `user_hourly_usage` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID',
                `user_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '用户ID',
                `traffic` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '当前总流量',
                `hourly_usage` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '过去一小时流量',
                `datetime` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '记录时间',
                PRIMARY KEY (`id`),
                KEY `user_id` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `user_invite_code` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID',
                `code` varchar(255) NOT NULL DEFAULT '' COMMENT '邀请码',
                `user_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '用户ID',
                `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '创建时间',
                `updated_at` timestamp NOT NULL DEFAULT '1989-06-04 00:05:00' COMMENT '更新时间',
                PRIMARY KEY (`id`),
                UNIQUE KEY `code` (`code`),
                UNIQUE KEY `user_id` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            
            CREATE TABLE `user_money_log` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID',
                `user_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '用户ID',
                `before` decimal(10,2) NOT NULL DEFAULT 0 COMMENT '用户变动前账户余额',
                `after` decimal(10,2) NOT NULL DEFAULT 0 COMMENT '用户变动后账户余额',
                `amount` decimal(10,2) NOT NULL DEFAULT 0 COMMENT '变动总额',
                `remark` text NOT NULL DEFAULT '' COMMENT '备注',
                `create_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
                PRIMARY KEY (`id`),
                KEY `user_id` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `user_subscribe_log` (
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
