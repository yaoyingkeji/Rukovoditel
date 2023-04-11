<?php

define('TEXT_UPDATE_VERSION_FROM','2.9');
define('TEXT_UPDATE_VERSION_TO','2.9');

include('includes/template_top.php');

$columns_array = array();
$columns_query = db_query("SHOW COLUMNS FROM app_entity_1");
while($columns = db_fetch_array($columns_query))
{
  $columns_array[] = $columns['Field'];
}

//print_r($columns_array);

//check if we have to run updat for current database
if(!in_array('multiple_access_groups',$columns_array))
{
    echo '<h3 class="page-title">' . TEXT_PROCESSING . '</h3>';
    
    //required sql update
    $sql = "
ALTER TABLE `app_entity_1` ADD `multiple_access_groups` VARCHAR(64) NOT NULL AFTER `password`;
ALTER TABLE `app_entity_1` ADD `client_id` BIGINT UNSIGNED NOT NULL AFTER `id`, ADD INDEX `idx_client_id` (`client_id`);
ALTER TABLE `app_listing_types` ADD `settings` TEXT NOT NULL AFTER `width`;
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