<?php

switch($app_module_action)
{
    case 'save':
        $custom_css = $_POST['custom_css'];
        file_put_contents(DIR_WS_CUSTOM_CSS_FILE, $custom_css);
        
        configuration::set('CFG_CUSTOM_CSS_TIME',time());
                        
        exit();
                
        break;
}