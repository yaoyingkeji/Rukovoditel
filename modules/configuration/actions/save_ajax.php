<?php

switch($app_module_action)
{
    case 'save':
        $name = $_POST['name']??false;
        $value = $_POST['value']??'';
        
        if($name)
        {
            $value = is_array($value) ? implode(',',$value) : $value;
            
            configuration::set($name, $value);
        }
        
        break;
}

exit();