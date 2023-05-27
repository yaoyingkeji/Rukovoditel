<?php

class fieldtype_image_ajax
{

    public $options;

    function __construct()
    {
        $this->options = array('title' => TEXT_FIELDTYPE_IMAGE_AJAX_TITLE);
    }

    function get_configuration()
    {
        $cfg = array();
        $cfg[] = array('title' => TEXT_ALLOW_SEARCH, 'name' => 'allow_search', 'type' => 'checkbox', 'tooltip_icon' => TEXT_ALLOW_SEARCH_TIP);
        $cfg[] = array('title'=>TEXT_ALLOW_CHANGE_FILE_NAME, 'name'=>'allow_change_file_name','type'=>'checkbox');
        $cfg[] = array('title'=>TEXT_FILES_UPLOAD_SIZE_LIMIT, 'name'=>'upload_size_limit','type'=>'input', 'tooltip_icon'=>TEXT_FILES_UPLOAD_SIZE_LIMIT_TIP,'tooltip'=>TEXT_MAX_UPLOAD_FILE_SIZE . ' ' . CFG_SERVER_UPLOAD_MAX_FILESIZE . 'MB ' . TEXT_MAX_UPLOAD_FILE_SIZE_TIP,'params'=>array('class'=>'form-control input-xsmall'));
        $cfg[] = array('title' => TEXT_PREVIEW_IMAGE_SIZE, 'name' => 'width', 'type' => 'input', 'tooltip_icon' => TEXT_PREVIEW_IMAGE_SIZE_TIP, 'params' => array('class' => 'form-control input-small'));
        $cfg[] = array('title' => TEXT_PREVIEW_IMAGE_SIZE_IN_LISTING, 'name' => 'width_in_listing', 'type' => 'input', 'params' => array('class' => 'form-control input-small'));
        
        $choices = [
            'gif'=>'gif',
            'jpg'=>'jpg',
            'jpeg'=>'jpeg',
            'png'=>'png',
        ];
        $cfg[] = array('title' => TEXT_ALLOWED_EXTENSIONS, 'name' => 'allowed_extensions', 'type' => 'dropdown','choices'=>$choices, 'params' => array('class' => 'form-control chosen-select input-large','multiple'=>'multiple'));
        
        $tooltip = TEXT_ENTER_TEXT_PATTERN_INFO_SHORT . '<br>' . TEXT_EXAMPLE . ': <code>myfile_[221]_[current_date_time]</code>';
        $cfg[] = array('title' => TEXT_FILENAME_TEMPLATE, 'name' => 'filename_template', 'type' => 'input', 'params' => array('class' => 'form-control input-larege'),'tooltip'=>$tooltip);

        return $cfg;
    }

