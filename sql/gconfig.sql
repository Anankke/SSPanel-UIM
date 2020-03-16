CREATE TABLE IF NOT EXISTS `gconfig` (
  `id`             int(11) unsigned NOT NULL AUTO_INCREMENT,
  `key`            varchar(128)     NOT NULL COMMENT '配置键名',
  `type`           varchar(32)      NOT NULL COMMENT '值类型',
  `value`          text             NOT NULL COMMENT '配置值',
  `oldvalue`       text             NOT NULL COMMENT '之前的配置值',
  `name`           varchar(128)     NOT NULL COMMENT '配置名称',
  `comment`        text             NOT NULL COMMENT '配置描述',
  `operator_id`    int(11)          NOT NULL COMMENT '操作员 ID',
  `operator_name`  varchar(128)     NOT NULL COMMENT '操作员名称',
  `operator_email` varchar(32)      NOT NULL COMMENT '操作员邮箱',
  `last_update`    bigint(20)       NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='网站配置';
