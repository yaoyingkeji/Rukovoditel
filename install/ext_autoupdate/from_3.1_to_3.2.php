<?php

define('TEXT_UPDATE_VERSION_FROM','3.1');
define('TEXT_UPDATE_VERSION_TO','3.2');

include('includes/template_top.php');

$tables_array = array();
$tables_query = db_query("show tables");
while($tables = db_fetch_array($tables_query))
{
    $tables_array[] = current($tables);
}

//print_r($columns_array);

//check if we have to run update for current database
if(!in_array('app_ext_report_page_blocks',$tables_array))
{
    echo '<h3 class="page-title">' . TEXT_PROCESSING . '</h3>';
    
    //required sql update
    $sql = "
ALTER TABLE `app_ext_pivot_map_reports` ADD `display_sidebar` TINYINT(1) NOT NULL AFTER `display_legend`;
ALTER TABLE `app_ext_pivot_map_reports_entities` ADD `fields_in_sidebar` TEXT NOT NULL AFTER `fields_in_popup`;
ALTER TABLE `app_ext_pivot_map_reports` ADD `sidebar_width` VARCHAR(16) NOT NULL AFTER `display_sidebar`;

ALTER TABLE `app_ext_map_reports` ADD `display_sidebar` TINYINT(1) NOT NULL AFTER `fields_in_popup`;
ALTER TABLE `app_ext_map_reports` ADD `fields_in_sidebar` TEXT NOT NULL AFTER `display_sidebar`;
ALTER TABLE `app_ext_map_reports` ADD `sidebar_width` VARCHAR(16) NOT NULL AFTER `fields_in_sidebar`;

ALTER TABLE `app_ext_resource_timeline` ADD `min_time` VARCHAR(5) NOT NULL AFTER `default_view`, ADD `max_time` VARCHAR(5) NOT NULL AFTER `min_time`;

ALTER TABLE `app_ext_call_history` ADD `recording` TEXT NOT NULL AFTER `sms_text`, ADD `client_name` VARCHAR(255) NOT NULL AFTER `recording`;
ALTER TABLE `app_ext_call_history` ADD INDEX `idx_type` (`type`);
ALTER TABLE `app_ext_call_history` ADD INDEX `idx_direction` (`direction`);
ALTER TABLE `app_ext_call_history` ADD INDEX `idx_phone` (`phone`);
ALTER TABLE `app_ext_call_history` ADD `comments` TEXT NOT NULL AFTER `client_name`;

ALTER TABLE `app_ext_call_history` ADD `is_star` TINYINT(1) NOT NULL DEFAULT '0' AFTER `comments`, ADD `is_new` TINYINT(1) NOT NULL DEFAULT '1' AFTER `is_star`;

CREATE TABLE IF NOT EXISTS `app_ext_report_page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `type` varchar(64) NOT NULL,
  `use_editor` tinyint(1) NOT NULL,
  `save_filename` varchar(255) NOT NULL,
  `save_as` varchar(16) NOT NULL,
  `button_title` varchar(64) NOT NULL,
  `button_position` varchar(64) NOT NULL,
  `button_color` varchar(7) NOT NULL,
  `button_icon` varchar(64) NOT NULL,
  `users_groups` varchar(255) NOT NULL,
  `assigned_to` varchar(255) NOT NULL,
  `page_orientation` varchar(16) NOT NULL,
  `settings` text NOT NULL,
  `css` longtext NOT NULL,
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_ext_report_page_blocks` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `report_id` int(11) NOT NULL,
  `block_type` varchar(32) NOT NULL,
  `name` varchar(64) NOT NULL,
  `field_id` int(11) NOT NULL,
  `settings` text NOT NULL,
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `report_id` (`report_id`) USING BTREE,
  KEY `field_id` (`field_id`) USING BTREE
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