<?php

$file = attachments::parse_filename(base64_decode($_GET['file']));
    		  	
if(!is_file($file['file_path']))
{
    die(TEXT_FILE_NOT_FOUD);
}


switch($app_module_action)
{
    case 'get_file':
        header('Content-type: ' . $file['mime_type']);
        echo file_get_contents($file['file_path']);
        exit();
        break;
}

//print_rr($file);

$field_id = $_GET['field_id'];
$field = db_find('app_fields',$field_id);
$cfg = new settings($field['configuration']);


$html = '
    
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . $file['name'] . '</title>
    <link href="template/plugins/font-awesome/css/font-awesome.min.css?v=4.7.0" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="js/3dviewer/1.0/styles.css">    
</head>
<body style="background-color: ' . (strlen($cfg->get('bakground_color')) ? $cfg->get('bakground_color') : '#ffffff'). '"> 

    <div id="loading_text">' . TEXT_LOADING . '</div>
    <input type="hidden" id="modelPath" value="' .  url_for('items/attachment_preview_3d', 'path=' . $app_path . '&action=get_file&file=' . urlencode(base64_encode($file['file'])))  . '">
    <input type="hidden" id="fileExtension" value="' . $file['extension'] . '">
    
    <div id="navigation">         
        <button id="center" style="width: 80px; height: 30px;"><i class="fa fa-video-camera" aria-hidden="true"></i></button>
        <input type="color" id="color" value="' . (strlen($cfg->get('object_color')) ? $cfg->get('object_color') : '#eeeeee'). '" style="width: 80px; height: 30px;">        
    </div>
    <script type="module" src="js/3dviewer/1.0/script.js"></script>
</body>
</html>
    ';

echo $html;


exit();
