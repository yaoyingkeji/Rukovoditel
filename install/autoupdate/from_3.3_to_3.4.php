<?php

define('TEXT_UPDATE_VERSION_FROM','3.3');
define('TEXT_UPDATE_VERSION_TO','3.4');

include('includes/template_top.php');

$columns_array = array();
$columns_query = db_query("SHOW COLUMNS FROM app_filters_panels_fields");
while($columns = db_fetch_array($columns_query))
{
  $columns_array[] = $columns['Field'];
}

//print_r($columns_array);

//check if we have to run updat for current database
if(!in_array('exclude_values_not_in_listing',$columns_array))
{
    echo '<h3 class="page-title">' . TEXT_PROCESSING . '</h3>';
    
    //required sql update
    $sql = "
ALTER TABLE `app_records_visibility_rules` ADD `php_code` TEXT NOT NULL AFTER `mysql_query`;
ALTER TABLE `app_filters_panels_fields` ADD `exclude_values_not_in_listing` TINYINT(1) NOT NULL DEFAULT '0' AFTER `exclude_values`;
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