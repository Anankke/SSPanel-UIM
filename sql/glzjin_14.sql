
ALTER TABLE `announcement` ADD `markdown` LONGTEXT NOT NULL AFTER `content`;

ALTER TABLE `announcement` CHANGE `content` `content` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

CREATE TABLE `ticket` ( `id` BIGINT NOT NULL AUTO_INCREMENT , `title` LONGTEXT NOT NULL , `content` LONGTEXT NOT NULL , `rootid` BIGINT NOT NULL , `userid` BIGINT NOT NULL , `datetime` BIGINT NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

ALTER TABLE `ticket` ADD `status` INT NOT NULL DEFAULT '1' AFTER `datetime`;

UPDATE `user` SET `theme` = 'material';