<?php

chdir(substr(__DIR__,0,-8));

define('IS_CRON',true);

//load core
require('includes/application_core.php');
require('plugins/ext/telephony_modules/megafon/megafon.php');

$result = $_POST;

//print_rr($result);

//error_log(print_r($result,true),3,'api/log.txt');

if(isset($result['cmd']) and $result['cmd']=='history')
{
    $data = [
        'type' => 'phone',
        'direction' => $result['type'],
        'date_added' =>time(),
        'phone' => $result['phone'],
        'recording' => $result['link'],
        'client_name' => '',
        'duration' => ($result['status']=='Success' ? $result['duration'] : 0),
    ];
    
	$crm_key = megafon::get_crm_key();
                
	if(isset($result['crm_token']) and $result['crm_token']==$crm_key) 
	{
		db_perform('app_ext_call_history', $data);
	}	
} 