CREATE TABLE `email_queue` (
  `id` bigint(20) NOT NULL,
  `to_email` varchar(32) NOT NULL,
  `subject` longtext NOT NULL,
  `template` longtext NOT NULL,
  `array` longtext NOT NULL,
  `time` int(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Email Queue 發件列表';

ALTER TABLE `email_queue`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `email_queue`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
