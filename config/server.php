<?php

// secure webserver
  define('ENABLE_SSL', false);
    
//Configure server host to build urls correctly in cron
//Enter [http or https]+[domainname]+[catalog] for example: https://mycompany.com/mypm/
  define('CRON_HTTP_SERVER_HOST','');      
  
//Use LDAP login only. Default login page will be disabled
  define('CFG_USE_LDAP_LOGIN_ONLY',false);
           
//list of available plugins separated by comma
  define('AVAILABLE_PLUGINS','ext');
    
// Define the webserver and path parameters
// * DIR_FS_* = Filesystem directories (local/physical)  
  define('DIR_FS_CATALOG', substr(__DIR__,0,-6));    
  define('DIR_FS_UPLOADS',DIR_FS_CATALOG . 'uploads/');
  define('DIR_FS_ATTACHMENTS',DIR_FS_UPLOADS . 'attachments/');
  define('DIR_FS_ATTACHMENTS_PREVIEW',DIR_FS_UPLOADS . 'attachments_preview/');
  define('DIR_FS_IMAGES',DIR_FS_UPLOADS . 'images/');
  define('DIR_FS_USERS',DIR_FS_UPLOADS . 'users/');
  define('DIR_FS_BACKUPS',DIR_FS_CATALOG . 'backups/');
  define('DIR_FS_BACKUPS_AUTO',DIR_FS_CATALOG . 'backups/auto/');
  define('DIR_FS_TMP',DIR_FS_CATALOG . 'tmp/');
  define('DIR_FS_CACHE',DIR_FS_CATALOG . 'cache/');    

//// * DIR_WS_* = Webserver directories (virtual/URL)
  define('DIR_WS_UPLOADS','uploads/');
  define('DIR_WS_ATTACHMENTS','uploads/attachments/');
  define('DIR_WS_ATTACHMENTS_PREVIEW','uploads/attachments_preview/');
  define('DIR_WS_IMAGES','uploads/images/');
  define('DIR_WS_USERS','uploads/users/');
  define('DIR_WS_MAIL_ATTACHMENTS','uploads/mail/');
  define('DIR_WS_TEMPLATES','uploads/templates/');
  define('DIR_WS_CUSTOM_CSS_FILE','css/custom.css');
  
//Session name
//For different installation on the same server use different session name
  define('SESSION_NAME','sid');  
  
//Session Directory
//If sessions are file based, store them in this directory.
  define('SESSION_WRITE_DIRECTORY','/tmp'); 
  
//session handler   
// leave empty '' for default handler or set to 'mysql'  
  define('STORE_SESSIONS', 'mysql');   
  
//Session Force Cookie Use
//Force the use of sessions when cookies are only enabled.
  define('SESSION_FORCE_COOKIE_USE',true);
  define('SESSION_COOKIE_DOMAIN','');  
  define('SESSION_COOKIE_PATH','');   
  
//force set_mode
    define('DB_FORCE_SQL_MODE',true); //true or false
    define('DB_SET_SQL_MODE',''); //to remove STRICT_TRANS_TABLES
        
	