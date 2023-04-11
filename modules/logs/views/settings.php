
<h3 class="page-title"><?php echo TEXT_LOGS . ' <i class="fa fa-angle-right"></i> ' . TEXT_SETTINGS ?></h3>

<p><?php echo TEXT_LOGS_INFO ?></p>

<?php echo form_tag('cfg', url_for('configuration/save','redirect_to=logs/settings'),array('class'=>'form-horizontal')) ?>
<div class="form-body">
    
    <div class="form-group">
        <label class="col-md-3 control-label"><?php echo TEXT_TOGGLE_ON ?></label>
        <div class="col-md-9">	
            <?php echo select_tag_boolean('CFG[APP_LOGS_ENABLE]', CFG_APP_LOGS_ENABLE); ?>
        </div>			
    </div>
    
<?php
    $choices = [
        'http'=>'HTTP',
        'mysql'=>'MySQL',
        'sql_error'=>'MySQL ' . TEXT_ERRORS,
        'php'=>'PHP',
        'email'=>'Email',
        'mail_error'=>'Email ' . TEXT_ERRORS,
        ];
?>
    
    <div class="form-group" form_display_rules="CFG_APP_LOGS_ENABLE:1">
        <label class="col-md-3 control-label"><?php echo TEXT_TYPE ?></label>
        <div class="col-md-9">	
            <?php echo select_checkboxes_tag('CFG[APP_LOGS_TYPES]',$choices,CFG_APP_LOGS_TYPES); ?>
        </div>			
    </div>
    
    <div class="form-group" form_display_rules="CFG_APP_LOGS_ENABLE:1">
        <label class="col-md-3 control-label"><?php echo TEXT_STORE_IN_DB ?></label>
        <div class="col-md-9">	
            <?php echo input_tag('CFG[APP_LOGS_STORE_DAYS]', CFG_APP_LOGS_STORE_DAYS,['class'=>'form-control input-small required','type'=>'number','min'=>1]); ?>
            <?php echo tooltip_text(TEXT_ENTER_NUMBER_OF_DAYS) ?>
        </div>			
    </div>
    
<?php
    $count_query = db_query("select count(*) as total from app_logs");
    $count = db_fetch_array($count_query);
?>
    <div class="form-group">
        <label class="col-md-3 control-label"><?php echo TEXT_NUMBER_OF_RECORDS ?></label>
        <div class="col-md-9">	
            <div class="input-group input-medium">
                <?= input_tag('total', $count['total'],['class'=>'form-control','readonly'=>'readonly']) ?>                
                <span class="input-group-btn">
                    <a href="<?= url_for('logs/settings','action=reset') ?>" class="btn btn-default"><?= TEXT_CLEAR ?></a>
                </span>
            </div>
        </div>			
    </div>
    
    <?php echo submit_tag(TEXT_BUTTON_SAVE) ?>
 
</div>
</form>

<script>
    $(function ()
    {
        $('#cfg').validate()
    })
</script>

   