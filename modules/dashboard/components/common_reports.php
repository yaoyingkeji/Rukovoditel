<?php

$where_sql = '';
if(strlen($app_users_cfg->get('hidden_common_reports')) > 0)
{
    $where_sql = " and r.id not in (" . $app_users_cfg->get('hidden_common_reports') . ")";
}


if($app_user['group_id'] > 0)
{
    $reports_query = db_query("select r.* from app_reports r, app_entities e, app_entities_access ea  where r.entities_id = e.id and e.id=ea.entities_id and length(ea.access_schema)>0 and ea.access_groups_id='" . db_input($app_user['group_id']) . "' and (find_in_set(" . $app_user['group_id'] . ",r.users_groups) or find_in_set(" . $app_user['id'] . ",r.assigned_to) ) and r.in_dashboard in (1,2) and r.reports_type = 'common' " . $where_sql . " order by r.dashboard_sort_order, r.name");
}
else
{
    $reports_query = db_query("select r.* from app_reports r, app_entities e where r.entities_id = e.id and (find_in_set(" . $app_user['group_id'] . ",r.users_groups) or find_in_set(" . $app_user['id'] . ",r.assigned_to) ) and r.in_dashboard in (1,2) and r.reports_type = 'common' " . $where_sql . " order by r.dashboard_sort_order, r.name");
}
while($reports = db_fetch_array($reports_query))
{
    //get report entity info
    $entity_info = db_find('app_entities', $reports['entities_id']);
    $entity_cfg = new entities_cfg($reports['entities_id']);

    //check if parent reports was not set
    if($entity_info['parent_id'] > 0 and $reports['parent_id'] == 0)
    {
        reports::auto_create_parent_reports($reports['id']);
    }

    //get report entity access schema
    $access_schema = users::get_entities_access_schema($reports['entities_id'], $app_user['group_id']);
    $user_access_schema = users::get_entities_access_schema(1, $app_user['group_id']);

    $add_button = '';
    if(users::has_access('create', $access_schema) and $entity_cfg->get('reports_hide_insert_button') != 1)
    {
        if($entity_info['parent_id'] == 0)
        {
            $url = url_for('items/form', 'path=' . $reports['entities_id'] . '&redirect_to=report_' . $reports['id']);
        }
        elseif($entity_info['parent_id'] == 1 and!users::has_access('view', $user_access_schema))
        {
            $url = url_for('items/form', 'path=1-' . $app_user['id'] . '/' . $reports['entities_id'] . '&redirect_to=report_' . $reports['id']);
        }
        else
        {
            $url = url_for('reports/prepare_add_item', 'reports_id=' . $reports['id']);
        }
        $add_button = button_tag((strlen($entity_cfg->get('insert_button')) > 0 ? $entity_cfg->get('insert_button') : TEXT_ADD), $url) . ' ';
    }



    $listing_container = 'entity_items_listing' . $reports['id'] . '_' . $reports['entities_id'];

    $gotopage = (isset($_GET['gotopage'][$reports['id']]) ? (int) $_GET['gotopage'][$reports['id']] : 1);


    $with_selected_menu = '';

    if(users::has_access('export_selected', $access_schema) and users::has_access('export', $access_schema))
    {
        $with_selected_menu .= '<li>' . link_to_modalbox('<i class="fa fa-file-excel-o"></i> ' . TEXT_EXPORT, url_for('items/export', 'path=' . $reports["entities_id"] . '&reports_id=' . $reports['id'])) . '</li>';
    }

    $with_selected_menu .= plugins::include_dashboard_with_selected_menu_items($reports['id']);



    $listing = new items_listing($reports['id']);
    $curren_listing_type = $listing->get_listing_type();
    $select_all_html = '';
    if(in_array($curren_listing_type, ['grid', 'mobile']))
    {
        if(listing_types::has_action_field($curren_listing_type, $reports['entities_id']))
        {
            $select_all_html = '
			  			<label>' . input_checkbox_tag('select_all_items', $reports['id'], array('class' => $listing_container . '_select_all_items_force', 'data-container-id' => $listing_container)) . ' ' . TEXT_SELECT_ALL . '</label>
		  			';
        }
    }
            
    $count_items = items::count_by_reports_id($reports["entities_id"], $reports['id']);
    
    if($reports['in_dashboard']!=2 or ($reports['in_dashboard']==2 and $count_items>0))
    {
        $portlets = new portlets('dashboard_common_report' . $reports['id'],($count_items==0 ? true:false));

        echo '

        <div class="row dashboard-reports-container" id="dashboard-reports-container">
          <div class="col-md-12">

          <div class="portlet portlet-dashboard">
                <div class="portlet-title">
                    <div class="caption">
                        <a href="' . url_for('reports/view','reports_id=' . $reports['id']) . '">' . $reports['name']  . ' (' . $count_items . ')'. '</a>
                    </div>
                    <div class="tools">
                    <a href="javascript:;" class="' . $portlets->button_css() . '"></a>
                    </div>
                </div>
            <div class="portlet-body" ' . $portlets->render_body() . '> 

            ' . (strlen($reports['description']) ? '<p>' . $reports['description'] . '</p>':'') . '
            ' . filters_panels::render_common_filters_panel($reports) . '    

          <div class="row">
            <div class="col-sm-6">
                    <div class="entitly-listing-buttons-left">
                 ' . $add_button . '      
                ' . (strlen($with_selected_menu) ? ' 		
                <div class="btn-group">
                                    <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" data-hover="dropdown">
                                    ' . TEXT_WITH_SELECTED . '<i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu" role="menu">
                                            ' . $with_selected_menu . '                
                                    </ul>
                            </div>' : '') .
        $select_all_html .
        '

              </div>               
            </div>        
            <div class="col-sm-6">                        
             ' . render_listing_search_form($reports["entities_id"], $listing_container, $reports['id']) . '                         
            </div>
          </div> 

          <div id="' . $listing_container . '" class="entity_items_listing"></div>
          ' . input_hidden_tag($listing_container . '_order_fields', $reports['listing_order_fields']) . '
          ' . input_hidden_tag($listing_container . '_has_with_selected', (strlen($with_selected_menu) ? 1 : 0)) . '

          </div>
        </div>
      </div>
    </div>


        <script>
          $(function() {     
            load_items_listing("' . $listing_container . '",' . $gotopage . ');                                                                         
          });    
        </script> 
      ';

        $has_reports_on_dashboard = true;
    }
}