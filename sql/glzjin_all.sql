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
  `reg_date` datetime NOT NULL COMMENT '注册时间',
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


CREATE TABLE IF NOT EXISTS `node` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `type` int(3) NOT NULL,
  `server` varchar(512) NOT NULL,
  `custom_config` text NOT NULL,
  `info` varchar(128) NOT NULL DEFAULT '',
  `status` varchar(128) NOT NULL DEFAULT '',
  `sort` int(3) NOT NULL,
  `traffic_rate` float NOT NULL DEFAULT 1,
  `node_class` int(11) NOT NULL DEFAULT 0,
  `node_speedlimit` decimal(12,2) NOT NULL DEFAULT 0.00,
  `node_connector` int(11) NOT NULL DEFAULT 0,
  `node_bandwidth` bigint(20) NOT NULL DEFAULT 0,
  `node_bandwidth_limit` bigint(20) NOT NULL DEFAULT 0,
  `bandwidthlimit_resetday` int(11) NOT NULL DEFAULT 0,
  `node_heartbeat` bigint(20) NOT NULL DEFAULT 0,
  `node_ip` varchar(182) DEFAULT NULL,
  `node_group` int(11) NOT NULL DEFAULT 0,
  `mu_only` tinyint(1) DEFAULT 0,
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


CREATE TABLE IF NOT EXISTS `config` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `item` text NOT NULL COMMENT '项',
  `value` text NOT NULL COMMENT '值',
  `class` varchar(16) NOT NULL DEFAULT 'default' COMMENT '配置分类',
  `is_public` int(11) NOT NULL DEFAULT 0 COMMENT '是否为公共参数',
  `type` text NOT NULL COMMENT '值类型',
  `default` text NOT NULL COMMENT '默认值',
  `mark` text NOT NULL COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


