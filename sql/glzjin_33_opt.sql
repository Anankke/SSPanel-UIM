TRUNCATE `alive_ip`;

ALTER TABLE `alive_ip` ADD INDEX(`userid`);


ALTER TABLE `alive_ip` CHANGE `ip` `ip` CHAR(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;


TRUNCATE `user_traffic_log`;

ALTER TABLE `telegram_session` CHANGE `session_content` `session_content` CHAR(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL; 


ALTER TABLE `speedtest` CHANGE `telecomping` `telecomping` CHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `telecomeupload` `telecomeupload` CHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `telecomedownload` `telecomedownload` CHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `unicomping` `unicomping` CHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `unicomupload` `unicomupload` CHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `unicomdownload` `unicomdownload` CHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `cmccping` `cmccping` CHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `cmccupload` `cmccupload` CHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `cmccdownload` `cmccdownload` CHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

TRUNCATE `email_verify`;

ALTER TABLE `email_verify` CHANGE `email` `email` CHAR(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `ip` `ip` CHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `code` `code` CHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

TRUNCATE `disconnect_ip`;

ALTER TABLE `disconnect_ip` CHANGE `ip` `ip` CHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL; 

TRUNCATE `blockip`;

ALTER TABLE `blockip` CHANGE `ip` `ip` CHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `blockip` ADD INDEX(`ip`);

ALTER TABLE `bought` ADD INDEX(`userid`);

ALTER TABLE `code` ADD INDEX(`userid`);

ALTER TABLE `detect_log` ADD INDEX(`user_id`);

ALTER TABLE `blockip` ADD INDEX(`nodeid`);

ALTER TABLE `email_verify` ADD INDEX(`email`);
ALTER TABLE `email_verify` ADD INDEX(`ip`);
ALTER TABLE `email_verify` ADD INDEX(`code`);


ALTER TABLE `link` CHANGE `token` `token` CHAR(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `link` ADD INDEX(`token`);

ALTER TABLE `login_ip` ADD INDEX(`userid`);

ALTER TABLE `payback` ADD INDEX(`userid`);

ALTER TABLE `radius_ban` ADD INDEX(`userid`);

ALTER TABLE `relay` ADD INDEX( `user_id`, `source_node_id`, `dist_node_id`);

ALTER TABLE `ss_invite_code` ADD INDEX( `code`, `user_id`); 

ALTER TABLE `ss_node` ADD INDEX( `type`, `sort`, `node_class`, `node_group`); 

TRUNCATE `ss_node_info`;

ALTER TABLE `ss_node_info` ADD INDEX(`node_id`);

TRUNCATE `ss_node_online_log`;

ALTER TABLE `ss_node_online_log` ADD INDEX(`node_id`);

ALTER TABLE `ss_password_reset` ADD INDEX(`token`);

ALTER TABLE `telegram_session` ADD INDEX(`session_content`);

ALTER TABLE `ticket` ADD INDEX( `rootid`, `userid`); 

ALTER TABLE `user` ADD INDEX( `email`, `telegram_id`); 

ALTER TABLE `user_token` ADD INDEX(`token`);

ALTER TABLE `user_traffic_log` ADD INDEX( `user_id`, `node_id`); 

ALTER TABLE `user` ADD INDEX(`t`);

ALTER TABLE `ss_node` CHANGE `node_ip` `node_ip` CHAR(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL; 

ALTER TABLE `ss_node` ADD INDEX(`node_ip`); 

ALTER TABLE `alive_ip` ADD INDEX(`datetime`);

ALTER TABLE `auto` ADD INDEX(`datetime`);

ALTER TABLE `blockip` ADD INDEX(`datetime`);

ALTER TABLE `ss_node_online_log` ADD INDEX(`log_time`);

ALTER TABLE `ss_node_info` ADD INDEX(`log_time`);

ALTER TABLE `user_traffic_log` ADD INDEX(`log_time`);