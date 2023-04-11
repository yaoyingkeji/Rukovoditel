<?php

chdir(substr(__DIR__,0,-8));

define('IS_CRON',true);

//load core
require('includes/application_core.php');


$result = json_decode(file_get_contents("php://input"), true);

//error_log(print_r($result,true),3,'log.txt');

if(isset($result['webhook']) and $result['webhook']['action']=='call.finish' and isset($result['event']))
{
    $data = [
        'type' => 'phone',
        'direction' => $result['event']['direction']==1 ? 'out':'in',
        'date_added' =>$result['event']['end_time'],
        'phone' => $result['event']['client_number'],
        'recording' => $result['event']['recording'],
        'client_name' => $result['event']['client_name'],
        'duration' => $result['event']['duration'],
    ];
    
    db_perform('app_ext_call_history', $data);
} 