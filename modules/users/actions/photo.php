<?php


switch($app_module_action)
{
    case 'upload':
        if (strlen($_FILES['Filedata']['tmp_name']) and is_image($_FILES['Filedata']['tmp_name']))
        {
            //$file = attachments::prepare_filename($_FILES['Filedata']['name']);
            
            $filename = fieldtype_user_photo::tmp_filename($_FILES['Filedata']['tmp_name']);
            
            if(!is_dir(DIR_FS_USERS . '/tmp'))
            {
                mkdir(DIR_FS_USERS . '/tmp');
            }

            if (move_uploaded_file($_FILES['Filedata']['tmp_name'], DIR_FS_USERS . 'tmp/' . $filename))
            {
                echo json_encode([       
                    'name'=>$filename,
                    'file'=>$filename,                    
                    ]);
                
                exit();
            }
        }
        
        echo 'error';
        
        exit();
        break;
        
    case 'save':
                
        $filename = strlen($_POST['filename']) ? $_POST['filename'] : fieldtype_user_photo::tmp_filename();
                                    
        $img = str_replace(['data:image/png;base64,','data:image/jpeg;base64','data:image/gif;base64',' '], ['','','','+'], $_POST['img']);        
                        
        file_put_contents(DIR_WS_USERS  . 'tmp/'. $filename, base64_decode($img));
                                
        if(!is_image(DIR_WS_USERS . 'tmp/'. $filename))
        {
            unlink(DIR_WS_USERS . 'tmp/'.$filename);
        }
        else
        {
            image_resize(DIR_FS_USERS  . 'tmp/'. $filename,DIR_FS_USERS  . 'tmp/'. $filename,250);
        }
                        
        echo json_encode([
            'name'=>$filename,
            'file'=>DIR_WS_USERS . 'tmp/' . $filename,                    
            ]);
                  
        exit();
        break;
}
