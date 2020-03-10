--
-- 审计封禁日志
--
CREATE TABLE IF NOT EXISTS `detect_ban_log` (
  `id`                int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_name`         varchar(128)     NOT NULL COMMENT '用户名',
  `user_id`           int(11)          NOT NULL COMMENT '用户 ID',
  `email`             varchar(32)      NOT NULL COMMENT '用户邮箱',
  `detect_number`     int(11)          NOT NULL COMMENT '本次违规次数',
  `ban_time`          int(11)          NOT NULL COMMENT '本次封禁时长',
  `start_time`        bigint(20)       NOT NULL COMMENT '统计开始时间',
  `end_time`          bigint(20)       NOT NULL COMMENT '统计结束时间',
  `all_detect_number` int(11)          NOT NULL COMMENT '累计违规次数',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='审计封禁日志';


ALTER TABLE `user` ADD `last_detect_ban_time` datetime DEFAULT '1989-06-04 00:05:00' AFTER `enable`;
ALTER TABLE `user` ADD `all_detect_number` int(11) NOT NULL DEFAULT '0' AFTER `last_detect_ban_time`;
