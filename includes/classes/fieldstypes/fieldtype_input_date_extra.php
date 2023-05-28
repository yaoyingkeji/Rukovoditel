<?php

class fieldtype_input_date_extra
{

    public $options;

    function __construct()
    {
        $this->options = array('title' => TEXT_FIELDTYPE_INPUT_DATE_EXTRA_TITLE);
    }

    function get_configuration()
    {
        global $app_entities_cache;
        
        $cfg = array();

        $cfg[TEXT_SETTINGS][] = array('title' => TEXT_NOTIFY_WHEN_CHANGED, 'name' => 'notify_when_changed', 'type' => 'checkbox', 'tooltip_icon' => TEXT_NOTIFY_WHEN_CHANGED_TIP);

        $cfg[TEXT_SETTINGS][] = array('title' => TEXT_HIDE_FIELD_IF_EMPTY, 'name' => 'hide_field_if_empty', 'type' => 'checkbox', 'tooltip_icon' => TEXT_HIDE_FIELD_IF_EMPTY_TIP);

        //$cfg[TEXT_SETTINGS][] = array('title' => TEXT_DATE_FORMAT, 'name' => 'date_format', 'type' => 'input', 'tooltip' => TEXT_DEFAULT . ': ' . CFG_APP_DATE_FORMAT . ', ' . TEXT_DATE_FORMAT_IFNO, 'params' => array('class' => 'form-control input-small'));
        
        $choices = [
            'Y-m-d' => 'yyyy-mm-dd',
            'Y/m/d' => 'yyyy/mm/dd',            
            'Y.m.d' => 'yyyy.mm.dd',
            'd-m-Y' => 'dd-mm-yyyy',
            'd/m/Y' => 'dd/mm/yyyy',
            'd.m.Y' => 'dd.mm.yyyy',
            'm-d-Y' => 'mm-dd-yyyy',            
            'm/d/Y' => 'mm/dd/yyyy',
            'm.d.Y' => 'mm.dd.yyyy',            
            
            
        ];
        
        $cfg[TEXT_SETTINGS][] = array('title' => TEXT_DATE_FORMAT_IN_CALENDAR, 'name' => 'date_format_in_calendar', 'type' => 'dropdown', 'choices' => $choices, 'params' => array('class' => 'form-control input-medium'));

        
        //extra
        $cfg[TEXT_EXTRA][] = array('title' => TEXT_SHOW_WEEK_NUMBERS, 'name' => 'show_week_numbers', 'type' => 'checkbox');

        $cfg[TEXT_EXTRA][] = array('title' => TEXT_DEFAULT_DATE,
            'name' => 'default_value',
            'type' => 'input',
            'tooltip_icon' => TEXT_DEFAULT_DATE_INFO,
            'params' => array('class' => 'form-control input-small', 'type' => 'number'));
        

        $cfg[TEXT_EXTRA][] = array(
            'title' => TEXT_MIN_DATE,
            'name' => 'min_date',
            'type' => 'input',
            'tooltip_icon' => TEXT_DEFAULT_DATE_INFO,
            'params' => array('class' => 'form-control input-small', 'type' => 'number'));

        $cfg[TEXT_EXTRA][] = array(
            'title' => TEXT_MAX_DATE,
            'name' => 'max_date',
            'type' => 'input',
            'tooltip_icon' => TEXT_DEFAULT_DATE_INFO,
            'params' => array('class' => 'form-control input-small', 'type' => 'number'));

        $cfg[TEXT_EXTRA][] = array('title' => TEXT_IS_UNIQUE_FIELD_VALUE, 'name' => 'is_unique', 'type' => 'dropdown', 'choices' => fields_types::get_is_unique_choices(_POST('entities_id')), 'tooltip_icon' => TEXT_IS_UNIQUE_FIELD_VALUE_TIP, 'params' => array('class' => 'form-control input-large'));
        $cfg[TEXT_EXTRA][] = array('title' => TEXT_ERROR_MESSAGE, 'name' => 'unique_error_msg', 'type' => 'input', 'tooltip_icon' => TEXT_UNIQUE_FIELD_VALUE_ERROR_MSG_TIP, 'tooltip' => TEXT_DEFAULT . ': ' . TEXT_UNIQUE_FIELD_VALUE_ERROR, 'params' => array('class' => 'form-control input-xlarge'));
        
        //time
        $cfg[TEXT_TIME][] = array(
            'title' => TEXT_TIME, 
            'name' => 'timepicker', 
            'type' => 'dropdown',
            'choices'=>['0'=>TEXT_TOGGLE_OFF,'1'=>TEXT_TOGGLE_ON],
            'default'=>0,
            'params' => ['class'=>'form-control input-small']);
        
        
        $cfg[TEXT_TIME][] = array(
            'title' => TEXT_FORMAT, 
            'name' => 'format_time', 
            'type' => 'dropdown',
            'choices'=>['H:i'=>'HH:ii','H:i:s'=>'HH:ii:ss'],            
            'params' => ['class'=>'form-control input-small'],
            'form_group' => ['form_display_rules' => 'fields_configuration_timepicker:1']);
        
        $choices = [];
        
        for($i=5;$i<=60;$i+=5)
        {
            $choices[$i] = $i;
        }
        
        $cfg[TEXT_TIME][] = array(
            'title' => TEXT_STEP, 
            'name' => 'step', 
            'type' => 'dropdown',
            'choices'=>$choices,
            'default'=>60,
            'params' => ['class'=>'form-control input-small'],
            'form_group' => ['form_display_rules' => 'fields_configuration_timepicker:1']);
        
        $cfg[TEXT_TIME][] = array(
            'title' => TEXT_MINIMUM_TIME,
            'name' => 'minTime',
            'type' => 'input',
            'tooltip_icon' => TEXT_EXAMPLE . ': 10:00',
            'params' => array('class' => 'form-control input-small inputmask','mask'=>'99:99'),
            'form_group' => ['form_display_rules' => 'fields_configuration_timepicker:1']);
        
        $cfg[TEXT_TIME][] = array(
            'title' => TEXT_MAXIMUM_TIME,
            'name' => 'maxTime',
            'type' => 'input',
            'tooltip_icon' => TEXT_EXAMPLE . ': 20:00',
            'params' => array('class' => 'form-control input-small inputmask','mask'=>'99:99'),
            'form_group' => ['form_display_rules' => 'fields_configuration_timepicker:1']);
        
        $cfg[TEXT_TIME][] = array(
            'title' => TEXT_ALLOWED_TIME,
            'name' => 'allowTimes',
            'type' => 'input',
            'tooltip' => TEXT_EXAMPLE . ': <code>10:00,11:00,12:00</code>',
            'params' => array('class' => 'form-control input-xlarge '),
            'form_group' => ['form_display_rules' => 'fields_configuration_timepicker:1']);
        
        if(isset($_POST['id']) and $_POST['id']>0)
        {
            $field_id = $_POST['id'];
            $entities_id = $_POST['entities_id'];
            $tooltip = TEXT_EXAMPLE . " 1: <code>select field_{$field_id} from app_entity_{$entities_id} where date_format(FROM_UNIXTIME(field_{$field_id}),'%Y-%m-%d')=[TODAY]</code>";
            $tooltip .= '<br>' . TEXT_EXAMPLE . " 2: <code>select field_{$field_id} as time_from, (field_{$field_id}+3600) as time_to from app_entity_{$entities_id} where date_format(FROM_UNIXTIME(field_{$field_id}),'%Y-%m-%d')=[TODAY]</code>";
            
            $cfg[TEXT_TIME][] = array(
                'title' => TEXT_DISABLE_TIME_BY_QUERY,
                'name' => 'disable_time_by_query',
                'type' => 'textarea',
                'tooltip' => $tooltip,
                'params' => array('class' => 'form-control code '),
                'form_group' => ['form_display_rules' => 'fields_configuration_timepicker:1']);
        }
        

        $cfg[TEXT_COLOR][] = array('title' => TEXT_OVERDUE_DATES,
            'name' => 'background',
            'type' => 'colorpicker',
            'tooltip_icon' => TEXT_DATE_BACKGROUND_TOOLTIP);

        $cfg[TEXT_COLOR][] = array('title' => TEXT_DAYS_BEFORE_DATE,
            'name' => 'day_before_date',
            'type' => 'input-with-colorpicker',
            'tooltip_icon' => TEXT_DAYS_BEFORE_DATE_TIP);

        $cfg[TEXT_COLOR][] = array('title' => TEXT_DAYS_BEFORE_DATE . ' 2',
            'name' => 'day_before_date2',
            'type' => 'input-with-colorpicker',
            'tooltip_icon' => TEXT_DAYS_BEFORE_DATE_TIP);


        $choices = ['' => ''];

        $fields_query = db_query("select * from app_fields where type in ('fieldtype_stages','fieldtype_dropdown','fieldtype_radioboxes','fieldtype_dropdown_multiple','fieldtype_tags','fieldtype_checkboxes','fieldtype_autostatus') and entities_id='" . db_input($_POST['entities_id']) . "'");
        while($fields = db_fetch_array($fields_query))
        {
            $choices[$fields['id']] = $fields['name'];
        }


        $cfg[TEXT_COLOR][] = array('title' => TEXT_DISABLE_COLOR,
            'name' => 'disable_color_by_field',
            'type' => 'dropdown',
            'choices' => $choices,
            'tooltip_icon' => TEXT_DISABLE_COLOR_BY_FIELD_TIP,
            'params' => array('class' => 'form-control input-large', 'onChange' => 'fields_types_ajax_configuration(\'disable_color_by_field_values\',this.value)'),
        );

        $cfg[TEXT_COLOR][] = array('name' => 'disable_color_by_field_values', 'type' => 'ajax', 'html' => '<script>fields_types_ajax_configuration(\'disable_color_by_field_values\',$("#fields_configuration_disable_color_by_field").val())</script>');
        
        //Disabled dates
                                        
        $cfg[TEXT_DISABLED_DATES][] = array(
            'title' => TEXT_DISABLED_WEEK_DAYS, 
            'name' => 'disabledWeekDays', 
            'type' => 'dropdown',
            'choices'=>app_get_datepicker_days_choices(),
            'default'=>0,
            'params' => ['class'=>'form-control input-xlarge chosen-select','multiple'=>'multiple']);
        
        if(isset($_POST['id']) and $_POST['id']>0)
        {
            //print_rr($_POST);
            
            $field_id = $_POST['id'];
            $entities_id = $_POST['entities_id'];
            $tooltip = TEXT_EXAMPLE . " : <code>select field_{$field_id} from app_entity_{$entities_id} where field_{$field_id}>UNIX_TIMESTAMP()</code>";
                                    
            $cfg[TEXT_DISABLED_DATES][] = array(
                'title' => TEXT_DISABLED_DATES_BY_QUERY, 
                'name' => 'disabledDatesQuery', 
                'type' => 'textarea',  
                'tooltip' => $tooltip, 
                'params' => array('class' => 'form-control code'));
            
            $tooltip = TEXT_EXAMPLE . " : <code>select field_X from app_entity_Y where id=[parent_item_id]</code>";
            
            $cfg[TEXT_DISABLED_DATES][] = array(
                'title' => TEXT_MIN_DATE, 
                'name' => 'min_date_by_query', 
                'type' => 'textarea',  
                'tooltip' => $tooltip, 
                'params' => array('class' => 'form-control code'));
            
            $cfg[TEXT_DISABLED_DATES][] = array(
                'title' => TEXT_MAX_DATE, 
                'name' => 'max_date_by_query', 
                'type' => 'textarea',  
                'tooltip' => $tooltip, 
                'params' => array('class' => 'form-control code'));
        }
        return $cfg;
    }

