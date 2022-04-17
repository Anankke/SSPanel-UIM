--
-- 用户订阅日志
--
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
