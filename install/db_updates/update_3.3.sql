ALTER TABLE `app_sessions` CHANGE `value` `value` LONGTEXT;

ALTER TABLE `app_reports` ADD `description` TEXT NOT NULL AFTER `name`;

CREATE TABLE IF NOT EXISTS `app_nested_entities_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `is_active` tinyint(4) NOT NULL,
  `name` varchar(64) NOT NULL,
  `entities` varchar(255) NOT NULL,
  `icon` varchar(64) NOT NULL,
  `icon_color` varchar(10) NOT NULL,
  `sort_order` smallint(6) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;