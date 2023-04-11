<?php

$report_info_query = db_query("select * from app_ext_report_page where id=" . _GET('report_id') . " and (find_in_set(" . $app_user['group_id'] . ",users_groups) or find_in_set(" . $app_user['id'] . ",assigned_to))");
if(!$report_info = db_fetch_array($report_info_query))
{
    redirect_to('dashboard/page_not_found');
}


switch($app_module_action)
{
    case 'print':
        if (!isset($app_selected_items[$_POST['reports_id']]))
            $app_selected_items[$_POST['reports_id']] = array();

        if (count($app_selected_items) == 0)
        {
            echo TEXT_PLEASE_SELECT_ITEMS;
            exit();
        }

        $selected_items_array = $app_selected_items[$_POST['reports_id']];
        
        //prepare forumulas query
        $listing_sql_query_select = fieldtype_formula::prepare_query_select($report_info['entities_id'], '');

        $listing_sql = "select e.* " . $listing_sql_query_select . " from app_entity_" . $report_info['entities_id'] . " e where e.id in (" . db_input_in($selected_items_array) . ") order by field(id," . db_input_in($selected_items_array) . ")";
        $items_query = db_query($listing_sql,false);
        $count_items = db_num_rows($items_query);
        
        $html = '';
        $page = new report_page\report($report_info);
        
        while ($item = db_fetch_array($items_query))
        {            
            $page->set_item($report_info['entities_id'],$item['id']);
            $html .= $page->get_html() . '<p style="page-break-after: always;"></p>';
            
            //print_rr($item);
        }
        
        $html = '<!DOCTYPE html>
            <html lang="' . APP_LANGUAGE_SHORT_CODE  . '" dir="' . APP_LANGUAGE_TEXT_DIRECTION . '">
              <head>
                  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
                  
                  <title>' . $report_info['name'] . '</title>
                  
                    <link href="template/plugins/font-awesome/css/font-awesome.min.css?v=4.7.0" rel="stylesheet" type="text/css"/>
                    <link href="template/plugins/line-awesome/css/line-awesome.min.css?v=1.3.0" rel="stylesheet" type="text/css"/>
                    <link href="template/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>

                    <link href="template/css/style-conquer.css?v=2" rel="stylesheet" type="text/css"/>
                    <link href="template/css/style.css" rel="stylesheet" type="text/css"/>
                    <link href="template/css/style-responsive.css" rel="stylesheet" type="text/css"/>
                    <link href="template/css/plugins.css" rel="stylesheet" type="text/css"/>
                                        
                    <link rel="stylesheet" type="text/css" href="css/default.css"/>    
                                                    
                 
                ' . app_include_custom_css() . '
                    
                ' . ($report_info['page_orientation'] == 'landscape' ? '<style type="text/css" media="print"> @page { size: landscape; } </style>' : '') . '			    
              </head>        
              <body>
               ' . $html . '
                <script>
                    window.print();
                </script>     
              </body>
            </html>
        ';
        
        echo $html;
        exit();  
        
        break;
}
