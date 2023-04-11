<?php

$obj = array();

if(isset($_GET['id']))
{
  $obj = db_find('app_nested_entities_menu',$_GET['id']);  
}
else
{
  $obj = db_show_columns('app_nested_entities_menu');
  $obj['is_active']=1;
  
  $check_query = db_query("select max(sort_order) as max_sort_order from app_nested_entities_menu where entities_id='" . _GET('entities_id'). "'");
  $check = db_fetch($check_query);
  $obj['sort_order'] = $check->max_sort_order+1;
  
}