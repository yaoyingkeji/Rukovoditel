<?php echo ajax_modal_template_header(TEXT_INFO) ?>

<?php echo form_tag('configuration_form', url_for('forms_tabs/groups','action=save&entities_id=' . _GET('entities_id') . (isset($_GET['id']) ? '&id=' . $_GET['id']:'') ),array('class'=>'form-horizontal')) ?>

<div class="modal-body">
    <div class="form-body"> 
                     
    <div class="form-group">
    	<label class="col-md-3 control-label" for="name"><?php echo TEXT_NAME ?></label>
        <div class="col-md-9">	
    	  <?php echo input_tag('name',$obj['name'],array('class'=>'form-control input-large required')) ?>        
        </div>			
    </div>
        
    <div class="form-group">
    	<label class="col-md-3 control-label" for="sort_order"><?php echo TEXT_SORT_ORDER ?></label>
        <div class="col-md-9">	
            <?php echo input_tag('sort_order',$obj['sort_order'],array('class'=>'form-control input-xsmall')) ?>        
        </div>			
    </div> 
        
    </div>
</div> 

<?php echo ajax_modal_template_footer() ?>

</form> 

<script>
  $(function() { 
    $('#configuration_form').validate({
        submitHandler: function (form)
        {
            app_prepare_modal_action_loading(form)
            return true;
        }       
    });                                                                                                          
  });      
</script>         