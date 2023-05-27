<?php

switch($app_module_action)
{
    case 'disabledTime':
        $entity_id = _GET('entity_id');
        $field_id = _GET('field_id');
        $item_id = _GET('item_id');
        $parent_item_id = _GET('parent_item_id');        
        $date = substr($_GET['date']??'',0,10);
                        
        if(!isset_entity($entity_id) or !isset_field($entity_id,$field_id))
        {
            exit();
        }
        
        $field = db_find('app_fields',$field_id);
        $cfg = new settings($field['configuration']);
        
        if(strlen($sql = $cfg->get('disable_time_by_query')))
        {
            $sql = str_replace('[parent_item_id]',$parent_item_id,$sql);
            $sql = str_replace('[TODAY]',"'" . $date . "'",$sql);
            
            if($item_id>0)
            {
                $sql .= " and id!=" . $item_id;
            }
            
            $disabledTime = [];            
            $disabled_time_query = db_query($sql,false);
            while($disabled_time = db_fetch_array($disabled_time_query))
            {
                if(isset($disabled_time['field_' . $field['id']]) and $disabled_time['field_' . $field['id']]>0)
                {
                    $disabledTime[] = str_replace(':00',':0',date('G:i',$disabled_time['field_' . $field['id']]));
                }
                elseif(isset($disabled_time['time_from']) and isset($disabled_time['time_to']) and $disabled_time['time_from']>0 and $disabled_time['time_to']>0 and $disabled_time['time_to']>$disabled_time['time_from'])
                {
                    for($d=$disabled_time['time_from'];$d<$disabled_time['time_to'];$d+=60)
                    {
                        $disabledTime[] = str_replace(':00',':0',date('G:i',$d));
                    }
                }
                
            } 
                                    
            if(count($disabledTime))
            {
               echo json_encode($disabledTime);  
            } 
        }
                
        
        break;
}

exit();