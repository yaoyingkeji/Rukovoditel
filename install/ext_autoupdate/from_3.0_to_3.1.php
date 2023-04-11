<?php

define('TEXT_UPDATE_VERSION_FROM','3.0');
define('TEXT_UPDATE_VERSION_TO','3.1');

include('includes/template_top.php');

$tables_array = array();
$tables_query = db_query("show tables");
while($tables = db_fetch_array($tables_query))
{
    $tables_array[] = current($tables);
}

//print_r($columns_array);

//check if we have to run update for current database
if(!in_array('app_ext_email_rules_blocks',$tables_array))
{
    echo '<h3 class="page-title">' . TEXT_PROCESSING . '</h3>';
    
    //required sql update
    $sql = "
ALTER TABLE `app_ext_export_templates` ADD `save_attachments` VARCHAR(255) NOT NULL AFTER `save_as`;

CREATE TABLE IF NOT EXISTS `app_ext_email_rules_blocks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
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