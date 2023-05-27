<?php

class entities
{

    public static function get_cache()
    {
        $cache = array();
        $entities_query = db_query("select * from app_entities");
        while ($entities = db_fetch_array($entities_query))
        {
            $cache[$entities['id']] = $entities;
        }

        return $cache;
    }

    public static function has_subentities($entities_id)
    {
        return db_count('app_entities', $entities_id, 'parent_id');
    }

    public static function delete($id)
    {
        $fields_query = db_fetch_all('app_fields', "entities_id='" . db_input($id) . "'");
        while ($fields = db_fetch_array($fields_query))
        {
            db_delete_row('app_fields', $fields['id']);
            db_delete_row('app_fields_choices', $fields['id'], 'fields_id');
        }

        db_delete_row('app_forms_tabs', $id, 'entities_id');
        db_delete_row('app_entities_configuration', $id, 'entities_id');
        db_delete_row('app_entities', $id);

        $reports_query = db_query("select * from app_reports where entities_id='" . $id . "'");
        while ($v = db_fetch_array($reports_query))
        {
            db_delete_row('app_reports_filters', $v['id'], 'reports_id');
        }

        db_delete_row('app_reports', $id, 'entities_id');

        db_query("delete from app_entities_access where entities_id='" . $id . "'");

        //delete notifications
        db_query("delete from app_users_notifications where entities_id='" . $id . "'");

        //access rules
        db_query("delete from app_access_rules where entities_id='" . db_input($id) . "'");
        db_query("delete from app_access_rules_fields where entities_id='" . db_input($id) . "'");

        //help pages
        db_query("delete from app_help_pages where entities_id='" . db_input($id) . "'");

        //delete timers
        if (class_exists('timer'))
        {
            db_query("delete from app_ext_timer where entities_id='" . $id . "'");
        }

        if (is_ext_installed())
        {
            export_templates::delete_by_entity_id($id);
        }
    }

    public static function get_flowchart_shcema($parent_id = 0, $tree = array(), $level = 0, $x = 0, $y = 0)
    {
        $entities_query = db_query("select * from app_entities where parent_id='" . $parent_id . "' order by sort_order, name");

        while ($entities = db_fetch_array($entities_query))
        {
            $tree['tree'][] = array(
                'id' => $entities['id'],
                'parent_id' => $entities['parent_id'],
                'name' => $entities['name'],
                'notes' => $entities['notes'],
                'sort_order' => $entities['sort_order'],
                'level' => $level,
                'x' => $x,
                'y' => $y,
            );

            $tree['y'] = $y;

            $tree = entities::get_flowchart_shcema($entities['id'], $tree, $level + 1, $x + 130, $y);

            $y = $tree['y'];

            $count_fields = 0;
            $check_fields_query = db_query("select * from app_fields where entities_id = '" . $entities['id'] . "' and type in ('fieldtype_users','fieldtype_users_ajax','fieldtype_grouped_users','fieldtype_entity','fieldtype_related_records','fieldtype_formula')");
            while ($check_fields = db_fetch_array($check_fields_query))
            {
                if ($check_fields['type'] == 'fieldtype_formula')
                {
                    $cfg = new fields_types_cfg($check_fields['configuration']);

                    if (strstr($cfg->get('formula'), '{'))
                    {
                        $count_fields++;
                    }
                } else
                {
                    $count_fields++;
                }
            }

            $y += 30 + ($count_fields * 11);
        }

        return $tree;
    }

    public static function insert_default_form_tab($id)
    {
        $sql_data = array('name' => TEXT_INFO, 'entities_id' => $id);

        db_perform('app_forms_tabs', $sql_data);

        return db_insert_id();
    }

    public static function insert_reserved_fields($id, $forms_tabs_id)
    {
        $sort_order = 0;
        foreach (fields_types::get_reserved_types() as $type)
        {
            $sql_data = array('forms_tabs_id' => $forms_tabs_id,
                'entities_id' => $id,
                'name' => '',
                'listing_status' => 1,
                'sort_order' => $sort_order,
                'listing_sort_order' => $sort_order,
                'type' => $type);
            db_perform('app_fields', $sql_data);

            $sort_order++;
        }
    }

