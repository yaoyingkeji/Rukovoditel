<?php

class fieldtype_textarea_encrypted
{
    public $options;
    
    function __construct()
    {
        $this->options = array('title' => TEXT_FIELDTYPE_TEXTAREA_ENCRYPTED_TITLE);
    }
    
    function get_configuration()
    {
        $cfg = array();
        
        $encryption_key = (defined('DB_ENCRYPTION_KEY') ? DB_ENCRYPTION_KEY :'');
        
        $html = '
                <div class="form-group">
        	        <label class="col-md-3 control-label" >' . TEXT_ENCRYPTION_KEY . '</label>
            	    <div class="col-md-9">'  . input_tag('encryption_key',$encryption_key,['class'=>'form-control input-large required','readonly'=>'readonly']) . tooltip_text(TEXT_ENCRYPTION_KEY_INFO) . '
        	        </div>
    	        </div>
            ';
        
        $cfg[] = array('type'=>'html','html'=>$html);
        
        $cfg[] = array('title'=>TEXT_WIDHT,
            'name'=>'width',
            'type'=>'dropdown',
            'choices'=>array('input-small'=>TEXT_INPTUT_SMALL,'input-medium'=>TEXT_INPUT_MEDIUM,'input-large'=>TEXT_INPUT_LARGE,'input-xlarge'=>TEXT_INPUT_XLARGE),
            'tooltip'=>TEXT_ENTER_WIDTH,
            'params'=>array('class'=>'form-control input-medium'));
                
        $cfg[] = array('title'=>TEXT_ALLOW_SEARCH, 'name'=>'allow_search','type'=>'checkbox','tooltip_icon'=>TEXT_ALLOW_SEARCH_TIP);
        
        $cfg[] = array('title'=>TEXT_HIDE_FIELD_IF_EMPTY, 'name'=>'hide_field_if_empty','type'=>'checkbox','tooltip_icon'=>TEXT_HIDE_FIELD_IF_EMPTY_TIP);
        
        $cfg[] = array(
            'title' => TEXT_NUMBER_DISPLAYED_CHARACTERS_IN_LIST, 
            'name' => 'number_characters_in_list', 
            'type' => 'input', 
            'tooltip_icon' => TEXT_NUMBER_DISPLAYED_CHARACTERS_IN_LIST_INFO,
            'params' => array('class' => 'form-control input-small','type'=>'number')
            );
        
        return $cfg;
    }
    
    function render($field,$obj,$params = array())
    {
        $cfg = fields_types::parse_configuration($field['configuration']);
        
        $attributes = array('rows'=>'3',
            'class'=>'form-control ' . $cfg['width'] .  ($field['is_heading']==1 ? ' autofocus':'') . ' fieldtype_textarea field_' . $field['id'] . ($field['is_required']==1 ? ' required noSpace':''));
        
        $value = fieldtype_input_encrypted::decrypt_value($obj['field_' . $field['id']]);
        
        return textarea_tag('fields[' . $field['id'] . ']',str_replace(array('&lt;','&gt;'),array('<','>'),$value),$attributes);
    }
    
    function process($options)
    {
        global $alerts;
        
        if(!db_has_encryption_key())
        {            
            $alerts->add(sprintf(TEXT_ENCRYPTION_KEY_ERROR,$options['field']['name']),'error');
            return '';
        }
        
        $value = str_replace(array('<','>'),array('&lt;','&gt;'),$options['value']);
        $value_query = db_query("select AES_ENCRYPT('" . db_input(trim($value)) . "','" . db_input(DB_ENCRYPTION_KEY) . "') as text",false);
        $value = db_fetch_array($value_query); 
        
        return $value['text'];
    }
    
    function output($options)
    {
        $cfg = new fields_types_cfg($options['field']['configuration']);
        
        if(isset($options['is_export']))
        {
            return (!isset($options['is_print']) ? str_replace(array('&lt;','&gt;'),array('<','>'),$options['value']) : nl2br($options['value']));
        }
        else
        {
            if(isset($options['is_listing']) and $options['is_listing']==1 and $cfg->get('number_characters_in_list')>0 and strlen(strip_tags($options['value']))>$cfg->get('number_characters_in_list'))
            {
                $html = '
                        <div class="truncated-text-block">
                            <div class="truncated-text">' . mb_substr(strip_tags($options['value']),0,$cfg->get('number_characters_in_list')). '... <a href="#" class="truncated-text-expand">' . TEXT_READ_MORE. ' <i class="fa fa-angle-right"></i></a></div>
                            <div class="full-text hidden">' . auto_link_text(nl2br($options['value'])) . ' <a href="#" class="truncated-text-collapse"><i class="fa fa-angle-left"></i> ' . TEXT_HIDE. '</a></div>
                        </div>
                    ';
                
                return $html;
            }
            else
            {
                return auto_link_text(nl2br($options['value']));
            }
        }
    }
}