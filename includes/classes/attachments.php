<?php

class attachments
{

    public static function delete_in_item($entity_id, $item_id, $field_id, $filename)
    {
        global $app_fields_cache;
        
        if($entity_id and $item_id and isset_field($entity_id, $field_id))
        {
            $item_query = db_query("select field_{$field_id} as attachments  from app_entity_{$entity_id} where id={$item_id} and length(field_{$field_id})>0");
            if($item = db_fetch($item_query))
            {
                $attachments = array_diff(explode(',',$item->attachments),[$filename]);
                                                
                $data = [
                    'field_' . $field_id => implode(',',$attachments)
                ];
                
                //print_rr($data);
                                                                                
                db_perform("app_entity_{$entity_id}", $data,'update',"id={$item_id}");                                
            }
        }        
    }
    
    public static function delete_in_queue($field_id, $filename)
    {
        global $uploadify_attachments, $uploadify_attachments_queue;

        $key = array_search($filename, $uploadify_attachments[$field_id]);
        $queue_key = array_search($filename, $uploadify_attachments_queue[$field_id]);

        //echo $filename . ' - ' . $field_id;
        //print_r($uploadify_attachments);

        if($key !== false or $queue_key !== false)
        {
            $file = attachments::parse_filename($filename);

            //delete file
            if(is_file(DIR_WS_ATTACHMENTS . $file['folder'] . '/' . $file['file_sha1']))
            {
                unlink(DIR_WS_ATTACHMENTS . $file['folder'] . '/' . $file['file_sha1']);
            }

            //delete from sessions
            if($key !== false)
            {
                unset($uploadify_attachments[$field_id][$key]);
            }

            if($queue_key !== false)
            {
                unset($uploadify_attachments_queue[$field_id][$queue_key]);
            }

            //delete from queue 
            db_query("delete from app_attachments where filename='" . db_input($filename) . "'");

            //delete files from file storage
            if(is_ext_installed())
            {
                $file_storage = new file_storage();
                $file_storage->delete_files($field_id, array($filename));
            }
        }
    }