    function get_ajax_configuration($name, $value)
    {
        $cfg = array();

        switch($name)
        {
            case 'disable_color_by_field_values':
                if(strlen($value))
                {
                    $field_query = db_query("select id, name, configuration from app_fields where id='" . $value . "'");
                    if($field = db_fetch_array($field_query))
                    {
                        $field_cfg = new fields_types_cfg($field['configuration']);

                        if($field_cfg->get('use_global_list') > 0)
                        {
                            $choices = global_lists::get_choices($field_cfg->get('use_global_list'), false);
                        }
                        else
                        {
                            $choices = fields_choices::get_choices($field['id'], false);
                        }

                        $cfg[] = array(
                            'title' => $field['name'],
                            'name' => 'disable_color_by_field_choices',
                            'type' => 'dropdown',
                            'choices' => $choices,
                            'params' => array('class' => 'form-control input-large chosen-select', 'multiple' => 'multiple'),
                        );
                    }
                }
                break;
        }

        return $cfg;
    }
    
    static function get_input_mask($cfg)
    {
        $mask = '';
             
        switch($cfg->get('date_format_in_calendar'))
        {
            case 'Y-m-d': 
                $mask = '9999-19-39';
                break;
            case 'Y/m/d':
                $mask = '9999/19/39';
                break;
            case 'Y.m.d':
                $mask = '9999.19.39';
                break;
            case 'd-m-Y':
                $mask = '39-19-9999';
                break;
            case 'd/m/Y':
                $mask = '39/19/9999';
                break;
            case 'd.m.Y':
                $mask = '39.19.9999';
                break;
            case 'm-d-Y':
                $mask = '19-39-9999';
                break;
            case 'm/d/Y':
                $mask = '19/39/9999';
                break;
            case 'm.d.Y':
                $mask = '19.39.9999';
                break;            
        }
        
        if($cfg->get('timepicker')==1)
        {
            switch($cfg->get('format_time'))
            {
               case 'H:i':
                   $mask .= ' 29:69';
                   break;
               case 'H:i:s':
                   $mask .= ' 29:69:69';
                   break;
            }
        }
        
        return $mask;
    }

