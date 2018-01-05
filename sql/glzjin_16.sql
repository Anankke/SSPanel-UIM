ALTER TABLE `user` ADD `auto_reset_day` INT NOT NULL DEFAULT '0' AFTER `node_group`, ADD `auto_reset_bandwidth` DECIMAL(12,2) NOT NULL DEFAULT '0.00' AFTER `auto_reset_day`;

ALTER TABLE `shop` ADD `auto_reset_bandwidth` INT NOT NULL DEFAULT '0' AFTER `auto_renew`;

ALTER TABLE `code` CHANGE `number` `number` DECIMAL(11,2) NOT NULL;

CREATE TABLE `auto` ( `id` BIGINT NOT NULL AUTO_INCREMENT , `type` INT NOT NULL , `value` LONGTEXT NOT NULL , `datetime` BIGINT NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;


ALTER TABLE `auto` ADD `sign` LONGTEXT NOT NULL AFTER `value`;
