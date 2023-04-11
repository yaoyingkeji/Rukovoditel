<?php

switch($app_module_action)
{
    case 'save':

        $sql_data = array(
            'entities_id' => $_GET['entities_id'],
            'name' => $_POST['name'],
            'is_active'	=> (isset($_POST['is_active']) ? 1:0),
            'icon' => $_POST['icon'],
            'icon_color' => $_POST['icon_color'],
            'entities' => (isset($_POST['entities']) ? implode(',', $_POST['entities']) : ''),            
            'sort_order' => $_POST['sort_order'],
        );

        if(isset($_GET['id']))
        {
            db_perform('app_nested_entities_menu', $sql_data, 'update', "id='" . db_input($_GET['id']) . "'");
        }
        else
        {
            db_perform('app_nested_entities_menu', $sql_data);
        }

        redirect_to('nested_entities_menu/menu', 'entities_id=' . $_GET['entities_id']);
        break;

    case 'delete':

        if(isset($_GET['id']))
        {
            db_delete_row('app_nested_entities_menu', $_GET['id']);

            $alerts->add(sprintf(TEXT_WARN_DELETE_SUCCESS, ''), 'success');
        }

        redirect_to('nested_entities_menu/menu', 'entities_id=' . $_GET['entities_id']);
        break;            
        
    case 'sort':
        $choices_sorted = $_POST['choices_sorted'];
        if(strlen($choices_sorted)>0)
        {      	      
            $choices_sorted = json_decode(stripslashes($choices_sorted),true);
                        
            foreach($choices_sorted as $sort_order=>$v)
            {
                db_query("update app_nested_entities_menu set sort_order={$sort_order} where id={$v['id']}");
            }
        }
        
        redirect_to('nested_entities_menu/menu', 'entities_id=' . $_GET['entities_id']);
        break;
}