    public static function get_listing_heading($entities_id)
    {
        $cfg = entities::get_cfg($entities_id);

        if (strlen($cfg['listing_heading']) > 0)
        {
            return $cfg['listing_heading'];
        } else
        {
            return entities::get_name_by_id($entities_id);
        }
    }

    public static function set_cfg($k, $v, $entities_id)
    {
        $cfq_query = db_query("select * from app_entities_configuration where configuration_name='" . db_input($k) . "' and entities_id='" . db_input($entities_id) . "'");
        if (!$cfq = db_fetch_array($cfq_query))
        {
            db_perform('app_entities_configuration', array('configuration_value' => $v, 'configuration_name' => $k, 'entities_id' => $entities_id));
        } else
        {
            db_perform('app_entities_configuration', array('configuration_value' => $v), 'update', "configuration_name='" . db_input($k) . "' and entities_id='" . db_input($entities_id) . "'");
        }
    }

    public static function get_cfg($id)
    {
        $cfg = array();
        $info_query = db_fetch_all('app_entities_configuration', "entities_id='" . db_input($id) . "'");
        while ($info = db_fetch_array($info_query))
        {
            $cfg[$info['configuration_name']] = $info['configuration_value'];
        }

        $cfg_keys = array('menu_title',
            'menu_icon',
            'listing_heading',
            'window_heading',
            'insert_button',
            'use_editor_in_comments',
            'use_comments',
            'email_subject_new_item',
            'email_subject_updated_item',
            'email_subject_new_comment',
            'number_fixed_field_in_listing',
            'heading_width_based_content',
            'change_col_width_in_listing',
        );

        foreach ($cfg_keys as $k)
        {
            if (!isset($cfg[$k]))
            {
                $cfg[$k] = '';
            }
        }

        return $cfg;
    }

    public static function check_before_delete($id)
    {
        $msg = '';
        $name = entities::get_name_by_id($id);

        //check if entity is Users
        if ($id == 1)
        {
            $msg = sprintf(TEXT_WARN_DELETE_ENTITY_USERS, $name);
        }
        //check if there are sub entities
        elseif (db_count('app_entities', $id, 'parent_id') > 0)
        {
            $msg = sprintf(TEXT_WARN_DELETE_ENTITY_HAS_PARENT, $name);
        }
        //chec if there is items
        elseif (db_count('app_entity_' . $id) > 0)
        {
            $msg = sprintf(TEXT_WARN_DELETE_ENTITY_HAS_ITEMS, $name);
        }
        //check if there are relationship with other entities
        else
        {
            $relationship = array();
            $fields_query = db_query("select * from app_fields where entities_id!='" . db_input($id) . "' and type in ('fieldtype_entity','fieldtype_related_records')");
            while ($fields = db_fetch_array($fields_query))
            {
                $cfg = new fields_types_cfg($fields['configuration']);
                if ($cfg->get('entity_id') == $id)
                {
                    $relationship[] = entities::get_name_by_id($fields['entities_id']) . ': ' . $fields['name'];
                }
            }

            if (count($relationship) > 0)
            {
                if (!defined(TEXT_WARN_DELETE_ENTITY_HAS_RELATIONSHIP))
                {
                    define('TEXT_WARN_DELETE_ENTITY_HAS_RELATIONSHIP', 'You can\'t delete entity <b>%s</b> because it has relationship with: <br>%s.<br><br>Delete all fields which related to this entity.');
                }

                $msg = sprintf(TEXT_WARN_DELETE_ENTITY_HAS_RELATIONSHIP, $name, implode('<br>', $relationship));
            }
        }

        return $msg;
    }

    public static function get_name_by_id($id)
    {
        global $app_entities_cache;

        return (isset($app_entities_cache[$id]) ? $app_entities_cache[$id]['name'] : '');
    }

    public static function get_name_cache()
    {
        $cache = array();
        $entities_query = db_query("select * from app_entities");
        while ($entities = db_fetch_array($entities_query))
        {
            $cache[$entities['id']] = $entities['name'];
        }

        return $cache;
    }

    public static function get_choices_with_empty($empty_text = TEXT_VIEW_ALL)
    {
        $choices = self::get_choices();
        $choices = array('0' => $empty_text) + $choices;

        return $choices;
    }

    public static function get_choices($add_id_to_name = false)
    {
        $choices = array();

        foreach (entities::get_tree(0, [], 0, [], [], $add_id_to_name) as $v)
        {
            $choices[$v['id']] = str_repeat('- ', $v['level']) . $v['name'];
        }

        return $choices;
    }
    
