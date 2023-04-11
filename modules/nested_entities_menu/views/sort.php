
<?php echo ajax_modal_template_header(TEXT_SORT_VALUES) ?>

<?php echo form_tag('choices_form', url_for('nested_entities_menu/menu','action=sort&entities_id=' . $_GET['entities_id']),array('class'=>'form-horizontal')) ?>

<div class="modal-body">
  <div class="form-body">
 
<div class="dd" id="choices_sort">   
    <ol class="dd-list">
<?php 
$menu_query = db_query("select * from app_nested_entities_menu where entities_id='" . _get::int('entities_id') . "' order by sort_order, name");
while($v = db_fetch_array($menu_query))
{
    echo  '
        <li class="dd-item" data-id="' . $v['id'] . '">
            <div class="dd-handle" style="height: auto;">' . $v['name'] . '</div>
        </li>';
}
?>
    </ol>    
</div>
      
   </div>
</div> 
<?php echo input_hidden_tag('choices_sorted') ?> 
<?php echo ajax_modal_template_footer() ?>

</form> 

<script>
$(function(){
  $('#choices_sort').nestable({
      group: 1,
      maxDepth: 1
  }).on('change',function(e){
    output = $(this).nestable('serialize');
    
    if (window.JSON) 
    {
      output = window.JSON.stringify(output);
      $('#choices_sorted').val(output);
    } 
    else 
    {
      alert('JSON browser support required!');      
    }    
  })
})

</script>
