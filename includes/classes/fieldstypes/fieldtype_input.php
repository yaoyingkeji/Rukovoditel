<?php

class fieldtype_input
{

    public $options;

    function __construct()
    {
        $this->options = array('title' => TEXT_FIELDTYPE_INPUT_TITLE);
    }

    function get_configuration()
    {
        $cfg = array();

        $cfg[] = array('title' => TEXT_ALLOW_SEARCH, 'name' => 'allow_search', 'type' => 'checkbox', 'tooltip_icon' => TEXT_ALLOW_SEARCH_TIP);

        $cfg[] = array('title' => TEXT_WIDHT,
            'name' => 'width',
            'type' => 'dropdown',
            'choices' => array('input-small' => TEXT_INPTUT_SMALL, 'input-medium' => TEXT_INPUT_MEDIUM, 'input-large' => TEXT_INPUT_LARGE, 'input-xlarge' => TEXT_INPUT_XLARGE),
            'tooltip_icon' => TEXT_ENTER_WIDTH,
            'params' => array('class' => 'form-control input-medium'));
        
        $cfg[] = array('title' => TEXT_DEFAULT_VALUE, 'name' => 'default_value', 'type' => 'input', 'params' => array('class' => 'form-control input-xlarge'));

        $cfg[] = array('title' => TEXT_HIDE_FIELD_IF_EMPTY, 'name' => 'hide_field_if_empty', 'type' => 'checkbox', 'tooltip_icon' => TEXT_HIDE_FIELD_IF_EMPTY_TIP);

        $cfg[] = array('title' => TEXT_IS_UNIQUE_FIELD_VALUE, 'name' => 'is_unique', 'type' => 'dropdown', 'choices' => fields_types::get_is_unique_choices(_POST('entities_id')), 'tooltip_icon' => TEXT_IS_UNIQUE_FIELD_VALUE_TIP, 'params' => array('class' => 'form-control input-large'));
        $cfg[] = array('title' => TEXT_ERROR_MESSAGE, 'name' => 'unique_error_msg', 'type' => 'input', 'tooltip_icon' => TEXT_UNIQUE_FIELD_VALUE_ERROR_MSG_TIP, 'tooltip' => TEXT_DEFAULT . ': ' . TEXT_UNIQUE_FIELD_VALUE_ERROR, 'params' => array('class' => 'form-control input-xlarge'));

        return $cfg;
    }

    function render($field, $obj, $params = array())
    {
        $cfg = new fields_types_cfg($field['configuration']);

        $attributes = array('class' => 'form-control ' . $cfg->get('width') .
            ' fieldtype_input field_' . $field['id'] .
            ($field['is_heading'] == 1 ? ' autofocus' : '') .
            ($field['is_required'] == 1 ? ' required noSpace' : '') .
            ($cfg->get('is_unique') > 0 ? ' is-unique' : '')
        );

        $attributes = fields_types::prepare_uniquer_error_msg_param($attributes, $cfg);
        
        $value = $obj['field_' . $field['id']];
        
        if(isset($params['is_new_item']) and $params['is_new_item']==true and strlen($cfg->get('default_value')))
        {            
            $value = $cfg->get('default_value');
        }

        return input_tag('fields[' . $field['id'] . ']', $value, $attributes);
    }

    function process($options)
    {
        return db_prepare_input($options['value']);
    }

    function output($options)
    {
        return $options['value'];
    }

}
