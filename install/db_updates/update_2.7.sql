ALTER TABLE `app_entities_menu` ADD `parent_id` INT NOT NULL AFTER `id`, ADD INDEX `idx_parent_id` (`parent_id`);

CREATE TABLE IF NOT EXISTS `app_listing_highlight_rules` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `entities_id` int(10) UNSIGNED NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `fields_id` int(10) UNSIGNED NOT NULL,
  `fields_values` text NOT NULL,
  `bg_color` varchar(7) NOT NULL,
  `sort_order` int(11) NOT NULL,
  `notes` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_fields_id` (`fields_id`),
  KEY `entities_id` (`entities_id`),
  KEY `fields_id` (`fields_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `app_entity_1` ADD `is_email_verified` TINYINT(1) NOT NULL DEFAULT '1' AFTER `password`;

ALTER TABLE `app_approved_items` CHANGE `date_added` `date_added` BIGINT UNSIGNED NOT NULL;
ALTER TABLE `app_backups` CHANGE `date_added` `date_added` BIGINT UNSIGNED NOT NULL;
ALTER TABLE `app_comments` CHANGE `date_added` `date_added` BIGINT UNSIGNED NOT NULL;
ALTER TABLE `app_emails_on_schedule` CHANGE `date_added` `date_added` BIGINT UNSIGNED NOT NULL;
ALTER TABLE `app_sessions` CHANGE `expiry` `expiry` BIGINT UNSIGNED NOT NULL;
ALTER TABLE `app_users_alerts` CHANGE `start_date` `start_date` BIGINT UNSIGNED NOT NULL, CHANGE `end_date` `end_date` BIGINT UNSIGNED NOT NULL;
ALTER TABLE `app_users_login_log` CHANGE `date_added` `date_added` BIGINT UNSIGNED NOT NULL;
ALTER TABLE `app_users_notifications` CHANGE `date_added` `date_added` BIGINT UNSIGNED NOT NULL;

ALTER TABLE `app_filters_panels_fields` ADD `title` VARCHAR(64) NOT NULL AFTER `fields_id`;
ALTER TABLE `app_records_visibility_rules` ADD `merged_fields_empty_values` TEXT NOT NULL AFTER `merged_fields`;
