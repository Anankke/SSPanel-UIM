ALTER TABLE `user` ADD `node_group` INT NOT NULL DEFAULT '0' AFTER `remark`;

ALTER TABLE `ss_node` ADD `node_group` INT NOT NULL DEFAULT '0' AFTER `node_ip`;


ALTER TABLE `smartline` ADD `node_group` INT NOT NULL DEFAULT '0' AFTER `c_id`;