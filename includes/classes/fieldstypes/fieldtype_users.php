<?php

class fieldtype_users
{

    public $options;

    function __construct()
    {
        $this->options = array('title' => TEXT_FIELDTYPE_USERS_TITLE);
    }

    function get_configuration($params = array())
    {
        $entity_info = db_find('app_entities', $params['entities_id']);

        $cfg = array();
        $cfg[TEXT_SETTINGS][] = array('title' => TEXT_DISPLAY_USERS_AS,
            'name' => 'display_as',
            'type' => 'dropdown',
            'params' => array('class' => 'form-control input-xlarge'),
            'choices' => array('dropdown' => TEXT_DISPLAY_USERS_AS_DROPDOWN, 'checkboxes' => TEXT_DISPLAY_USERS_AS_CHECKBOXES, 'dropdown_muliple' => TEXT_DISPLAY_USERS_AS_DROPDOWN_MULTIPLE));

        $cfg[TEXT_SETTINGS][] = array('title' => TEXT_HIDE_FIELD_NAME, 'name' => 'hide_field_name', 'type' => 'checkbox', 'tooltip_icon' => TEXT_HIDE_FIELD_NAME_TIP);

        $cfg[TEXT_SETTINGS][] = array('title' => TEXT_DISABLE_NOTIFICATIONS, 'name' => 'disable_notification', 'type' => 'checkbox', 'tooltip_icon' => TEXT_DISABLE_NOTIFICATIONS_FIELDS_INFO);

        if ($entity_info['parent_id'] > 0)
        {
            $cfg[TEXT_SETTINGS][] = array('title' => TEXT_DISABLE_USERS_DEPENDENCY, 'name' => 'disable_dependency', 'type' => 'checkbox', 'tooltip_icon' => TEXT_DISABLE_USERS_DEPENDENCY_INFO);
        }

        $cfg[TEXT_SETTINGS][] = array('title' => TEXT_HIDE_ADMIN, 'name' => 'hide_admin', 'type' => 'checkbox');

        $cfg[TEXT_SETTINGS][] = array('title' => TEXT_AUTHORIZED_USER_BY_DEFAULT, 'name' => 'authorized_user_by_default', 'type' => 'checkbox', 'tooltip_icon' => TEXT_AUTHORIZED_USER_BY_DEFAULT_INFO);
        
        $cfg[TEXT_SETTINGS][] = array('title' => TEXT_ALLOW_SEARCH, 'name' => 'allow_search', 'type' => 'checkbox', 'tooltip_icon' => TEXT_ALLOW_SEARCH_TIP);

        $cfg[TEXT_EXTRA][] = array('title' => TEXT_HIDE_ACCESS_GROUP, 'name' => 'hide_access_group', 'type' => 'checkbox');
        $cfg[TEXT_EXTRA][] = array('title' => TEXT_USERS_GROUPS, 'name' => 'use_groups', 'type' => 'dropdown', 'choices' => access_groups::get_choices(), 'tooltip_icon' => TEXT_USE_GROUPS_TIP, 'params' => array('class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple'));

        return $cfg;
    }

    static function get_choices($field, $params, $value = '')
    {
        global $app_users_cache, $app_user;

        $cfg = new fields_types_cfg($field['configuration']);

        $entities_id = $field['entities_id'];

        //get access schema
        $access_schema = users::get_entities_access_schema_by_groups($entities_id);

        //check if parent item has users fields and if users are assigned
        $has_parent_users = false;
        $parent_users_list = [];

        if (isset($params['parent_entity_item_id']) and $params['parent_entity_item_id'] > 0 and $cfg->get('disable_dependency') != 1)
        {
            if ($parent_users_list = items::get_paretn_users_list($entities_id, $params['parent_entity_item_id']))
            {
                $has_parent_users = true;
            }
        }

        //get users choices
        //select all active users or already assigned users
        $where_sql = (strlen($value) ? "(u.field_5=1 or u.id in (" . $value . "))" : "u.field_5=1");
               
        $choices = array();
        $order_by_sql = ($cfg->get('hide_access_group') != 1 ? 'group_name,' : '');
        $order_by_sql .= (CFG_APP_DISPLAY_USER_NAME_ORDER == 'firstname_lastname' ? ' u.field_7, u.field_8' : ' u.field_8, u.field_7');
        $users_query = db_query("select u.*,a.name as group_name from app_entity_1 u left join app_access_groups a on a.id=u.field_6 where {$where_sql} order by " . $order_by_sql);
        while ($users = db_fetch_array($users_query))
        {
            $multiple_access_groups = strlen($users['multiple_access_groups']) ? explode(',',$users['multiple_access_groups']) : [$users['field_6']];
            
            foreach($multiple_access_groups as $access_group_id)
            {
                //hide administrators
                if ($cfg->get('hide_admin') == 1 and $access_group_id==0)
                {
                    continue;
                }
                
                 //display users from selected users groups only
                if (is_array($cfg->get('use_groups')) and count($cfg->get('use_groups')) and !in_array($access_group_id,$cfg->get('use_groups')))
                {
                    continue;
                }
            
                if (!isset($access_schema[$access_group_id]))
                {
                    $access_schema[$access_group_id] = array();
                }

                if ($access_group_id == 0 or in_array('view', $access_schema[$access_group_id]) or in_array('view_assigned', $access_schema[$access_group_id]))
                {
                    //check parent users and check already assigned
                    if ($has_parent_users and!in_array($users['id'], $parent_users_list) and!in_array($users['id'], explode(',', $value)))
                    {
                        continue;
                    }

                    $group_name = (strlen($access_group_id) > 0 ? access_groups::get_name_by_id($access_group_id) : TEXT_ADMINISTRATOR);

                    if ($cfg->get('hide_access_group') == 1)
                    {
                        $choices[$users['id']] = $app_users_cache[$users['id']]['name'];
                    } else
                    {
                        $choices[$group_name][$users['id']] = $app_users_cache[$users['id']]['name'];
                    }   
                    
                    //break from foreach to add only one user in list
                    break;
                }
            }
        }

        return $choices;
    }

