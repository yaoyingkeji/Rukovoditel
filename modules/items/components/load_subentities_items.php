<?php

app_reset_selected_items();

if(isset($subentities_items_position))
{
    if($app_user['group_id'] == 0)
    {
        $entities_query = db_query("select e.* from app_entities e where parent_id='" . db_input($current_entity_id) . "' order by e.sort_order, e.name");
    }
    else
    {
        $entities_query = db_query("select e.* from app_entities e, app_entities_access ea where e.id=ea.entities_id and length(ea.access_schema)>0 and ea.access_groups_id='" . db_input($app_user['group_id']) . "' and e.parent_id = '" . db_input($current_entity_id) . "' order by e.sort_order, e.name");
    }

    while($entities = db_fetch_array($entities_query))
    {
        if($entity_cfg->get('item_page_subentity' . $entities['id'] . '_position') == $subentities_items_position)
        {
            //check if subentity hidden by filter that set on item page configuration
            if(entities::is_hidden_by_condition($entities['id'], $current_item_id))
                continue;

            //try to get report type parent_item_info_page
            $subentity_report_query = db_query("select * from app_reports where entities_id='" . db_input($entities['id']) . "' and reports_type='parent_item_info_page'");
            if(!$subentity_report = db_fetch_array($subentity_report_query))
            {
                $sql_data = array('name' => '',
                    'entities_id' => $entities['id'],
                    'reports_type' => 'parent_item_info_page',
                    'in_menu' => 0,
                    'in_dashboard' => 0,
                    'created_by' => 0,
                );

                db_perform('app_reports', $sql_data);

                $reports_id = db_insert_id();

                $subentity_report = db_find('app_reports', $reports_id);
            }

            $subentity_cfg = new entities_cfg($entities['id']);

            $listing_container = 'entity_items_listing' . $subentity_report['id'] . '_' . $subentity_report['entities_id'];


            //get report entity access schema
            $access_schema = users::get_entities_access_schema($subentity_report['entities_id'], $app_user['group_id']);

            $add_button = '';
            if(users::has_access('create', $access_schema) and access_rules::has_add_buttons_access($entities['id'], $current_item_id))
            {
                $url = url_for('items/form', 'path=' . $app_path . '/' . $subentity_report['entities_id'] . '&redirect_to=parent_item_info_page');

                $add_button = button_tag((strlen($subentity_cfg->get('insert_button')) > 0 ? $subentity_cfg->get('insert_button') : TEXT_ADD), $url, true, array('class' => 'btn btn-primary btn-sm')) . ' ';
            }

            $with_selected_menu = '';

            if(users::has_access('export_selected', $access_schema) and users::has_access('export', $access_schema))
            {
                $with_selected_menu .= '<li>' . link_to_modalbox('<i class="fa fa-file-excel-o"></i> ' . TEXT_EXPORT, url_for('items/export', 'path=' . $subentity_report["entities_id"] . '&reports_id=' . $subentity_report['id'])) . '</li>';
            }

            $with_selected_menu .= plugins::include_dashboard_with_selected_menu_items($subentity_report['id'], '&path=' . $app_path . '/' . $subentity_report['entities_id'] . '&redirect_to=parent_item_info_page');
            
            $in_listing_buttons = '';
            if(is_ext_installed())
            {                
                $processes = new processes($subentity_report['entities_id']);
                $processes->rdirect_to = 'parent_item_info_page';
                $in_listing_buttons .= str_replace('btn-primary','btn-primary btn-sm',$processes->render_buttons('in_listing', $subentity_report['id'])) ;
            }
            
            $portlets = new portlets('entity_items_listing' . $subentity_report['id'], $entity_cfg->get('collapsed_subentity' . $entities['id'],false) );
            
            $filters_panels_html = '';
            if($entity_cfg->get('display_filter_panel_subentity' . $entities['id'])==1)
            {
                $filets_reports_id = reports::auto_create_report_by_type($entities['id'],'item_page_subentity_filters' . $subentity_report['id'],true);
                $filters_panels = new filters_panels($entities['id'], $filets_reports_id, $listing_container, $current_item_id);
                $filters_panels->custom_panel_css = '.subentity-portlet-' . $entities['id'];
                $filters_panels->custom_panel_id = 'subentity' . $entities['id'];
                $filters_panels_html =  $filters_panels->render_horizontal();
            }

            $html = '
					
                <div class="row info-page-reports-container" id="' . $listing_container . '_info_block" ' . ($entity_cfg->get('hide_subentity' . $entities['id'] . '_if_empty') == 1 ? 'style="display:none"' : '') . '>
                    <div class="col-md-12">
	      	        		
                        <div class="portlet subentity-portlet-' . $entities['id'] . '">
                            <div class="portlet-title">
				<div class="caption">        
                                    <a href="' . url_for('items/items', 'path=' . $app_path . '/' . $subentity_report['entities_id']) . '">' . 
                                    (strlen($entity_cfg->get('item_page_subentity' . $entities['id'] . '_heading')) ? $entity_cfg->get('item_page_subentity' . $entities['id'] . '_heading') : (strlen($subentity_cfg->get('listing_heading')) > 0 ? $subentity_cfg->get('listing_heading') : $entities['name'])) . 
                                    '&nbsp;<span class="portlet-count listing-' . $subentity_report['id'] . '-number-of-rows"></span>
                                    </a>             
                                </div>
                                <div class="tools">
                                    <a href="javascript:;" class="' . $portlets->button_css() . '"></a>
				</div>
                            </div>
                            <div class="portlet-body" ' . $portlets->render_body() . '>  		
                            
                            ' . $filters_panels_html . ' 
                                
                            <div class="row">
                              <div class="col-sm-6 entitly-listing-buttons-left">   
                                   ' . $add_button . '
                                   ' . (strlen($with_selected_menu) ? '
                                  <div class="btn-group">
                                                      <button class="btn btn-default dropdown-toggle btn-sm" type="button" data-toggle="dropdown" data-hover="dropdown">
                                                      ' . TEXT_WITH_SELECTED . '<i class="fa fa-angle-down"></i>
                                                      </button>
                                                      <ul class="dropdown-menu" role="menu">
                                                              ' . $with_selected_menu . '                
                                                      </ul>
                                              </div>' : '')  . $in_listing_buttons .
                                  '</div>         
                              <div class="col-sm-6">                        
                               ' . render_listing_search_form($subentity_report["entities_id"], $listing_container, $subentity_report['id'], 'input-small') . '                         
                              </div>
                            </div> 

                            <div id="' . $listing_container . '" class="entity_items_listing"></div>
                            ' . input_hidden_tag($listing_container . '_order_fields', $subentity_report['listing_order_fields']) .
                                  input_hidden_tag($listing_container . '_has_with_selected', (strlen($with_selected_menu) ? 1 : 0)) .
                                  input_hidden_tag($listing_container . '_force_item_page_subentity_filters', 1) .
                                  input_hidden_tag('subentity' . $subentity_report['entities_id'] . '_items_listing_path', $app_path . '/' . $subentity_report['entities_id']) . ' 	      


                            </div>
                        </div>  		

                    </div>
                </div>		
									
		<script>
                    $(function() {     
                        load_items_listing("' . $listing_container . '",' . (isset($_GET['gotopage'][$subentity_report['id']]) ? (int) $_GET['gotopage'][$subentity_report['id']] : 1) . ');                                                                         
                    });    
		</script>
		';

            echo $html;
        }
    }
}
