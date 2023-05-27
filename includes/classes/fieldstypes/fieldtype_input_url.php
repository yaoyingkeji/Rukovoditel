<?php

class fieldtype_input_url
{

    public $options;

    function __construct()
    {
        $this->options = array('title' => TEXT_FIELDTYPE_INPUT_URL_TITLE);
    }

    function get_configuration()
    {
        $cfg = array();

        $cfg[] = array('title' => TEXT_TARGET, 'name' => 'target', 'type' => 'dropdown', 'choices' => ['_blank' => TEXT_TARGET_BLANK, '_self' => TEXT_TARGET_SELF], 'params' => array('class' => 'form-control input-medium'));
        $cfg[] = array('title' => TEXT_VALIDATE_URL, 'name' => 'validate_url', 'type' => 'checkbox');
        $cfg[] = array('title' => TEXT_ALLOW_SEARCH, 'name' => 'allow_search', 'type' => 'checkbox', 'tooltip_icon' => TEXT_ALLOW_SEARCH_TIP);
        $cfg[] = array('title' => TEXT_URL_PREVIEW_TEXT, 'name' => 'preview_text', 'type' => 'input', 'tooltip_icon' => TEXT_URL_PREVIEW_TEXT_TIP, 'params' => array('class' => 'form-control input-medium'));
        $cfg[] = array('title' => TEXT_URL_PREFIX, 'name' => 'prefix', 'type' => 'input', 'tooltip_icon' => TEXT_URL_PREFIX_TIP, 'params' => array('class' => 'form-control input-small'));
        
        $cfg[] = array('title' => TEXT_DISPLAY_COPY_TO_CLIPBOARD_ICON, 'name' => 'copy_to_clipboard', 'type' => 'checkbox', 'tooltip_icon' => TEXT_ICON_WILL_DISPLAYED_ON_RECORD_PAGE);
        $cfg[] = array('title' => TEXT_HIDE_FIELD_IF_EMPTY, 'name' => 'hide_field_if_empty', 'type' => 'checkbox', 'tooltip_icon' => TEXT_HIDE_FIELD_IF_EMPTY_TIP);
        

        $cfg[] = array('title' => TEXT_IS_UNIQUE_FIELD_VALUE, 'name' => 'is_unique', 'type' => 'dropdown', 'choices' => fields_types::get_is_unique_choices(_POST('entities_id')), 'tooltip_icon' => TEXT_IS_UNIQUE_FIELD_VALUE_TIP, 'params' => array('class' => 'form-control input-large'));
        $cfg[] = array('title' => TEXT_ERROR_MESSAGE, 'name' => 'unique_error_msg', 'type' => 'input', 'tooltip_icon' => TEXT_UNIQUE_FIELD_VALUE_ERROR_MSG_TIP, 'tooltip' => TEXT_DEFAULT . ': ' . TEXT_UNIQUE_FIELD_VALUE_ERROR, 'params' => array('class' => 'form-control input-xlarge'));

        return $cfg;
    }

    function render($field, $obj, $params = array())
    {
        $cfg = new fields_types_cfg($field['configuration']);

        $attributes = array(
            'class' => 'form-control input-large fieldtype_input_url field_' . $field['id'] .
            ($field['is_required'] == 1 ? ' required noSpace' : '') .
            ($cfg->get('is_unique') > 0 ? ' is-unique' : '') .
            ($cfg->get('validate_url') == 1 ? ' url' : '')
        );

        $attributes = fields_types::prepare_uniquer_error_msg_param($attributes, $cfg);

        return input_tag('fields[' . $field['id'] . ']', $obj['field_' . $field['id']], $attributes);
    }

    function process($options)
    {
        return db_prepare_input($options['value']);
    }

    function output($options)
    {

        $cfg = new settings($options['field']['configuration']);

        $url = $options['value'];
        $url_text = TEXT_VIEW;

        if($cfg->get('preview_text') == 'none')
        {
            $url_text = $url;
        }
        elseif(strlen($cfg->get('preview_text')) > 0)
        {
            $url_text = $cfg->get('preview_text');
        }


        if(strlen($cfg->get('prefix')) > 0)
        {
            $url = (!stristr($url, $cfg->get('prefix')) ? $cfg->get('prefix') : '') . $url;
        }
        elseif(!stristr($url, '://'))
        {
            $url = 'http://' . $url;
        }


        if(strlen($options['value']) > 0)
        {
            if(isset($options['is_export']))
            {
                return $url;
            }
            else
            {
                $html = '';
                if(($options['is_item_page']??false)==true and $cfg->get('copy_to_clipboard')==1)
                {
                    $html = app_clipboardjs_icon($url);
                }
                
                return '<a href="' . $url . '" target="' . $cfg->get('target', '_blank') . '">' . $url_text . '</a>' . $html;
            }
        }
        else
        {
            return '';
        }
    }

}
