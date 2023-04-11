<?php echo ajax_modal_template_header(TEXT_HEADING_REPORTS_SORTING) ?>

<?php

  if($app_redirect_to=='listng_filters')
  {
    $redirect_url =  url_for('entities/listing_filters','entities_id=' . $reports_info['entities_id']);
  }
  elseif($app_redirect_to=='entityfield_filters')
  {
  	$fields_id = str_replace('entityfield','',$reports_info['reports_type']);
  	$fields_info = db_find('app_fields',$fields_id);
  	$redirect_url =  url_for('entities/entityfield_filters','entities_id=' . $fields_info['entities_id'] . '&fields_id=' . $fields_id);
  }
  elseif($app_redirect_to == 'related_records_field_settings')
  {
  	$redirect_url =  url_for('entities/fields_settings','entities_id=' . $_GET['entities_id'] . '&fields_id=' . $_GET['fields_id']);
  }
  elseif($app_redirect_to == 'parent_infopage_filters')
  {
  	$redirect_url =  url_for('entities/parent_infopage_filters','entities_id=' . $reports_info['entities_id']);
  }
  elseif($app_redirect_to == 'infopage_entityfield_filters')
  {
  	$redirect_url =  url_for('entities/infopage_entityfield_filters','entities_id=' . $reports_info['entities_id'] . '&related_entities_id=' . $_GET['related_entities_id'] . '&fields_id=' . $_GET['fields_id']);
  }
  elseif($app_redirect_to=='common_reports')
  {
    $redirect_url =  url_for('ext/common_reports/reports');
  }
  elseif($app_redirect_to=='common_filters')
  {
  	$redirect_url =  url_for('ext/common_filters/reports');
  }
  elseif($app_redirect_to=='item_pivot_tables')
  {
  	$redirect_url =  url_for('ext/item_pivot_tables/reports');
  }
  elseif(strstr($app_redirect_to,'funnelchart'))
  {
  	$id = str_replace('funnelchart','',$app_redirect_to);
  	$redirect_url =  url_for('ext/funnelchart/view','id=' . $id . (strlen($app_path) ? '&path=' . $app_path:''));
  }
  elseif(strstr($app_redirect_to,'kanban'))
  {
  	$id = str_replace('kanban','',$app_redirect_to);
  	$redirect_url =  url_for('ext/kanban/view','id=' . $id . (strlen($app_path) ? '&path=' . $app_path:''));
  }
  elseif(strstr($app_redirect_to,'report_page_block'))
  {
  	$id = str_replace('report_page_block','',$app_redirect_to);
  	$redirect_url =  url_for('ext/report_page/blocks','report_id=' . $id);
  }
  elseif(strstr($app_redirect_to,'email_notification'))
  {
  	$id = str_replace('email_notification','',$app_redirect_to);
  	$redirect_url =  url_for('ext/email_notification/rules','entities_id=' . $reports_info['entities_id']);
  }
  elseif(isset($_GET['path']))
  {
    $redirect_url =  url_for('items/','path=' . $_GET['path']);
  }
  else
  { 
    $redirect_url =  url_for('reports/view','reports_id=' . $reports_info['id']);
  } 
  
    
  $entities_cfg = new entities_cfg($reports_info['entities_id']);
?>

<div class="modal-body">
    

<div class="form-section" style="margin-top: 15px;">
    <?= TEXT_FIELDS_FOR_SORTING ?>
    <span style="float:right;font-size: 13px; color: black;">
        <img src="images/arrow_down.png"> <?php echo TEXT_ASCENDING_ORDER  ?>&nbsp;&nbsp;&nbsp;<img src="images/arrow_up.png"> <?php echo TEXT_DESCENDING_ORDER ?>
    </span>
</div>
     
<div class="cfg_listing">        
  <ul id="fields_for_sorting" class="sortable" style="padding-top: 0">

  </ul>         
</div>
              
   
<?php 
//print_rr($sorting_fields);
$choices = [];
if($entities_cfg->get('use_comments') and users::has_comments_access('view', users::get_comments_access_schema($reports_info['entities_id'], $app_user['group_id'])))
{
    $choices['lastcommentdate'] = TEXT_LAST_COMMENT_DATE;
}

$fields_query = fields::get_query($reports_info['entities_id'], "and f.type not in (" . fields_types::get_type_list_excluded_in_sorting() . ")");
while($fields = db_fetch_array($fields_query))
{
    //check field access
    if(isset($fields_access_schema[$fields['id']]) and $fields_access_schema[$fields['id']] == 'hide')
    {
        continue;
    }
    
    $choices[$fields['id']] = fields::get_name($fields);
}

echo select_tag('entity_fields[]',$choices,$sorting_fields,['class'=>'form-control chosen-select','multiple'=>'multiple']) ?>

</div>


<script>
        
  function prepare_condition_icons()
  {
    $('#fields_excluded_from_sorting .condition_icon').each(function(){ $(this).css('opacity',0.5) })
    $('#fields_for_sorting .condition_icon').each(function(){ $(this).css('opacity',1); $(this).css('cursor','pointer') })
    
    $('#fields_for_sorting .condition_icon').each(function(){
    
      if(!$(this).hasClass('clickevent'))
      {    
        $(this).addClass('clickevent')
        
        $(this).click(function(){
          id = $(this).attr('id').replace('condition_icon_','');
          if($(this).attr('rel')=='asc')
          {        
            $.ajax({type: "POST",url: '<?php echo url_for("reports/sorting","action=set_sorting_condition&reports_id=" . $_GET["reports_id"])?>',data: {field_id:id,condition:'desc'} });
            
            $(this).attr('rel','desc')
            $(this).attr('src','images/arrow_up.png');
            
          }
          else
          {
            $.ajax({type: "POST",url: '<?php echo url_for("reports/sorting","action=set_sorting_condition&reports_id=" . $_GET["reports_id"])?>',data: {field_id:id,condition:'asc'} });
            $(this).attr('rel','asc')
            $(this).attr('src','images/arrow_down.png');
          }
        })
      }
    
     })
          
  }
  
  function render_fields_for_sorting()
  {
      $('#fields_for_sorting').load('<?php echo url_for("reports/sorting","action=render_fields_for_sorting&reports_id=" . $reports_info["id"])?>')
  }
    
  $(function() {
      prepare_condition_icons();
               
    	$( "ul.sortable" ).sortable({
    		connectWith: "ul.sortable",
                cancel:'.condition_icon', 
    		update: function(event,ui)
                {
                  
                  data = '';  
                  $( "ul.sortable" ).each(function() {data = data +'&'+$(this).attr('id')+'='+$(this).sortable("toArray") });                            
                  data = data.slice(1)                      
                  $.ajax({type: "POST",url: '<?php echo url_for("reports/sorting","action=set_sorting&reports_id=" . $reports_info["id"])?>',data: data});
                }
    	});
        
        $('#entity_fields').change(function(){
            //console.log($(this).val())
            $.ajax({type: "POST",url: '<?php echo url_for("reports/sorting","action=set_sorting_fields&reports_id=" . $reports_info["id"])?>',data: {
                    sorting_fields: $(this).val()
            }}).done(function(){
                render_fields_for_sorting()
            });
        })
        
        render_fields_for_sorting()
      
  });  
</script>
 
<?php echo ajax_modal_template_footer('hide-save-button','<a href="' . $redirect_url . '" class="btn btn-primary">' . TEXT_SAVE . '</a>') ?>

</form> 