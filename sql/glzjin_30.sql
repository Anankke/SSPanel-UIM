CREATE TABLE `telegram_session` ( `id` BIGINT NOT NULL AUTO_INCREMENT , `user_id` BIGINT NOT NULL , `type` INT NOT NULL , `session_content` TEXT NOT NULL , `datetime` BIGINT NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;

ALTER TABLE `user` ADD `telegram_id` BIGINT NULL AFTER `is_multi_user`; 
