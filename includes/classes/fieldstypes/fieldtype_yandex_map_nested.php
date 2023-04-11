<?php

class fieldtype_yandex_map_nested
{

    public $options;

    function __construct()
    {
        $this->options = array('title' => TEXT_FIELDTYPE_YANDEX_MAP_NESTED_TITLE);
    }

    function get_configuration()
    {
        $cfg = array();

        $cfg[TEXT_SETTINGS][] = array('title' => TEXT_API_KEY, 'name' => 'api_key', 'type' => 'input', 'tooltip' => TEXT_FIELDTYPE_YANDEX_MAP_API_KEY_TIP, 'params' => array('class' => 'form-control input-xlarge required'));
        
        
        $choices = [''=>''];
        $entities_query = db_query("select id,name from app_entities where parent_id='" . _POST('entities_id'). "'");
        while($entities = db_fetch_array($entities_query))
        {
            $choices[$entities['id']] = $entities['name'];
        }
        
        $cfg[TEXT_SETTINGS][] = array(
            'title' => TEXT_ENTITY, 
            'name' => 'entity_id', 
            'type' => 'dropdown', 
            'choices' => $choices, 
            'params' => [
                'class' => 'form-control input-xlarge required',
                'onChange' => 'fields_types_ajax_configuration(\'nested_entity_fields\',this.value)']
            );
        
        $cfg[TEXT_SETTINGS][] = array('name' => 'nested_entity_fields', 'type' => 'ajax', 'html' => '<script>fields_types_ajax_configuration(\'nested_entity_fields\',$("#fields_configuration_entity_id").val())</script>');
               
        $cfg[TEXT_SETTINGS][] = array('title' => TEXT_WIDHT, 'name' => 'map_width', 'type' => 'input', 'tooltip_icon' => TEXT_WIDTH_INPUT_TIP, 'params' => array('class' => 'form-control input-small'));
        $cfg[TEXT_SETTINGS][] = array('title' => TEXT_HEIGHT, 'name' => 'map_height', 'type' => 'input', 'tooltip_icon' => TEXT_HEIGHT_INPUT_TIP, 'params' => array('class' => 'form-control input-small'));

        $choices = [];
        for ($i = 1; $i <= 19; $i++)
        {
            $choices[$i] = $i;
        }       
        
        $choices = [
            'ru_RU'=>'ru_RU',
            'en_US'=>'en_US',
            'en_RU'=>'en_RU',
            'ru_UA'=>'ru_UA',
            'uk_UA'=>'uk_UA',
            'tr_TR'=>'tr_TR',                        
        ];
        
        $cfg[TEXT_SETTINGS][] = array('title' => TEXT_LANGUAGE,
            'name' => 'lang',
            'type' => 'dropdown',
            'choices' => $choices,
            'default' => 'ru_RU',
            'params' => array('class' => 'form-control input-small'));
                
         //Distance
        $cfg[TEXT_DISTANCE][] = array('title' => TEXT_CALCULATE_TOTAL_DISTANCE, 'name' => 'calculate_total_distance', 'type' => 'checkbox');
        
        $cfg[TEXT_DISTANCE][] = array('title' => TEXT_SAVE_VALUE,            
            'type' => 'section');
        
                
        $choices = [''=>''];
        $fields_query = db_query("select id, name from app_fields where type in ('fieldtype_input_numeric') and entities_id = '" . _POST('entities_id'). "'");
        while($fields = db_fetch_array($fields_query))
        {
            $choices[$fields['id']] =  $fields['name'];
        }
        
        $cfg[TEXT_DISTANCE][] = array('title' => TEXT_DISTANCE,
            'name' => 'save_distance_in',
            'type' => 'dropdown',
            'choices' => $choices,      
            'tooltip' => TEXT_FIELDTYPE_GOOGLE_MAP_DIRETIONS_SAVE_VALUE_TIP,
            'params' => array('class' => 'form-control input-medium'));
        
        $choices = [''=>''];
        $fields_query = db_query("select id, name from app_fields where type in ('fieldtype_time') and entities_id = '" . _POST('entities_id'). "'");
        while($fields = db_fetch_array($fields_query))
        {
            $choices[$fields['id']] =  $fields['name'];
        }
        
        $cfg[TEXT_DISTANCE][] = array('title' => TEXT_TIME,
            'name' => 'save_time_in',
            'type' => 'dropdown',
            'choices' => $choices,      
            'tooltip' => TEXT_SELECT_FIELD . '. ' . TEXT_TYPE . ': ' .  TEXT_FIELDTYPE_TIME,
            'params' => array('class' => 'form-control input-medium'));
        
        
        //galiosoft        
        $cfg[TEXT_GELIOSSOFT_SITE][] = array('title' => TEXT_GELIOSSOFT_ENABLE,
            'name' => 'is_geliossoft',
            'type' => 'dropdown',
            'choices' => ['0'=>TEXT_NO,'1'=>TEXT_YES],
            'default' => '0',
            'params' => array('class' => 'form-control input-small'));
        
        $cfg[TEXT_GELIOSSOFT_SITE][] = array('title' => TEXT_USERNAME, 'name' => 'username', 'type' => 'input', 'params' => array('class' => 'form-control input-medium'),'form_group' => ['form_display_rules' => 'fields_configuration_is_geliossoft:1']);
        $cfg[TEXT_GELIOSSOFT_SITE][] = array('title' => TEXT_PASSWORD, 'name' => 'password', 'type' => 'input', 'params' => array('class' => 'form-control input-medium'),'form_group' => ['form_display_rules' => 'fields_configuration_is_geliossoft:1']);
        $cfg[TEXT_GELIOSSOFT_SITE][] = array(
            'title' => TEXT_DATA_REFRESH_INTERVAL, 
            'name' => 'refresh_interval', 
            'type' => 'input',
            'default'=>60, 
            'tooltip'=>TEXT_SECONDS, 
            'params' => array('class' => 'form-control input-small required','type'=>'number','min'=>1),
            'form_group' => ['form_display_rules' => 'fields_configuration_is_geliossoft:1']);

        return $cfg;
    }
    
