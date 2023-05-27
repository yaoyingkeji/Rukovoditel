<?php

$field_id = _GET('field_id');
if(!isset_field($current_entity_id,$field_id))
{
    redirect_to('items/info','path=' . $app_path);
}

$item_info_query = db_query("select field_{$field_id} from app_entity_{$current_entity_id} where id={$current_item_id}");
if(!$item_info = db_fetch_array($item_info_query))
{
    redirect_to('dashboard/page_not_found');
}

switch($app_module_action)
{
    case 'sort':
        $choices_sorted = $_POST['choices_sorted'];
        if(strlen($choices_sorted))
        {
            $choices_sorted = json_decode(stripslashes($choices_sorted), true);
            if($choices_sorted)
            {
                
                $attachments = [];
                foreach($choices_sorted as $file)
                {
                    $attachments[] = $file['id'];
                }
                
                db_query("update app_entity_{$current_entity_id} set field_{$field_id}='" . implode(',',$attachments). "' where id={$current_item_id}");
            }
        }
        redirect_to('items/info','path=' . $app_path);
        break;
}


