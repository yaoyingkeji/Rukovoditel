<?php

define('TEXT_UPDATE_VERSION_FROM','3.2');
define('TEXT_UPDATE_VERSION_TO','3.3');

include('includes/template_top.php');

$tables_array = array();
$tables_query = db_query("show tables");
while($tables = db_fetch_array($tables_query))
{
    $tables_array[] = current($tables);
}

//print_r($columns_array);

//check if we have to run updat for current database
if(!in_array('app_nested_entities_menu',$tables_array))
{
    echo '<h3 class="page-title">' . TEXT_PROCESSING . '</h3>';
    
    //required sql update
    $sql = "
ALTER TABLE `app_sessions` CHANGE `value` `value` LONGTEXT;

ALTER TABLE `app_reports` ADD `description` TEXT NOT NULL AFTER `name`;

CREATE TABLE IF NOT EXISTS `app_nested_entities_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `is_active` tinyint(4) NOT NULL,
  `name` varchar(64) NOT NULL,
  `entities` varchar(255) NOT NULL,
  `icon` varchar(64) NOT NULL,
  `icon_color` varchar(10) NOT NULL,
  `sort_order` smallint(6) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`)
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