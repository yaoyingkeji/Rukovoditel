<?php

chdir(substr(__DIR__,0,-5));

define('IS_CRON',true);

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

require(CFG_PATH_TO_PHPSPREADSHEET);

$app_users_cache  = users::get_cache();

$app_user = array('language'=>CFG_APP_LANGUAGE);

$template_info_query = db_query("select * from app_ext_import_templates where length(filepath)>0 and is_active=1");
while($template_info = db_fetch_array($template_info_query))
{    
    $xls_import = new xls_import('',$template_info);
    
    $xls_import->get_file_by_path();
        
    $xls_import->import_data();
        
    $xls_import->unlink_import_file();
}