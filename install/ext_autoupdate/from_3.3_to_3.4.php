<?php

define('TEXT_UPDATE_VERSION_FROM','3.3');
define('TEXT_UPDATE_VERSION_TO','3.4');

include('includes/template_top.php');

$columns_array = array();
$columns_query = db_query("SHOW COLUMNS FROM app_ext_sms_rules");
while($columns = db_fetch_array($columns_query))
{
  $columns_array[] = $columns['Field'];
}

//print_r($columns_array);

//check if we have to run update for current database
if(!in_array('is_active',$columns_array))
{
    echo '<h3 class="page-title">' . TEXT_PROCESSING . '</h3>';
    
    //required sql update
    $sql = "
ALTER TABLE `app_ext_calendar` ADD `reminder_status` TINYINT(1) NOT NULL AFTER `filters_panel`, ADD `reminder_type` VARCHAR(64) NOT NULL AFTER `reminder_status`, ADD `reminder_minutes` SMALLINT NOT NULL AFTER `reminder_type`, ADD `reminder_item_heading` text NOT NULL AFTER `reminder_minutes`;
ALTER TABLE `app_ext_pivot_calendars_entities` ADD `reminder_status` TINYINT(1) NOT NULL AFTER `use_background`, ADD `reminder_type` VARCHAR(64) NOT NULL AFTER `reminder_status`, ADD `reminder_minutes` SMALLINT NOT NULL AFTER `reminder_type`, ADD `reminder_item_heading` text NOT NULL AFTER `reminder_minutes`;
ALTER TABLE `app_ext_xml_export_templates` ADD `related_entities_template` TEXT NOT NULL AFTER `transliterate_filename`;
ALTER TABLE `app_ext_mail` ADD INDEX `idx_date_added` (`date_added`);
ALTER TABLE `app_ext_mail` ADD INDEX `idx_is_new` (`is_new`);
ALTER TABLE `app_ext_mail` ADD INDEX `idx_is_sent` (`is_sent`);
ALTER TABLE `app_ext_mail` ADD INDEX `idx_is_star` (`is_star`);
ALTER TABLE `app_ext_mail` ADD INDEX `idx_in_trash` (`in_trash`);
ALTER TABLE `app_ext_mail` ADD INDEX `idx_is_spam` (`is_spam`);
ALTER TABLE `app_ext_pivot_tables` ADD `chart_show_labels` TINYINT(1) NOT NULL AFTER `colors`, ADD `chart_number_format` VARCHAR(6) NOT NULL AFTER `chart_show_labels`, ADD `chart_number_prefix` VARCHAR(16) NOT NULL AFTER `chart_number_format`, ADD `chart_number_suffix` VARCHAR(16) NOT NULL AFTER `chart_number_prefix`;
ALTER TABLE `app_ext_processes` ADD `success_message_status` TINYINT(1) NOT NULL DEFAULT '1' AFTER `success_message`;
ALTER TABLE `app_ext_smart_input_rules` ADD `settings` TEXT NOT NULL AFTER `rules`;
ALTER TABLE `app_ext_pivot_tables` ADD `chart_types` TEXT NOT NULL AFTER `colors`;
ALTER TABLE `app_ext_processes` ADD `assigned_to_all` TINYINT(1) NOT NULL AFTER `assigned_to`;
ALTER TABLE `app_ext_sms_rules` ADD `is_active` TINYINT NOT NULL DEFAULT '1' AFTER `modules_id`;
CREATE TABLE IF NOT EXISTS `app_ext_mail_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `accounts_id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_accounts_id` (`accounts_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";
    
    db_query_from_content(trim($sql));
    
    //if there are no any errors display success message
    echo '<div class="alert alert-success">' . TEXT_UPDATE_COMPLATED . '</div>';
}
else
{
    echo '<div class="alert alert-warning">' . TEXT_UPDATE_ALREADY_RUN . '</div>';
}

include('includes/template_bottom.php');