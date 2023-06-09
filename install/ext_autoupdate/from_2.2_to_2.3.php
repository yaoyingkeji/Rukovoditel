<?php

define('TEXT_UPDATE_VERSION_FROM','2.2');
define('TEXT_UPDATE_VERSION_TO','2.3');

include('includes/template_top.php');

$tables_array = array();
$tables_query = db_query("show tables");
while($tables = db_fetch_array($tables_query))
{
  $tables_array[] = current($tables);      
}

//print_r($columns_array);

//check if we have to run update for current database
if(!in_array('app_ext_recurring_tasks',$tables_array))
{
  echo '<h3 class="page-title">' . TEXT_PROCESSING . '</h3>';

//required sql update   
$sql = "  
ALTER TABLE `app_ext_sms_rules` ADD `monitor_choices` TEXT NOT NULL AFTER `monitor_fields_id`;

ALTER TABLE `app_ext_track_changes_log` ADD `is_cron` TINYINT(1) NOT NULL AFTER `created_by`;

CREATE TABLE IF NOT EXISTS `app_ext_recurring_tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) NOT NULL,
  `date_added` int(11) NOT NULL,
  `entities_id` int(11) NOT NULL,
  `items_id` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `repeat_type` varchar(16) NOT NULL,
  `repeat_interval` int(11) NOT NULL,
  `repeat_days` varchar(16) NOT NULL,
  `repeat_start` int(11) NOT NULL,
  `repeat_end` int(11) NOT NULL,
  `repeat_limit` int(11) NOT NULL,
  `repeat_time` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_items_id` (`items_id`),
  KEY `idx_created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `app_ext_recurring_tasks_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tasks_id` int(11) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_tasks_id` (`tasks_id`),
  KEY `idx_fields_id` (`fields_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `app_ext_kanban` ADD `exclude_choices` TEXT NOT NULL AFTER `group_by_field`;

CREATE TABLE IF NOT EXISTS `app_ext_image_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `users_groups` text NOT NULL,
  `in_menu` tinyint(1) NOT NULL,
  `scale` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_fields_id` (`fields_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `app_ext_funnelchart` ADD `exclude_choices` TEXT NOT NULL AFTER `group_by_field`;

CREATE TABLE IF NOT EXISTS `app_ext_map_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `users_groups` text NOT NULL,
  `in_menu` tinyint(1) NOT NULL,
  `background` int(11) NOT NULL,
  `fields_in_popup` text NOT NULL,
  `zoom` tinyint(1) NOT NULL,
  `latlng` varchar(16) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_fields_id` (`fields_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `app_ext_ipages` ADD `html_code` TEXT NOT NULL AFTER `description`;

CREATE TABLE IF NOT EXISTS `app_ext_mind_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `users_groups` text NOT NULL,
  `in_menu` tinyint(1) NOT NULL,
  `use_background` int(11) NOT NULL,
  `icons` text NOT NULL,
  `fields_in_popup` text NOT NULL,
  `shape` varchar(16) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_fields_id` (`fields_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
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