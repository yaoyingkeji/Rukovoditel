<?php 

$access_rules = new access_rules($current_entity_id, $current_item_id);

if(!users::has_comments_access('view', $access_rules->get_comments_access_schema())) exit() 

?>

<?php

$entity_cfg = new entities_cfg($current_entity_id);

$listing_sql_query = '';

if(strlen($_POST['search_keywords'])>0)
{
  echo '<div class="alert alert-info">' . sprintf(TEXT_SEARCH_RESULT_FOR,htmlspecialchars($_POST['search_keywords'])) . ' <span onClick="reset_search()" class="reset_search">' . TEXT_RESET_SEARCH . '</span></div>';
  
  require(component_path('items/add_search_comments_query'));
}


$user_has_comments_access = (users::has_comments_access('update', $access_rules->get_comments_access_schema()) or users::has_comments_access('delete', $access_rules->get_comments_access_schema()) or users::has_comments_access('create', $access_rules->get_comments_access_schema()));
	
$fields_access_schema = users::get_fields_access_schema($current_entity_id,$app_user['group_id']);
$choices_cache = fields_choices::get_cache();

$html = '<ul class="chats">';
$count = 1;
$listing_sql = "select * from app_comments where entities_id='" . db_input($current_entity_id) . "' and items_id='" . db_input($current_item_id) . "' " . $listing_sql_query . " order by id desc";
$listing_split = new split_page($listing_sql,'items_comments_listing');
$listing_split->listing_funciton = 'load_comments_listing';
$items_query = db_query($listing_split->sql_query);
while($item = db_fetch_array($items_query))
{
  $html_action_column = '';
  if($user_has_comments_access)
  {
    $html_action_column = '
        <div class="action">
            <div class="btn-group">
                <button class="btn btn-default btn-sm dropdown-toggle" type="button" data-toggle="dropdown"><i class="fa fa-bars"></i></button>
                <ul class="dropdown-menu pull-right" role="menu">
                    ' . ((users::has_comments_access('delete', $access_rules->get_comments_access_schema()) and ($item['created_by']==$app_user['id'] or $app_user['group_id']==0 or users::has_comments_access('full', $access_rules->get_comments_access_schema()))) ? '<li>' . link_to_modalbox('<i class="fa fa-trash-o"></i> ' . TEXT_DELETE,url_for('items/comments_delete','id=' .$item['id'] . '&path=' . $_POST['path'])) . '</li>':'') . '
                    ' . ((users::has_comments_access('update', $access_rules->get_comments_access_schema()) and ($item['created_by']==$app_user['id'] or $app_user['group_id']==0 or users::has_comments_access('full', $access_rules->get_comments_access_schema()))) ? '<li>' . link_to_modalbox('<i class="fa fa-edit"></i> ' . TEXT_EDIT,url_for('items/comments_form','id=' .$item['id'] . '&path=' . $_POST['path'])) . '</li>':'') . '
                    ' . (users::has_comments_access('create', $access_rules->get_comments_access_schema()) ?  '<li>' . link_to_modalbox('<i class="fa fa-reply"></i> ' . TEXT_REPLY,url_for('items/comments_form','reply_to=' .$item['id'] . '&path=' . $_POST['path'])) . '</li>':'') . '
                </ul>    
            </div>    
      </div>
    ';
  }
  
  $html_fields = '';
  $comments_fields_query = db_query("select f.*,ch.fields_value from app_comments_history ch, app_fields f where comments_id='" . db_input($item['id']) . "' and f.id=ch.fields_id order by ch.id");
  while($field = db_fetch_array($comments_fields_query))
  {
    //check field access
    if(isset($fields_access_schema[$field['id']]))
    {
      if($fields_access_schema[$field['id']]=='hide') continue;
    }
        
    $output_options = array('class'=>$field['type'],
                            'value'=>$field['fields_value'],
                            'field'=>$field, 
                            'path'=>$_POST['path'],
    												'is_listing'=>true,
    												'is_comments_listing' =>true,
    );
        
    $html_fields .='                      
        <tr><th>&bull;&nbsp;' . fields_types::get_option($field['type'],'name',$field['name']) . ':&nbsp;</th><td>' . fields_types::output($output_options). '</td></tr>           
    ';
  }
  
  if(strlen($html_fields)>0)
  {
    $html_fields = '<table class="comments-history">' . $html_fields . '</table>';
  }
  
  
  $output_options = array('class'=>'fieldtype_attachments',
                          'value'=>$item['attachments'],
                          'path'=>$_POST['path'],
                          'field'=>array('entities_id'=>$current_entity_id,'configuration'=>json_encode(['use_image_preview'=>$entity_cfg->get('image_preview_in_comments',0)])),
                          'item'=>array('id'=>$current_item_id)); 
                          
  $attachments = fields_types::output($output_options);

  if($entity_cfg->get('use_editor_in_comments')!=1)
  {
    $item['description'] = nl2br($item['description']);
  }
  
  $photo = '';
  if($entity_cfg->get('disable_avatar_in_comments',0)!=1 and $item['created_by'])
  {
      $photo = render_user_photo($app_users_cache[$item['created_by']]['photo'],'avatar img-responsive');
  }
    
  $html .= '
        <li class="' . ($count/2==floor($count/2) ? 'out':'in') . '">
            <div class="message">
                <div class="head">' . $html_action_column . $photo . '                    
                    <span class="datetime">' . format_date_time($item['date_added']) . '</span><br>    
                    <span class="name" ' . users::render_publi_profile($app_users_cache[$item['created_by']],false). '>' . $app_users_cache[$item['created_by']]['name']. '</span>                    
                </div>    
                    
                <span class="body">
                    <div class="ckeditor-images-content-prepare"><div class="fieldtype_textarea_wysiwyg">' . auto_link_text($item['description'])  . '</div></div>
                    ' . $attachments . $html_fields . '
                </span>
            </div>
        </li>
      ';
  
  $count++;
}

if($listing_split->number_of_rows==0)
{
  $html .= '
    <li class="in">
        <div class="message"><span class="body">' . TEXT_NO_RECORDS_FOUND . '</div></div>
    </li>
  '; 
}

$html .= '
  </ul>
';

//add pager
$html .= '
  <table width="100%">
    <tr>
      <td>' . $listing_split->display_count() . '</td>
      <td align="right">' . $listing_split->display_links(). '</td>
    </tr>
  </table>
';

echo $html;  

exit();
