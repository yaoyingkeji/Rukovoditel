<?php

define('TEXT_UPDATE_VERSION_FROM','2.7');
define('TEXT_UPDATE_VERSION_TO','2.8');

include('includes/template_top.php');

$tables_array = array();
$tables_query = db_query("show tables");
while($tables = db_fetch_array($tables_query))
{
    $tables_array[] = current($tables);
}

//print_r($columns_array);

//check if we have to run updat for current database
if(!in_array('app_forms_rows',$tables_array))
{
    echo '<h3 class="page-title">' . TEXT_PROCESSING . '</h3>';
    
    //required sql update
    $sql = "
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