    function render($field, $obj, $params = array())
    {
        $cfg = new fields_types_cfg($field['configuration']);
        
        $date_format = self::get_date_format($cfg);

        if(strlen($obj['field_' . $field['id']]) > 0 and $obj['field_' . $field['id']] != 0)
        {
            $value = date($date_format, $obj['field_' . $field['id']]);
        }
        else
        {
            $value = '';
        }

        if(!isset($params['is_new_item']))
        {
            $params['is_new_item'] = false;
        }

        //handle default value            
        if($params['is_new_item'] == true and strlen($cfg->get('default_value')) > 0 and (strlen($obj['field_' . $field['id']]) == 0 or $obj['field_' . $field['id']] == 0))
        {
            $value = date($date_format, strtotime("+" . (int) $cfg->get('default_value') . " day"));
        }

        $attributes = array(
            'class' => 'form-control fieldtype_input_date xdatetimepicker field_' . $field['id'] . ($field['is_required'] == 1 ? ' required' : '') . ($cfg->get('is_unique') > 0 ? ' is-unique' : ''),
            'autocomplete' => 'off',
            );

        $attributes = fields_types::prepare_uniquer_error_msg_param($attributes, $cfg);
        
        
        //handle extra attributes
        $settings = self::get_data_settings($cfg);
        
        //print_rr($params);
               
        //allowed dates by query
        if(strlen($sql = $cfg->get('disabledDatesQuery')))
        {
            $sql = str_replace('[parent_item_id]',$params['parent_entity_item_id']??0,$sql); 
            $disabledDates = [];            
            $disabled_dates_query = db_query($sql);
            while($disabled_dates = db_fetch_array($disabled_dates_query))
            {
                $disabled_date = $disabled_dates['field_' . $field['id']]??0;
                if($disabled_date>0)
                {
                    $disabledDates[] = date('Y-m-d',$disabled_date);
                }
            } 
            
            if(count($disabledDates))
            {
               $settings['disabledDates'] = implode(',',$disabledDates);  
            }            
        }
        
        
        //disable time        
        if(strlen($sql = $cfg->get('disable_time_by_query')) and $cfg->get('timepicker')==1)
        {
            $settings['disabledTime'] = url_for('dashboard/xdsoft_datetimepicker','action=disabledTime&entity_id=' . $field['entities_id'] . '&field_id=' . $field['id'] . '&item_id=' . ($obj['id']??'') . '&parent_item_id=' . $params['parent_entity_item_id']??0);                                             
        }
        
        //min date by query
        if(strlen($sql = $cfg->get('min_date_by_query')))
        {            
            $sql = str_replace('[parent_item_id]',$params['parent_entity_item_id']??0,$sql); 
            $date_query = db_query($sql);
            if($date = db_fetch_array($date_query))
            {                
                 $minDate = current($date);
                 if($minDate>0)
                 {
                    $settings['minDate'] = date('Y-m-d',$minDate);
                 }
            }
        }
        
        //max date by query
        if(strlen($sql = $cfg->get('max_date_by_query')))
        {            
            $sql = str_replace('[parent_item_id]',$params['parent_entity_item_id']??0,$sql); 
            $date_query = db_query($sql);
            if($date = db_fetch_array($date_query))
            {                
                 $maxDate = current($date);
                 if($maxDate>0)
                 {
                    $settings['maxDate'] = date('Y-m-d',$maxDate);
                 }
            }
        }
        
        //print_rr($settings);
                    
        if(strlen($cfg->get('min_date')) 
                or strlen($cfg->get('max_date'))
                or strlen($cfg->get('disabledDatesQuery'))
                or isset($settings['disabledWeekDays'])
                or ($cfg->get('timepicker')==1  and (strlen($cfg->get('allowTimes')) or strlen($cfg->get('minTime')) or strlen($cfg->get('maxTime'))) )
        )
        {            
            $attributes['readonly'] = 'readonly';
            
            $settings['mask'] = '';
        }
        
         //set settings
        if(count($settings))
        {          
            $attributes['data-settings'] = json_encode($settings);
        }  

        return '
            <div class="input-group input-medium">' . input_tag('fields[' . $field['id'] . ']', $value, $attributes) . 
                '<span class="input-group-btn"><button class="btn btn-default date-set" type="button"><i class="fa fa-calendar"></i></button></span>
            </div>' . fields_types::custom_error_handler($field['id']);
    }
    
