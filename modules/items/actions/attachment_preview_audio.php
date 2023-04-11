<?php

$file = attachments::parse_filename(base64_decode($_GET['file']));
    		  	
if(!is_file($file['file_path']))
{
    die(TEXT_FILE_NOT_FOUD);
}

switch($app_module_action)
{
    case 'get_audio_file':
        header('Content-type: ' . $file['mime_type']);
        echo file_get_contents($file['file_path']);
        exit();
        break;
}


$html = '
    <div class="attachment-previw-window' . (is_mobile() ? '-mobile':''). '">    
        <br>
        <audio controls autoplay controlsList="nodownload" style="width:100%">
            <source src="' . url_for('items/attachment_preview_audio', 'path=' . $app_path . '&action=get_audio_file&file=' . urlencode(base64_encode($file['file']))) . '" type="' . $file['mime_type'] . '">
              Your browser does not support the audio element.
        </audio>
    </div>
    ';

echo $html;
