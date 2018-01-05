ALTER TABLE `user` ADD `remark` TEXT NULL AFTER `pac`; 

ALTER TABLE `user` ADD `im_type` INT NULL DEFAULT '1' AFTER `is_admin`; 

ALTER TABLE `user` CHANGE `wechat` `im_value` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL;