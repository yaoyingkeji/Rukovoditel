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

//check if we have to run updat for current database
if(!in_array('app_logs',$tables_array))
{
    echo '<h3 class="page-title">' . TEXT_PROCESSING . '</h3>';
    
    //required sql update
    $sql = "
CREATE TABLE IF NOT EXISTS `app_logs` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `users_id` int(11) UNSIGNED NOT NULL,
  `ip_address` varchar(64) NOT NULL,
  `log_type` varchar(16) NOT NULL,
  `date_added` int(11) NOT NULL,
  `http_url` varchar(255) NOT NULL,
  `is_ajax` tinyint(1) NOT NULL,
  `description` text NOT NULL,
  `seconds` decimal(11,4) NOT NULL,
  `errno` int(10) UNSIGNED NOT NULL,
  `filename` varchar(255) NOT NULL,
  `linenum` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_users_id` (`users_id`),
  KEY `idx_date_added` (`date_added`),
  KEY `idx_ip_address` (`ip_address`),
  KEY `idx_log_type` (`log_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `app_records_visibility_rules` ADD `mysql_query` TEXT NOT NULL AFTER `notes`;

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