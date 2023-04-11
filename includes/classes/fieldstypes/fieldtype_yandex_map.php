<?php

class fieldtype_yandex_map
{

    public $options;

    function __construct()
    {
        $this->options = array('title' => TEXT_FIELDTYPE_YANDEX_MAP_TITLE);
    }

    function get_configuration()
    {
        $cfg = array();

        $cfg[TEXT_SETTINGS][] = array('title' => TEXT_API_KEY, 'name' => 'api_key', 'type' => 'input', 'tooltip' => TEXT_FIELDTYPE_YANDEX_MAP_API_KEY_TIP, 'params' => array('class' => 'form-control input-xlarge required'));

        $cfg[TEXT_SETTINGS][] = array('title' => TEXT_ADDRESS . fields::get_available_fields_helper($_POST['entities_id'], 'fields_configuration_address_pattern', TEXT_SELECT_FIELD, ['fieldtype_input', 'fieldtype_input_masked', 'fieldtype_mysql_query', 'fieldtype_textarea', 'fieldtype_textarea_wysiwyg', 'fieldtype_text_pattern', 'fieldtype_text_pattern_static']),
            'name' => 'address_pattern', 'type' => 'input', 'tooltip' => TEXT_ADDRESS_PATTERN_INOF, 'params' => array('class' => 'form-control input-xlarge required'));

        $cfg[TEXT_SETTINGS][] = array('title' => TEXT_WIDHT, 'name' => 'map_width', 'type' => 'input', 'tooltip_icon' => TEXT_WIDTH_INPUT_TIP, 'params' => array('class' => 'form-control input-small'));
        $cfg[TEXT_SETTINGS][] = array('title' => TEXT_HEIGHT, 'name' => 'map_height', 'type' => 'input', 'tooltip_icon' => TEXT_HEIGHT_INPUT_TIP, 'params' => array('class' => 'form-control input-small'));

        $choices = [];
        for ($i = 1; $i <= 19; $i++)
        {
            $choices[$i] = $i;
        }

        $cfg[TEXT_SETTINGS][] = array('title' => TEXT_DEFAULT_ZOOM,
            'name' => 'zoom',
            'type' => 'dropdown',
            'choices' => $choices,
            'default' => 16,
            'params' => array('class' => 'form-control input-small'));
        
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
        
        //skip
        if (!strlen($cfg->get('address_pattern'))  or !strlen($options['value']) or isset($options['is_listing']) or isset($options['is_export']))
            return '';

        $field_id = $options['field']['id'];
        
        //save address
        $value = explode("\t", $options['value']);

        $lat = $value[0]??'55.76';
        $lng = $value[1]??'37.64';
        $current_address = $value[2]??'';
        $formated_address = $value[3]??'';       
        
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
        
        $access_rules = new access_rules($options['field']['entities_id'], $options['item']);
        $has_update_access = users::has_access('update', $access_rules->get_access_schema());
        
        $html .= '
             <script type="text/javascript">
                ymaps.ready(function(){
                    let is_geliossoft = ' . $cfg->get('is_geliossoft',0) . '
                        
                    var myMap = new ymaps.Map("yandex_map_container' . $field_id . '", {
                        center: [' . $lat . ', ' . $lng . '],
                        zoom: ' . $cfg->get('zoom') . '
                    });
                    
                    var myPlacemark = new ymaps.Placemark([' . $lat . ', ' . $lng . '],{
                                hintContent :"' . addslashes($formated_address) . '"
                            },
                            {
                                preset:"islands#dotIcon",
                                draggable: ' . ($has_update_access ? 'true' : 'false') . '                                 
                            }
                            );
                            
                    myPlacemark.events.add("dragend", function(e){
			var cord = e.get("target").geometry.getCoordinates();
                        //console.log(cord)
                        
                    $.ajax({
                        type: "POST",
                        url: url_for("items/yandex_map","action=update_latlng&path=' . $options['path'] . '"),
                        data: { cord: cord,filed_id: ' . $field_id . ' }
                      })
			
                    });        
                    myMap.geoObjects.add(myPlacemark);
                    
                    if(is_geliossoft==1)
                    {
                        new geliossoft_objects(myMap,"' . $field_id . '","' . $cfg->get('refresh_interval',60) . '","yandex")
                    }                                        
                    
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
    
    public static function update_items_fields($entities_id, $items_id, $item_info = false)
    {
        global $app_fields_cache, $alerts;

        if (isset($app_fields_cache[$entities_id]))
        {
            foreach ($app_fields_cache[$entities_id] as $fields)
            {
                if ($fields['type'] == 'fieldtype_yandex_map')
                {
                    $fields_id = $fields['id'];

                    $cfg = new fields_types_cfg($fields['configuration']);

                    //skip if no pattern setup
                    if (!strlen($cfg->get('address_pattern')))
                        return false;

                    //get item info
                    if(!$item_info)
                    {
                        $item_info_query = db_query("select * from app_entity_{$entities_id} where id={$items_id}");
                        $item_info = db_fetch_array($item_info_query);
                    }

                    //get address by pattern
                    $pattern_options = array(
                        'field' => $fields,
                        'item' => $item_info,
                        'custom_pattern' => $cfg->get('address_pattern'),
                        'path' => $entities_id . '-' . $items_id,
                    );

                    $fieldtype_text_pattern = new fieldtype_text_pattern;
                    $use_address = urlencode(strip_tags($fieldtype_text_pattern->output($pattern_options)));
                    $use_address = str_replace(["\n\r","\n","\r"]," ",$use_address);

                    //skip if address empty
                    if (!strlen($use_address))
                    {
                        db_query("update app_entity_{$entities_id} set field_{$fields_id}='' where id='" . db_input($items_id) . "'");

                        return false;
                    }

                    $lat = '';
                    $lng = '';
                    $current_address = '';

                    //get current address
                    if (strlen($item_info['field_' . $fields_id]))
                    {
                        $value = explode("\t", $item_info['field_' . $fields_id]);

                        $lat = $value[0];
                        $lng = $value[1];
                        $current_address = $value[2];
                    }

                    //update address if it needs
                    if (!strlen($lat) or $use_address != $current_address)
                    {        
                        //echo 'use=' . $use_address . '<br>cur=' . $current_address;
                        //exit();
                        
                        $url = "https://geocode-maps.yandex.ru/1.x?geocode=" . $use_address . "&apikey=" . $cfg->get('api_key') . "&format=json&results=1&lang=" . $cfg->get('lang');

                        $ch = curl_init($url);
                        curl_setopt($ch, CURLOPT_HEADER, false);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                        $result = curl_exec($ch);
                        curl_close($ch);


                        $result = json_decode($result, true);
                                                
                        //print_rr($result);
                        //exit();

                        if (isset($result['error']))
                        {
                            $alerts->add(TEXT_FIELD . ' "' . $fields['name'] . '": ' . $result['error'] . ': ' . $result['message'], 'error');
                        } 
                        else
                        {
                            $formated_address = $result['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['metaDataProperty']['GeocoderMetaData']['text'];
                            
                            $pos = $result['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['Point']['pos'];
                            $pos = explode(' ',$pos);
                            $lat = $pos[0];
                            $lng = $pos[1];

                            $value = $lng . "\t" . $lat . "\t" . $use_address . "\t" . $formated_address;

                            //echo $value;

                            db_query("update app_entity_{$entities_id} set field_{$fields_id}='" . db_input($value) . "' where id='" . db_input($items_id) . "'");
                        }
                    }
                }
            }
        }
    }
    
}
