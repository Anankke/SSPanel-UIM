CREATE TABLE IF NOT EXISTS `announcement` (
  `id` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `content` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `announcement`
  ADD PRIMARY KEY (`id`);

CREATE TABLE IF NOT EXISTS `code` (
  `id` bigint(20) NOT NULL,
  `code` text NOT NULL,
  `type` int(11) NOT NULL,
  `number` int(11) NOT NULL,
  `isused` int(11) NOT NULL DEFAULT '0',
  `userid` bigint(20) NOT NULL,
  `usedatetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `code`
  ADD PRIMARY KEY (`id`);
  
CREATE TABLE IF NOT EXISTS `radius_ban` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `radius_ban`
  ADD PRIMARY KEY (`id`);
  
ALTER TABLE `ss_node` ADD `node_class` INT NOT NULL DEFAULT '0' AFTER `offset`, ADD `node_speedlimit` INT NOT NULL DEFAULT '0' AFTER `node_class`, ADD `node_connector` INT NOT NULL DEFAULT '0' AFTER `node_speedlimit`, ADD `node_bandwidth` BIGINT NOT NULL DEFAULT '0' AFTER `node_connector`, ADD `node_bandwidth_limit` BIGINT NOT NULL DEFAULT '0' AFTER `node_bandwidth`, ADD `bandwidthlimit_resetday` INT NOT NULL DEFAULT '0' AFTER `node_bandwidth_limit`, ADD `node_speed_sum` BIGINT NOT NULL DEFAULT '0' AFTER `bandwidthlimit_resetday`, ADD `node_heartbeat` BIGINT NOT NULL DEFAULT '0' AFTER `node_speed_sum`, ADD `node_ping` INT NOT NULL DEFAULT '0' AFTER `node_heartbeat`, ADD `node_ip` TEXT NULL DEFAULT NULL AFTER `node_ping`;


DROP TABLE IF EXISTS `ss_node_info`;
CREATE TABLE `ss_node_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `node_id` int(11) NOT NULL,
  `uptime` float NOT NULL,
  `load` varchar(32) NOT NULL,
  `log_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `user` ADD `plan` TEXT NULL DEFAULT NULL AFTER `reg_ip`, ADD `money` DECIMAL(12,2) NOT NULL DEFAULT '0' AFTER `plan`, ADD `node_class` TEXT NULL DEFAULT NULL AFTER `money`, ADD `node_speedlimit` TEXT NULL DEFAULT NULL AFTER `node_class`, ADD `node_connector` INT NOT NULL DEFAULT '0' AFTER `node_speedlimit`, ADD `node_speed` INT NOT NULL DEFAULT '0' AFTER `node_connector`, ADD `node_period` BIGINT NOT NULL DEFAULT '0' AFTER `node_speed`, ADD `wechat` INT NULL DEFAULT NULL AFTER `node_period`, ADD `last_day_t` INT NOT NULL DEFAULT '0' AFTER `wechat`, ADD `sendDailyMail` INT NOT NULL DEFAULT '1' AFTER `last_day_t`, ADD `class` INT NOT NULL DEFAULT '0' AFTER `sendDailyMail`, ADD `class_expire` DATETIME NOT NULL DEFAULT '1989-06-04 00:05:00' AFTER `class`, ADD `expire_in` DATETIME NOT NULL DEFAULT '2099-06-04 00:05:00' AFTER `class_expire`, ADD `theme` TEXT NULL DEFAULT NULL AFTER `expire_in`;