INSERT INTO `config` (`id`, `item`, `value`, `class`, `is_public`, `type`, `default`, `mark`) VALUES
(1, 'payment_gateway', '[]', 'pay', 1, 'array', '[]', '支付网关'),
(2, 'f2f_pay_app_id', '', 'f2f', 0, 'string', '', '当面付AppID'),
(3, 'f2f_pay_pid', '', 'f2f', 0, 'string', '', '当面付PID'),
(4, 'f2f_pay_public_key', '', 'f2f', 0, 'string', '', '当面付公钥'),
(5, 'f2f_pay_private_key', '', 'f2f', 0, 'string', '', '当面付私钥'),
(6, 'f2f_pay_notify_url', '', 'f2f', 0, 'string', '', '当面付自定义回调地址'),
(9, 'vmq_gateway', '', 'vmq', 0, 'string', '', 'V免签网关'),
(10, 'vmq_key', '', 'vmq', 0, 'string', '', 'V免签密钥'),
(16, 'smtp_host', '', 'smtp', 0, 'string', '', 'smtp发信主机'),
(17, 'smtp_username', '', 'smtp', 0, 'string', '', 'smtp账户名称'),
(18, 'smtp_password', '', 'smtp', 0, 'string', '', 'smtp账户密码'),
(19, 'smtp_port', '', 'smtp', 0, 'string', '', 'smtp发信端口'),
(20, 'smtp_name', '', 'smtp', 0, 'string', '', 'smtp发信名称'),
(21, 'smtp_sender', '', 'smtp', 0, 'string', '', 'smtp发信地址'),
(22, 'smtp_ssl', '1', 'smtp', 0, 'bool', '1', '是否使用 TLS/SSL 发信'),
(23, 'smtp_bbc', '', 'smtp', 0, 'string', '', '发给用户的邮件密送给指定邮箱备份'),
(28, 'mail_driver', 'none', 'mail', 0, 'string', 'none', '邮件服务提供商'),
(29, 'captcha_provider', 'recaptcha', 'captcha', 1, 'string', 'recaptcha', '验证码提供商'),
(30, 'enable_reg_captcha', '0', 'captcha', 1, 'bool', '0', '注册验证码'),
(31, 'enable_login_captcha', '0', 'captcha', 1, 'bool', '0', '登录验证码'),
(32, 'enable_checkin_captcha', '0', 'captcha', 1, 'bool', '0', '签到验证码'),
(33, 'recaptcha_sitekey', '', 'recaptcha', 0, 'string', '', 'reCaptcha网站密钥'),
(34, 'recaptcha_secret', '', 'recaptcha', 0, 'string', '', 'reCaptcha密钥'),
(35, 'geetest_id', '', 'geetest', 0, 'string', '', '极验id'),
(36, 'geetest_key', '', 'geetest', 0, 'string', '', '极验密钥'),
(37, 'mailgun_key', '', 'mailgun', 0, 'string', '', 'mailgun密钥'),
(38, 'mailgun_domain', '', 'mailgun', 0, 'string', '', 'mailgun域名'),
(39, 'mailgun_sender', '', 'mailgun', 0, 'string', '', 'mailgun发送者'),
(40, 'sendgrid_key', '', 'sendgrid', 0, 'string', '', 'sendgrid密钥'),
(41, 'sendgrid_sender', '', 'sendgrid', 0, 'string', '', 'sendgrid发件邮箱'),
(42, 'sendgrid_name', '', 'sendgrid', 0, 'string', '', 'sendgrid发件人名称'),
(43, 'aws_access_key_id', '', 'aws_ses', 0, 'string', '', 'aws密钥id'),
(44, 'aws_secret_access_key', '', 'aws_ses', 0, 'string', '', 'aws密钥key'),
(45, 'auto_backup_email', '', 'backup', 0, 'string', '', '接收备份的邮箱'),
(46, 'auto_backup_password', '', 'backup', 0, 'string', '', '备份的压缩密码'),
(47, 'payjs_mchid', '', 'payjs', 0, 'string', '', 'payjs_mchid'),
(48, 'payjs_key', '', 'payjs', 0, 'string', '', 'payjs_key'),
(49, 'enable_admin_contact', '0', 'contact', 1, 'bool', '0', '是否显示站长联系方式'),
(50, 'admin_contact1', 'qq', 'contact', 1, 'string', 'qq', '站长联系方式一'),
(51, 'admin_contact2', 'mail', 'contact', 1, 'string', 'mail', '站长联系方式二'),
(52, 'admin_contact3', 'telegram', 'contact', 1, 'string', 'telegram', '站长联系方式三'),
(53, 'tawk_id', '', 'live_chat', 1, 'string', '', 'tawk_id'),
(54, 'crisp_id', '', 'live_chat', 1, 'string', '', 'crisp_id'),
(55, 'livechat_id', '', 'live_chat', 1, 'string', '', 'livechat_id'),
(56, 'mylivechat_id', '', 'live_chat', 1, 'string', '', 'mylivechat_id'),
(57, 'live_chat', 'none', 'live_chat', 1, 'string', 'none', '客服系统开关'),
(64, 'theadpay_url', '', 'theadpay', 0, 'string', '', 'theadpay_url'),
(65, 'theadpay_mchid', '', 'theadpay', 0, 'string', '', 'theadpay_mchid'),
(66, 'theadpay_key', '', 'theadpay', 0, 'string', '', 'theadpay_key'),
(69, 'coinpay_appid', '', 'coinpay', 0, 'string', '', 'CoinPay应用ID'),
(70, 'coinpay_secret', '', 'coinpay', 0, 'string', '', 'CoinPay验证密钥'),
(73, 'user_center_bg', '0', 'personalise', 1, 'bool', '0', '是否启用自定义用户中心背景图片'),
(74, 'admin_center_bg', '0', 'personalise', 1, 'bool', '0', '是否启用自定义管理中心背景图片'),
(75, 'user_center_bg_addr', '/theme/material/css/images/bg/amber.jpg', 'personalise', 1, 'string', '/theme/material/css/images/bg/amber.jpg', '用户中心背景图片地址'),
(76, 'admin_center_bg_addr', '/theme/material/css/images/bg/amber.jpg', 'personalise', 1, 'string', '/theme/material/css/images/bg/amber.jpg', '管理中心背景图片地址'),
(83, 'pmw_publickey', '', 'pmw', 0, 'string', '', 'pmw公钥'),
(84, 'pmw_privatekey', '', 'pmw', 0, 'string', '', 'pmw私钥'),
(85, 'pmw_widget', 'm2_1', 'pmw', 0, 'string', 'm2_1', 'pmw_widget'),
(86, 'pmw_height', '350px', 'pmw', 0, 'string', '350px', 'pmw_height'),
(90, 'auto_backup_notify', '0', 'backup', 0, 'bool', '0', '备份是否通知到TG群中'),
(91, 'reg_mode', 'open', 'register', 1, 'string', 'open', '注册模式'),
(92, 'reg_email_verify', '0', 'register', 1, 'bool', '0', '邮箱验证'),
(93, 'email_verify_ttl', '3600', 'register', 0, 'int', '3600', '邮箱验证码有效期'),
(94, 'email_verify_ip_limit', '5', 'register', 0, 'int', '5', '验证码有效期内单个ip可请求的发信次数'),
(95, 'sign_up_for_free_traffic', '20', 'register', 0, 'int', '20', '注册时赠送的流量（单位：GB）'),
(96, 'sign_up_for_free_time', '7', 'register', 0, 'int', '7', '注册时赠送的时长（单位：天）'),
(99, 'connection_device_limit', '0', 'register', 0, 'int', '0', '连接设备限制'),
(100, 'connection_rate_limit', '0', 'register', 0, 'int', '0', '使用速率限制'),
(101, 'sign_up_for_class', '0', 'register', 0, 'int', '0', '注册时设定的等级'),
(102, 'sign_up_for_class_time', '7', 'register', 0, 'int', '7', '注册时设定的等级过期时间（单位：天）'),
(103, 'sign_up_for_method', 'chacha20-ietf', 'register', 0, 'string', 'chacha20-ietf', '默认加密'),
(104, 'sign_up_for_protocol', 'auth_aes128_sha1', 'register', 0, 'string', 'auth_aes128_sha1', '默认协议'),
(105, 'sign_up_for_obfs', 'http_simple', 'register', 0, 'string', 'http_simple', '默认混淆'),
(106, 'sign_up_for_protocol_param', '', 'register', 0, 'string', '', '默认协议参数'),
(107, 'sign_up_for_obfs_param', 'www.jd.hk', 'register', 0, 'string', 'www.jd.hk', '默认混淆参数'),
(108, 'sign_up_for_daily_report', '0', 'register', 0, 'bool', '0', '注册后是否默认接收每日用量邮件推送'),
(112, 'sign_up_for_invitation_codes', '10', 'register', 0, 'int', '10', '初始邀请注册链接使用次数限制'),
(113, 'invitation_to_register_balance_reward', '1', 'invite', 1, 'int', '1', '邀请注册余额奖励（单位：元）'),
(114, 'invitation_to_register_traffic_reward', '10', 'invite', 1, 'int', '10', '邀请注册流量奖励（单位：GB）'),
(115, 'invitation_mode', 'after_recharge', 'invite', 0, 'string', 'after_purchase', '邀请模式'),
(116, 'invite_rebate_mode', 'limit_frequency', 'invite', 0, 'string', 'limit_amount', '返利模式'),
(117, 'rebate_frequency_limit', '6', 'invite', 0, 'string', '3', '返利总次数限制'),
(118, 'rebate_amount_limit', '9', 'invite', 0, 'int', '100', '返利总金额限制'),
(119, 'rebate_ratio', '0.2', 'invite', 1, 'string', '0.2', '返利比例'),
(120, 'rebate_time_range_limit', '180', 'invite', '0', 'int', '180', '返利时间范围限制（单位：天）'),
(121, 'stripe_currency', 'GBP', 'stripe', 0, 'string', 'HKD', '货币代码'),
(122, 'stripe_sk', 'stripe_sk', 'stripe', 0, 'string', 'stripe_sk', 'stripe_sk'),
(123, 'stripe_pk', 'stripe_pk', 'stripe', 0, 'string', 'stripe_pk', 'stripe_pk'),
(124, 'stripe_webhook_key', 'stripe_webhook_key', 'stripe', 0, 'string', 'stripe_webhook_key', 'web_hook密钥'),
(125, 'stripe_min_recharge', '10', 'stripe', 1, 'int', '10', '最低充值限额'),
(126, 'stripe_card', '0', 'stripe', 0, 'bool', '0', '银行卡支付'),
(127, 'stripe_alipay', '0', 'stripe', 0, 'bool', '0', '支付宝支付'),
(128, 'stripe_wechat', '0', 'stripe', 0, 'bool', '0', '微信支付'),
(129, 'stripe_max_recharge', '1000', 'stripe', 1, 'int', '1000', '最高充值限额');


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
  CONSTRAINT `detect_log_ibfk_5` FOREIGN KEY (`node_id`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
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


CREATE TABLE IF NOT EXISTS `user_invite_code` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT '2016-06-01 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  UNIQUE KEY `user_id` (`user_id`),
  CONSTRAINT `user_invite_code_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `node_info` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `node_id` int(11) NOT NULL,
  `uptime` float NOT NULL,
  `load` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `log_time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `node_id` (`node_id`),
  CONSTRAINT `node_info_ibfk_2` FOREIGN KEY (`node_id`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `node_online_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `node_id` int(11) NOT NULL,
  `online_user` int(11) NOT NULL,
  `log_time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `node_id` (`node_id`),
  CONSTRAINT `node_online_log_ibfk_3` FOREIGN KEY (`node_id`) REFERENCES `node` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `user_password_reset` (
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
  `traffic` bigint(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hourly_usage` bigint(20) COLLATE utf8mb4_unicode_ci NOT NULL,
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
