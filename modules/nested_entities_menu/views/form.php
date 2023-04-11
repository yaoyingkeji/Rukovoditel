
<?php echo ajax_modal_template_header(TEXT_RULE_FOR_FIELD) ?>

<?php echo form_tag('menu_form', url_for('nested_entities_menu/menu', 'action=save&entities_id=' . $_GET['entities_id'] . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')), array('class' => 'form-horizontal')) ?>
<div class="modal-body">
    <div class="form-body ajax-modal-width-790">

        <div class="form-group">
            <label class="col-md-3 control-label" for="is_active"><?php echo TEXT_IS_ACTIVE ?></label>
            <div class="col-md-9">	
                <p class="form-control-static"><?php echo input_checkbox_tag('is_active', $obj['is_active'], array('checked' => ($obj['is_active'] == 1 ? 'checked' : ''))) ?></p>
            </div>			
        </div>
        
        <div class="form-group">
            <label class="col-md-3 control-label" for="name"><?php echo TEXT_NAME ?></label>
            <div class="col-md-9">	
                <?php echo input_tag('name', $obj['name'], array('class' => 'form-control input-large required')) ?>
            </div>			
        </div> 
        
        <div class="form-group">
            <label class="col-md-3 control-label" for="icon"><?php echo TEXT_MENU_ICON_TITLE; ?></label>
            <div class="col-md-9">	
                <?php echo input_tag('icon', $obj['icon'], array('class' => 'form-control input-large')); ?>
                <?php echo tooltip_text(TEXT_MENU_ICON_TITLE_TOOLTIP) ?>       
            </div>			
        </div>
        
        <div class="form-group">
            <label class="col-md-3 control-label" for="icon_color"><?php echo TEXT_ICON_COLOR ?></label>
            <div class="col-md-9">	
                <?php echo input_color('icon_color',$obj['icon_color']) ?>
            </div>			
        </div> 
        
<?php
    $choices = [];
    $entities_query = db_query("select e.id, e.name, (select count(*) from app_nested_entities_menu m where find_in_set(e.id,m.entities)) is_used from app_entities e where e.parent_id='" . _GET('entities_id'). "' having is_used=0 or find_in_set(e.id,'" . db_input_in($obj['entities']). "') order by e.sort_order, e.name");
    while($entities = db_fetch($entities_query))
    {               
        $choices[$entities->id] = $entities->name;         
    }        
?>
        
        <div class="form-group">
            <label class="col-md-3 control-label" for="name"><?php echo TEXT_SELECT_ENTITIES ?></label>
            <div class="col-md-9">	
                <?php echo select_tag('entities[]',$choices, $obj['entities'], array('class' => 'form-control input-xlarge required chosen-select','multiple'=>'multiple')) ?>
            </div>			
        </div>


        <div class="form-group">
            <label class="col-md-3 control-label" for="sort_order"><?php echo TEXT_SORT_ORDER ?></label>
            <div class="col-md-9">	
                <?php echo input_tag('sort_order', $obj['sort_order'], array('class' => 'form-control input-xsmall')) ?>
            </div>			
        </div>     
    </div>
</div>

<?php echo ajax_modal_template_footer() ?>

</form> 

<script>
    $(function ()
    {
        $('#menu_form').validate({ignore: '',
            submitHandler: function (form)
            {
                app_prepare_modal_action_loading(form)
                return true;
            }
        });
    });

</script>   


