SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET time_zone = '+00:00';


CREATE TABLE IF NOT EXISTS `user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '用户名',
  `email` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'E-Mail',
  `pass` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '登录密码',
  `passwd` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '节点密码',
  `uuid` varchar(146) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'UUID',
  `t` bigint(20) unsigned NOT NULL DEFAULT 0,
  `u` bigint(20) unsigned NOT NULL DEFAULT 0,
  `d` bigint(20) unsigned NOT NULL DEFAULT 0,
  `transfer_enable` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '总流量',
  `port` int(11) NOT NULL COMMENT '用户端口',
  `enable` tinyint(4) NOT NULL DEFAULT 1 COMMENT '是否启用',
  `last_detect_ban_time` datetime DEFAULT '1989-06-04 00:05:00' COMMENT '最后一次被封禁的时间',
  `all_detect_number` int(11) NOT NULL DEFAULT 0 COMMENT '累计违规次数',
  `last_check_in_time` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '最后签到时间',
  `reg_date` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT '注册时间',
  `invite_num` int(8) NOT NULL DEFAULT 0 COMMENT '可用邀请次数',
  `money` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT '钱包余额',
  `ref_by` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '邀请人ID',
  `method` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'rc4-md5' COMMENT 'SS/SSR加密方式',
  `reg_ip` varchar(182) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '127.0.0.1' COMMENT '注册IP',
  `node_speedlimit` decimal(12,2) NOT NULL DEFAULT 0.00,
  `node_connector` int(11) NOT NULL DEFAULT 0,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否管理员',
  `im_type` int(11) DEFAULT 1 COMMENT '联系方式类型',
  `im_value` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '联系方式',
  `last_day_t` bigint(20) NOT NULL DEFAULT 0 COMMENT '今天之前已使用的流量',
  `sendDailyMail` tinyint(1) NOT NULL DEFAULT 0 COMMENT '每日报告开关',
  `class` int(11) NOT NULL DEFAULT 0 COMMENT '用户等级',
  `class_expire` datetime NOT NULL DEFAULT '1989-06-04 00:05:00' COMMENT '等级过期时间',
  `expire_in` datetime NOT NULL DEFAULT '2099-06-04 00:05:00',
  `theme` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '网站主题',
  `ga_token` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ga_enable` int(11) NOT NULL DEFAULT 0,
  `remark` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '备注',
  `node_group` int(11) NOT NULL DEFAULT 0 COMMENT '节点分组',
  `protocol` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT 'origin' COMMENT 'SS/SSR协议方式',
  `protocol_param` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `obfs` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT 'plain' COMMENT 'SS/SSR混淆方式',
  `obfs_param` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_hide` int(11) NOT NULL DEFAULT 0,
  `is_multi_user` int(11) NOT NULL DEFAULT 0,
  `telegram_id` bigint(20) DEFAULT NULL,
  `expire_notified` tinyint(1) NOT NULL DEFAULT 0,
  `traffic_notified` tinyint(1) DEFAULT 0,
  `forbidden_ip` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `forbidden_port` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT '''''',
  `auto_reset_day` int(11) NOT NULL DEFAULT 0,
  `auto_reset_bandwidth` decimal(12,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `ga_token` (`ga_token`),
  KEY `user_name` (`user_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `ss_node` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` int(3) NOT NULL,
  `server` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL,
  `method` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `info` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `status` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `sort` int(3) NOT NULL,
  `custom_method` tinyint(1) NOT NULL DEFAULT 0,
  `traffic_rate` float NOT NULL DEFAULT 1,
  `node_class` int(11) NOT NULL DEFAULT 0,
  `node_speedlimit` decimal(12,2) NOT NULL DEFAULT 0.00,
  `node_connector` int(11) NOT NULL DEFAULT 0,
  `node_bandwidth` bigint(20) NOT NULL DEFAULT 0,
  `node_bandwidth_limit` bigint(20) NOT NULL DEFAULT 0,
  `bandwidthlimit_resetday` int(11) NOT NULL DEFAULT 0,
  `node_heartbeat` bigint(20) NOT NULL DEFAULT 0,
  `node_ip` varchar(182) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `node_group` int(11) NOT NULL DEFAULT 0,
  `custom_rss` int(11) NOT NULL DEFAULT 0,
  `mu_only` int(11) DEFAULT 0,
  `online` tinyint(1) NOT NULL DEFAULT 1,
  `gfw_block` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `alive_ip` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `nodeid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `ip` varchar(182) COLLATE utf8mb4_unicode_ci NOT NULL,
  `datetime` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `announcement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `markdown` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `blockip` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `nodeid` int(11) NOT NULL,
  `ip` varchar(182) COLLATE utf8mb4_unicode_ci NOT NULL,
  `datetime` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `bought` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `userid` bigint(20) NOT NULL,
  `shopid` bigint(20) NOT NULL,
  `datetime` bigint(20) NOT NULL,
  `renew` bigint(11) NOT NULL,
  `coupon` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `is_notified` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `code` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `code` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` int(11) NOT NULL,
  `number` decimal(11,2) NOT NULL,
  `isused` int(11) NOT NULL DEFAULT 0,
  `userid` bigint(20) NOT NULL,
  `usedatetime` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `coupon` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `code` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `onetime` int(11) NOT NULL,
  `expire` bigint(20) NOT NULL,
  `shop` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `credit` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `detect_ban_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '用户名',
  `user_id` bigint(20) unsigned NOT NULL COMMENT '用户 ID',
  `email` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '用户邮箱',
  `detect_number` int(11) NOT NULL COMMENT '本次违规次数',
  `ban_time` int(11) NOT NULL COMMENT '本次封禁时长',
  `start_time` bigint(20) NOT NULL COMMENT '统计开始时间',
  `end_time` bigint(20) NOT NULL COMMENT '统计结束时间',
  `all_detect_number` int(11) NOT NULL COMMENT '累计违规次数',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `detect_ban_log_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='审计封禁日志';


