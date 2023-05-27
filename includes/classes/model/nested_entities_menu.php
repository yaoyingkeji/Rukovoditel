<?php


class nested_entities_menu
{
    private $entity_id, $item_id, $path_to_item;
    
    function __construct($entity_id, $item_id, $path_to_item)
    {
        $this->entity_id = $entity_id;
        $this->path_to_item = $path_to_item;
        $this->item_id = $item_id;
    }
    
    function render($menu)
    {
        global $app_users_access, $app_user;
        
        $parent_entity_cfg = new entities_cfg($this->entity_id);
        
        $menu_query = db_query("select * from app_nested_entities_menu where entities_id='" . $this->entity_id . "' and length(entities)>0 and is_active=1 order by sort_order, name");
        while($v = db_fetch($menu_query))
        {
            $s = [];
            foreach(explode(',',$v->entities) as $id)
            {
                $entity_cfg = new entities_cfg($id);
                
                if (!isset($app_users_access[$id]) and $app_user['group_id'] > 0)
                    continue;

                //check if subentity hidden by filter that set on item page configuration
                if (entities::is_hidden_by_condition($id, $this->item_id))
                    continue;
                
                //skip hidden in menu
                if ($parent_entity_cfg->get('hide_subentity' . $id . '_in_top_menu') == 1)
                    continue;
                
                $path = implode('/', $this->path_to_item) . '/' . $id;

                $s[] = array('title' => (strlen($entity_cfg->get('menu_title')) > 0 ? $entity_cfg->get('menu_title') : entities::get_name_by_id($id)), 'url' => url_for('items/items', 'path=' . $path), 'selected_id' => $id,'menu_css'=>'navbar-nav-entity-' . $id);
            }
            
            $title = (strlen($v->icon) ? app_render_icon($v->icon, (strlen($v->icon_color) ? 'style="color:' . $v->icon_color . '"':'')) . ' ': '') . $v->name;
            
            if(count($s)==1)
            {
                $menu[] = array('title' => $title,  'url' => $s[0]['url'],'menu_css'=>'navbar-nav-entity-' . $v->entities);
            }
            elseif(count($s)>1)
            {
                $menu[] = array('title' => $title,  'submenu' => $s,'menu_css'=>'navbar-nav-entity-group-' . $v->id);
            }
        }
        
        return $menu;
    }
}
