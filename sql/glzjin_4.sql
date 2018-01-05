ALTER TABLE `user` ADD `theme` TEXT NOT NULL DEFAULT '' AFTER `expire_in`;
UPDATE `user` SET `theme` = 'phantom' ;

DROP TABLE IF EXISTS `ss_node_info`;
CREATE TABLE `ss_node_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `node_id` int(11) NOT NULL,
  `uptime` float NOT NULL,
  `load` varchar(32) NOT NULL,
  `log_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;