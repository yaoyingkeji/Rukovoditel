<?php

chdir(substr(__DIR__, 0, -5));

define('IS_CRON', true);

//load core
require('includes/application_core.php');


//load app lagn
if(is_file($v = 'includes/languages/' . CFG_APP_LANGUAGE))
{
    require($v);
}

if(is_file($v = 'plugins/ext/languages/' . CFG_APP_LANGUAGE))
{
    require($v);
}

$app_users_cache  = users::get_cache();

$app_user = [
    'id'=>0,
    'group_id'=>0,
    'email'=>CFG_EMAIL_ADDRESS_FROM,
    'name'=>CFG_EMAIL_NAME_FROM,
];

sms::msg_by_date('hour');