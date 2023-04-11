
<?php echo ajax_modal_template_header($report_info['name']) ?>

<?php
if(!isset($app_selected_items[$_GET['reports_id']]))
    $app_selected_items[$_GET['reports_id']] = array();

if(count($app_selected_items[$_GET['reports_id']]) == 0)
{
    echo '
    <div class="modal-body">    
      <div>' . TEXT_PLEASE_SELECT_ITEMS . '</div>
    </div>    
  ' . ajax_modal_template_footer('hide-save-button');
}
else
{
?>
    <?php echo form_tag('export-form', url_for('items/report_page_print_selected', 'path=' . $_GET['path'] . '&report_id=' . $_GET['report_id']), array('target' => '_blank', 'class' => 'form-horizontal')) ?>
    <?php echo input_hidden_tag('action', 'print') ?>
    <?php echo input_hidden_tag('reports_id', $_GET['reports_id']) ?>

    

        <div class="modal-body ajax-modal-width-790">
            <div><?php echo TEXT_EXT_PRINT_BUTTON_PDF_NOTE ?></div>
        </div> 

        <?php
        
        $buttons_html = '';                
        
        $buttons_html .= ' <button type="button" class="btn btn-primary btn-template-print"><i class="fa fa-print" aria-hidden="true"></i> ' . TEXT_PRINT . '</button>';

        $count_selected_text = sprintf(TEXT_SELECTED_RECORDS, count($app_selected_items[$_GET['reports_id']]));
        echo ajax_modal_template_footer('hide-save-button', $buttons_html, $count_selected_text);
    
    ?>

    </form>  

<script>

    $(function ()
    {
        $('#export-form').validate({
            submitHandler: function (form)
            {
                return true;
            }
        });
        
        $('.btn-template-print').click(function ()
        {
            $('#action').val('print');
            $('#export-form').attr('target', '_new')
            $('#export-form').submit();
        })
    });
    

</script>
    
<?php } ?>