    function render($field, $obj, $params = array())
    {
        global $uploadify_attachments, $uploadify_attachments_queue, $current_path, $app_user, $app_items_form_name, $public_form, $app_session_token;

        $filename = $obj['field_' . $field['id']];
        $html = '';
        

        $field_id = $field['id'];

        $uploadify_attachments[$field_id] = array();
        $uploadify_attachments_queue[$field_id] = array();

        if(strlen($obj['field_' . $field['id']]) > 0)
        {
            $uploadify_attachments[$field_id] = explode(',', $obj['field_' . $field['id']]);
        }

        $timestamp = time();

        $delete_file_url = '';
        
        if($app_items_form_name == 'registration_form')
        {
            $form_token = md5($app_session_token . $timestamp);
            $uploadScript = url_for('users/registration', 'action=attachments_upload&field_id=' . $field_id, true);
            $previewScript = url_for('users/registration', 'action=attachments_preview&field_id=' . $field_id . '&token=' . $form_token);
        }
        elseif($app_items_form_name == 'public_form' or (isset($_GET['form_name'])) and $_GET['form_name']=='public_form')
        {
            $public_form['id'] = isset($_GET['public_form_id']) ? _GET('public_form_id'):$public_form['id'];
            $form_token = md5($app_session_token . $timestamp);
            $uploadScript = url_for('ext/public/form', 'action=attachments_upload&id=' . $public_form['id'] . '&field_id=' . $field_id, true);
            $previewScript = url_for('ext/public/form', 'action=attachments_preview&field_id=' . $field_id . '&id=' . $public_form['id'] . '&token=' . $form_token, true);
        }
        elseif($app_items_form_name == 'account_form')
        {
            $form_token = md5($app_user['id'] . $timestamp);
            $uploadScript = url_for('users/account', 'action=attachments_upload&path=' . $current_path . '&field_id=' . $field_id, true);
            $previewScript = url_for('users/account', 'action=attachments_preview&field_id=' . $field_id . '&path=' . $current_path . '&token=' . $form_token);
            $delete_file_url = url_for('users/account', 'action=attachments_delete_in_queue');
        }
        else
        {
            $form_token = md5($app_user['id'] . $timestamp);
            $uploadScript = url_for('items/items', 'action=attachments_upload&path=' . $current_path . '&field_id=' . $field_id, true);
            $previewScript = url_for('items/items', 'action=attachments_preview&field_id=' . $field_id . '&path=' . $current_path . '&token=' . $form_token);
            $delete_file_url = url_for('items/items', 'action=attachments_delete_in_queue&path=' . $_GET['path']);
        }

        $cfg = new fields_types_cfg($field['configuration']);
        
                
        $allowed_extensions = is_array($cfg->get('allowed_extensions')) ? $cfg->get('allowed_extensions') : ['gif','jpg','jpeg','png'];

        $mime_types = fieldtype_attachments::get_mime_types();
        $allowed_mime_types = [];
        foreach($allowed_extensions as $v)
        {
            foreach($mime_types[$v] as $vv)
            {
                $allowed_mime_types[] = $vv;
            }
        }
        
        $attachments_preview_html = attachments::render_preview($field_id, $uploadify_attachments[$field_id],$delete_file_url);

        $html .= '
        <div class="form-control-static"> 
          <input style="cursor: pointer" type="file" name="uploadifive_attachments_upload_' . $field_id . '" id="uploadifive_attachments_upload_' . $field_id . '" /> 
        </div>

        <div id="uploadifive_queue_list_' . $field_id . '"></div>
        <div id="uploadifive_attachments_list_' . $field_id . '">
          ' . $attachments_preview_html . '        
        </div>
<script>       

function uploadifive_oncomplate_filed_' . $field_id . '()
{
    $(".uploadifive-queue-item.complete").fadeOut();
    $("#uploadifive_attachments_list_' . $field_id . '").append("<div class=\"loading_data\"></div>");
    $("#uploadifive_attachments_list_' . $field_id . '").load("' .  $previewScript  . '"); 
    $("#uploadifive_queue_list_' . $field_id . '").html("");
}

$(function(){            
        
    $("#uploadifive_attachments_upload_' . $field['id'] . '").uploadifive({
        auto             : true,  
        dnd              : false, 
        fileType         : [\'' . implode("','", $allowed_mime_types) . '\'],
        fileTypeExtra    : "' . implode(',',$allowed_extensions). '",
        buttonClass      : "btn btn-default btn-upload",
        buttonText       : "<i class=\"fa fa-upload\"></i> ' . TEXT_SELECT_IMAGE . '",				            
        formData       :  {
                                "timestamp" : ' . $timestamp . ',
                                "token"     : "' .  $form_token . '",
                                "form_session_token" : "' . $app_session_token. '",
                                "app_form_name": "' . $app_items_form_name . '",                                
                                "filename_template": "' . addslashes($cfg->get('filename_template')) . '"    
                            },    
        queueID          : "uploadifive_queue_list_' . $field_id . '",
        fileSizeLimit : "' . (strlen($cfg->get('upload_size_limit')) ? (int)$cfg->get('upload_size_limit') : CFG_SERVER_UPLOAD_MAX_FILESIZE) . 'MB",
        multi: false,
        uploadScript: "' . $uploadScript . '",
        onUpload: function (filesToUpload)
        {
            
        },
        onUploadComplete: function (file, data)
        {
            uploadifive_oncomplate_filed_' . $field_id . '()              
        },
        onError: function (errorType)
        {
            
        },
        onCancel: function ()
        {
            
        }
    });
})    
</script>    
        ';

        return  $html;
    }

    function process($options)
    {
        $attachment = ''; 
        
        if(is_array($options['value']))
        {
            foreach($options['value'] as $v)
            {
                $file = attachments::parse_filename($v['file']);

                if($file['filename']==$v['name'])
                {
                    $attachment = $v['file'];
                }
                else
                {
                    $new_name = $file['date_added'] . '_' .  db_input_protect($v['name']) . (strlen($file['extension']) ? '.' . $file['extension'] : '');
                    $filepath = DIR_WS_ATTACHMENTS . $file['folder'] . '/'. (CFG_ENCRYPT_FILE_NAME == 1 ? sha1($new_name) : $new_name);

                    if(is_file($file['file_path']))
                    {                    
                        rename($file['file_path'],$filepath);
                    }

                    $attachment = $new_name;
                }
            }                
        } 
        else
        {
            $attachment = $options['value'];
        }
                
        return  $attachment;
    }

    function output($options)
    {
        $options_cfg = new fields_types_options_cfg($options);

        if(strlen($options['value']) > 0)
        {
            $file = attachments::parse_filename($options['value']);

            if(isset($options['is_print']))
            {
                return '<img width=120 height=120 src=' . url_for('items/info&path=' . $options['field']['entities_id'], '&action=download_attachment&preview=small&file=' . urlencode(base64_encode($options['value']))) . '>';
            }
            elseif(isset($options['is_email']))
            {
                if($options_cfg->get('hide_attachments_url') == 1)
                {
                    return $file['name'];
                }
                else
                {
                    return link_to($file['name'], url_for('items/info', 'path=' . $options['path'] . '&action=download_attachment&file=' . urlencode(base64_encode($options['value'])) . '&field=' . $options['field']['id']), array('target' => '_blank')) . (!$use_file_storage ? ' <small>(' . $file['size'] . ')</small>' : '');
                }
            }
            elseif(isset($options['is_export']))
            {
                return $file['name'];
            }
            else
            {
                if($file['is_image'])
                {
                    $cfg = new fields_types_cfg($options['field']['configuration']);

                    $fancybox_css_class = 'fancybox' . $options['field']['id'] . time();

                    $img = '<img class="fieldtype_image field_' . $options['field']['id'] . '"   src="' . url_for('items/info&path=' . $options['path'], '&action=download_attachment&preview=small&file=' . urlencode(base64_encode($options['value']))) . '">';

                    $width = (isset($options['is_listing']) ? (strlen($cfg->get('width_in_listing')) ? $cfg->get('width_in_listing') : 250) : (strlen($cfg->get('width')) ? $cfg->get('width') : 250));

                    $html = '
          <div class="fieldtype-image-container" style="width: ' . $width . 'px; max-height: ' . $width . 'px;">' .
                            link_to($img, url_for('items/info&path=' . $options['path'], '&action=preview_attachment_image&file=' . urlencode(base64_encode($options['value']))), array('class' => $fancybox_css_class)) . '
           </div> 
          ';
                    //add public link
                    $public_link = '';
                    if(isset($options['field']['id']) and in_array($options['field']['id'], explode(',', CFG_PUBLIC_ATTACHMENTS)))
                    {
                        $public_link = ' ' . link_to('<i class="fa fa-link"></i>', url_for('export/file', 'id=' . $options['field']['id'] . '&path=' . $options['field']['entities_id'] . '-' . $options['item']['id'] . '&file=' . urlencode($options['value'])), ['target' => "_blank"]);
                    }

                    if(!isset($options['is_listing']))
                    {
                        $html .= '
                            <div class="fieldtype-image-filename" style="width: ' . $width . 'px">
                                ' . link_to('<img src="images/fileicons/jpg.png"> ' . $file['name'],url_for('items/info&path=' . $options['path'] ,'&action=preview_attachment_image&file=' . urlencode(base64_encode($options['value']))),array('class'=>$fancybox_css_class)) . '
                                ' . link_to('<i class="fa fa-download"></i>' ,url_for('items/info','path=' . $options['path'] . '&action=download_attachment&file=' . urlencode(base64_encode($options['value'])))) . '
                                ' . $public_link . '    
                                <small>(' . $file['size'] . ')</small>
                            </div>';
                    }

                    $html .= '
          <script>
            $(document).ready(function() {
            	$(".' . $fancybox_css_class . '").fancybox({
                    type: "ajax",
                    beforeLoad : function() { 
                        this.href = this.href+\'&windowWidth=\' + $(window).width()+\'&windowHeight=\' + $(window).height();
                    }
                });
            });
          </script>
          ';

                    return $html;
                }
                else
                {
                    return '<img src="' . $file['icon'] . '"> ' . link_to($file['name'], url_for('items/info', 'path=' . $options['path'] . '&action=download_attachment&file=' . urlencode(base64_encode($options['value']))), array('target' => '_blank')) . '  <small>(' . $file['size'] . ')</small>';
                }
            }
        }
        else
        {
            return '';
        }
    }

}
