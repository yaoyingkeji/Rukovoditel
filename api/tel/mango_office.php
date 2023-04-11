<?php

chdir(substr(__DIR__,0,-8));

define('IS_CRON',true);

//load core
require('includes/application_core.php');
require('plugins/ext/telephony_modules/mango_office/mango_office.php');

//error_log(date('Y-m-d H:i:s') . print_r($_REQUEST,true),3,'api/tel/log.txt');

//check if api key exit
if(!isset($_POST['vpbx_api_key']))
{
    exit();
}

//check api key
if(isset($_POST['vpbx_api_key']) and $_POST['vpbx_api_key']!=mango_office::get_crm_key()) 
{
    exit();
} 

//seva recording
if(isset($_POST['json']) and $result = json_decode($_POST['json'],true) and isset($result['recording_id']) and isset($result['completion_code']) and $result['completion_code']==1000)
{
    $data = [
        'type' => 'phone',
        'direction' => '',
        'date_added' =>time(),
        'phone' => $result['entry_id'],
        'recording' => $result['recording_id'],
        'client_name' => '',
        'duration' => 0,
        'module' => 'mango_office',
    ];
              
    db_perform('app_ext_call_history', $data);    
}


//save call
if(isset($_POST['json']) and $result = json_decode($_POST['json'],true) and isset($result['call_direction']))
{
    $data = [
        'type' => 'phone',
        'direction' => $result['call_direction']==1 ? 'in':'out',
        'date_added' =>time(),
        'phone' => $result['call_direction']==1 ? $result['from']['number'] : $result['to']['number'],        
        'client_name' => '',
        'duration' => $result['talk_time']>0 ? $result['end_time']-$result['talk_time'] : 0,
        'module' => 'mango_office',
    ];
               
    $check_query = db_query("select id from app_ext_call_history where phone = '" . $result['entry_id']. "' and module='mango_office'");
    if($check = db_fetch($check_query))
    {
        db_perform('app_ext_call_history', $data,"update","id={$check->id}");
    }
    else
    {
        db_perform('app_ext_call_history', $data);
    }    
}