<?php

class fieldtype_php_code
{
    public $options;
    
    function __construct()
    {
        $this->options = array('title' => TEXT_FIELDTYPE_PHP_CODE_TITLE);
    }
    
    function get_configuration()
    {
        $cfg = array();
        
        $cfg[TEXT_PHP_CODE][] = array(
            'title'=>'<div style="text-align:left; margin-bottom: 5px;">' . fields::get_available_fields_helper($_POST['entities_id'], 'fields_configuration_php_code') . '</div>', 
            'name'=>'php_code',
            'type'=>'code',
            'params'=>array('class'=>'form-control','mode'=>'php'));
        
        $cfg[TEXT_SETTINGS][] = array('title'=>TEXT_DEBUG_MODE, 'name'=>'debug_mode','type'=>'checkbox');
        
        $cfg[TEXT_SETTINGS][] = array('title'=>tooltip_icon(TEXT_FIELDTYPE_PHP_CODE_RUN_DYNAMIC_INFO) . TEXT_RUN_DYNAMIC, 'name'=>'dinamic_query','type'=>'checkbox');
        
        return $cfg;
    }
    
    function render($field,$obj,$params = array())
    {                
        return '';
    }
    
    function process($options)
    {               
        return '';
    }
    
    function output($options)
    {
        $cfg = new fields_types_cfg($options['field']['configuration']);
                       
        if($cfg->get('dinamic_query')==1)
        {
            return self::run_code($options['field']['entities_id'], $options['item']['id'], $options['field']['id'], $options['item']);
        }
        else 
        {        
            return $options['value'];
        }
    }
    
    static function run($entities_id, $items_id, $item_info = false)
    {
        global $app_fields_cache;
        
        foreach($app_fields_cache[$entities_id] as $field_id=>$field)
        {
            $cfg = new fields_types_cfg($field['configuration']);
            
            if($field['type']=='fieldtype_php_code' and $cfg->get('dinamic_query')!=1)
            {                      
                $output_value = self::run_code($entities_id, $items_id, $field_id, $item_info);
                
                db_query("update app_entity_{$entities_id} set field_{$field_id}='" . db_input($output_value) . "' where id='" . db_input($items_id) . "'");
            }
            
        }
    }
    
    static function get_fields_in_code($php_code)
    {
        
        if(preg_match_all('/\[(\d+)\]/', $php_code, $output_array))
        {
            return implode(',',$output_array[1]);
        }
        else
        {
            return '';
        }                
    }
    
    static function run_code($entities_id, $items_id, $field_id, $item_info = false)
    {
        global $app_entities_cache, $app_fields_cache, $current_field_value, $parent_item_holder, $app_user;
                        
        $cfg = new fields_types_cfg($app_fields_cache[$entities_id][$field_id]['configuration']);
            
        $is_dinamic_query =false;
        
        if(!$item_info)
        {
            $is_dinamic_query = true;                        
        }
        
        //get item info again to handle php code that already updated
        $item_info_query = db_query("select e.* " . fieldtype_formula::prepare_query_select($entities_id, '',false,['fields_in_query'=>self::get_fields_in_code($cfg->get('php_code'))]) . " from app_entity_" . $entities_id . " e where e.id='" . db_input($items_id) . "'",false);
        $item_info = db_fetch_array($item_info_query);
                        
        $current_field_value = $item_info['field_' . $field_id];
        
        $fields_values = $item_info;
        
        if($app_entities_cache[$entities_id]['parent_id']>0)
        {
            if(!isset($parent_item_holder[$item_info['parent_item_id']]))
            {
                $parent_item_query = db_query("select * from app_entity_{$app_entities_cache[$entities_id]['parent_id']} where id={$item_info['parent_item_id']}");
                $parent_item_holder[$item_info['parent_item_id']] = $parent_item = db_fetch_array($parent_item_query);                
            }
            else
            {
                $parent_item = $parent_item_holder[$item_info['parent_item_id']];
            }
                
            if($parent_item)
            {
                foreach($parent_item as $fields_id=>$fields_value)
                {
                    if(strstr($fields_id,'field_'))
                    {
                        $fields_values[$fields_id] = $fields_value;
                    }
                }
                
            }                        
        }
                
        
        $php_code = $cfg->get('php_code');
        
        $php_code = str_replace('[current_user_id]',$app_user['id'],$php_code);
        
        //prepare values to replace
        foreach($fields_values as $fiels_id=>$fields_value)
        {
            $fiels_id = str_replace('field_','',$fiels_id);
            
            if(is_null($fields_value) or !strlen($fields_value))
            {
                $fields_value = 0;
            }
            elseif(is_string($fields_value))
            {
                $fields_value = "'" . addslashes($fields_value) . "'";
            }
            
            $php_code = str_replace('[' . $fiels_id . ']',$fields_value,$php_code);                        
        }
        
        if($cfg->get('debug_mode')==1 and !$is_dinamic_query)
        {
            print_rr($fields_values);
            print_rr(htmlspecialchars($php_code));
        }
        
        if(!strlen($php_code)) return '';
        
                
        try
        {                        
            eval($php_code);
        }
        catch (Error $e)
        {
            echo alert_error(TEXT_ERROR . ' '.  TEXT_FIELD . ' #' . $field_id . ' - '. $e->getMessage() . ' on line ' . $e->getLine());
        }
                           
        return (isset($output_value) ? $output_value : '');
    }
    
    function reports_query($options)
    {
        $filters = $options['filters'];
        $sql_query = $options['sql_query'];
        
        $sql = reports::prepare_numeric_sql_filters($filters, $options['prefix']);
        
        if(count($sql)>0)
        {
            $sql_query[] =  implode(' and ', $sql);
        }
        
        return $sql_query;
    }
}