CREATE TABLE IF NOT EXISTS `user_hourly_usage` (
    `id`           int(11)     NOT NULL,
    `user_id`      int(11)     NOT NULL,
    `traffic`      varchar(32) NOT NULL,
    `hourly_usage` varchar(32) NOT NULL,
    `datetime`     int(11)     NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
