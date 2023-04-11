<?php

class public_registration
{
    static function send_user_activation_email_msg($user_id,$previous_item_info)
    {
        //skip notification
        if(CFG_USE_PUBLIC_REGISTRATION==0 or CFG_PUBLIC_REGISTRATION_USER_ACTIVATION!='manually') return false;
        
        if($previous_item_info['field_5']==0)
        {
            $item_query = db_query("select e.* from app_entity_1 e where e.id='" . $user_id . "' and e.field_5=1");
            if($item = db_fetch_array($item_query))
            {
                $to_name = (CFG_APP_DISPLAY_USER_NAME_ORDER=='firstname_lastname' ? $item['field_7'] . ' ' . $item['field_8'] : $item['field_8'] . ' ' . $item['field_7']);
                
                $options = array('to' => $item['field_9'],
                    'to_name' => $to_name,
                    'subject'=>(strlen(CFG_USER_ACTIVATION_EMAIL_SUBJECT)>0 ? CFG_USER_ACTIVATION_EMAIL_SUBJECT :TEXT_USER_ACTIVATION_EMAIL_SUBJECT),
                    'body'=>(strlen(CFG_USER_ACTIVATION_EMAIL_BODY)>0 ? CFG_USER_ACTIVATION_EMAIL_BODY : sprintf(TEXT_USER_ACTIVATION_EMAIL_BODY,url_for('users/login','',true))),
                    'from'=> CFG_EMAIL_ADDRESS_FROM,
                    'from_name'=> CFG_EMAIL_NAME_FROM );
                
                users::send_email($options);
            }
        }
    }
    
}