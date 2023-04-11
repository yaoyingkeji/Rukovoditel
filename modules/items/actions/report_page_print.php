<?php

$report_info_query = db_query("select * from app_ext_report_page where id=" . _GET('report_id') . " and (find_in_set(" . $app_user['group_id'] . ",users_groups) or find_in_set(" . $app_user['id'] . ",assigned_to))");
if(!$report_info = db_fetch_array($report_info_query))
{
    redirect_to('dashboard/page_not_found');
}

switch($app_module_action)
{
    case 'export_pdf':
        
        $page = new report_page\report($report_info);
        $page->set_item($current_entity_id,$current_item_id);
        $html = $page->get_html();
                        
        
        $html = '
            <html>
              <head>
                  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
                  <link href="template/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
                  
                <style>               
                    body { 
                       font-family: arial;                 
                    }

                    .h1, .h2, .h3, .h4, .h5, .h6, h1, h2, h3, h4, h5, h6 {
                       font-family: arial;   
                       font-weight: normal;
                    }
                 </style>
                 
                ' . app_include_custom_css() . '
              </head>        
              <body>
               ' . $html . '            
              </body>
            </html>
        ';
        
        //echo $html;
        //exit();                
        
        $filename = str_replace(' ','_',trim($_POST['filename']));
                              
        require_once(CFG_PATH_TO_DOMPDF);   

        $dompdf = new Dompdf\Dompdf(); 

        if($report_info['page_orientation']=='landscape')
        {
          $dompdf->set_paper('letter', 'landscape');
        }

        $dompdf->load_html($html);
        $dompdf->render();
              
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.$filename . '.pdf');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');          

        flush();

        echo $dompdf->output();
             
        exit();
        break;
}
