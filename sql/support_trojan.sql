ALTER TABLE `user`
	ADD COLUMN `uuid` varchar(146) NOT NULL COMMENT 'UUID' AFTER `passwd`;