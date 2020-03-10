CREATE TABLE IF NOT EXISTS `telegram_tasks` (
  `id`           int(11) unsigned NOT     NULL AUTO_INCREMENT,
  `type`         int(8)           NOT     NULL                COMMENT '任务类型',
  `status`       int(2)           NOT     NULL DEFAULT '0'    COMMENT '任务状态',
  `chatid`       varchar(128)     NOT     NULL DEFAULT '0'    COMMENT 'Telegram Chat ID',
  `messageid`    varchar(128)     NOT     NULL DEFAULT '0'    COMMENT 'Telegram Message ID',
  `content`      text             DEFAULT NULL                COMMENT '任务详细内容',
  `process`      varchar(32)      DEFAULT NULL                COMMENT '临时任务进度',
  `userid`       int(11)          NOT     NULL DEFAULT '0'    COMMENT '网站用户 ID',
  `tguserid`     varchar(32)      NOT     NULL DEFAULT '0'    COMMENT 'Telegram User ID',
  `executetime`  bigint(20)       NOT     NULL                COMMENT '任务执行时间',
  `datetime`     bigint(20)       NOT     NULL                COMMENT '任务产生时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Telegram 任务列表';