    static function get_date_format($cfg)
    {
        return $cfg->get('date_format_in_calendar') . ($cfg->get('timepicker')==1 ? ' ' . $cfg->get('format_time'): '');
    }
    
    static function get_data_settings($cfg)
    {
        $settings = []; 
        
        $date_format = self::get_date_format($cfg);
        
        $settings['format'] = self::get_date_format($cfg);
        $settings['formatDate'] = 'Y-m-d';
        $settings['mask'] = self::get_input_mask($cfg);
        $settings['yearEnd'] = date('Y')+60;
        $settings['dayOfWeekStart'] = CFG_APP_FIRST_DAY_OF_WEEK;
        
        if(is_array($cfg->get('disabledWeekDays')) and count($cfg->get('disabledWeekDays')))
        {
            $settings['disabledWeekDays'] = implode(',',$cfg->get('disabledWeekDays'));
        }
        
        if(strlen($cfg->get('min_date')))
        {
            if(substr($cfg->get('min_date'),0,1)=='-')
            {
                $settings['minDate'] = date('Y-m-d', strtotime("-" . (int) $cfg->get('min_date') . " day"));
            }
            else
            {
                $settings['minDate'] = date('Y-m-d', strtotime("+" . (int) $cfg->get('min_date') . " day"));
            }
        }
        
        if(strlen($cfg->get('max_date')))
        {
            $settings['maxDate'] = date('Y-m-d', strtotime("+" . (int) $cfg->get('max_date') . " day"));
        }
        
        if($cfg->get('show_week_numbers')==1)
        {
            $settings['weeks'] = 1;
        }
        
                
        $settings['timepicker'] = (int)$cfg->get('timepicker');
        $settings['step'] = (int)$cfg->get('step');
                
        if(strlen($cfg->get('allowTimes')))
        {
            $settings['allowTimes'] = $cfg->get('allowTimes');                        
        }
        else
        {        
            if(strlen($cfg->get('minTime')))
            {
                $settings['minTime'] = $cfg->get('minTime');
            }

            if(strlen($cfg->get('maxTime')))
            {
                $settings['maxTime'] = $cfg->get('maxTime');
            }
        }
        
        return $settings;
    }
    
