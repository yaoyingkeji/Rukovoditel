<?php

class fieldtype_textarea_wysiwyg
{
  public $options;
  
  function __construct()
  {
    $this->options = array('title' => TEXT_FIELDTYPE_TEXTAREA_WYSIWYG_TITLE);
  }
  
  function get_configuration()
  {
    $cfg = array();
    $cfg[] = array('title'=>TEXT_TOOLBAR, 'name'=>'toolbar','type'=>'dropdown','choices'=>[''=>TEXT_DEFAULT,'small'=>TEXT_IN_ONE_LINE,'full'=>TEXT_EXTENDED],'params'=>['class'=>'form-control input-medium']);
    $cfg[] = array('title'=>TEXT_ALLOW_SEARCH, 'name'=>'allow_search','type'=>'checkbox','tooltip_icon'=>TEXT_ALLOW_SEARCH_TIP);
    $cfg[] = array('title'=>TEXT_HIDE_FIELD_IF_EMPTY, 'name'=>'hide_field_if_empty','type'=>'checkbox','tooltip_icon'=>TEXT_HIDE_FIELD_IF_EMPTY_TIP);
        
    return $cfg;
  }
  
  function render($field,$obj,$params = array())
  {                
    $cfg =  new fields_types_cfg($field['configuration']);
    
    $attributes = array(
        'class' => 'form-control editor field_' . $field['id'] . ($field['is_required']==1 ? ' required':''),
        'toolbar' => $cfg->get('toolbar'),
    );
    
        
    return textarea_tag('fields[' . $field['id'] . ']',$obj['field_' . $field['id']],$attributes);
  }
  
  function process($options)
  {
    return db_prepare_html_input($options['value']);
  }
  
  function output($options)
  {
  	if(isset($options['is_export']))
  	{  		  		
  		return (!isset($options['is_print']) ? str_replace(array('&lt;','&gt;'),array('<','>'),$options['value']) : $options['value']);
  	}
  	else
  	{
    	return auto_link_text($options['value']);
  	}
  }
}