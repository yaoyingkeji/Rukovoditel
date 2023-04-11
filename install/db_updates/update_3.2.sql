CREATE TABLE IF NOT EXISTS `app_logs` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `users_id` int(11) UNSIGNED NOT NULL,
  `ip_address` varchar(64) NOT NULL,
  `log_type` varchar(16) NOT NULL,
  `date_added` int(11) NOT NULL,
  `http_url` varchar(255) NOT NULL,
  `is_ajax` tinyint(1) NOT NULL,
  `description` text NOT NULL,
  `seconds` decimal(11,4) NOT NULL,
  `errno` int(10) UNSIGNED NOT NULL,
  `filename` varchar(255) NOT NULL,
  `linenum` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_users_id` (`users_id`),
  KEY `idx_date_added` (`date_added`),
  KEY `idx_ip_address` (`ip_address`),
  KEY `idx_log_type` (`log_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `app_records_visibility_rules` ADD `mysql_query` TEXT NOT NULL AFTER `notes`;
