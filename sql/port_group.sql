ALTER TABLE `ss_node` ADD column `port_group` int(3) NOT NULL DEFAULT '0';
ALTER TABLE `ss_node` ADD column `min_port` int(11) NOT NULL DEFAULT '1025';
ALTER TABLE `ss_node` ADD column `max_port` int(11) NOT NULL DEFAULT '65500';

CREATE TABLE `user_method` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `node_id` int(11) NOT NULL,
    `port` int(11) NOT NULL,
    `passwd` varchar(16) NOT NULL,
    `method` varchar(64) NOT NULL DEFAULT 'chacha-20',
    `protocol` varchar(128) DEFAULT 'origin',
    `protocol_param` varchar(128) DEFAULT '',
    `obfs` varchar(128) DEFAULT 'plain',
    `obfs_param` varchar(128) DEFAULT '',
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;