    static function get_input_data_settings($cfg)
    {
        return json_encode(self::get_data_settings($cfg));
    }

    function get_date_timestamp($value,$cfg)
    {        
        //echo $value;
        
        if(!strlen(trim($value)))
        {
            return 0;
        }
                        
        $val = preg_split('/\.| |:|-|\//',$value);
        
        //print_rr($val);
        
        switch($cfg->get('date_format_in_calendar'))
        {
            case 'Y-m-d':                 
            case 'Y/m/d':                
            case 'Y.m.d':
                $date = $val[0] . '-' . $val[1] . '-' . $val[2];
                break;
            case 'd-m-Y':                
            case 'd/m/Y':                
            case 'd.m.Y':
                $date = $val[2] . '-' . $val[1] . '-' . $val[0];
                break;
            case 'm-d-Y':               
            case 'm/d/Y':               
            case 'm.d.Y':
                $date = $val[2] . '-' . $val[0] . '-' . $val[1];
                break;            
        }
        
        if($cfg->get('timepicker')==1)
        {
            switch($cfg->get('format_time'))
            {
               case 'H:i':
                   $date .= ' ' . $val[3] . ':' . $val[4];
                   break;
               case 'H:i:s':
                   $date .= ' ' . $val[3] . ':' . $val[4] . ':' . $val[5];
                   break;
            }
        }
        
        //echo $date;
        
        return get_date_timestamp($date);
    }
    
