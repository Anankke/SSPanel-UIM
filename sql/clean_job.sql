ALTER TABLE `bought` ADD `is_renewed` BOOLEAN NOT NULL DEFAULT FALSE AFTER `price`
    ,CHANGE `is_renewed` `is_notified` BOOLEAN NOT NULL DEFAULT FALSE;
ALTER TABLE `ss_node` ADD `online` BOOLEAN NOT NULL DEFAULT TRUE AFTER `mu_only`
    ,ADD `gfw_block` BOOLEAN NOT NULL DEFAULT FALSE AFTER `online`;
ALTER TABLE `user` ADD `expire_notified` BOOLEAN NOT NULL DEFAULT FALSE AFTER `telegram_id`
    ,ADD `traffic_notified` BOOLEAN NULL DEFAULT FALSE AFTER `expire_notified`;
