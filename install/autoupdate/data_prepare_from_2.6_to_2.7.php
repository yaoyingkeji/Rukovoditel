<?php

define('TEXT_UPDATE_VERSION_FROM','2.6');
define('TEXT_UPDATE_VERSION_TO','2.7');

define('IS_HTTPS',(isset($_SERVER['HTTPS']) ? (strtolower($_SERVER['HTTPS'])=='on' ? true : false): false));

$ajax_url = (IS_HTTPS ? "https://" : "http://") . $_SERVER["HTTP_HOST"] . $_SERVER['REQUEST_URI'];


if(isset($_GET['entity_id']))
{
    require_once('../../config/database.php');
    
    require_once('includes/database.php');
    
    if(!defined('DB_SERVER_PORT')) define('DB_SERVER_PORT','');
    
    db_connect(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD, DB_DATABASE,DB_SERVER_PORT);
    
    set_time_limit(0);
}

if(isset($_GET['entity_id']) and $_GET['entity_id']==0)
{
    db_query("ALTER DATABASE `" . DB_DATABASE . "` CHARACTER SET utf8mb4");
    
    $step = 10;
    $current_step = (isset($_GET['current_step']) ? (int)$_GET['current_step'] : 0);
    
    $tables_query = db_query("show tables");
    $count_tables = mysqli_num_rows($tables_query);
    $count = 0;
    while($tables = db_fetch_array($tables_query))
    {
        $count++;
        
        if($count<=$current_step) continue;
        
        $table= current($tables);
                        
        //skip some tables
        if(in_array($table,['app_ext_mail_groups_from'])) continue;
        
        //db_query("ALTER TABLE `" . $table . "` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        
        $cols_query = db_query("show full columns from " . $table);
        while ( $cols = db_fetch_array($cols_query))
        {
            if(strlen($cols['Collation']))
            {                
                $check_query = db_query("SHOW INDEX FROM " . $table . " WHERE KEY_NAME = 'idx_" . $cols['Field'] . "'");
                if($check = db_fetch_array($check_query))
                {
                    db_query("ALTER TABLE " . $table . " DROP INDEX idx_" . $cols['Field']);
                    db_query("ALTER TABLE " . $table . " ADD INDEX idx_" . $cols['Field']. " (" . $cols['Field'] . "(128));");                                 
                }
                
                $sql = "ALTER TABLE " . $table . " CHANGE `" . $cols['Field'] . "` `" . $cols['Field'] . "` " . $cols['Type'] . " CHARACTER SET utf8mb4 NOT NULL";
                db_query($sql);
            }
        }
        
        db_query("ALTER TABLE `" . $table . "` CONVERT TO CHARACTER SET utf8mb4");
        
        
        
        if($count/$step == floor($count/$step))
        {            
            $percent = (int)(($count/$count_tables)*100);
            echo '<div><i class="fa fa-spinner fa-spin"></i> ' . ' (' . $percent . '%)</div>';
            
            if(strstr($ajax_url,'?'))
            {
                $ajax_url = explode('?',$ajax_url);
                $ajax_url = $ajax_url[0];
            }
            
            echo '
                    <script>
                        $("#entity_container_0").load("' . $ajax_url. '?entity_id=0&current_step=' . $count . '");
                    </script>
                 ';
            
            exit();
        }
    }
    
    echo '<div><i class="fa fa-check-circle"></i> OK (100%)</div>';
    
    exit();
}


if(isset($_GET['entity_id']))
{
   
  //check entity
  $entity_query = db_query("select * from app_entities where id='" . (int)$_GET['entity_id']. "'");
  if($entity = db_fetch_array($entity_query))
  {  
    //get groupoed users fields
    $fields_array = array();
    $fields_query = db_query("select * from app_fields where type in ('fieldtype_input_date','fieldtype_input_datetime','fieldtype_dynamic_date') and entities_id='" . $entity['id'] . "'");
    while($fields = db_fetch_array($fields_query))
    {
      $fields_array[] = $fields['id'];
    }
        
    if(count($fields_array)>0)
    {                            
      foreach($fields_array as $filed_id)      
      {        
          $update_query = "ALTER TABLE app_entity_" . $entity['id'] . " CHANGE field_" . $filed_id . " field_" . $filed_id . " BIGINT NOT NULL DEFAULT '0'";
        
        db_query($update_query);
      }                        
    }
    
    db_query("ALTER TABLE app_entity_" . $entity['id'] . " CHANGE date_added date_added BIGINT NOT NULL DEFAULT '0'");
    db_query("ALTER TABLE app_entity_" . $entity['id'] . " CHANGE date_updated date_updated BIGINT NOT NULL DEFAULT '0'");      
  }
  
  
  echo '<div><i class="fa fa-check-circle"></i> OK</div>';
  
  exit();
}

include('includes/template_top.php');

if($lang=='ru')
{
  define('TEXT_UPDATING_DATA','Обновление данных');
  define('TEXT_UPDATING_DATA_WARN','Дождитесь успешного выполнения для каждой сущности');  
}
else
{
  define('TEXT_UPDATING_DATA','Update Data');
  define('TEXT_UPDATING_DATA_WARN','Wait for the successful execution of each entity');  
}

        
  $html = '

<script>
  function update_data_for_entity(entity_id)
  {
    $("#entity_container_"+entity_id).load("' . $ajax_url. '?entity_id="+entity_id);
  }
</script>  
  
  <h3 class="page-title">' . TEXT_UPDATING_DATA . '</h3>
  <div class="alert alert-warning">' . TEXT_UPDATING_DATA_WARN . '</div>
  <table >

      <tr>
        <td style="padding-right: 15px;">' . DB_DATABASE . '</td>
        <td>
          <div id="entity_container_0"><i class="fa fa-spinner fa-spin"></i></div>
          
          <script>
            update_data_for_entity(0)
          </script>
        </td>
      </tr>
';
  $enitites_query = db_query("select * from app_entities order by name");
  while($enitites = db_fetch_array($enitites_query))
  {
    $html .= '
      <tr>
        <td style="padding-right: 15px;">' . $enitites['name']. '</td>
        <td>
          <div id="entity_container_' . $enitites['id'] . '"><i class="fa fa-spinner fa-spin"></i></div>
          
          <script>
            update_data_for_entity(' . $enitites['id']  . ')
          </script>
        </td>
      </tr>';
  }
  
  $html .= '</table>';
  
  echo $html;
    

include('includes/template_bottom.php');