    function render($field, $obj, $params = array())
    {
        global $app_users_cache, $app_user;

        $cfg = new fields_types_cfg($field['configuration']);

        $entities_id = $field['entities_id'];
                
        $value = '';
        
        if(strlen($obj['field_' . $field['id']]??''))
        {
            $value = $obj['field_' . $field['id']]; 
        }
        elseif($cfg->get('authorized_user_by_default') == 1)
        {
            $value = $app_user['id'];
        }

        $choices = self::get_choices($field, $params, $value);

        if ($cfg->get('display_as') == 'dropdown')
        {
            //add empty value for comment form
            $choices = ($params['form'] == 'comment' ? array('' => '') + $choices : $choices);

            $attributes = array('class' => 'form-control chosen-select input-large field_' . $field['id'] . ($field['is_required'] == 1 ? ' required' : ''));

            return select_tag('fields[' . $field['id'] . ']', array('' => TEXT_NONE) + $choices, $value, $attributes) . fields_types::custom_error_handler($field['id']);
        } elseif ($cfg->get('display_as') == 'checkboxes')
        {
            $attributes = array('class' => 'field_' . $field['id'] . ($field['is_required'] == 1 ? ' required' : ''));

            return '<div class="checkboxes_list ' . ($field['is_required'] == 1 ? ' required' : '') . '">' . select_checkboxes_tag('fields[' . $field['id'] . ']', $choices, $value, $attributes) . '</div>';
        } elseif ($cfg->get('display_as') == 'dropdown_muliple')
        {
            $attributes = array('class' => 'form-control input-xlarge chosen-select field_' . $field['id'] . ($field['is_required'] == 1 ? ' required' : ''),
                'multiple' => 'multiple',
                'data-placeholder' => TEXT_SELECT_SOME_VALUES);
            return select_tag('fields[' . $field['id'] . '][]', $choices, explode(',', $value), $attributes) . fields_types::custom_error_handler($field['id']);
        }
    }

    function process($options)
    {
        global $app_send_to, $app_send_to_new_assigned;

        $cfg = new fields_types_cfg($options['field']['configuration']);

        if ($cfg->get('disable_notification') != 1)
        {
            if (is_array($options['value']))
            {
                $app_send_to = array_merge($options['value'], $app_send_to??[]);
            } else
            {
                $app_send_to[] = $options['value'];
            }
        }

        $value = (is_array($options['value']) ? implode(',', $options['value']) : $options['value']);

        //check if value changed
        if ($cfg->get('disable_notification') != 1)
        {
            if (!$options['is_new_item'])
            {
                if ($value != $options['current_field_value'])
                {
                    foreach (array_diff(explode(',', $value), explode(',', $options['current_field_value'])) as $v)
                    {
                        $app_send_to_new_assigned[] = $v;
                    }
                }
            }
        }

        return $value;
    }

    function output($options)
    {
        global $app_users_cache;

        if (isset($options['is_export']))
        {
            $users_list = array();
            foreach (explode(',', $options['value']??'') as $id)
            {
                if (isset($app_users_cache[$id]))
                {
                    $users_list[] = $app_users_cache[$id]['name'];
                }
            }

            return implode(', ', $users_list);
        } else
        {
            $users_list = array();
            foreach (explode(',', $options['value']??'') as $id)
            {
                if (isset($app_users_cache[$id]))
                {

                    if (isset($options['display_user_photo']))
                    {
                        $photo = '<div class="user-photo-box">' . render_user_photo($app_users_cache[$id]['photo']) . '</div>';
                        $is_photo_display = true;
                    } else
                    {
                        $photo = '';
                        $is_photo_display = false;
                    }

                    $users_list[] = $photo . ' <span class="user-name" ' . users::render_publi_profile($app_users_cache[$id], $is_photo_display) . '>' . $app_users_cache[$id]['name'] . '</span> <div style="clear:both"></div>';
                }
            }

            return implode('', $users_list);
        }
    }

    function reports_query($options)
    {
        global $app_user;

        $filters = $options['filters'];
        $sql_query = $options['sql_query'];
        
        $prefix = (strlen($options['prefix']) ? $options['prefix'] : 'e');

        if (strlen($filters['filters_values']) > 0)
        {
            $filters['filters_values'] = str_replace('current_user_id', $app_user['id'], $filters['filters_values']);

            $sql_query[] = "(select count(*) from app_entity_" . $options['entities_id'] . "_values as cv where cv.items_id={$prefix}.id and cv.fields_id='" . db_input($options['filters']['fields_id']) . "' and cv.value in (" . $filters['filters_values'] . ")) " . ($filters['filters_condition'] == 'include' ? '>0' : '=0');
        }

        return $sql_query;
    }

}
