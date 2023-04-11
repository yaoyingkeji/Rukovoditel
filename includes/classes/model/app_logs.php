<?php


if(!defined('IS_AJAX'))
{
    define('IS_AJAX',0);
}

class app_logs
{
    static function reset_log()
    {
        if(CFG_APP_LOGS_ENABLE==1 and (CFG_APP_LOGS_DATE_UPDATED==0 or date('Y-m-d',CFG_APP_LOGS_DATE_UPDATED)!=date('Y-m-d')))
        {
            configuration::set('CFG_APP_LOGS_DATE_UPDATED', time());
            
            db_query("delete from app_logs where date_added<" . strtotime("-" . CFG_APP_LOGS_STORE_DAYS. " day"),false);
        }
    }
    
    static function email_log($to, $subject, $error = '')
    {
        global $app_user;
        
        if(defined('CFG_APP_LOGS_ENABLE') and CFG_APP_LOGS_ENABLE==1 
                and ((strstr(CFG_APP_LOGS_TYPES,'email') and  !strlen($error)) or (strstr(CFG_APP_LOGS_TYPES,'mail_error') and strlen($error)>0)))
        {
            $data = [
                'users_id' => $app_user['id']??0,
                'log_type' => 'email',
                'date_added' => time(),
                'http_url' => $_SERVER['REQUEST_URI'] ?? '',
                'is_ajax' => IS_AJAX,
                'errno' => (strlen($error) ? 1:0),
                'description' =>$subject . (strlen($error) ? '<br><code>' . $error . '</code>':''),
                'filename' => $to,
                'linenum' =>0,
            ];

            db_perform('app_logs', $data);
        }
                        
    }
    
    static function php_log()
    {
        if(CFG_APP_LOGS_ENABLE==1 and strstr(CFG_APP_LOGS_TYPES,'php'))
        {        
            //set custom error handler    
            error_reporting(E_ALL);                        

            set_error_handler(function($errno, $errmsg, $filename, $linenum){
                global $app_user;    
                                                
                $data = [
                    'users_id' => $app_user['id']??0,
                    'log_type' =>'php',
                    'date_added' => time(),
                    'http_url' => $_SERVER['REQUEST_URI'] ?? '',
                    'is_ajax' => IS_AJAX,
                    'errno' => $errno,
                    'description' =>$errmsg,
                    'filename' => $filename,
                    'linenum' =>$linenum,
                ];

                db_perform('app_logs', $data);
            });    
        }
    }
    
    static function db_log($query, $time, $errno=0)
    {
        global $app_user;

        $http_url = $_SERVER['REQUEST_URI'] ?? '';


        if(defined('CFG_APP_LOGS_ENABLE') and CFG_APP_LOGS_ENABLE==1 
                and ((strstr(CFG_APP_LOGS_TYPES,'mysql') and  $errno==0) or (strstr(CFG_APP_LOGS_TYPES,'sql_error') and $errno==1)) 
                and !strstr($query,'insert into')
                and !strstr($http_url,'module=logs/view'))
        {      
            $data = [
                'users_id' => $app_user['id']??0,
                'log_type' =>'mysql',
                'date_added' => time(),
                'http_url' => $_SERVER['REQUEST_URI'] ?? '',
                'is_ajax' => IS_AJAX,
                'errno' => $errno,
                'description' =>$query,
                'seconds' => $time,
            ];

            db_perform('app_logs', $data);
        } 
    }
    
    static function http_log()
    {
        global $app_user, $app_page_starttime;
        
        if(CFG_APP_LOGS_ENABLE==1 and strstr(CFG_APP_LOGS_TYPES,'http'))
        { 
            $time = number_format((microtime(true) - $app_page_starttime), 4);

            $data = [
                    'users_id' => $app_user['id']??0,
                    'log_type' =>'http',
                    'date_added' => time(),
                    'http_url' => $_SERVER['REQUEST_URI'] ?? '',
                    'is_ajax' => IS_AJAX,
                    'errno' => 0,
                    'description' =>'',
                    'seconds' => $time,
                ];

            db_perform('app_logs', $data);
        }
    }
}
