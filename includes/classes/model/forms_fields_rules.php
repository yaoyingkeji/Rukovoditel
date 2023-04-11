<?php

class forms_fields_rules
{

    static function prepare_hidden_fields($entity_id, $item, $fields_access_schema)
    {
        global $app_module_path;

        $html = '';
        $form_fields_query = db_query("select r.* from app_forms_fields_rules r where r.entities_id='" . $entity_id . "' and is_active=1 group by r.fields_id");
        while($v = db_fetch_array($form_fields_query))
        {
            //check if there is limited access or field ID is 6 (user group)
            if(isset($fields_access_schema[$v['fields_id']]) or ($v['fields_id'] == 6 and $app_module_path == 'users/account'))
            {
                $html .= input_hidden_tag('fields[' . $v['fields_id'] . ']', $item['field_' . $v['fields_id']], ['class' => 'field_' . $v['fields_id']]);
            }
        }

        return $html;
    }
    
    static function hidden_form_fields($entity_id, $check_user_group = true)
    {
        global $app_user;
                
        //admin can view all fields
        if($check_user_group and app_session_is_registered('app_logged_users_id') and $app_user['group_id']==0)
        {
            return '';
        }                
        
        $entity_cfg = new entities_cfg($entity_id); 
        
        $hidden_form_fields = $entity_cfg->get('hidden_form_fields');
        
        $html = '';
        if(strlen($hidden_form_fields))
        {
            $html .= '
                <style>';            
            
            foreach(explode(',',$hidden_form_fields) as $field_id)
            {
                $html .= '
                    .form-horizontal .form-group-' . $field_id . '{
                        visibility: hidden;
                        position: absolute;
                        z-index: 1;
                    }
                    ';
            }
            
            $html .= '
                </style>';
        }
        
        return $html;
    }
    
    static function get_chocies_values_by_field_type($v, $separator = '<br>')
    {
        $chocies_values = [];
        if(strlen($v['choices']))
        {
            if(in_array($v['type'], ['fieldtype_boolean_checkbox', 'fieldtype_boolean']))
            {
                foreach(explode(',', $v['choices']) as $id)
                {
                    switch($id)
                    {
                        case 1:
                            $chocies_values[] = TEXT_BOOLEAN_TRUE;
                            break;

                        case 2:
                            $chocies_values[] = TEXT_BOOLEAN_FALSE;
                            break;
                    }
                }
            }
            elseif($v['type'] == 'fieldtype_user_accessgroups')
            {
                foreach(explode(',', $v['choices']) as $id)
                {
                    $chocies_values[] = access_groups::get_name_by_id($id);
                }
            }
            elseif(in_array($v['type'], ['fieldtype_entity', 'fieldtype_entity_ajax', 'fieldtype_entity_multilevel']))
            {
                $cfg = new fields_types_cfg($v['configuration']);

                foreach(explode(',', $v['choices']) as $item_id)
                {
                    $chocies_values[] = items::get_heading_field($cfg->get('entity_id'), $item_id);
                }
            }
            else
            {
                $cfg = new fields_types_cfg($v['configuration']);

                if($cfg->get('use_global_list') > 0)
                {
                    $choices_query = db_query("select * from app_global_lists_choices where lists_id = '" . db_input($cfg->get('use_global_list')) . "' and id in (" . $v['choices'] . ") order by sort_order, name");
                }
                else
                {
                    $choices_query = db_query("select * from app_fields_choices where fields_id = '" . db_input($v['fields_id']) . "' and id in (" . $v['choices'] . ") order by sort_order, name");
                }

                while($choices = db_fetch_array($choices_query))
                {
                    $chocies_values[] = $choices['name'];
                }
            }                                    
        }
        
        return count($chocies_values) ? implode($separator, $chocies_values) : '';
    }
    
    static function fields_by_form_tab_helper($entity_id,$apply_to)
    {
        $html = '
            <div class="dropdown">
                <button class="btn btn-default btn-sm dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                  ' . TEXT_FORM_TAB . '
                  <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1" style="max-height: 250px; overflow-y: auto">
            ';
        $tabs_tree = forms_tabs::get_tree($entity_id);    
          
        foreach($tabs_tree as $tabs)
        {               
            if($tabs['is_folder'])
            {
                $html .= '
                    <li class="divider"></li>
                    <li>
                        <a href="#" ><b>' . $tabs['name'] . '</b></a>  		      
                    </li>';
            }
            else
            {
            
                $fields_query = db_query("select group_concat(f.id) as ids from app_fields f where f.type not in (" . fields_types::get_reserverd_types_list(). ") and  f.entities_id={$entity_id} and  f.forms_tabs_id='" . db_input($tabs['id']) . "' group by f.entities_id",false);
                $fields = db_fetch_array($fields_query);                                
        
                $html .= '
  		    <li>
  		  	<a href="#" class="apply-fields-by-tab" data-apply-to="' . $apply_to . '" data-fields="' . ($fields['ids']??'') . '">' . $tabs['name'] . '</a>
  		    </li>';
            }
        } 
        
        $html .= '</ul></div>';
        
        return $html;
    }

}