    function get_ajax_configuration($name, $value)
    {
        $cfg = array();                        
        
        switch ($name)
        {
            case 'nested_entity_fields':
                $entities_id = (int)$value;
                
                if(!$entities_id) return $cfg;
                
                $cfg[] = array('title' => TEXT_ADDRESS . fields::get_available_fields_helper($entities_id, 'fields_configuration_address_pattern', TEXT_SELECT_FIELD, ['fieldtype_input', 'fieldtype_input_masked', 'fieldtype_mysql_query', 'fieldtype_textarea', 'fieldtype_textarea_wysiwyg', 'fieldtype_text_pattern', 'fieldtype_text_pattern_static']),
                    'name' => 'address_pattern', 'type' => 'input', 'tooltip' => TEXT_ADDRESS_PATTERN_INOF, 'params' => array('class' => 'form-control input-xlarge required'));
                
                
                $choices = [];
                $choices[]= '';
                $fields_query = fields::get_query($entities_id," and f.type in ('fieldtype_input_numeric','fieldtype_input_date','fieldtype_input_date_extra','fieldtype_input_datetime','fieldtype_dynamic_date')");
                while($v = db_fetch_array($fields_query))
                {
                    $choices[$v['id']] = $v['name'];
                }
            
                $cfg[] = array('title' => TEXT_SORT_ORDER,
                    'name' => 'sort_order',
                    'type' => 'dropdown',
                    'choices' => $choices,
                    'tooltip' => TEXT_DEFAULT . ': ' . TEXT_DATE_ADDED,
                    'params' => array('class' => 'form-control input-xlarge'));
                
                break;            
        }
        
        return $cfg;
    }

    function render($field, $obj, $params = array())
    {
        return false;
    }

    function process($options)
    {
        return db_prepare_input($options['value']);
    }

    function output($options)
    {
        global $is_yandex_map_script;

        $cfg = new fields_types_cfg($options['field']['configuration']);
        
        $referencePoints = $this->get_reference_points($options);
        
        //skip
        if (!strlen($cfg->get('address_pattern')) or !count($referencePoints) or isset($options['is_listing']) or isset($options['is_export']))
            return '';

        $field_id = $options['field']['id'];
        
        
        $lat = '55.76';
        $lng = '37.64';        
        
        $html = '';
        if ($is_yandex_map_script != true)
        {
            $html .= '<script src="https://api-maps.yandex.ru/2.1/?apikey=' . $cfg->get('api_key') . '&lang=' . $cfg->get('lang') . '" type="text/javascript"></script>';
            $html .= '<script src="js/geliossoft/geliossoft_objects.js?v=' . PROJECT_VERSION . '" type="text/javascript"></script>';
            $is_yandex_map_script = true;
        }
                        
        $map_width = (strlen($cfg->get('map_width')) ? $cfg->get('map_width') : '600px');
        $map_height = (strlen($cfg->get('map_height')) ? $cfg->get('map_height') : '400px');
        
        $html .= '<div id="yandex_map_container' . $field_id . '" style="width: ' . $map_width . '; height: ' . $map_height . '"></div>';                
              
        $html .= '
             <script type="text/javascript">
                ymaps.ready(function(){
                    let is_geliossoft = ' . $cfg->get('is_geliossoft',0) . '
                        
                    var myMap = new ymaps.Map("yandex_map_container' . $field_id . '", {
                        center: [' . $lat . ', ' . $lng . '],
                        zoom: 22
                    });
                                                            
                    // Построение маршрута.
                    // По умолчанию строится автомобильный маршрут.
                    var multiRoute = new ymaps.multiRouter.MultiRoute({   
                        // Точки маршрута. Точки могут быть заданы как координатами, так и адресом. 
                        referencePoints: [
                           ' . implode(',',$referencePoints).  ' 
                        ],
                        params: {
                            //Если точки маршрута задаются координатами, то чтобы на карте подписывался их адрес, следует выставить параметр reverseGeocoding в true.
                            reverseGeocoding: true
                        }
                    }, {
                          // Автоматически устанавливать границы карты так,
                          // чтобы маршрут был виден целиком.
                          boundsAutoApply: true,                          
                    });

                    // Добавление маршрута на карту.
                    myMap.geoObjects.add(multiRoute);
                    
                    if(is_geliossoft==1)
                    {
                        new geliossoft_objects(myMap,"' . $field_id . '","' . $cfg->get('refresh_interval',60) . '","yandex")
                    }       
                    
                    ' . fieldtype_yandex_map_directions::calculate_total_distance($options, $map_width) . '
                    
                });
                
                
                    
            </script>
            ';
        
        
        return $html;
       
    }
    