    public static function get_nested_choices($parent_id=0)
    {
        $choices = [];
        $entities_query = db_query("select id,name from app_entities where parent_id={$parent_id}");
        while($entities = db_fetch_array($entities_query))
        {
            $choices[$entities['id']] =  $entities['name'];
        }
        
        return $choices;
    }

    public static function get_tree($parent_id = 0, $tree = array(), $level = 0, $path = array(), $skip = array(), $add_id_to_name = false, $entities_filter = 0)
    {
        global $app_user;

        if ($app_user['group_id'] == 0)
        {
            $entities_query = db_query("select e.* from app_entities e left join app_entities_groups eg on e.group_id=eg.id  where e.parent_id='" . $parent_id . "' " . (($parent_id==0 and $entities_filter>0) ? " and e.group_id={$entities_filter}":"") . " order by eg.sort_order, eg.name, e.sort_order, e.name");
        } else
        {
            $entities_query = db_query("select e.* from app_entities e, app_entities_access ea where e.parent_id='" . $parent_id . "' and e.id=ea.entities_id and length(ea.access_schema)>0 and ea.access_groups_id='" . db_input($app_user['group_id']) . "' order by e.sort_order, e.name");
        }

        while ($entities = db_fetch_array($entities_query))
        {
            if (in_array($entities['id'], $skip))
                continue;

            $tree[] = array(
                'id' => $entities['id'],
                'parent_id' => $entities['parent_id'],
                'group_id' => $entities['group_id'],
                'name' => $entities['name'] . ($add_id_to_name ? ' [#' . $entities['id'] . ']' : ''),
                'notes' => $entities['notes'],
                'sort_order' => $entities['sort_order'],
                'level' => $level,
                'path' => $path,
            );

            $tree = entities::get_tree($entities['id'], $tree, $level + 1, array_merge($path, array($entities['id'])), $skip, $add_id_to_name);
        }

        return $tree;
    }

    public static function get_parents($entities_id, $parents = array())
    {
        global $app_entities_cache;

        //$entities_query = db_query("select * from app_entities where id='" . $entities_id . "'");
        //if($entities = db_fetch_array($entities_query))
        if (isset($app_entities_cache[$entities_id]))
        {
            $entities = $app_entities_cache[$entities_id];

            if ($entities['parent_id'] > 0)
            {
                $parents[] = $entities['parent_id'];

                $parents = self::get_parents($entities['parent_id'], $parents);
            }
        }

        return $parents;
    }