    public static function render_preview($field_id, $attachments_list, $delete_file_url)
    {
        global $app_session_token, $app_module_path;

        $html = '';

        $field_query = db_query("select id, name, configuration,type from app_fields where id='" . $field_id . "'");
        if($field = db_fetch_array($field_query))
        {
            $cfg = new fields_types_cfg($field['configuration']);
        }
        else
        {
            $cfg = new settings('');
        }


        if(is_array($attachments_list) and count($attachments_list) > 0)
        {
            $has_delete_access = true;

            if(in_array($app_module_path, ['items/form', 'items/items']))
            {
                if($cfg->get('check_delete_access'))
                {
                    $has_delete_access = users::has_access('delete');
                }
            }
            
            //prepare image ajax
            if(isset($field['type']) and in_array($field['type'],['fieldtype_image_ajax','fieldtype_image_map_nested']))
            {
                $attachments_list = [0=>end($attachments_list)];                
            }
            
                                    
            if($cfg->get('allow_change_file_name') and in_array($app_module_path, ['items/form', 'items/items','items/processes']))
            {
                foreach($attachments_list as $k => $v)
                {
                    $file = attachments::parse_filename($v);
                    $row_id = 'attachments_row_' . $field_id . '_' . $k;
                    
                    $html .= '
                        <div class="input-group input-group-attachments ' . $row_id . '">
                        ' . input_tag('fields[' . $field_id . '][' . $k. '][name]',$file['filename'],['class'=>'form-control input-sm']) . '
                        ' . input_hidden_tag('fields[' . $field_id . '][' . $k. '][file]', $v);
                    
                    if(strlen($file['extension']))
                    {
                        $html .= '
                            <span class="input-group-addon">
                                .' . $file['extension'] . '
                            </span>
                            ';
                    }
                    
                    if($has_delete_access)
                    {
                        $html .= '
                            <span class="input-group-addon">
                                <i class="fa fa-trash-o pointer delete_attachments_checkbox" data-confirm-delation="' . $cfg->get('confirm_delation') . '" data-filename="' . $file['file'] . '" data-row-id="' . $row_id . '" title="' . TEXT_DELETE . '"></i>
                            </span>        
                            ';
                    }

                    $html .= ' 
                        </div>
                        ';
                }
            }
            else
            {

                $html .= '
                <div class="table-scrollable attachments-form-list">
                <table class="table table-striped table-hover">
                  <tbody>';
                      foreach($attachments_list as $k => $v)
                      {
                          $file = attachments::parse_filename($v);

                          $row_id = 'attachments_row_' . $field_id . '_' . $k;

                          if($has_delete_access)
                          {
                              $html .= '
                            <tr class="' . $row_id . '">
                              <td width="100%">' . app_crop_str($file['name']) . '<small>' . fieldtype_attachments::add_file_date_added($file, $cfg) . '</small></td>
                              <td align="right"><label class="checkbox delete_attachments">
                                          <i class="fa fa-trash-o pointer delete_attachments_checkbox" data-confirm-delation="' . $cfg->get('confirm_delation') . '" data-filename="' . $file['file'] . '" data-row-id="' . $row_id . '" title="' . TEXT_DELETE . '"></i></label>
                              </td>
                            </tr>
                          ';
                          }
                          else
                          {
                              $html .= '
                            <tr class="' . $row_id . '">
                              <td width="100%" style="padding-bottom: 5px;">' . $file['name'] . '<small>' . fieldtype_attachments::add_file_date_added($file, $cfg) . '</small></td>	           
                            </tr>
                          ';
                          }
                      }

                      $html .= '
                  </tbody>
                </table>
                </div>';
                      
                $html .= input_hidden_tag('fields[' . $field_id . ']', implode(',', $attachments_list));
            }


            $html .= '              
                <script>
                          appHandleAttachmentsDelete(\'' . $field_id . '\',\'' . $delete_file_url . '\',\'' . $app_session_token . '\');
                          appHandleUniformCheckbox();
                </script>
                ';

            
        }
        else
        {
            $field = db_find('app_fields', $field_id);

            if($field['is_required'])
            {
                $html .= input_hidden_tag('fields[' . $field_id . ']', '', array('class' => 'form-control field_' . $field['id'] . ' required'));
            }
        }

        return $html;
    }

    public static function delete_attachments($entities_id, $items_id)
    {

        $fields_query = db_query("select * from app_fields where entities_id='" . db_input($entities_id) . "' and type in ('fieldtype_attachments','fieldtype_input_file','fieldtype_image','fieldtype_image_ajax')");
        while($fields = db_fetch_array($fields_query))
        {
            $items_query = db_query("select * from app_entity_" . $entities_id . " where id='" . db_input($items_id) . "'");
            if($items = db_fetch_array($items_query))
            {
                if(strlen($files = $items['field_' . $fields['id']]) > 0)
                {
                    foreach(explode(',', $files) as $file)
                    {
                        $file = attachments::parse_filename($file);
                        if(is_file(DIR_WS_ATTACHMENTS . $file['folder'] . '/' . $file['file_sha1']))
                        {
                            unlink(DIR_WS_ATTACHMENTS . $file['folder'] . '/' . $file['file_sha1']);

                            //delete image preview if exist
                            self::delete_image_preview($file);
                        }
                    }

                    //delete files from file storage
                    if(class_exists('file_storage'))
                    {
                        $file_storage = new file_storage();
                        $file_storage->delete_files($fields['id'], explode(',', $files));
                    }
                }
            }
        }
    }

    public static function delete_comments_attachments($comments_id)
    {
        $comments_query = db_query("select * from app_comments where id='" . db_input($comments_id) . "' and length(attachments)>0");
        if($comments = db_fetch_array($comments_query))
        {
            foreach(explode(',', $comments['attachments']) as $file)
            {
                $file = attachments::parse_filename($file);
                if(is_file(DIR_WS_ATTACHMENTS . $file['folder'] . '/' . $file['file_sha1']))
                {
                    unlink(DIR_WS_ATTACHMENTS . $file['folder'] . '/' . $file['file_sha1']);

                    //delete image preview if exist
                    self::delete_image_preview($file);
                }
            }
        }
    }

    public static function prepare_image_filename($filename)
    {
        $filename = str_replace(array(" ", ","), "_", trim($filename));

        if(!is_dir(DIR_WS_IMAGES . date('Y')))
        {
            mkdir(DIR_WS_IMAGES . date('Y'));
        }

        if(!is_dir(DIR_WS_IMAGES . date('Y') . '/' . date('m')))
        {
            mkdir(DIR_WS_IMAGES . date('Y') . '/' . date('m'));
        }

        if(!is_dir(DIR_WS_IMAGES . date('Y') . '/' . date('m') . '/' . date('d')))
        {
            mkdir(DIR_WS_IMAGES . date('Y') . '/' . date('m') . '/' . date('d'));
        }

        return array('file' => $filename,
            'folder' => date('Y') . '/' . date('m') . '/' . date('d'));
    }

    public static function prepare_filename($filename)
    {
        $filename = str_replace(array(" ", ","), "_", trim($filename));

        if(!is_dir(DIR_WS_ATTACHMENTS . date('Y')))
        {
            mkdir(DIR_WS_ATTACHMENTS . date('Y'));
        }

        if(!is_dir(DIR_WS_ATTACHMENTS . date('Y') . '/' . date('m')))
        {
            mkdir(DIR_WS_ATTACHMENTS . date('Y') . '/' . date('m'));
        }

        if(!is_dir(DIR_WS_ATTACHMENTS . date('Y') . '/' . date('m') . '/' . date('d')))
        {
            mkdir(DIR_WS_ATTACHMENTS . date('Y') . '/' . date('m') . '/' . date('d'));
        }

        return array('name' => time() . '_' . $filename,
            'file' => (CFG_ENCRYPT_FILE_NAME == 1 ? sha1(time() . '_' . $filename) : time() . '_' . $filename),
            'folder' => date('Y') . '/' . date('m') . '/' . date('d'));
    }

    public static function parse_filename($filename)
    {
        if(substr($filename,0,4)=='user')
        {
            return [
                'file' => $filename,
                'name' => $filename,
                'file_sha1' => $filename,
            ];
        }
                
        //get filetime
        $filename_array = explode('_', $filename);
        $filetime = (int) $filename_array[0];

        //get foler
        $folder = date('Y', $filetime) . '/' . date('m', $filetime) . '/' . date('d', $filetime);

        //get filename
        $name = substr($filename, strpos($filename, '_') + 1);

        //get extension
        $filename_array = explode('.', $filename);
        $extension = strtolower($filename_array[sizeof($filename_array) - 1]);

        if(is_file('images/fileicons/' . $extension . '.png'))
        {
            $icon = 'images/fileicons/' . $extension . '.png';
        }
        else
        {
            $icon = 'images/fileicons/attachment.png';
        }


        if(is_file($file_path = DIR_WS_ATTACHMENTS . $folder . '/' . sha1($filename)))
        {
            $size = attachments::file_size_convert(filesize($file_path));
        }
        //in encryption disabled
        elseif(is_file($file_path = DIR_WS_ATTACHMENTS . $folder . '/' . $filename))
        {
            $size = attachments::file_size_convert(filesize($file_path));
        }
        else
        {
            $size = 0;
        }
        
        $pathinfo = pathinfo($name);

        return array(
            'name' => $name,
            'file' => $filename,
            'file_sha1' => (CFG_ENCRYPT_FILE_NAME == 1 ? sha1($filename) : $filename),
            'file_path' => $file_path,
            'folder' => DIR_WS_ATTACHMENTS . $folder . '/',
            'mime_type' => ($size ? mime_content_type($file_path) : ''),
            'is_image' => is_image($file_path),
            'is_pdf' => is_pdf($filename),
            'is_audio' => is_audio($file_path),
            'is_video' => is_video($file_path),
            'is_exel' => is_excel($filename),
            'is_text' => is_text($file_path),
            'icon' => $icon,
            'size' => $size,
            'folder' => $folder,
            'extension'=> $pathinfo['extension']??'',
            'filename'=> $pathinfo['filename']??'',
            'date_added' => substr($filename, 0, strpos($filename, '_')),
        );
    }
    


    public static function file_size_convert($bytes)
    {
        $bytes = floatval($bytes);
        $arBytes = array(
            0 => array(
                "UNIT" => "TB",
                "VALUE" => pow(1024, 4)
            ),
            1 => array(
                "UNIT" => "GB",
                "VALUE" => pow(1024, 3)
            ),
            2 => array(
                "UNIT" => "MB",
                "VALUE" => pow(1024, 2)
            ),
            3 => array(
                "UNIT" => "KB",
                "VALUE" => 1024
            ),
            4 => array(
                "UNIT" => "B",
                "VALUE" => 1
            ),
        );

        foreach($arBytes as $arItem)
        {
            if($bytes >= $arItem["VALUE"])
            {
                $result = $bytes / $arItem["VALUE"];
                $result = str_replace(".", ",", strval(round($result, 1))) . " " . $arItem["UNIT"];
                break;
            }
        }

        return (isset($result) ? $result : 0);
    }

    public static function copy($files)
    {
        $new_files_list = array();

        if(strlen($files) > 0)
        {
            $files = explode(',', $files);

            foreach($files as $file)
            {
                $file_info = self::parse_filename($file);

                $new_file = self::prepare_filename($file_info['name']);
                $new_file_path = DIR_WS_ATTACHMENTS . '/' . $new_file['folder'] . '/' . $new_file['file'];

                $current_file_path = $file_info['file_path'];

                if(is_file($current_file_path))
                {
                    if(copy($current_file_path, $new_file_path))
                    {
                        $new_files_list[] = $new_file['name'];
                    }
                }
            }
        }

        return implode(',', $new_files_list);
    }

    public static function resize($filepath, $field_id =false)        
    {
        //skip resize for some field types
        if($field_id)
        {
            $field_query = db_query("select id from app_fields where id={$field_id} and type in ('fieldtype_image_map_nested')");
            if($field = db_fetch_array($field_query))
            {
                return false;
            }
        }
        
        $max_img_width = (int) CFG_MAX_IMAGE_WIDTH;
        $max_img_height = (int) CFG_MAX_IMAGE_HEIGHT;

        if(($max_img_width > 0 or $max_img_height > 0) and CFG_RESIZE_IMAGES == 1 and is_image($filepath))
        {
            $img = getimagesize($filepath);

            //skip large images
            $skip_size = (int) CFG_SKIP_IMAGE_RESIZE;
            if($skip_size > 0)
            {
                if($img[0] > $skip_size or $img[1] > $skip_size)
                {
                    return false;
                }
            }

            //skip image types
            if(strlen(CFG_RESIZE_IMAGES_TYPES) > 0)
            {
                if(!in_array($img[2], explode(',', CFG_RESIZE_IMAGES_TYPES)))
                {
                    return false;
                }
            }

            //resize image
            if($img[0] > $max_img_width or $img[1] > $max_img_height)
            {
                if($img[0] > $img[1])
                {
                    image_resize($filepath, $filepath, $max_img_width);
                }
                else
                {
                    image_resize($filepath, $filepath, '', $max_img_height);
                }
            }
        }
    }

    static function rotate_image($filepath, $rotate_type)
    {
        $mime_type = mime_content_type($filepath);

        switch($rotate_type)
        {
            case 'left': $degrees = 90;
                break;
            case 'right': $degrees = -90;
                break;
        }

        switch($mime_type)
        {
            case 'image/jpeg':
                if($source = imagecreatefromjpeg($filepath))
                {
                    if($rotate = imagerotate($source, $degrees, 0))
                    {
                        sleep(1);
                        imagejpeg($rotate, $filepath, 85);
                    }
                }
                break;
            case 'image/png':
                if($source = imagecreatefrompng($filepath))
                {
                    imagealphablending($source, false);
                    imagesavealpha($source, true);

                    if($rotate = imagerotate($source, $degrees, 0))
                    {
                        imagealphablending($rotate, false);
                        imagesavealpha($rotate, true);

                        imagepng($rotate, $filepath);
                    }
                }
                break;

            case 'image/gif':
                if($source = imagecreatefromgif($filepath))
                {
                    if($rotate = imagerotate($source, $degrees, 0, 1))
                    {
                        imagegif($rotate, $filepath, 75);
                    }
                }
                break;
        }

        if($source)
        {
            imagedestroy($source);
            imagedestroy($rotate);
        }
    }

    static function get_image_preview_filepath($file)
    {
        $filename = $file['file'];
        //get filetime
        $filename_array = explode('_', $filename);
        $filetime = (int) $filename_array[0];

        //get foler
        $filepath = DIR_WS_ATTACHMENTS_PREVIEW . date('Y', $filetime) . '/' . date('m', $filetime) . '/' . date('d', $filetime) . '/' . $file['file_sha1'];

        return ['filepath' => $filepath, 'filetime' => $filetime];
    }

    static function prepare_image_preview($file)
    {
        $info = self::get_image_preview_filepath($file);
        $filepath = $info['filepath'];
        $filetime = $info['filetime'];

        if(!is_dir(DIR_WS_ATTACHMENTS_PREVIEW . date('Y', $filetime)))
        {
            mkdir(DIR_WS_ATTACHMENTS_PREVIEW . date('Y', $filetime));
        }

        if(!is_dir(DIR_WS_ATTACHMENTS_PREVIEW . date('Y', $filetime) . '/' . date('m', $filetime)))
        {
            mkdir(DIR_WS_ATTACHMENTS_PREVIEW . date('Y', $filetime) . '/' . date('m', $filetime));
        }

        if(!is_dir(DIR_WS_ATTACHMENTS_PREVIEW . date('Y', $filetime) . '/' . date('m', $filetime) . '/' . date('d', $filetime)))
        {
            mkdir(DIR_WS_ATTACHMENTS_PREVIEW . date('Y', $filetime) . '/' . date('m', $filetime) . '/' . date('d', $filetime));
        }

        if(!is_file($filepath))
        {
            if(!image_resize($file['file_path'], $filepath, 300))
            {
                $filepath = $file['file_path'];
            }
        }

        return $filepath;
    }

    static function has_image_preview($file)
    {
        $info = self::get_image_preview_filepath($file);

        return (is_file($info['filepath']) ? true : false);
    }

    static function delete_image_preview($file)
    {
        $info = self::get_image_preview_filepath($file);

        if(is_file($info['filepath']))
        {
            unlink($info['filepath']);
        }
    }

}
