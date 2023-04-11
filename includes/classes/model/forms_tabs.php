<?php

class forms_tabs
{

    public static function get_name_by_id($id)
    {
        $obj = db_find('app_forms_tabs', $id);

        return $obj['name'];
    }

    public static function check_before_delete($forms_tabs_id)
    {
        $msg = '';

        if(db_count('app_fields', $forms_tabs_id, 'forms_tabs_id') > 0)
        {
            $msg = sprintf(TEXT_WARN_DELETE_FROM_TAB, forms_tabs::get_name_by_id($forms_tabs_id));
        }

        return $msg;
    }

    public static function get_choices($entities_id)
    {
        $choices = array();
        $query = db_fetch_all('app_forms_tabs', "entities_id='" . (int) $entities_id . "' and is_folder=0", 'sort_order, name');
        while($v = db_fetch_array($query))
        {
            $choices[$v['id']] = $v['name'];
        }

        return $choices;
    }

    public static function get_last_sort_number($entities_id)
    {
        $v = db_fetch_array(db_query("select max(sort_order) as max_sort_order from app_forms_tabs where entities_id = '" . db_input($entities_id) . "'"));

        return $v['max_sort_order'];
    }

    public static function is_reserved($tabs_id)
    {
        $tab_query = db_query("select * from app_fields where forms_tabs_id='" . db_input($tabs_id) . "' and type='fieldtype_id'");
        if($tab = db_fetch_array($tab_query))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    static function get_tree($entities_id, $parent_id = 0, $tree = array(), $level = 0,$parent_name='')
    {
        $tabs_query = db_query("select * from app_forms_tabs where parent_id=" . $parent_id . " and entities_id={$entities_id} order by sort_order, name");

        while($tabs = db_fetch_array($tabs_query))
        {
            $tabs['level'] = $level;
            $tabs['parent_name'] = $parent_name;

            $tree[] = $tabs;

            $tree = self::get_tree($entities_id, $tabs['id'], $tree, $level + 1, $tabs['name']);
        }

        return $tree;
    }
    
    static function render_tabs_nav($entities_id)
    {
        $html = '';
        $tabs_query = db_query("select * from app_forms_tabs where parent_id=0 and entities_id={$entities_id} order by sort_order, name");
        while($tabs = db_fetch_array($tabs_query))
        {
            if($tabs['is_folder'])
            {
                $html .= '
                    <li class="dropdown check-form-tabs-dropdown">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown">' .  $tabs['name'] . ' <i class="fa fa-angle-down"></i></a>
                            <ul class="dropdown-menu" role="menu">';
                    
                
                $subtabs_query = db_query("select * from app_forms_tabs where parent_id={$tabs['id']} and entities_id={$entities_id} order by sort_order, name");
                while($subtabs = db_fetch_array($subtabs_query))
                {
                    $html .= '<li class="form_tab_' . $subtabs['id'] . ' check-form-tabs" cfg_tab_id="form_tab_' . $subtabs['id'] . '"><a data-toggle="tab" href="#form_tab_' . $subtabs['id'] . '">' . $subtabs['name'] . '</a></li>';
                } 
                
                $html .= '
                        </ul>
                    </li>';
            }
            else
            {                           
                $html .= '<li class="form_tab_' . $tabs['id'] . ' check-form-tabs" cfg_tab_id="form_tab_' . $tabs['id'] . '"><a data-toggle="tab" href="#form_tab_' . $tabs['id'] . '">' . $tabs['name'] . '</a></li>';
            } 
        }
        
        return $html;
    }

}
