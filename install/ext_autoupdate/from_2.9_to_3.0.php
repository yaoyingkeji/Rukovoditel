<?php

define('TEXT_UPDATE_VERSION_FROM','2.9');
define('TEXT_UPDATE_VERSION_TO','3.0');

include('includes/template_top.php');

$tables_array = array();
$tables_query = db_query("show tables");
while($tables = db_fetch_array($tables_query))
{
    $tables_array[] = current($tables);
}

//print_r($columns_array);

//check if we have to run update for current database
if(!in_array('app_ext_process_form_rows',$tables_array))
{
    echo '<h3 class="page-title">' . TEXT_PROCESSING . '</h3>';
    
    //required sql update
    $sql = "
ALTER TABLE `app_ext_processes` ADD `print_template` VARCHAR(32) NOT NULL AFTER `button_icon`;

CREATE TABLE IF NOT EXISTS `app_ext_export_selected` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_active` tinyint(1) NOT NULL,
  `type` varchar(64) NOT NULL,
  `entities_id` int(11) NOT NULL,
  `users_groups` text NOT NULL,
  `assigned_to` text NOT NULL,
  `name` varchar(255) NOT NULL,
  `button_title` varchar(64) NOT NULL,
  `button_position` varchar(64) NOT NULL,
  `button_color` varchar(7) NOT NULL,
  `button_icon` varchar(64) NOT NULL,
  `template_filename` varchar(64) NOT NULL,
  `export_fields` text NOT NULL,
  `export_url` tinyint(1) NOT NULL,
  `filename` varchar(128) NOT NULL,
  `settings` text NOT NULL,
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_export_selected_blocks` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `templates_id` int(11) NOT NULL,
  `block_type` varchar(32) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `settings` text NOT NULL,
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `templates_id` (`templates_id`),
  KEY `fields_id` (`fields_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `app_ext_graphicreport` ADD `show_totals` TINYINT(1) NOT NULL AFTER `period`;
ALTER TABLE `app_ext_graphicreport` ADD `hide_zero` TINYINT(1) NOT NULL AFTER `show_totals`;
ALTER TABLE `app_ext_funnelchart` ADD `colors` TEXT NOT NULL AFTER `users_groups`;
ALTER TABLE `app_ext_pivot_tables` ADD `colors` TEXT NOT NULL AFTER `chart_height`;
ALTER TABLE `app_ext_processes` ADD `window_width` VARCHAR(64) NOT NULL AFTER `access_to_assigned`;

CREATE TABLE IF NOT EXISTS `app_ext_process_form_rows` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `process_id` int(11) NOT NULL,
  `forms_tabs_id` int(11) NOT NULL,
  `columns` tinyint(4) NOT NULL,
  `column1_width` tinyint(4) NOT NULL,
  `column2_width` tinyint(4) NOT NULL,
  `column3_width` tinyint(4) NOT NULL,
  `column4_width` tinyint(4) NOT NULL,
  `column5_width` tinyint(4) NOT NULL,
  `column6_width` tinyint(4) NOT NULL,
  `field_name_new_row` tinyint(1) NOT NULL,
  `column1_fields` text NOT NULL,
  `column2_fields` text NOT NULL,
  `column3_fields` text NOT NULL,
  `column4_fields` text NOT NULL,
  `column5_fields` text NOT NULL,
  `column6_fields` text NOT NULL,
  `sort_order` smallint(6) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `entities_id` (`process_id`),
  KEY `forms_tabs_id` (`forms_tabs_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_process_form_tabs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `process_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `description` text NOT NULL,
  `fields` text NOT NULL,
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_process_id` (`process_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `app_ext_export_templates` CHANGE `description` `description` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;

ALTER TABLE `app_ext_email_rules` ADD `date_fields_id` INT NOT NULL AFTER `monitor_choices`, ADD `number_of_days` VARCHAR(32) NOT NULL AFTER `date_fields_id`;
ALTER TABLE `app_ext_sms_rules` ADD `date_fields_id` INT NOT NULL AFTER `monitor_choices`, ADD `number_of_days` VARCHAR(32) NOT NULL AFTER `date_fields_id`;
ALTER TABLE `app_ext_sms_rules` ADD `date_type` VARCHAR(16) NOT NULL AFTER `date_fields_id`;
ALTER TABLE `app_ext_email_rules` ADD `notes` TEXT NOT NULL AFTER `attach_template`;
ALTER TABLE `app_ext_sms_rules` ADD `send_to_assigned_users` TEXT NOT NULL AFTER `phone`;
ALTER TABLE `app_ext_processes` ADD `warning_text` TEXT NOT NULL AFTER `confirmation_text`;
ALTER TABLE `app_ext_export_templates` ADD `save_as` VARCHAR(32) NOT NULL AFTER `template_filename`;
ALTER TABLE `app_ext_public_forms` ADD `is_active` TINYINT(1) NOT NULL DEFAULT '1' AFTER `hide_parent_item`;
ALTER TABLE `app_ext_public_forms` ADD `inactive_message` TEXT NOT NULL AFTER `is_active`;
ALTER TABLE `app_ext_processes_actions` ADD `is_active` TINYINT(1) NOT NULL DEFAULT '1' AFTER `process_id`;
ALTER TABLE `app_ext_ipages` ADD `icon_color` VARCHAR(7) NOT NULL AFTER `menu_icon`, ADD `bg_color` VARCHAR(7) NOT NULL AFTER `icon_color`;
ALTER TABLE `app_ext_processes` ADD `is_form_wizard` TINYINT(1) NOT NULL DEFAULT '0' AFTER `javascript_onsubmit`, ADD `is_form_wizard_progress_bar` TINYINT(0) NOT NULL AFTER `is_form_wizard`;
ALTER TABLE `app_ext_processes` ADD `submit_button_title` VARCHAR(32) NOT NULL AFTER `is_form_wizard_progress_bar`;

";
    
    db_query_from_content(trim($sql));
    
    //extra code for update
    
    //if there are no any errors display success message
    echo '<div class="alert alert-success">' . TEXT_UPDATE_COMPLATED . '</div>';
}
else
{
    echo '<div class="alert alert-warning">' . TEXT_UPDATE_ALREADY_RUN . '</div>';
}

include('includes/template_bottom.php');