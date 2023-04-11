ALTER TABLE `app_fields` ADD `forms_rows_position` VARCHAR(255) NOT NULL AFTER `comments_forms_tabs_id`;

CREATE TABLE IF NOT EXISTS `app_forms_rows` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `forms_tabs_id` int(11) NOT NULL,
  `columns` tinyint(4) NOT NULL,
  `column1_width` tinyint(4) NOT NULL,
  `column2_width` tinyint(4) NOT NULL,
  `column3_width` tinyint(4) NOT NULL,
  `column4_width` tinyint(4) NOT NULL,
  `field_name_new_row` tinyint(1) NOT NULL,
  `sort_order` smallint(6) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `entities_id` (`entities_id`),
  KEY `forms_tabs_id` (`forms_tabs_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `app_reports_sections` ADD `count_columns` TINYINT(1) NOT NULL DEFAULT 2 AFTER `created_by`;

ALTER TABLE app_reports ADD INDEX `idx_reports_type` (`reports_type`);
ALTER TABLE app_reports ADD INDEX `idx_in_dashboard` (`in_dashboard`);
ALTER TABLE app_reports ADD INDEX `idx_in_dashboard_counter` (`in_dashboard_counter`);

ALTER TABLE app_fields ADD INDEX `idx_type` (`type`);

ALTER TABLE app_sessions ADD INDEX `idx_expiry` (`expiry`);

ALTER TABLE `app_emails_on_schedule` ADD `email_attachments` TEXT NOT NULL AFTER `email_from_name`;