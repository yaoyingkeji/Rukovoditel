ALTER TABLE `app_ext_map_reports` ADD `is_public_access` TINYINT(1) NOT NULL AFTER `latlng`;

CREATE TABLE IF NOT EXISTS `app_ext_resource_timeline` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `heading_template` varchar(64) NOT NULL,
  `fields_in_listing` varchar(255) NOT NULL,
  `display_legend` tinyint(1) NOT NULL,
  `listing_width` varchar(4) NOT NULL,
  `column_width` varchar(64) NOT NULL,
  `fields_in_popup` varchar(255) NOT NULL,
  `default_view` varchar(16) NOT NULL,
  `view_modes` varchar(255) NOT NULL,
  `time_slot_duration` varchar(8) NOT NULL,
  `in_menu` tinyint(1) NOT NULL,
  `users_groups` text NOT NULL,
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_resource_timeline_entities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `calendars_id` int(11) NOT NULL,
  `entities_id` int(11) NOT NULL,
  `related_entity_field_id` int(11) NOT NULL,
  `bg_color` varchar(10) NOT NULL,
  `start_date` int(11) NOT NULL,
  `end_date` int(11) NOT NULL,
  `heading_template` varchar(64) NOT NULL,
  `fields_in_popup` text NOT NULL,
  `background` varchar(10) NOT NULL,
  `use_background` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_calendars_id` (`calendars_id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_rss_feeds` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `rss_id` int(10) UNSIGNED NOT NULL,
  `entities_id` int(10) UNSIGNED NOT NULL,
  `type` varchar(64) NOT NULL,
  `name` varchar(255) NOT NULL,
  `users_groups` text NOT NULL,
  `assigned_to` text NOT NULL,
  `heading_template` varchar(64) NOT NULL,
  `start_date` int(10) UNSIGNED NOT NULL,
  `end_date` int(10) UNSIGNED NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_rss_id` (`rss_id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `app_ext_calendar` ADD `enable_ical` TINYINT(1) NOT NULL AFTER `entities_id`;
ALTER TABLE `app_ext_pivot_calendars` ADD `enable_ical` TINYINT(1) NOT NULL AFTER `users_groups`;