--
-- 用户订阅日志
--
CREATE TABLE IF NOT EXISTS `user_subscribe_log` (
  `id`                 int(11) unsigned     NOT NULL AUTO_INCREMENT,
  `user_name`          varchar(128)         NOT NULL COMMENT '用户名',
  `user_id`            int(11)              NOT NULL COMMENT '用户 ID',
  `email`              varchar(32)          NOT NULL COMMENT '用户邮箱',
  `subscribe_type`     varchar(20)      DEFAULT NULL COMMENT '获取的订阅类型',
  `request_ip`         varchar(128)     DEFAULT NULL COMMENT '请求 IP',
  `request_time`       datetime         DEFAULT NULL COMMENT '请求时间',
  `request_user_agent` text                          COMMENT '请求 UA 信息',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='用户订阅日志';