    function geliossoft_js($cfg)
    {
        if($cfg->get('is_geliossoft')!='1') return '';
        
        $html = '
            ';
    }
    
    function get_reference_points($options)
    {
        $cfg = new fields_types_cfg($options['field']['configuration']);
        
        $entity_id = $cfg->get('entity_id');
        $current_item_id = $options['item']['id'];
        
        $current_reference_points = strlen($options['value']) ? json_decode($options['value'], true) : [];
        $update_reference_points = false;   
        //print_rr($current_reference_points);
        
        
        $reference_points = [];    
        
        //set sort order
        $where_sql = " order by ";
        if(strlen($cfg->get('sort_order')) and isset_field($entity_id, $cfg->get('sort_order')))
        {
            $where_sql .= "(field_" . $cfg->get('sort_order') . "+0), ";
        }
        $where_sql .= 'id';
        
        //build query
        $item_query = db_query("select * from app_entity_{$entity_id} where parent_item_id={$current_item_id} {$where_sql}",false);
        while($item = db_fetch_array($item_query))
        {
            $text_pattern = new fieldtype_text_pattern();
            $address = urlencode($text_pattern->output_singe_text($cfg->get('address_pattern'),$entity_id,$item));                        
            
            if(!isset($current_reference_points[$item['id']]) or $current_reference_points[$item['id']]['address']!=$address)
            {
                $coordinates = $this->get_coordinates_by_address($cfg,$address);
                if($coordinates)
                {
                    $update_reference_points = true;
                    
                    $current_reference_points[$item['id']] = [
                        'lat' => $coordinates['lat'],
                        'lng' => $coordinates['lng'],
                        'address' => $address,
                    ]; 
                                        
                    $reference_points[] = "[{$coordinates['lng']},{$coordinates['lat']}]";
                }                        
            }
            else
            {
                $lat = $current_reference_points[$item['id']]['lat'];
                $lng = $current_reference_points[$item['id']]['lng'];
                $reference_points[] = "[{$lng},{$lat}]";   
                
                //echo 'OK';
            }
            //print_rr($item);
        }
        
        if($update_reference_points)                
        {
            //print_rr($current_reference_points);
            
            db_query("update app_entity_{$options['field']['entities_id']} set field_{$options['field']['id']}='" . json_encode($current_reference_points) . "' where id='" . db_input($current_item_id) . "'");
        }
        
        //print_rr($reference_points);
        
        return $reference_points;
    }
    
    function get_coordinates_by_address($cfg, $address)
    {
        $url = "https://geocode-maps.yandex.ru/1.x?geocode=" . $address . "&apikey=" . $cfg->get('api_key') . "&format=json&results=1&lang=" . $cfg->get('lang');

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $result = curl_exec($ch);
        curl_close($ch);


        $result = json_decode($result, true);

        //echo $url;
        //print_rr($result);
        //exit();

        if (isset($result['error']))
        {
            $alerts->add(TEXT_FIELD . ' "' . $fields['name'] . '": ' . $result['error'] . ': ' . $result['message'], 'error');
            return false;
        } 
        else
        {
            $formated_address = $result['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['metaDataProperty']['GeocoderMetaData']['text'];

            $pos = $result['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['Point']['pos'];
            $pos = explode(' ',$pos);
            $lat = $pos[0];
            $lng = $pos[1];

            return [
                'lat' => $lat,
                'lng' => $lng,
            ];
        }
    }                
    
}
