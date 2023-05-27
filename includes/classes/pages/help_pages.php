<?php

class help_pages
{

    public $entities_id;

    function __construct($entities_id)
    {
        $this->entities_id = $entities_id;
    }

    function render_announcements($position="listing", $item_id = 0)
    {
        global $app_user;

        $html = '';

        $where_sql = " and ((FROM_UNIXTIME(start_date,'%Y-%m-%d')<=date_format(now(),'%Y-%m-%d') or start_date=0) and (FROM_UNIXTIME(end_date,'%Y-%m-%d')>=date_format(now(),'%Y-%m-%d') or end_date=0))";
        
        
        $where_sql .= ($position=="listing") ? " and (find_in_set('" . $position ."',position) or length(position)=0)" : " and find_in_set('" . $position ."',position)";

        $pages_query = db_query("select * from app_help_pages where type='announcement' and entities_id='" . $this->entities_id . "' and find_in_set(" . $app_user['group_id'] . ", users_groups) and is_active=1 {$where_sql} order by sort_order, name");
        while($pages = db_fetch_array($pages_query))
        {
            if(!$this->check_filters($pages,$position,$item_id)) continue;
            
            if($pages['position']=='info')
            {
                $pattern = new fieldtype_text_pattern();
                
                $item_sql = new items_query($this->entities_id, [
                    'add_formula'=>true,
                    'fields_in_query' => $pages['description'],
                    'where'=>"and id={$item_id}",
                ]);
                $item_query = db_query($item_sql->get_sql());                
                if($item = db_fetch_array($item_query))
                {                                    
                    $pages['name'] = $pattern->output_singe_text($pages['name'], $this->entities_id, $item);                    
                    $pages['description'] = $pattern->output_singe_text($pages['description'], $this->entities_id, $item);                    
                }
            }
            
            if($pages['color'] == 'default')
            {
                $html .= '
						<div>
							<p>' . (strlen($pages['icon']) ? app_render_icon($pages['icon']) . ' ' : '') . (strlen($pages['name']) ? '<b>' . $pages['name'] . '</b><br>' : '') . $pages['description'] . '</p>
						</div>';
            }
            else
            {
                $html .= '
						<div class="alert alert-' . $pages['color'] . '">' . (strlen($pages['icon']) ? app_render_icon($pages['icon']) . ' ' : '') . (strlen($pages['name']) ? '<b>' . $pages['name'] . '</b><br>' : '') . $pages['description'] . '</div>';
            }
        }

        return '<div class="help-pages-announcement">' . $html . '</div>';
    }
    
    function check_filters($page,$position, $item_id)
    {
        if($position!='info' or $page['position']!='info' or $item_id==0) return true;
        
        if($report_id = reports::get_reports_id_by_type($page['entities_id'], 'help_pages' . $page['id']))
        {
            $query = new items_query($page['entities_id'],[
                'report_id'=>$report_id,
                'add_filters'=>true,
                'where' => "and e.id={$item_id}",
            ]);
            
           $item_query = db_query($query->get_sql() . ' limit 1',false);
           if($item = db_fetch_array($item_query))
           {
               
               return true;
           }
           else
           {
               return false;
           }
        }
        
        return true;
    }

    function render_icon($position)
    {
        global $app_user;

        $html = '';

        $pages_array = [];
        $pages_query = db_query("select * from app_help_pages where type='page' and position='" . $position . "' and entities_id='" . $this->entities_id . "' and find_in_set(" . $app_user['group_id'] . ", users_groups) and is_active=1 order by sort_order, name");
        while($pages = db_fetch_array($pages_query))
        {
            $pages_array[$pages['id']] = $pages['name'];
        }

        if(count($pages_array) == 1)
        {
            $html = '&nbsp;<a title="' . TEXT_HELP . '" class="help-icon" href="javascript: open_dialog(\'' . url_for('help_system/page', 'id=' . key($pages_array)) . '\')"><i class="fa fa-question-circle" aria-hidden="true"></i></a>';
        }
        elseif(count($pages_array) > 1)
        {
            foreach($pages_array as $id => $name)
            {
                $html .= '<li><a href="javascript: open_dialog(\'' . url_for('help_system/page', 'id=' . $id) . '\')">' . $name . '</a></li>';
            }

            $html = '
					<div class="btn-group btn-group-help-icon">
					<a title="' . TEXT_HELP . '" class="help-icon" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-question-circle" aria-hidden="false"></i></a>
					<ul class="dropdown-menu ' . ($position == 'info' ? 'pull-right' : '') . '">
				    ' . $html . '
				  </ul>
					</div>
					';
        }

        return $html;
    }

    static function get_position_choices()
    {
        $choices = array(
            'listing' => TEXT_ITEMS_LISTING,
            'info' => TEXT_ITEM_DETAILS_POSITION,
        );

        return $choices;
    }

    static function get_position_by_name($name)
    {
        if(!strlen($name))
            return '';

        $types = self::get_position_choices();

        $names = [];
        foreach(explode(',', $name) as $v)
        {
            $names[] = (isset($types[$v]) ? $types[$v] : '');
        }

        return implode('<br>',$names);
    }

    static function get_color_choices()
    {
        $choices = array(
            'default' => TEXT_DEFAULT,
            'warning' => TEXT_ALERT_WARNING,
            'danger' => TEXT_ALERT_DANGER,
            'success' => TEXT_ALERT_SUCCESS,
            'info' => TEXT_ALERT_INFO,
        );

        return $choices;
    }

    static function get_color_by_name($name)
    {
        $types = self::get_color_choices();

        return (isset($types[$name]) ? $types[$name] : '');
    }

}
