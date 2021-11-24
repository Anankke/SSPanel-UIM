-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2021-11-05 23:58:53
-- 服务器版本： 10.2.38-MariaDB-log
-- PHP 版本： 7.4.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `sspanel2`
--

-- --------------------------------------------------------

--
-- 表的结构 `config`
--

CREATE TABLE `config` (
  `id` int(11) NOT NULL COMMENT '主键',
  `item` text NOT NULL COMMENT '项',
  `value` text NOT NULL COMMENT '值',
  `class` varchar(16) NOT NULL DEFAULT 'default' COMMENT '配置分类',
  `is_public` int(11) NOT NULL DEFAULT 0 COMMENT '是否为公共参数',
  `type` text NOT NULL COMMENT '值类型',
  `default` text NOT NULL COMMENT '默认值',
  `mark` text NOT NULL COMMENT '备注'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- 转存表中的数据 `config`
--

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
(120, 'rebate_time_range_limit', '180', 'invite', '0', 'int', '180', '返利时间范围限制（单位：天）');

--
-- 转储表的索引
--

--
-- 表的索引 `config`
--
ALTER TABLE `config`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `config`
--
ALTER TABLE `config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键', AUTO_INCREMENT=121;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
