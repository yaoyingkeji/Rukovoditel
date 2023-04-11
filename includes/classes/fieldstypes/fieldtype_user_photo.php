<?php

class fieldtype_user_photo
{

    public $options;

    function __construct()
    {
        $this->options = array('name' => TEXT_FIELDTYPE_USER_PHOTO_TITLE, 'title' => TEXT_FIELDTYPE_USER_PHOTO_TITLE);
    }

    function render($field, $obj, $params = array())
    {
        global $app_module_path;
        
        $filename = $obj['field_' . $field['id']];

        $html = '<div class="user-photo-preview">';
        if(strlen($filename) > 0)
        {            
            $file = attachments::parse_filename($filename);            

            $html .= image_tag(DIR_WS_USERS . $file['file_sha1'], array('class' => 'user-photo-in-form')) . input_hidden_tag('user_photo', $filename);
        }

        $html .= '</div>';

        if(IS_AJAX or $app_module_path == 'users/registration')
        {
            $html .= '<a href="javascript: open_sub_dialog(\'' . url_for('users/photo') . '\')" class="btn btn-sm btn-default">' . TEXT_UPLOAD . '</a>';
        }
        else
        {
            $html .= '<a href="javascript: open_dialog(\'' . url_for('users/photo') . '\')" class="btn btn-sm btn-default">' . TEXT_UPLOAD . '</a>';
        }

        $html .= ' <a style="' . (!strlen($filename) ? 'display:none' : '') . '" href="javascript: delete_user_photo()" class="btn btn-sm btn-default btn-delete-user-photo"><i class="fa fa-trash-o"></i></a>' . input_hidden_tag('delete_user_photo', 0);

        $html .= '
        <script>
        function delete_user_photo()
        {
            $("#delete_user_photo").val(1)
            $(".user-photo-preview img").hide()
            $(".btn-delete-user-photo").hide();
        }
        </script>    
        ';

        return $html;
    }

    function process($options)
    {
        global $alerts;

        $field_id = $options['field']['id'];

        if(isset($_POST['delete_user_photo']) and $_POST['delete_user_photo'] == 1)
        {
            $file = str_replace(['..', '/', '\/'], '', $_POST['user_photo']);

            if(is_file(DIR_FS_USERS . $file))
            {
                unlink(DIR_FS_USERS . $file);
            }

            return '';
        }


        if(isset($_POST['user_photo']))
        {
            return str_replace(['..', '/', '\/'], '', $_POST['user_photo']);
        }
        else
        {
            return '';
        }
    }

    function output($options)
    {
        if(strlen($options['value']) > 0)
        {
            
            $file = attachments::parse_filename($options['value']);                        
            
            $filename = $file['file'];

            $filepath = (is_file(DIR_WS_USERS . $file['file_sha1']) ? DIR_WS_USERS . $file['file_sha1'] : 'images/no_photo.png');

            if(isset($options['is_print']))
            {
                return '<img width=120 height=120 src=' . $filepath . ' class="user-profile-photo">';
            }
            elseif(isset($options['is_export']))
            {
                return $file['name'];
            }
            elseif(isset($options['is_listing']))
            {
                return image_tag(DIR_WS_USERS . $file['file_sha1'], array('width' => 50));
            }
            else
            {
                return '
        		<div class="attachments-gallery">
        			<ul>
        				<li>
        					<div class="gallery-image"><a class="fancybox" href="' . url_for('items/info&path=' . $options['path'], '&action=preview_user_photo&file=' . urlencode(base64_encode($filename))) . '">' . image_tag(DIR_WS_USERS . $file['file_sha1']) . '</a></div>
        					<div class="gallery-download-link">' . link_to('<i class="fa fa-download"></i> ' . TEXT_DOWNLOAD, url_for('items/info&path=' . $options['path'], '&action=download_user_photo&file=' . urlencode(base64_encode($filename)))) . '</div>
        				</li>
        			</ul>
        		</div>
        		<script type="text/javascript">
            	$(document).ready(function() {
            		$(".fancybox").fancybox({type: "ajax"});
            	});
            </script>';
            }
        }
        else
        {
            return '<img  src="images/no_photo.png" class="user-profile-photo">';
        }
    }

    public static function tmp_filename($filepath = '')
    {
        global $app_session_token;
        
        $mime_type = (strlen($filepath) and is_file($filepath)) ? mime_content_type($filepath): 'image/png';
        $ext = str_replace('image/','',$mime_type);        
        
        
        return 'tmp_photo_' . time()  . '_'. $app_session_token  . '.' . $ext;               
    }
    
    static function prepare_filename($entity_id, $item)
    {        
        if($entity_id!=1) return false;
        
        if(strlen($item['field_10']) and strstr($item['field_10'],'tmp_photo_'))
        {            
            self::reset_tmp_dir($item['id']);
                    
            $pathinfo = pathinfo(DIR_WS_USERS . 'tmp/' . $item['field_10']);
            $new_name = 'user_' . $item['id'] . '_' . time() . '.' . $pathinfo['extension'];
            
            copy(DIR_WS_USERS  . 'tmp/'. $item['field_10'], DIR_WS_USERS . $new_name);
            
            unlink(DIR_WS_USERS  . 'tmp/'. $item['field_10']);
            
            db_query("update app_entity_1 set field_10='{$new_name}' where id={$item['id']}");                                    
        }
    }
    
    static function reset_tmp_dir($item_id)
    {
        //reset tmp photos
        $files = glob(DIR_WS_USERS . 'tmp/*');

        if($files)
        {
            foreach($files as $file)
            {
                if(is_file($file))
                {                                        
                    if(time() - filemtime($file) >= 3600)
                    {
                        unlink($file);
                    }
                }
            }
        }
        
        //reset old  photos
        $files = glob(DIR_WS_USERS . '*');

        if($files)
        {
            foreach($files as $file)
            {
                if(is_file($file))
                {                                        
                    if(strstr($file,'user_' . $item_id . '.') or strstr($file,'user_' . $item_id . '_'))
                    {
                        unlink($file);
                    }
                }
            }
        }
    }

}
