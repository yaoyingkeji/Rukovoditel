ALTER TABLE `app_records_visibility_rules` ADD `php_code` TEXT NOT NULL AFTER `mysql_query`;

ALTER TABLE `app_filters_panels_fields` ADD `exclude_values_not_in_listing` TINYINT(1) NOT NULL DEFAULT '0' AFTER `exclude_values`;