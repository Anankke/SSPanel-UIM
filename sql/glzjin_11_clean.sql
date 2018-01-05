DROP TABLE invite_code;

ALTER TABLE `user`
  DROP `node_class`,
  DROP `node_speed`,
  DROP `node_period`;
  
DROP TABLE ss_reset_pwd;

ALTER TABLE `ss_node`
  DROP `offset`,
  DROP `node_speed_sum`,
  DROP `node_ping`;CREATE TABLE `smartline` ( `id` BIGINT NOT NULL AUTO_INCREMENT , `node_class` BIGINT NOT NULL , `domain_prefix` TEXT NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;


INSERT INTO `ss_node` (`id`, `name`, `type`, `server`, `method`, `info`, `status`, `sort`, `custom_method`, `traffic_rate`, `node_class`, `node_speedlimit`, `node_connector`, `node_bandwidth`, `node_bandwidth_limit`, `bandwidthlimit_resetday`, `node_heartbeat`, `node_ip`) VALUES (NULL, '智能线路（速度） - Shadowsocks', '1', 'smart.zhaoj.in', 'aes-256-cfb', '智能线路，注重速度。', '可用', '0', '1', '1', '0', '0', '0', '0', '0', '0', '0', NULL);

INSERT INTO `ss_node` (`id`, `name`, `type`, `server`, `method`, `info`, `status`, `sort`, `custom_method`, `traffic_rate`, `node_class`, `node_speedlimit`, `node_connector`, `node_bandwidth`, `node_bandwidth_limit`, `bandwidthlimit_resetday`, `node_heartbeat`, `node_ip`) VALUES (NULL, '智能线路（延时） - Shadowsocks', '1', 'smart.zhaoj.in', 'aes-256-cfb', '智能线路，降低延时。', '可用', '0', '1', '1', '0', '0', '0', '0', '0', '0', '0', NULL);

ALTER TABLE `smartline` ADD `type` INT NULL DEFAULT '0' AFTER `domain_prefix`;

ALTER TABLE `smartline` ADD `t_node` INT NULL AFTER `type`, ADD `u_node` INT NULL AFTER `t_node`, ADD `c_node` INT NULL AFTER `u_node`;

ALTER TABLE `smartline` ADD `t_id` BIGINT NOT NULL AFTER `c_node`, ADD `u_id` BIGINT NOT NULL AFTER `t_id`, ADD `c_id` BIGINT NOT NULL AFTER `u_id`;

CREATE TABLE `blockip` ( `id` BIGINT NOT NULL AUTO_INCREMENT , `nodeid` INT NOT NULL , `ip` TEXT NOT NULL , `datetime` BIGINT NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

CREATE TABLE `unblockip` ( `id` BIGINT NOT NULL AUTO_INCREMENT , `ip` TEXT NOT NULL , `datetime` BIGINT NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB; 

ALTER TABLE `unblockip` ADD `userid` BIGINT NOT NULL AFTER `datetime`;
