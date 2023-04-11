<?php

$log_type = $_GET['type']??'';

if(!in_array($log_type,['http','mysql','php','email']))
{
    redirect_to('logs/settings');
}

switch($app_module_action)
{
    case 'listing':            
        
        require(component_path('logs/' . $log_type . '_listing'));
        app_exit();
        
        break;
    case 'reset':
        db_query("delete from app_logs where log_type='{$log_type}'");
        
        redirect_to('logs/view','type=' . $log_type);
        break;
}