    public static function prepare_tables($entities_id)
    {
        $sql = '
      CREATE TABLE IF NOT EXISTS app_entity_' . (int) $entities_id . ' (
        id int(11) UNSIGNED NOT NULL auto_increment,
        parent_id int(11) UNSIGNED default 0,
        parent_item_id int(11) UNSIGNED default 0,
        linked_id int(11) UNSIGNED default 0,
        date_added BIGINT(11) default 0,
      	date_updated BIGINT(11) default 0,	
        created_by int(11) UNSIGNED default NULL,
        sort_order int(11) default 0,
        PRIMARY KEY  (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ';

        db_query($sql);

        $sql = 'ALTER TABLE app_entity_' . (int) $entities_id . ' ADD INDEX idx_parent_id (parent_id);';
        db_query($sql);

        $sql = 'ALTER TABLE app_entity_' . (int) $entities_id . ' ADD INDEX idx_parent_item_id (parent_item_id);';
        db_query($sql);

        $sql = 'ALTER TABLE app_entity_' . (int) $entities_id . ' ADD INDEX idx_created_by (created_by);';
        db_query($sql);

        $sql = '
    		CREATE TABLE IF NOT EXISTS app_entity_' . (int) $entities_id . '_values (
				  id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
				  items_id int(11) UNSIGNED NOT NULL,
				  fields_id int(11) UNSIGNED NOT NULL,
				  value int(11) UNSIGNED NOT NULL,
				  PRIMARY KEY (`id`),
				  KEY `idx_items_id` (`items_id`),
				  KEY `idx_fields_id` (`fields_id`),
    			KEY `idx_items_fields_id` (`items_id`,`fields_id`),
    			KEY `idx_value_id` (`value`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    	';

        db_query($sql);
    }

    public static function delete_tables($entities_id)
    {
        $sql = 'DROP TABLE IF EXISTS app_entity_' . (int) $entities_id;
        db_query($sql);

        $sql = 'DROP TABLE IF EXISTS app_entity_' . (int) $entities_id . '_values';
        db_query($sql);
    }

    public static function prepare_field_type($type)
    {
        switch ($type)
        {            
            case 'fieldtype_js_formula':
            case 'fieldtype_mysql_query':
            case 'fieldtype_input_numeric':
            case 'fieldtype_input_numeric_comments':
            case 'fieldtype_auto_increment':
            case 'fieldtype_phone':
            case 'fieldtype_signature':
            case 'fieldtype_ajax_request':
                $db_type = 'VARCHAR(64)';
                break;
            case 'fieldtype_input':
            case 'fieldtype_input_masked':
            case 'fieldtype_input_dynamic_mask':
            case 'fieldtype_input_url':
            case 'fieldtype_input_email':
            case 'fieldtype_google_map':
            case 'fieldtype_input_protected':
            case 'fieldtype_php_code':
            case 'fieldtype_video':
            case 'fieldtype_random_value':
                $db_type = 'VARCHAR(255)';
                break;
            case 'fieldtype_input_encrypted':
                $db_type = 'VARBINARY(255)';
                break;
            case 'fieldtype_textarea_encrypted':
                $db_type = 'VARBINARY(2500)';
                break;
            case 'fieldtype_nested_calculations':
            case 'fieldtype_months_difference':
            case 'fieldtype_years_difference':
            case 'fieldtype_days_difference':
            case 'fieldtype_hours_difference':
                $db_type = 'FLOAT';
                break;
            case 'fieldtype_boolean_checkbox':
            case 'fieldtype_boolean':
                $db_type = 'VARCHAR(8)';
                break;
            case 'fieldtype_input_date':
            case 'fieldtype_input_datetime':
            case 'fieldtype_dynamic_date':
            case 'fieldtype_jalali_calendar':
            case 'fieldtype_input_date_extra':
                $db_type = 'BIGINT(11)';
                break;
            case 'fieldtype_dropdown':
            case 'fieldtype_radioboxes':
            case 'fieldtype_progress':
            case 'fieldtype_image_map':
            case 'fieldtype_entity_multilevel':
            case 'fieldtype_stages':
            case 'fieldtype_autostatus':
            case 'fieldtype_time':
                $db_type = 'INT(11)';
                break;
            case 'fieldtype_input_ip':
                $db_type = 'INT(11) UNSIGNED';
                break;
            case 'fieldtype_input_vpic':
            case 'fieldtype_barcode':
            case 'fieldtype_image':
            case 'fieldtype_image_ajax':
            case 'fieldtype_image_map_nested':
            case 'fieldtype_input_file':
            case 'fieldtype_dropdown_multilevel':
            case 'fieldtype_color':    
                $db_type = 'VARCHAR(255)';
                break;
            case 'fieldtype_formula':
            case 'fieldtype_text_pattern':
            case 'fieldtype_related_records':            
            case 'fieldtype_qrcode':
            case 'fieldtype_section':
            case 'fieldtype_parent_value':
            case 'fieldtype_items_by_query':
            case 'fieldtype_process_button':
            case 'fieldtype_subentity_form':
            case 'fieldtype_related_mail':
                $db_type = 'VARCHAR(1)';
                break;
            default:
                $db_type = 'TEXT';
                break;
        }

        return $db_type;
    }

    public static function prepare_field($entities_id, $fields_id, $type)
    {
        $db_type = self::prepare_field_type($type);
        $sql = 'ALTER TABLE  app_entity_' . (int) $entities_id . ' ADD  field_' . (int) $fields_id . ' ' . $db_type . ' NOT NULL';
        db_query($sql);

        //add index
        entities::prepare_field_index($entities_id, $fields_id, $type);
    }

    public static function prepare_field_index($entities_id, $fields_id, $type)
    {
        if (in_array($type, [
                    'fieldtype_dropdown',
                    'fieldtype_radioboxes',
                    'fieldtype_progress',
                    'fieldtype_stages',
                    'fieldtype_entity',
                    'fieldtype_entity_ajax',
                    'fieldtype_entity_multilevel',
                    'fieldtype_access_group',
                    'fieldtype_users',
                    'fieldtype_users_ajax',
                    'fieldtype_users_approve',
                    'fieldtype_user_roles',
                    'fieldtype_tags',
                    'fieldtype_dropdown_multiple',
                    'fieldtype_dropdown_multilevel',
                    'fieldtype_checkboxes',
                    'fieldtype_autostatus',
                    'fieldtype_random_value',
            
                    'fieldtype_input_date',
                    'fieldtype_input_date_extra',
                    'fieldtype_input_datetime'
                ]))
        {
            if (entities::prepare_field_type($type) == 'TEXT')
            {
                db_query("ALTER TABLE app_entity_" . (int) $entities_id . " ADD INDEX idx_field_" . (int) $fields_id . " (field_" . (int) $fields_id . "(128));");
            } else
            {
                db_query("ALTER TABLE app_entity_" . (int) $entities_id . " ADD INDEX idx_field_" . (int) $fields_id . " (field_" . (int) $fields_id . ");");
            }
        }
    }

    public static function delete_field($entities_id, $fields_id)
    {
        $sql = 'ALTER TABLE app_entity_' . (int) $entities_id . ' DROP field_' . (int) $fields_id;
        db_query($sql);
    }

    public static function is_hidden_by_condition($entities_id, $parent_item_id)
    {
        global $app_entities_cache, $sql_query_having;

        $parent_entity_id = $app_entities_cache[$entities_id]['parent_id'];

        $reports_info_query = db_query("select id from app_reports where entities_id='" . $parent_entity_id . "' and reports_type='hide_subentity_" . $entities_id . "'", false);
        if ($reports_info = db_fetch_array($reports_info_query))
        {
            $sql_query_having[$parent_entity_id] = [];
            
            $listing_sql_query = reports::add_filters_query($reports_info['id'], '', 'e');

            //prepare having query for formula fields
            if (isset($sql_query_having[$parent_entity_id]))
            {
                $listing_sql_query .= reports::prepare_filters_having_query($sql_query_having[$parent_entity_id]);
            }

            //if not filters set
            if (!strlen($listing_sql_query))
                return false;

            //print_rr($reports_info);
            $item_query = db_query("select e.id " . fieldtype_formula::prepare_query_select($parent_entity_id, '', false, array('reports_id' => $reports_info['id'])) . " from app_entity_{$parent_entity_id} e where e.id={$parent_item_id} " . $listing_sql_query, false);
            if ($item = db_fetch_array($item_query))
            {
                return true;
            }
        }

        return false;
    }
    
    static function render_goto_menu($entity_id)
    {
        global $app_entities_cache;
        
        $html = '';
        
        if($app_entities_cache[$entity_id]['parent_id']>0 or self::has_subentities($entity_id))
        {
            $html = '
                <li class="nav_entities_goto">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">' . TEXT_GO_TO . ' <i class="fa fa-angle-down"></i></a>
                    <ul class="dropdown-menu tree-table-menu">    
                ';
            if($app_entities_cache[$entity_id]['parent_id']>0)
            {
                $parents = array_reverse(self::get_parents($entity_id));
                $parent_entity_id = $parents[0];
            }
            else
            {
                $parent_entity_id = $entity_id;
            }
            
            $html_tt = '<div class="tt" data-tt-id="entity_' . $parent_entity_id . '"></div> ';
            $html .= '
                    <li class="' . ($parent_entity_id==$entity_id ? 'active':'') . '">
                         ' . link_to($html_tt . $app_entities_cache[$parent_entity_id]['name'], url_for('entities/fields&entities_id=' . $parent_entity_id)). '
                    </li>';
            
            foreach(self::get_tree($parent_entity_id) as $v)
            {
                $html_tt = '<div class="tt" data-tt-id="entity_' . $v['id']. '" ' . ($v['parent_id']>0 ? 'data-tt-parent="entity_' . $v['parent_id'] . '"':''). '></div> ';
                
                
                
                $html .= '
                    <li class="' . ($v['id']==$entity_id ? 'active':'') . '">
                        ' . link_to($html_tt . $v['name'], url_for('entities/fields&entities_id=' . $v['id'])). '
                    </li>';
            }
            $html .= '</ul></li>';
        }
        
        return $html;
    }
    
}
