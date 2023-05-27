<?php if (isset($_GET['zd_echo'])) exit($_GET['zd_echo']); ?>

<?php

chdir(substr(__DIR__,0,-8));

define('IS_CRON',true);

//load core
require('includes/application_core.php');

//error_log(date('Y-m-d H:i:s') . print_r($_REQUEST,true),3,'api/tel/log.txt');

if(isset($_REQUEST['event']) and in_array($_REQUEST['event'],['NOTIFY_OUT_END','NOTIFY_END']))
{
    $data = [
        'type' => 'phone',
        'direction' =>$_REQUEST['event']=='NOTIFY_OUT_END' ? 'out':'in',
        'date_added' => get_date_timestamp($_REQUEST['call_start']),
        'phone' => $_REQUEST['event']=='NOTIFY_OUT_END' ? $_REQUEST['destination']: $_REQUEST['caller_id'],
        'recording' => $_REQUEST['call_id_with_rec']??'',
        'client_name' => '',
        'duration' => $_REQUEST['duration'],
        'module' => 'zadarma',
    ];
    
    db_perform('app_ext_call_history', $data);
    
}