<?php
  //check access
  if($app_user['group_id']>0)
  {
    redirect_to('dashboard/access_forbidden');
  }
  
  $app_title = app_set_title(TEXT_NESTED_ENTITIES_MENU);
  
  if(!isset_entity($_GET['entities_id']))
  {
      redirect_to('entities/entities');
  }