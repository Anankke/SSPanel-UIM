<?php

declare(strict_types=1);

use App\Interfaces\MigrationInterface;
use App\Services\DB;

return new class() implements MigrationInterface {
    public function up(): int
    {
        DB::getPdo()->exec(
            "CREATE TABLE `announcement` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '公告ID',
                `status` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '公告状态',
                `sort` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '公告排序',
                `date` datetime NOT NULL DEFAULT '1989-06-04 00:05:00' COMMENT '公告日期',
                `content` longtext NOT NULL DEFAULT '' COMMENT '公告内容',
                PRIMARY KEY (`id`),
                KEY `status` (`status`),
                KEY `sort` (`sort`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `config` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '配置ID',
                `item` varchar(255) NOT NULL DEFAULT '' COMMENT '配置项',
                `value` varchar(2048) NOT NULL DEFAULT '' COMMENT '配置值',
                `class` varchar(16) NOT NULL DEFAULT '' COMMENT '配置类别',
                `is_public` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否为公共参数',
                `type` varchar(16) NOT NULL DEFAULT '' COMMENT '配置值类型',
                `default` varchar(2048) NOT NULL DEFAULT '' COMMENT '默认值',
                `mark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
                PRIMARY KEY (`id`),
                KEY `item` (`item`),
                KEY `class` (`class`),
                KEY `is_public` (`is_public`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `detect_ban_log` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '封禁记录ID',
                `user_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '用户ID',
                `detect_number` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '本次违规次数',
                `ban_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '封禁时长',
                `start_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '封禁开始时间',
                `end_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '封禁结束时间',
                `all_detect_number` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '累计违规次数',
                PRIMARY KEY (`id`),
                KEY `user_id` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `detect_list` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '审计规则ID',
                `name` varchar(255) NOT NULL DEFAULT '' COMMENT '规则名称',
                `text` varchar(255) NOT NULL DEFAULT '' COMMENT '规则介绍',
                `regex` varchar(255) NOT NULL DEFAULT '' COMMENT '正则表达式',
                `type` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '规则类型',
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `detect_log` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '审计记录ID',
                `user_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '用户ID',
                `list_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '规则ID',
                `datetime` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '触发时间',
                `node_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '节点ID',
                `status` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '状态',
                PRIMARY KEY (`id`),
                KEY `user_id` (`user_id`),
                KEY `list_id` (`list_id`),
                KEY `node_id` (`node_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `docs` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '文档ID',
                `status` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '文档状态',
                `sort` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '文档排序',
                `date` datetime NOT NULL DEFAULT '1989-06-04 00:05:00' COMMENT '文档日期',
                `title` varchar(255) NOT NULL DEFAULT '' COMMENT '文档标题',
                `content` longtext NOT NULL DEFAULT '' COMMENT '文档内容',
                PRIMARY KEY (`id`),
                KEY `status` (`status`),
                KEY `sort` (`sort`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `email_queue` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID',
                `to_email` varchar(255) NOT NULL DEFAULT '' COMMENT '收件人邮箱',
                `subject` varchar(255) NOT NULL DEFAULT '' COMMENT '邮件主题',
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
            
            CREATE TABLE `hourly_usage` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID',
                `user_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '用户ID',
                `date` date NOT NULL DEFAULT 0 COMMENT '记录日期',
                `usage` longtext NOT NULL DEFAULT '{}' COMMENT '流量用量' CHECK (json_valid(`usage`)),
                PRIMARY KEY (`id`),
                KEY `user_id` (`user_id`),
                KEY `date` (`date`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `invoice` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '账单ID',
                `type` varchar(255) NOT NULL DEFAULT 'product' COMMENT '类型',
                `user_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '归属用户ID',
                `order_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '订单ID',
                `content` longtext NOT NULL DEFAULT '{}' COMMENT '账单内容' CHECK (json_valid(`content`)),
                `price` decimal(12,2) unsigned NOT NULL DEFAULT 0 COMMENT '账单金额',
                `status` varchar(255) NOT NULL DEFAULT '' COMMENT '账单状态',
                `create_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
                `update_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '更新时间',
                `pay_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '支付时间',
                PRIMARY KEY (`id`),
                KEY `id` (`id`),
                KEY `type` (`type`),
                KEY `user_id` (`user_id`),
                KEY `order_id` (`order_id`),
                KEY `status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `link` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID',
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
                `type` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '节点启用',
                `server` varchar(255) NOT NULL DEFAULT '' COMMENT '节点地址',
                `custom_config` longtext NOT NULL DEFAULT '{}' COMMENT '自定义配置' CHECK (json_valid(`custom_config`)),
                `sort` tinyint(2) unsigned NOT NULL DEFAULT 14 COMMENT '节点类型',
                `traffic_rate` decimal(5,2) unsigned NOT NULL DEFAULT 1 COMMENT '流量倍率',
                `is_dynamic_rate` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否启用动态流量倍率',
                `dynamic_rate_type` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '动态流量倍率计算方式',
                `dynamic_rate_config` longtext NOT NULL DEFAULT '{}' COMMENT '动态流量倍率配置' CHECK (json_valid(`custom_config`)),
                `node_class` smallint(5) unsigned NOT NULL DEFAULT 0 COMMENT '节点等级',
                `node_speedlimit` smallint(6) unsigned NOT NULL DEFAULT 0 COMMENT '节点限速',
                `node_bandwidth` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '节点流量',
                `node_bandwidth_limit` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '节点流量限制',
                `bandwidthlimit_resetday` tinyint(2) unsigned NOT NULL DEFAULT 0 COMMENT '流量重置日',
                `node_heartbeat` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '节点心跳',
                `online_user` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '节点在线用户',
                `ipv4` inet4 NOT NULL DEFAULT '127.0.0.1' COMMENT 'IPv4地址',
                `ipv6` inet6 NOT NULL DEFAULT '::1' COMMENT 'IPv6地址',
                `node_group` smallint(5) unsigned NOT NULL DEFAULT 0 COMMENT '节点群组',
                `online` tinyint(1) NOT NULL DEFAULT 1 COMMENT '在线状态',
                `gfw_block` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否被GFW封锁',
                `password` varchar(255) NOT NULL DEFAULT '' COMMENT '后端连接密码',
                PRIMARY KEY (`id`),
                UNIQUE KEY `password` (`password`),
                KEY `type` (`type`),
                KEY `sort` (`sort`),
                KEY `is_dynamic_rate` (`is_dynamic_rate`),
                KEY `node_class` (`node_class`),
                KEY `bandwidthlimit_resetday` (`bandwidthlimit_resetday`),
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
                KEY `node_id` (`node_id`),
                KEY `last_time` (`last_time`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `order` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '订单ID',
                `user_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '提交用户ID',
                `product_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '商品ID',
                `product_type` varchar(255) NOT NULL DEFAULT '' COMMENT '商品类型',
                `product_name` varchar(255) NOT NULL DEFAULT '' COMMENT '商品名称',
                `product_content` longtext NOT NULL DEFAULT '{}' COMMENT '商品内容' CHECK (json_valid(`product_content`)),
                `coupon` varchar(255) NOT NULL DEFAULT '' COMMENT '订单优惠码',
                `price` decimal(12,2) unsigned NOT NULL DEFAULT 0 COMMENT '订单金额',
                `status` varchar(255) NOT NULL DEFAULT '' COMMENT '订单状态',
                `create_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
                `update_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '更新时间',
                PRIMARY KEY (`id`),
                KEY `id` (`id`),
                KEY `user_id` (`user_id`),
                KEY `product_id` (`product_id`),
                KEY `product_type` (`product_type`),
                KEY `status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `payback` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID',
                `total` decimal(12,2) unsigned NOT NULL DEFAULT 0 COMMENT '总金额',
                `userid` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '用户ID',
                `ref_by` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '推荐人ID',
                `ref_get` decimal(12,2) unsigned NOT NULL DEFAULT 0 COMMENT '推荐人获得金额',
                `invoice_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '账单ID',
                `datetime` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
                PRIMARY KEY (`id`),
                KEY `userid` (`userid`),
                KEY `ref_by` (`ref_by`),
                KEY `invoice_id` (`invoice_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `paylist` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID',
                `userid` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '用户ID',
                `total` decimal(12,2) NOT NULL DEFAULT 0 COMMENT '总金额',
                `status` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '状态',
                `invoice_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '账单ID',
                `tradeno` varchar(255) NOT NULL DEFAULT '' COMMENT '网关识别码',
                `gateway` varchar(255) NOT NULL DEFAULT '' COMMENT '支付网关',
                `datetime` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
                PRIMARY KEY (`id`),
                UNIQUE KEY `tradeno` (`tradeno`),
                KEY `userid` (`userid`),
                KEY `status` (`status`),
                KEY `invoice_id` (`invoice_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `product` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '商品ID',
                `type` varchar(255) NOT NULL DEFAULT 'tabp' COMMENT '类型',
                `name` varchar(255) NOT NULL DEFAULT '' COMMENT '名称',
                `price` decimal(12,2) unsigned NOT NULL DEFAULT 0 COMMENT '售价',
                `content` longtext NOT NULL DEFAULT '{}' COMMENT '内容' CHECK (json_valid(`content`)),
                `limit` longtext NOT NULL DEFAULT '{}' COMMENT '购买限制' CHECK (json_valid(`limit`)),
                `status` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '销售状态',
                `create_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
                `update_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '更新时间',
                `sale_count` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '累计销量',
                `stock` int(11) NOT NULL DEFAULT -1 COMMENT '库存',
                PRIMARY KEY (`id`),
                KEY `id` (`id`),
                KEY `type` (`type`),
                KEY `status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            
            CREATE TABLE `syslog` (
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
            
            CREATE TABLE `subscribe_log` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID',
                `user_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '用户ID',
                `type` varchar(255) NOT NULL DEFAULT '' COMMENT '获取的订阅类型',
                `request_ip` varchar(255) NOT NULL DEFAULT '' COMMENT '请求IP',
                `request_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '请求时间',
                `request_user_agent` varchar(1024) NOT NULL DEFAULT '' COMMENT '请求UA',
                PRIMARY KEY (`id`),
                KEY `user_id` (`user_id`),
                KEY `type` (`type`),
                KEY `request_ip` (`request_ip`),
                KEY `request_time` (`request_time`),
                KEY `request_user_agent` (`request_user_agent`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `ticket` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '工单ID',
                `title` varchar(255) NOT NULL DEFAULT '' COMMENT '工单标题',
                `content` longtext NOT NULL DEFAULT '{}' COMMENT '工单内容' CHECK (json_valid(`content`)),
                `userid` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '用户ID',
                `status` varchar(255) NOT NULL DEFAULT '' COMMENT '工单状态',
                `type` varchar(255) NOT NULL DEFAULT '' COMMENT '工单类型',
                `datetime` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
                PRIMARY KEY (`id`),
                KEY `userid` (`userid`),
                KEY `status` (`status`),
                KEY `type` (`type`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `user` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
                `user_name` varchar(255) NOT NULL DEFAULT '' COMMENT '用户名',
                `email` varchar(255) NOT NULL COMMENT '用户Email',
                `pass` varchar(255) NOT NULL COMMENT '登录密码',
                `passwd` varchar(255) NOT NULL COMMENT '连接密码',
                `uuid` uuid NOT NULL COMMENT 'UUID',
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
                `money` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT '账户余额',
                `ref_by` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '邀请人ID',
                `method` varchar(255) NOT NULL DEFAULT 'aes-128-gcm' COMMENT '加密方式',
                `reg_ip` varchar(255) NOT NULL DEFAULT '127.0.0.1' COMMENT '注册IP',
                `node_speedlimit` smallint(6) unsigned NOT NULL DEFAULT 0 COMMENT '用户限速',
                `node_iplimit` smallint(6) unsigned NOT NULL DEFAULT 0 COMMENT '同时可连接IP数',
                `is_admin` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否管理员',
                `im_type` tinyint(3) unsigned NOT NULL DEFAULT 0 COMMENT '联系方式类型',
                `im_value` varchar(255) NOT NULL DEFAULT '' COMMENT '联系方式',
                `contact_method` tinyint(3) unsigned NOT NULL DEFAULT 1 COMMENT '偏好的联系方式',
                `daily_mail_enable` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '每日报告开关',
                `class` smallint(5) unsigned NOT NULL DEFAULT 0 COMMENT '等级',
                `class_expire` datetime NOT NULL DEFAULT '1989-06-04 00:05:00' COMMENT '等级过期时间',
                `theme` varchar(255) NOT NULL DEFAULT 'tabler' COMMENT '网站主题',
                `ga_token` varchar(255) NOT NULL DEFAULT '' COMMENT 'GA密钥',
                `ga_enable` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT 'GA开关',
                `remark` text NOT NULL DEFAULT '' COMMENT '备注',
                `node_group` smallint(6) unsigned NOT NULL DEFAULT 0 COMMENT '节点分组',
                `is_banned` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否封禁',
                `banned_reason` varchar(255) NOT NULL DEFAULT '' COMMENT '封禁理由',
                `is_shadow_banned` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否处于账户异常状态',
                `expire_notified` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '过期提醒',
                `traffic_notified` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '流量提醒',
                `auto_reset_day` smallint(6) unsigned NOT NULL DEFAULT 0 COMMENT '自动重置流量日',
                `auto_reset_bandwidth` decimal(12,2) unsigned NOT NULL DEFAULT 0 COMMENT '自动重置流量',
                `api_token` varchar(255) NOT NULL DEFAULT '' COMMENT 'API 密钥',
                `is_dark_mode` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否启用暗黑模式',
                `is_inactive` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '是否处于闲置状态',
                `locale` varchar(16) NOT NULL DEFAULT 'zh-TW' COMMENT '显示语言',
                PRIMARY KEY (`id`),
                UNIQUE KEY `email` (`email`),
                UNIQUE KEY `passwd` (`passwd`),
                UNIQUE KEY `uuid` (`uuid`),
                UNIQUE KEY `ga_token` (`ga_token`),
                UNIQUE KEY `api_token` (`api_token`),
                KEY `is_admin` (`is_admin`),
                KEY `contact_method` (`contact_method`),
                KEY `class` (`class`),
                KEY `class_expire` (`class_expire`),
                KEY `node_group` (`node_group`),
                KEY `is_banned` (`is_banned`),
                KEY `is_shadow_banned` (`is_shadow_banned`),
                KEY `is_inactive` (`is_inactive`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `user_coupon` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '优惠码ID',
                `code` varchar(255) NOT NULL DEFAULT '' COMMENT '优惠码',
                `content` longtext NOT NULL DEFAULT '{}' COMMENT '优惠码内容' CHECK (json_valid(`content`)),
                `limit` longtext NOT NULL DEFAULT '{}' COMMENT '优惠码限制' CHECK (json_valid(`limit`)),
                `use_count` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '累计使用次数',
                `create_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
                `expire_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '过期时间',
                PRIMARY KEY (`id`),
                UNIQUE KEY `code` (`code`),
                KEY `expire_time` (`expire_time`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE `user_invite_code` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID',
                `code` varchar(255) NOT NULL DEFAULT '' COMMENT '邀请码',
                `user_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '用户ID',
                PRIMARY KEY (`id`),
                UNIQUE KEY `code` (`code`),
                UNIQUE KEY `user_id` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            
            CREATE TABLE `user_money_log` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '记录ID',
                `user_id` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '用户ID',
                `before` decimal(12,2) NOT NULL DEFAULT 0 COMMENT '用户变动前账户余额',
                `after` decimal(12,2) NOT NULL DEFAULT 0 COMMENT '用户变动后账户余额',
                `amount` decimal(12,2) NOT NULL DEFAULT 0 COMMENT '变动总额',
                `remark` text NOT NULL DEFAULT '' COMMENT '备注',
                `create_time` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
                PRIMARY KEY (`id`),
                KEY `user_id` (`user_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
        );

        return 2023020100;
    }

    public function down(): int
    {
        return 2023020100;
    }
};
