<?php

$file = attachments::parse_filename(base64_decode($_GET['file']));
    		  	
if(!is_file($file['file_path']))
{
    die(TEXT_FILE_NOT_FOUD);
}

switch($app_module_action)
{
    case 'get_video_file':
        header('Content-type: ' . $file['mime_type']);
        echo file_get_contents($file['file_path']);
        exit();
        break;
}


$html = '
    <div class="attachment-previw-window' . (is_mobile() ? '-mobile':''). '">
        <video id="player" playsinline controls autoplay  style="width:100%">
                <source src="' . url_for('items/attachment_preview_video','action=get_video_file&path=' . $app_path . '&file=' . urlencode(base64_encode($file['file']))) . '" type="' . $file['mime_type'] . '" />
        </video>
    </div>
    ';


echo $html;
