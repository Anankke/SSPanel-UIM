CREATE TABLE `alive_ip` ( `id` BIGINT NOT NULL AUTO_INCREMENT , `userid` INT NOT NULL , `ip` TEXT NOT NULL , `datetime` BIGINT NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

ALTER TABLE `alive_ip` ADD `nodeid` INT NOT NULL AFTER `id`;


