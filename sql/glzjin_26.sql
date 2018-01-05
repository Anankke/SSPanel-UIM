CREATE TABLE `detect_list` ( `id` BIGINT NOT NULL AUTO_INCREMENT , `name` LONGTEXT NOT NULL , `text` LONGTEXT NOT NULL , `regex` LONGTEXT NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

CREATE TABLE `detect_log` ( `id` BIGINT NOT NULL AUTO_INCREMENT , `user_id` BIGINT NOT NULL , `list_id` BIGINT NOT NULL , `datetime` BIGINT NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

ALTER TABLE `detect_list` ADD `type` INT NOT NULL AFTER `regex`;

ALTER TABLE `detect_log` ADD `node_id` INT NOT NULL AFTER `datetime`;


