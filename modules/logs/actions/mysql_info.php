<?php

require('includes/libs/SqlFormatter.php');

$log_info_query = db_query("select l.*,u.field_12 as username from app_logs l left join app_entity_1 u on u.id=l.users_id where log_type='mysql' and l.id=" . _GET('id'));
if(!$log_info = db_fetch_array($log_info_query))
{
    redirect_to('logs/view','type=mysql');
}
