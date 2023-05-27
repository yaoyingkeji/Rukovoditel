<?php

$file = attachments::parse_filename(base64_decode($_GET['file']));
    		  	
if(!is_file($file['file_path']))
{
    die(TEXT_FILE_NOT_FOUD);
}

switch($app_module_action)
{
    case 'get_pdf_file':
        header('Content-type: ' . $file['mime_type']);
        header('Content-Disposition: inline; filename="' . $file['name']. '"');
        echo file_get_contents($file['file_path']);
        exit();
        break;
}

if(is_mobile())
{
    $html = '    
            <iframe id="text_preview" class="attachment-previw-doc' . (is_mobile() ? '-mobile':''). '" src="js/pdfViewerJS/#' . url_for('items/attachment_preview_pdf','action=get_pdf_file&path=' . $app_path . '&file=' . urlencode(base64_encode($file['file'])))  . '"></iframe>    
        ';     
}
else
{

    $html = '    
            <iframe id="text_preview" class="attachment-previw-doc' . (is_mobile() ? '-mobile':''). '" src="' . url_for('items/attachment_preview_pdf','action=get_pdf_file&path=' . $app_path . '&file=' . urlencode(base64_encode($file['file'])))  . '"></iframe>    
        ';            
}

$html .= '    
        <script>    
            if(is_mobile)
            {
                $("#text_preview").css("width",($(window).width()-100)+"px").css("height",($(window).height()-100)+"px")
            }
            else
            {
                $("#text_preview").css("height",($(window).height()-200)+"px")
            }
        </script>
        ';

echo $html;

