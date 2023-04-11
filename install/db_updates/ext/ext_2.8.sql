ALTER TABLE `app_ext_ganttchart` ADD `skin` VARCHAR(32) NOT NULL AFTER `default_view`;
ALTER TABLE `app_ext_ganttchart` ADD `auto_scheduling` TINYINT(1) NOT NULL AFTER `skin`, ADD `highlight_critical_path` TINYINT(1) NOT NULL AFTER `auto_scheduling`;
ALTER TABLE `app_ext_mail_accounts` ADD `email` VARCHAR(64) NOT NULL AFTER `login`;
ALTER TABLE `app_ext_funnelchart` ADD `hide_zero_values` TINYINT(1) NOT NULL AFTER `group_by_field`;
ALTER TABLE `app_ext_track_changes` ADD `color_delete` VARCHAR(7) NOT NULL AFTER `color_comment`;
ALTER TABLE `app_ext_track_changes_log` ADD `items_name` VARCHAR(255) NOT NULL AFTER `comments_id`;
ALTER TABLE `app_ext_pivot_calendars` ADD `event_limit` SMALLINT NOT NULL DEFAULT '6' AFTER `view_modes`;
ALTER TABLE `app_ext_calendar` ADD `event_limit` SMALLINT NOT NULL AFTER `view_modes`;
ALTER TABLE `app_ext_calendar` ADD `filters_panel` VARCHAR(16) NOT NULL DEFAULT 'default' AFTER `fields_in_popup`;

CREATE TABLE IF NOT EXISTS `app_ext_pivot_tables` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `in_menu` tinyint(1) NOT NULL,
  `filters_panel` varchar(16) NOT NULL,
  `height` smallint(6) NOT NULL,
  `users_groups` text NOT NULL,
  `sort_order` int(11) NOT NULL,
  `chart_type` varchar(16) NOT NULL,
  `chart_position` varchar(16) NOT NULL,
  `chart_height` smallint(6) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_pivot_tables_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reports_id` int(11) NOT NULL,
  `entities_id` int(11) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `fields_name` varchar(64) NOT NULL,
  `cfg_date_format` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entitites_id` (`entities_id`),
  KEY `idx_fields_id` (`fields_id`),
  KEY `reports_id` (`reports_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_pivot_tables_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reports_id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `settings` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_reports_id` (`reports_id`),
  KEY `idx_users_id` (`users_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `app_ext_email_rules` ADD `attach_template` TEXT NOT NULL AFTER `attach_attachments`;

ALTER TABLE `app_ext_export_templates` ADD `label_size` VARCHAR(16) NOT NULL AFTER `type`;

ALTER TABLE app_ext_modules ADD INDEX `idx_is_active` (`is_active`);