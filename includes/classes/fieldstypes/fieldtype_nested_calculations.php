<?php

class fieldtype_nested_calculations
{
    public $options;

    function __construct()
    {
        $this -> options = array('title' => TEXT_FIELDTYPE_NESTED_CALCULATIONS_TITLE);
    }

    function get_configuration()
    {
        $cfg = array();
        
        $choices = [
            'COUNT' => TEXT_FUNCTION_COUNT,
            'SUM' => TEXT_FUNCTION_SUM,            
        ];
        
        $cfg[] = array('title' => TEXT_FUNCTION, 'name' => 'calc_function', 'type' => 'dropdown','choices'=>$choices, 'params' => array('class' => 'form-control input-xlarge'));
        
        
        $choices = [
            'top_level'=>TEXT_ONLY_AT_THE_TOP_LEVEL,
            'all_tree'=>TEXT_ALL_OVER_TREE_BRANCH,
        ];
        $cfg[] = array('title' => TEXT_PERFORM_CALCULATION, 'name' => 'calc_type', 'type' => 'dropdown','choices'=>$choices, 'params' => array('class' => 'form-control input-medium'));
        
        $choices = [];
        $fields_query = fields::get_query(_POST('entities_id')," and f.type in ('fieldtype_input_numeric', 'fieldtype_input_numeric_comments', 'fieldtype_js_formula', 'fieldtype_mysql_query', 'fieldtype_php_code')");
        while($fields = db_fetch_array($fields_query))
        {
            $choices[$fields['id']] = $fields['name'];
        }
        
        $cfg[] = array('title' => TEXT_FIELD, 'name' => 'calc_field_id', 'type' => 'dropdown','choices'=>$choices, 
            'params' => array('class' => 'form-control input-xlarge'),
            'form_group'=>['form_display_rules'=>'fields_configuration_calc_function:SUM,MIN,MAX']);

        $cfg[] = array('title' => tooltip_icon(TEXT_NUMBER_FORMAT_INFO) . TEXT_NUMBER_FORMAT, 'name' => 'number_format', 'type' => 'input', 'params' => array('class' => 'form-control input-small input-masked', 'data-mask' => '9/~/~'), 'default' => CFG_APP_NUMBER_FORMAT);
        $cfg[] = array('title' => TEXT_PREFIX, 'name' => 'prefix', 'type' => 'input', 'params' => array('class' => 'form-control input-small'));
        $cfg[] = array('title' => TEXT_SUFFIX, 'name' => 'suffix', 'type' => 'input', 'params' => array('class' => 'form-control input-small'));


        return $cfg;
    }
    
    function process($options)
    {
        return $options['value'];
    }

    function output($options)
    {
        $cfg = new fields_types_cfg($options['field']['configuration']);
        
        $value = $options['value'];
        
  	//return non-formated value if export
        if(isset($options['is_export']) and !isset($options['is_print']) and is_numeric($options['value']))
        {
            if(strlen($cfg -> get('number_format')) > 0 and strlen($value) > 0)
            {
                $format = explode('/', str_replace('*', '', $cfg -> get('number_format')));
                $value = number_format($value, $format[0], '.', '');
            }
            
            return $value;
        }
                                    
        if(strlen($cfg->get('number_format'))>0 and strlen($options['value'])>0 and is_numeric($options['value']))
        {
          $format = explode('/',str_replace('*','',$cfg->get('number_format')));

          $value = number_format($options['value'],$format[0],$format[1],$format[2]);
        }
        else
        {
          $value = $options['value'];
        }        

        //add prefix and sufix
        $value = (strlen($value) ? $cfg->get('prefix') . $value . $cfg->get('suffix') : '');

        return $value;
    }
    
    static function update_items_fields($entities_id,$item_id, $parent_id)
    {
        global $app_fields_cache;
        
        foreach($app_fields_cache[$entities_id] as $field)
        {
            if($field['type']=='fieldtype_nested_calculations')
            {                                
                if($parent_id!=0)
                {
                   $item_id = tree_table::get_top_parent_item_id($entities_id,$parent_id); 
                }
                                                
                $cfg = new settings($field['configuration']);
                
                $calc_function = $cfg->get('calc_function');
                $calc_field_id = $cfg->get('calc_field_id'); 
                $update_field_id = $field['id'];
                
                //stop calculation if field not exist
                if($calc_function=='SUM' and !isset($app_fields_cache[$entities_id][$calc_field_id])) return false;
                                        
                switch($calc_function)
                {
                    case 'SUM':    
                        if($cfg->get('calc_type')=='top_level')
                        {
                            $sum = self::calc_sum($entities_id, $item_id, $calc_field_id);
                            
                            db_query("update app_entity_{$entities_id} set field_{$update_field_id}={$sum} where id={$item_id}");
                        }
                        else
                        {
                            foreach(tree_table::get_items_tree($entities_id, $item_id, [$item_id]) as $update_item_id)
                            {                                                     
                                $sum = self::calc_sum($entities_id, $update_item_id, $calc_field_id);
                            
                                db_query("update app_entity_{$entities_id} set field_{$update_field_id}={$sum} where id={$update_item_id}");
                            }
                        }
                        
                        break;
                    case 'COUNT':    
                        if($cfg->get('calc_type')=='top_level')
                        {
                            $sum = self::calc_count($entities_id, $item_id);
                            
                            db_query("update app_entity_{$entities_id} set field_{$update_field_id}={$sum} where id={$item_id}");
                        }
                        else
                        {
                            foreach(tree_table::get_items_tree($entities_id, $item_id, [$item_id]) as $update_item_id)
                            {                                                     
                                $sum = self::calc_count($entities_id, $update_item_id);
                            
                                db_query("update app_entity_{$entities_id} set field_{$update_field_id}={$sum} where id={$update_item_id}");
                            }
                        }
                        
                        break;
                }
            }
        }
    }
    
    static function calc_sum($entities_id, $item_id, $calc_field_id, $sum=0)
    {
        global $app_fields_cache;
                        
        $items_query = db_query("select id, field_{$calc_field_id} from app_entity_{$entities_id} where parent_id={$item_id}");
        while($items = db_fetch_array($items_query))
        {
            if(isset($items['field_' . $calc_field_id]) and strlen($items['field_' . $calc_field_id]))
            {
                $sum+=(float)$items['field_' . $calc_field_id];
            }
            
            $sum = self::calc_sum($entities_id,$items['id'],$calc_field_id,$sum);                        
        }
        
        return $sum;        
    }
    
    static function calc_count($entities_id, $item_id, $count=0)
    {                                
        $items_query = db_query("select id from app_entity_{$entities_id} where parent_id={$item_id}");
        while($items = db_fetch_array($items_query))
        {
            $count = $count+1;
            
            $count = self::calc_count($entities_id,$items['id'],$count);                        
        }
        
        return $count;        
    }

}
