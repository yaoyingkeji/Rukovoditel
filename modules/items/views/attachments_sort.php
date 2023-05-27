
<?php echo ajax_modal_template_header(TEXT_SORT  . ': '. $app_fields_cache[$current_entity_id][$field_id]['name']) ?>

<?php
    $attachments = $item_info['field_' . $field_id]??'';
    $count = count(explode(',',$attachments));
    $column_count = 1;
    switch(true)
    {
        case $count>10 and $count<=20:
            $column_count = 2;
            break;
        case $count>20 and $count<30:
            $column_count = 3;
            break;
        case $count>30:
            $column_count = 4;
            break;
    }
?>

<?php echo form_tag('choices_form',url_for('items/attachments_sort','path=' . $app_path . '&action=sort&field_id=' . $field_id)) ?>
<div class="modal-body ajax-modal-width-1100">         
    <div class="dd" id="choices_sort">
        <?php  
        $attachments = $item_info['field_' . $field_id]??'';
        $html = '<ol class="dd-list" style="column-count:' . $column_count . '">';
        foreach(explode(',',$attachments) as $filename)
        {
            $file = attachments::parse_filename($filename);
            
            $img = '';
            if($file['is_image'] and is_file($file['file_path'])) 
            {
                $img = '<img src="data:image/jpeg;base64,' . base64_encode(file_get_contents($file['file_path'])) . '" width="50"> ';
            }
            else
            {
                $img = '<img src="' . url_for_file($file['icon']) . '" widht="16"> ';
            }
            
            $html .= '
                    <li class="dd-item" data-id="' . $file['file'] . '">
                        <div class="dd-handle" style="height: auto; max-height: 60px; overflow:hidden;">
                            ' . $img  . $file['name'] . '
                        </div>
                     </li>
                ';
        }
        $html .= '</ol>';
        echo $html;
        ?>
    </div>
</div>

<?php echo input_hidden_tag('choices_sorted') ?> 
<?php echo ajax_modal_template_footer() ?>
</form>

<script>
$(function(){
  $('#choices_sort').nestable({
      group: 1,
      maxDepth:1,
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

