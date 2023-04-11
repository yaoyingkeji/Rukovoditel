CREATE TABLE IF NOT EXISTS `app_entities_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `sort_order` smallint(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `app_entities` ADD `group_id` INT NOT NULL AFTER `parent_id`, ADD INDEX `idx_group_id` (`group_id`);

CREATE TABLE IF NOT EXISTS `app_portlets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `users_id` int(11) NOT NULL,
  `is_collapsed` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_name` (`name`,`users_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `app_entities_menu` ADD `type` VARCHAR(16) NULL DEFAULT 'entity' AFTER `reports_list`, ADD `url` VARCHAR(255) NOT NULL AFTER `type`, ADD `users_groups` TEXT NOT NULL AFTER `url`, ADD `assigned_to` TEXT NOT NULL AFTER `users_groups`;

ALTER TABLE `app_forms_rows` ADD `column5_width` TINYINT(4) NOT NULL AFTER `column4_width`, ADD `column6_width` TINYINT(4) NOT NULL AFTER `column5_width`;

ALTER TABLE `app_global_lists_choices` ADD `value` VARCHAR(64) NOT NULL AFTER `bg_color`;

ALTER TABLE `app_entities_menu` ADD `pages_list` TEXT NOT NULL AFTER `reports_list`;

ALTER TABLE `app_reports_groups` ADD `assigned_to` TEXT NOT NULL AFTER `users_groups`;

ALTER TABLE `app_access_groups` ADD `notes` TEXT NOT NULL AFTER `sort_order`;

ALTER TABLE `app_reports` ADD `in_dashboard_counter_bg_color` VARCHAR(16) NOT NULL AFTER `in_dashboard_counter_color`;

CREATE TABLE IF NOT EXISTS `app_favorites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL,
  `entities_id` int(11) NOT NULL,
  `items_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_users_id` (`users_id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_items_Id` (`items_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_image_map_markers_nested` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `items_id` int(11) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `x` int(11) NOT NULL,
  `y` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_items_id` (`items_id`),
  KEY `idx_fields_id` (`fields_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `app_entities_menu` ADD `icon_color` VARCHAR(7) NOT NULL AFTER `icon`, ADD `bg_color` VARCHAR(7) NOT NULL AFTER `icon_color`;
ALTER TABLE `app_reports` ADD `icon_color` VARCHAR(7) NOT NULL AFTER `menu_icon`, ADD `bg_color` VARCHAR(7) NOT NULL AFTER `icon_color`;
ALTER TABLE `app_reports_groups` ADD `icon_color` VARCHAR(7) NOT NULL AFTER `menu_icon`, ADD `bg_color` VARCHAR(7) NOT NULL AFTER `icon_color`;
ALTER TABLE `app_forms_fields_rules` ADD `is_active` TINYINT(1) DEFAULT 1 NOT NULL AFTER `fields_id`;
ALTER TABLE `app_forms_fields_rules` ADD `sort_order` INT NOT NULL AFTER `hidden_fields`;

CREATE TABLE IF NOT EXISTS `app_global_vars` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `is_folder` tinyint(1) NOT NULL,
  `name` varchar(64) NOT NULL,
  `value` varchar(255) NOT NULL,
  `notes` text NOT NULL,
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_custom_php` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `is_folder` tinyint(1) NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` longtext NOT NULL,
  `notes` text NOT NULL,
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `app_reports_groups` ADD `in_dashboard` TINYINT(1) NOT NULL AFTER `in_menu`;
ALTER TABLE `app_backups` ADD `is_auto` TINYINT(1) NOT NULL AFTER `users_id`;
ALTER TABLE `app_forms_tabs` ADD `parent_id` INT NOT NULL DEFAULT '0' AFTER `entities_id`, ADD `is_folder` TINYINT(1) NOT NULL DEFAULT '0' AFTER `parent_id`;