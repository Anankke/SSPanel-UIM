CREATE TABLE `link` ( `id` BIGINT NOT NULL AUTO_INCREMENT , `type` INT NOT NULL , `address` TEXT NOT NULL , `port` INT NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;
ALTER TABLE `link` ADD `token` TEXT NOT NULL AFTER `port`; 
ALTER TABLE `link` ADD `ios` INT NOT NULL DEFAULT '0' AFTER `port`;
ALTER TABLE `link` ADD `userid` BIGINT NOT NULL AFTER `ios`; 
ALTER TABLE `user` ADD `pac` LONGTEXT NULL AFTER `ga_enable`; 
ALTER TABLE `link` ADD `isp` TEXT NULL AFTER `userid`, ADD `geo` INT NULL AFTER `isp`;
ALTER TABLE `link` ADD `method` TEXT NULL AFTER `geo`; 