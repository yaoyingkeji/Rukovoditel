<?php

$file = attachments::parse_filename(base64_decode($_GET['file']));
    		  	
if(!is_file($file['file_path']))
{
    die(TEXT_FILE_NOT_FOUD);
}

$download_url = url_for('items/info','path=' . $app_path . '&action=download_attachment&file=' . urlencode(base64_encode($file['file'])));

require(component_path('items/attachment_preview_text'));
