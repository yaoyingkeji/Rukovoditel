<?php

define('TEXT_UPDATE_VERSION_FROM','2.6');
define('TEXT_UPDATE_VERSION_TO','2.7');

include('includes/template_top.php');

$tables_array = array();
$tables_query = db_query("show tables");
while($tables = db_fetch_array($tables_query))
{
    $tables_array[] = current($tables);
}

//print_r($columns_array);

//check if we have to run update for current database
if(!in_array('app_ext_xml_import_templates',$tables_array))
{
    echo '<h3 class="page-title">' . TEXT_PROCESSING . '</h3>';
    
    //required sql update
    $sql = "
ALTER TABLE `app_ext_xml_export_templates` ADD `template_filename` VARCHAR(255) NOT NULL AFTER `template_footer`;
ALTER TABLE `app_ext_xml_export_templates` ADD `transliterate_filename` TINYINT(1) NOT NULL DEFAULT '0' AFTER `template_filename`;

CREATE TABLE IF NOT EXISTS `app_ext_xml_import_templates` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `button_title` varchar(64) NOT NULL,
  `button_position` varchar(64) NOT NULL,
  `button_color` varchar(7) NOT NULL,
  `button_icon` varchar(64) NOT NULL,
  `users_groups` text NOT NULL,
  `assigned_to` text NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `data_path` varchar(255) NOT NULL,
  `import_fields` text NOT NULL,
  `import_fields_path` text NOT NULL,
  `import_action` varchar(16) NOT NULL,
  `update_by_field` int(11) NOT NULL,
  `update_by_field_path` varchar(255) NOT NULL,
  `filepath` varchar(255) NOT NULL,
  `parent_item_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_pivot_map_reports` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `users_groups` text NOT NULL,
  `in_menu` tinyint(1) NOT NULL,
  `zoom` tinyint(1) NOT NULL,
  `latlng` varchar(16) NOT NULL,
  `display_legend` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_pivot_map_reports_entities` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `reports_id` int(11) NOT NULL,
  `entities_id` int(11) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `background` int(11) NOT NULL,
  `fields_in_popup` text NOT NULL,
  `marker_color` varchar(16) NOT NULL,
  `marker_icon` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_fields_id` (`fields_id`),
  KEY `idx_reports_id` (`reports_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_cryptopro_certificates` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL,
  `thumbprint` varchar(64) NOT NULL,
  `certbase64` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `users_id` (`users_id`),
  KEY `thumbprint` (`thumbprint`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_signed_items` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `items_id` int(11) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `date_added` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `company` varchar(255) NOT NULL,
  `position` varchar(64) NOT NULL,
  `inn` varchar(64) NOT NULL,
  `ogrn` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `entities_id` (`entities_id`),
  KEY `items_id` (`items_id`),
  KEY `users_id` (`users_id`),
  KEY `fields_id` (`fields_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_signed_items_signatures` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `signed_items_id` int(11) NOT NULL,
  `signed_text` text NOT NULL,
  `singed_filename` varchar(255) NOT NULL,
  `signature` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `signed_items_id` (`signed_items_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `app_ext_processes` ADD `javascript_in_from` TEXT NOT NULL AFTER `disable_comments`, ADD `javascript_onsubmit` TEXT NOT NULL AFTER `javascript_in_from`;
ALTER TABLE `app_ext_processes` ADD `hide_entity_name` TINYINT(1) NOT NULL DEFAULT '0' AFTER `apply_fields_display_rules`;

ALTER TABLE `app_ext_calendar_events` CHANGE `start_date` `start_date` BIGINT UNSIGNED NOT NULL, CHANGE `end_date` `end_date` BIGINT NOT NULL;
ALTER TABLE `app_ext_call_history` CHANGE `date_added` `date_added` BIGINT UNSIGNED NOT NULL;
ALTER TABLE `app_ext_chat_conversations` CHANGE `date_added` `date_added` BIGINT UNSIGNED NOT NULL;
ALTER TABLE `app_ext_chat_conversations_messages` CHANGE `date_added` `date_added` BIGINT UNSIGNED NOT NULL;
ALTER TABLE `app_ext_chat_messages` CHANGE `date_added` `date_added` BIGINT UNSIGNED NOT NULL;
ALTER TABLE `app_ext_mail` CHANGE `date_added` `date_added` BIGINT UNSIGNED NOT NULL;
ALTER TABLE `app_ext_recurring_tasks` CHANGE `date_added` `date_added` BIGINT UNSIGNED NOT NULL, CHANGE `repeat_start` `repeat_start` BIGINT UNSIGNED NOT NULL, CHANGE `repeat_end` `repeat_end` BIGINT UNSIGNED NOT NULL;
ALTER TABLE `app_ext_track_changes_log` CHANGE `date_added` `date_added` BIGINT UNSIGNED NOT NULL;

ALTER TABLE `app_ext_kanban` ADD `in_menu` TINYINT(1) NOT NULL DEFAULT '0' AFTER `entities_id`;

ALTER TABLE `app_ext_export_templates` ADD `type` VARCHAR(16) NOT NULL DEFAULT 'html' AFTER `entities_id`, ADD `filename` VARCHAR(255) NOT NULL AFTER `type`;

CREATE TABLE IF NOT EXISTS `app_ext_items_export_templates_blocks` (
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