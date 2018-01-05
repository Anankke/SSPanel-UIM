ALTER TABLE `ss_node` ADD `custom_rss` INT NOT NULL DEFAULT '0' AFTER `node_group`;

ALTER TABLE `user` ADD `protocol` VARCHAR(128) NOT NULL DEFAULT 'origin' AFTER `relay_info`, ADD `protocol_param` VARCHAR(128) NULL DEFAULT NULL AFTER `protocol`, ADD `obfs` VARCHAR(128) NOT NULL DEFAULT 'plain' AFTER `protocol_param`, ADD `obfs_param` VARCHAR(128) NULL DEFAULT NULL AFTER `obfs`;



