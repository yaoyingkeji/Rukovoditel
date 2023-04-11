
<?php echo ajax_modal_template_header(TEXT_INFO) ?>

<?php
$url_label = ($log_info['is_ajax'] ? '<span style="color: #c7254e">[AJAX]</span>': (strstr($log_info['http_url'],'action=') ? '<span style="color: #c7254e">[ACTION]</span>':''));
    
if(strlen($url_label))
{
    $http_url = $log_info['http_url'] . ' ' . $url_label;
}
else
{
    $http_url = link_to($log_info['http_url'],'//' . $_SERVER['HTTP_HOST'] . $log_info['http_url'],['target'=>'_blank']);
}

if($log_info['errno']==0)
{
    $description = textarea_tag('description',SqlFormatter::format($log_info['description'],false),['class'=>'form-control code select-all','readonly'=>'readonly','style'=>'height: 250px;']);
}
else
{
    $description = '<div class="alert alert-danger">' . $log_info['description'] . '</div>';
}
?>

<form class="form-horizontal">
    <div class="modal-body ajax-modal-width-1100">    
        <div class="form-group">
            <label class="col-md-3 control-label"><?php echo TEXT_USERNAME ?></label>
            <div class="col-md-9">	
                <p class="form-control-static"><?php echo $log_info['username']; ?></p>
            </div>			
        </div>
        
        <div class="form-group">
            <label class="col-md-3 control-label"><?php echo TEXT_DATE_ADDED ?></label>
            <div class="col-md-9">	
                <p class="form-control-static"><?php echo format_date_time($log_info['date_added']); ?></p>
            </div>			
        </div>
        
        <div class="form-group">
            <label class="col-md-3 control-label"><?php echo TEXT_URL ?></label>
            <div class="col-md-9">	
                <p class="form-control-static"><?php echo $http_url; ?></p>
            </div>			
        </div>
        
        <div class="form-group">
            <label class="col-md-3 control-label"><?php echo TEXT_TIME . ' (' . TEXT_SECONDS . ')'?></label>
            <div class="col-md-9">	
                <p class="form-control-static"><?php echo $log_info['seconds']; ?></p>
            </div>			
        </div>
        
        <div class="form-group">
            <div class="col-md-12">
                <?php echo $description ?>
            </div>
        </div>
    </div> 
</form>

<?php echo ajax_modal_template_footer('hide-save-button') ?>