CREATE TABLE IF NOT EXISTS `detect_list` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `text` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `regex` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `detect_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `list_id` bigint(20) unsigned NOT NULL,
  `datetime` bigint(20) unsigned NOT NULL,
  `node_id` int(11) NOT NULL,
  `status` int(2) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `node_id` (`node_id`),
  KEY `list_id` (`list_id`),
  CONSTRAINT `detect_log_ibfk_4` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detect_log_ibfk_5` FOREIGN KEY (`node_id`) REFERENCES `ss_node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detect_log_ibfk_6` FOREIGN KEY (`list_id`) REFERENCES `detect_list` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `email_queue` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `to_email` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `template` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `array` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `time` int(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Email Queue 發件列表';


CREATE TABLE IF NOT EXISTS `email_verify` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `email` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip` varchar(182) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `expire_in` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `gconfig` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '配置键名',
  `type` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '值类型',
  `value` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '配置值',
  `oldvalue` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '之前的配置值',
  `name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '配置名称',
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '配置描述',
  `operator_id` int(11) NOT NULL COMMENT '操作员 ID',
  `operator_name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '操作员名称',
  `operator_email` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '操作员邮箱',
  `last_update` bigint(20) NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='网站配置';


CREATE TABLE IF NOT EXISTS `link` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `token` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `userid` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  UNIQUE KEY `userid` (`userid`),
  CONSTRAINT `link_ibfk_2` FOREIGN KEY (`userid`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `login_ip` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `userid` bigint(20) unsigned NOT NULL,
  `ip` varchar(182) COLLATE utf8mb4_unicode_ci NOT NULL,
  `datetime` bigint(20) NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  CONSTRAINT `login_ip_ibfk_2` FOREIGN KEY (`userid`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `payback` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `total` decimal(12,2) NOT NULL,
  `userid` bigint(20) NOT NULL,
  `ref_by` bigint(20) NOT NULL,
  `ref_get` decimal(12,2) NOT NULL,
  `datetime` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `paylist` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `userid` bigint(20) unsigned NOT NULL,
  `total` decimal(12,2) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `tradeno` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `datetime` bigint(20) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  CONSTRAINT `paylist_ibfk_2` FOREIGN KEY (`userid`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `shop` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `auto_renew` int(11) NOT NULL,
  `auto_reset_bandwidth` int(11) NOT NULL DEFAULT 0,
  `status` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `ss_invite_code` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT '2016-06-01 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `ss_invite_code_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `ss_node_info` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `node_id` int(11) NOT NULL,
  `uptime` float NOT NULL,
  `load` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `log_time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `node_id` (`node_id`),
  CONSTRAINT `ss_node_info_ibfk_2` FOREIGN KEY (`node_id`) REFERENCES `ss_node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `ss_node_online_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `node_id` int(11) NOT NULL,
  `online_user` int(11) NOT NULL,
  `log_time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `node_id` (`node_id`),
  CONSTRAINT `ss_node_online_log_ibfk_3` FOREIGN KEY (`node_id`) REFERENCES `ss_node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `ss_password_reset` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `init_time` int(11) NOT NULL,
  `expire_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `telegram_session` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `type` int(11) NOT NULL,
  `session_content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `datetime` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `ticket` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `rootid` bigint(20) NOT NULL,
  `userid` bigint(20) NOT NULL,
  `datetime` bigint(20) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `unblockip` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `ip` varchar(182) COLLATE utf8mb4_unicode_ci NOT NULL,
  `datetime` bigint(20) NOT NULL,
  `userid` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `user_hourly_usage` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `traffic` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hourly_usage` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `datetime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_hourly_usage_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `user_subscribe_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_name` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '用户名',
  `user_id` bigint(20) unsigned NOT NULL COMMENT '用户 ID',
  `email` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '用户邮箱',
  `subscribe_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '获取的订阅类型',
  `request_ip` varchar(182) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '请求 IP',
  `request_time` datetime NOT NULL COMMENT '请求时间',
  `request_user_agent` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '请求 UA 信息',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_subscribe_log_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户订阅日志';


CREATE TABLE IF NOT EXISTS `user_token` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `token` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `create_time` bigint(20) unsigned NOT NULL,
  `expire_time` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_token_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