    function process($options)
    {
        global $app_changed_fields;

        $cfg = new fields_types_cfg($options['field']['configuration']);
        
        $value = !is_numeric($options['value']) ? (int) $this->get_date_timestamp($options['value'],$cfg) : (int) $options['value'];

        if(!$options['is_new_item'])
        {            
            if($value != $options['current_field_value'] and $cfg->get('notify_when_changed') == 1)
            {
                $app_changed_fields[] = array(
                    'name' => $options['field']['name'],
                    'value' => format_date($value),
                    'fields_id' => $options['field']['id'],
                    'fields_value' => $value,
                );
            }
        }

        return $value;
    }

    function output($options)
    {
        $cfg = new fields_types_cfg($options['field']['configuration']);

        if(isset($options['is_export']) and strlen($options['value']) > 0 and $options['value'] != 0)
        {
            return format_date($options['value'], self::get_date_format($cfg));
        }
        elseif(strlen($options['value']) > 0 and $options['value'] != 0)
        {
            $html = format_date($options['value'], self::get_date_format($cfg));

            //return simple value if color is disabled
            if(strlen($cfg->get('disable_color_by_field')))
            {
                if(isset($options['item']['field_' . $cfg->get('disable_color_by_field')]))
                {
                    if(is_array($cfg->get('disable_color_by_field_choices')))
                        foreach($cfg->get('disable_color_by_field_choices') as $choices_id)
                        {
                            if(in_array($choices_id, explode(',', $options['item']['field_' . $cfg->get('disable_color_by_field')])))
                            {
                                return $html;
                            }
                        }
                }
            }

            //highlight field if overdue date    
            if((date('Y-m-d', $options['value']) == date('Y-m-d') or $options['value'] < time()) and strlen($cfg->get('background')) > 0)
            {
                $html = render_bg_color_block($cfg->get('background'), format_date($options['value'], self::get_date_format($cfg)));
            }

            //highlight field before due date
            if(strlen($cfg->get('day_before_date')) > 0 and strlen($cfg->get('day_before_date_color')) > 0 and $options['value'] > time())
            {
                if($options['value'] < strtotime('+' . $cfg->get('day_before_date') . ' day'))
                {
                    $html = render_bg_color_block($cfg->get('day_before_date_color'), format_date($options['value'], self::get_date_format($cfg)));
                }
            }

            //highlight 2 field before due date      
            if(strlen($cfg->get('day_before_date2')) > 0 and strlen($cfg->get('day_before_date2_color')) > 0 and $options['value'] > time())
            {
                if($options['value'] < strtotime('+' . $cfg->get('day_before_date2') . ' day'))
                {
                    $html = render_bg_color_block($cfg->get('day_before_date2_color'), format_date($options['value'], self::get_date_format($cfg)));
                }
            }

            //return single value      
            return $html;
        }
        else
        {
            return '';
        }
    }

    function reports_query($options)
    {
        $filters = $options['filters'];
        $sql_query = $options['sql_query'];

        $sql = reports::prepare_dates_sql_filters($filters, $options['prefix']);

        if(count($sql) > 0)
        {
            $sql_query[] = implode(' and ', $sql);
        }

        return $sql_query;
    }

}
