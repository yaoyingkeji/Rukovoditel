<?php

class forms_fields_rules
{
    private $entity_id;
    private $form_name;
    private $allowed_field_types;
    private $item;

    function __construct($entity_id, $form_name = false)
    {
        $this->entity_id = $entity_id;
        $this->form_name = $form_name;        
        $this->item = false;
        
        $this->allowed_field_types = [
            'fieldtype_entity',
            'fieldtype_entity_ajax',
            'fieldtype_entity_multilevel',
            'fieldtype_dropdown',
            'fieldtype_dropdown_multiple',
            'fieldtype_checkboxes',
            'fieldtype_radioboxes',
            'fieldtype_user_accessgroups',
            'fieldtype_grouped_users',
            'fieldtype_boolean_checkbox',
            'fieldtype_boolean',
            'fieldtype_autostatus', 
            'fieldtype_stages', 
            'fieldtype_color'
        ];              
    }
    
    function set_item($item)
    {
        $this->item = $item;
    }

    function apply()
    {
        global $app_user, $app_module_path;
        
        $html = '';
                
        $form_fields_query = db_query("select r.*, f.name, f.id as fields_id, f.type from app_forms_fields_rules r, app_fields f where r.is_active=1 and  f.type in (" . db_input_in($this->allowed_field_types). ") and r.fields_id=f.id and r.entities_id='" . $this->entity_id . "'",false);

        if(db_num_rows($form_fields_query) > 0)
        {                   
            $rules_for_fields = array();

            while($form_fields = db_fetch_array($form_fields_query))
            {
                if(strlen($form_fields['visible_fields']??'') and strlen($form_fields['choices']??''))
                {
                    $html .= '
				<input class="disply-fields-rules-' . $form_fields['fields_id'] . '" type="hidden" data-type="visible" data-choices="' . $form_fields['choices'] . '" value="' . $form_fields['visible_fields'] . '">';
                }

                if(strlen($form_fields['hidden_fields']??'') and strlen($form_fields['choices']??''))
                {
                    $html .= '
				<input class="disply-fields-rules-' . $form_fields['fields_id'] . '" type="hidden" data-type="hidden" data-choices="' . $form_fields['choices'] . '" value="' . $form_fields['hidden_fields'] . '">';
                }

                $rules_for_fields[$form_fields['fields_id']] = $form_fields['type'];
            }

//include form rules if form exist

            if($this->form_name)
            {
                $html .= '
		<script>
			$(function(){
			';

                $container = ((IS_AJAX and $app_user['id'] != 0) ? 'ajax-modal' : '');

                foreach($rules_for_fields as $fields_id => $fields_type)
                {
                    $html .= '
			$(".field_' . $fields_id . '").change(function(){					
				app_handle_forms_fields_display_rules(\'' . $container . '\',' . $fields_id . ',\'' . $fields_type . '\',false,false)						
			})	
			
			' . (($app_module_path != 'items/info' and $app_module_path != 'items/comments_form' and $app_module_path != 'items/processes') ? 'app_handle_forms_fields_display_rules(\'' . $container . '\',' . $fields_id . ',\'' . $fields_type . '\',false,false)' : '') . '
		';

                    //handle comments and process forms
                    if(($app_module_path == 'items/comments_form' or $app_module_path == 'items/processes'))
                    {
                        $field = db_find('app_fields', $fields_id);
                        $cfg = new fields_types_cfg($field['configuration']);

                        $is_multiple = false;

                        if(in_array($field['type'], ['fieldtype_dropdown_multiple', 'fieldtype_checkboxes']))
                        {
                            $is_multiple = true;
                        }

                        if($field['type'] == 'fieldtype_grouped_users' and in_array($cfg->get('display_as'), ['checkboxes', 'dropdown_muliple']))
                        {
                            $is_multiple = true;
                        }

                        if($this->item)
                        {
                            $value = items::prepare_field_value_by_type($field, $this->item);
                            $html .= 'app_handle_forms_fields_display_rules(\'\',' . $field['id'] . ',"","' . (strlen($value) ? $value : '0') . '",' . (int) $is_multiple . '); ';
                        }
                        else
                        {
                            $html .= 'app_handle_forms_fields_display_rules(\'\',' . $field['id'] . ',"",false,' . (int) $is_multiple . '); ';
                        }
                    }
                }

                $html .= '
			})
		</script>
			';
            }
           
        }
        
        return $html;
    }

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
