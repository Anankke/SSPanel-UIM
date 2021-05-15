CREATE TABLE IF NOT EXISTS `user_hourly_usage` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `traffic` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hourly_usage` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `datetime` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_hourly_usage_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
