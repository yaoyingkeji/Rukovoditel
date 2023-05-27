<?php

if(!IS_AJAX)
{
    //exit();
}

switch($app_module_action)
{
    case 'select_values':
        $panel_field_query = db_query("select * from app_filters_panels_fields where id='" . _POST('panel_field_id'). "'",false);
        $panel_field = db_fetch_array($panel_field_query);
                
        $entity_info = db_find('app_entities', _POST('entity_id'));
        $field = $app_fields_cache[_POST('entity_id')][_POST('field_id')];
        $cfg = new settings($field['configuration']);
        
        $parent_item_id = isset($_POST['parent_item_id']) ? _POST('parent_item_id') : false;
                        
        $search = isset($_POST['search']) ? $_POST['search']:'';
        
        $where_sql = '';
        
        if(strlen($search))
        {
            $where_sql .= " and c.name like '%" . db_input($search). "%'";
        }
        
        $where_sql .= filters_panels::exclude_choices_values_not_in_listing_sql(_POST('panel_field_id'),_POST('reports_id'),$parent_item_id);
        
        if(strlen($panel_field['exclude_values']))
        {
            $where_sql .= " and c.id not in (" . db_input_in($panel_field['exclude_values']) . ")";
        }
                        
        $results = [];
                
        if($cfg->get('use_global_list') > 0)
        {
            $choices_query = db_query("select c.* from app_global_lists_choices c where c.lists_id = '" . db_input($cfg->get('use_global_list')) . "' and c.is_active=1 {$where_sql} order by c.sort_order, c.name");

            while($choices = db_fetch_array($choices_query))
            {
                $results[] = ['id' => $choices['id'], 'text' => $choices['name'], 'html' => '<div>' . $choices['name'] . '</div>'];
            }
        }
        else
        {
            $choices_query = db_query("select c.* from app_fields_choices c where c.fields_id = '" . db_input($field['id']) . "' and c.is_active=1 {$where_sql} order by c.sort_order, c.name");
            while($choices = db_fetch_array($choices_query))
            {
                $results[] = ['id' => $choices['id'], 'text' => $choices['name'], 'html' => '<div>' . $choices['name'] . '</div>'];
            }
        }   
        
        $response = ['results' => $results];
        
        echo json_encode($response);

        exit();
        
        break;
}