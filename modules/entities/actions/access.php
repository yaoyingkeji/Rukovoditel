<?php

switch($app_module_action)
{
  case 'set_access':
                  
        if(isset($_POST['access']))
        {
          
          foreach($_POST['access'] as $access_groups_id=>$access)
          {
            $access_schema = array();
            
            foreach($access as $v)
            {            	
            	$access_schema[] = $v;
            }
            
            $access_schema = access_groups::prepare_entities_access_schema($access_schema);  
                                    
                                               
            $sql_data = array('access_schema'=>implode(',',$access_schema));
            
            $acess_info_query = db_query("select access_schema from app_entities_access where entities_id='" . db_input($_GET['entities_id']) . "' and access_groups_id='" . $access_groups_id. "'");
            if($acess_info = db_fetch_array($acess_info_query))
            {
              db_perform('app_entities_access',$sql_data,'update',"entities_id='" . db_input($_GET['entities_id']) . "' and access_groups_id='" . $access_groups_id. "'");
            }
            else
            {
              $sql_data['entities_id'] = $_GET['entities_id'];
              $sql_data['access_groups_id'] = $access_groups_id;
              db_perform('app_entities_access',$sql_data);
            }          
          }
                   
          
          $alerts->add(TEXT_ACCESS_UPDATED,'success');
        }
                        
      redirect_to('entities/access','entities_id=' . $_GET['entities_id']);
    break;
}