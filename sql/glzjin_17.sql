ALTER TABLE `user` ADD `relay_enable` INT NOT NULL DEFAULT '0' AFTER `auto_reset_bandwidth`, ADD `relay_info` LONGTEXT NULL AFTER `relay_enable`;
