<?php

class fieldtype_google_map_nested
{

    public $options;

    function __construct()
    {
        $this->options = array('title' => TEXT_FIELDTYPE_GOOGLE_MAP_NESTED_TITLE);
    }

    function get_configuration()
    {
        $cfg = array();

        $cfg[TEXT_SETTINGS][] = array('title' => TEXT_API_KEY, 'name' => 'api_key', 'type' => 'input', 'tooltip' => TEXT_FIELDTYPE_GOOGLE_MAP_API_KEY_TIP, 'params' => array('class' => 'form-control input-xlarge required'));

        
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
        for ($i = 3; $i <= 20; $i++)
        {
            $choices[$i] = $i;
        }

        $cfg[TEXT_SETTINGS][] = array('title' => TEXT_DEFAULT_ZOOM,
            'name' => 'zoom',
            'type' => 'dropdown',
            'choices' => $choices,
            'default' => 11,
            'params' => array('class' => 'form-control input-small'));
       

        $cfg[TEXT_DIRECTIONS][] = array('title' => TEXT_MODE,
            'name' => 'travel_mode',
            'type' => 'dropdown',
            'choices' => array('DRIVING' => 'DRIVING', 'BICYCLING' => 'BICYCLING', 'TRANSIT' => 'TRANSIT', 'WALKING' => 'WALKING'),
            'tooltip' => TEXT_FIELDTYPE_GOOGLE_MAP_DIRETIONS_MODE_TIP,
            'params' => array('class' => 'form-control input-medium'));

        $cfg[TEXT_DIRECTIONS][] = array('title' => TEXT_OPTIMIZE_WAYPOINTS, 'name' => 'optimizeWaypoints', 'type' => 'checkbox');
        $cfg[TEXT_DIRECTIONS][] = array('title' => TEXT_PROVIDE_ROUTE_ALTERNATIVES, 'name' => 'provideRouteAlternatives', 'type' => 'checkbox');
        $cfg[TEXT_DIRECTIONS][] = array('title' => TEXT_AVOID_FERRIES, 'name' => 'avoidFerries', 'type' => 'checkbox');
        $cfg[TEXT_DIRECTIONS][] = array('title' => TEXT_AVOID_HIGHWAYS, 'name' => 'avoidHighways', 'type' => 'checkbox');
        $cfg[TEXT_DIRECTIONS][] = array('title' => TEXT_AVOID_TOLLS, 'name' => 'avoidTolls', 'type' => 'checkbox');
        
        $cfg[TEXT_DISTANCE][] = array('title' => TEXT_CALCULATE_TOTAL_DISTANCE, 'name' => 'calculate_total_distance', 'type' => 'checkbox');
        
        $cfg[TEXT_DISTANCE][] = array('title' => TEXT_DISPLAY_AS,
            'name' => 'unit_system',
            'type' => 'dropdown',
            'choices' => array('kilometers' => TEXT_KILOMETERS, 'miles' => TEXT_MILES ),            
            'params' => array('class' => 'form-control input-medium'));
        
        $choices = [''=>''];
        $fields_query = db_query("select id, name from app_fields where type in ('fieldtype_input_numeric') and entities_id = '" . _POST('entities_id'). "'");
        while($fields = db_fetch_array($fields_query))
        {
            $choices[$fields['id']] =  $fields['name'];
        }
        
        $cfg[TEXT_DISTANCE][] = array('title' => TEXT_SAVE_VALUE,
            'name' => 'save_value_in',
            'type' => 'dropdown',
            'choices' => $choices,      
            'tooltip' => TEXT_FIELDTYPE_GOOGLE_MAP_DIRETIONS_SAVE_VALUE_TIP,
            'params' => array('class' => 'form-control input-medium'));
        
        
                  
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
        global $is_google_map_script, $app_user;

        $cfg = new fields_types_cfg($options['field']['configuration']);
        
        //bild markers
        $address_array = $this->get_reference_points($options);

        //skip
        if (!strlen($cfg->get('address_pattern')) or !count($address_array) or isset($options['is_listing']) or isset($options['is_export']))
            return '';

        $field_id = $options['field']['id'];

        $has_update_access = false;

        $html_map_center = '';
        $map_center = [];
        $markers_array = [];

        $html_directions = '';       
                
        $fields_in_popup_array = $cfg->get('fields_in_popup');
        
        //print_rr($fields_in_popup_array);

        foreach ($address_array as $address_key => $value)
        {
            
            $lat = $value['lat'] ?? '';
            $lng = $value['lng'] ?? '';
            $current_address = $value['address'];
            
            
            //$current_address = $value[2] ?? '';
            
            if (strlen($lat) and strlen($lng))
            {
                
                if (!count($map_center))
                {
                    $map_center[] = $lat;
                    $map_center[] = $lng;
                }

                //configure marker label
                $label = '';
                

                //configure marker icon
                $icon = '';
                

                //configure marker
                $markers_html = '
                    var markerLatlng' . $address_key . ' = new google.maps.LatLng(' . $lat . ',' . $lng . ');';

                
                $markers_array[$address_key] = $markers_html;
            }
        }

        if (count($map_center))
        {
            $html_map_center = '
                    var myLatlng = new google.maps.LatLng(' . $map_center[0] . ',' . $map_center[1] . ');

                      //Got result, center the map and put it out there
                    map.setCenter(myLatlng);	
            ';
        }

        //build directions if mode set
        if (strlen($cfg->get('travel_mode')) and count($markers_array) > 1)
        {

            $waypts_html = '';
            
            $first_marker_key = false;
            $last_marker_key = 0;

            //print_rr($markers_array);

            foreach($markers_array as $k=>$v)
            {
                $waypts_html .= '
                            waypts.push({
                                location: markerLatlng' . $k . ',
                                stopover: true
                              });
                            ';

                if($first_marker_key===false)
                {
                    $first_marker_key = $k;
                }

                $last_marker_key = $k;
            }            

            $html_directions = '
                var isDirectionInit = false
	  	var directionsService = new google.maps.DirectionsService();
	        var directionsRenderer = new google.maps.DirectionsRenderer({
	  						map: map
	  					});
	  								
		 
	  	  					  
            var waypts = [];

            ' . $waypts_html . '
	  	
  	    directionsService.route(
            {
                origin:  markerLatlng' . $first_marker_key . ',
                destination: markerLatlng' . $last_marker_key . ',
                travelMode: "' . $cfg->get('travel_mode') . '",
                waypoints: waypts,
                optimizeWaypoints: ' . ($cfg->get('optimizeWaypoints') == 1 ? 'true' : 'false') . ',
                provideRouteAlternatives: ' . ($cfg->get('provideRouteAlternatives') == 1 ? 'true' : 'false') . ',
                avoidFerries: ' . ($cfg->get('avoidFerries') == 1 ? 'true' : 'false') . ',
                avoidHighways: ' . ($cfg->get('avoidHighways') == 1 ? 'true' : 'false') . ',
                avoidTolls: ' . ($cfg->get('avoidTolls') == 1 ? 'true' : 'false') . ',                
            },
            function(response, status) {
              if (status === "OK") {
                directionsRenderer.setDirections(response);
                
                const route = response.routes[0];
                //console.log(route.legs)
                calculateDistance(route.legs)
              } else {
                window.alert("Directions request failed due to " + status);
              }
            });
                        
  	';
        }


        if (count($markers_array) or strlen($html_directions))
        {
            $html = '';

            if ($is_google_map_script != true)
            {
                $html .= '<script src="https://maps.googleapis.com/maps/api/js?key=' . $cfg->get('api_key') . '&libraries=places"></script>';
                $is_google_map_script = true;
            }


            $html .= '
  				
  				<script>
					  				
  					$(function(){
  						  				
						  var mapOptions = {
						    zoom: ' . $cfg->get('zoom') . ',    
						  }
						  
						  var map = new google.maps.Map(document.getElementById("goolge_map_container' . $field_id . '"), mapOptions);
						  
						  geocoder = new google.maps.Geocoder();
						
						  ' . $html_map_center . '	
						  		
						  var infowindow = new google.maps.InfoWindow();
                         
			        ' . implode('', $markers_array) . '
			        		
			        ' . $html_directions . '
						  		
						})
																		
						</script>  
					';

            $map_width = (strlen($cfg->get('map_width')) ? $cfg->get('map_width') : '470px');
            $map_height = (strlen($cfg->get('map_height')) ? $cfg->get('map_height') : '470px');

            if (!strstr($map_width, '%') and !strstr($map_width, 'px'))
                $map_width = $map_width . 'px';
            if (!strstr($map_height, '%') and !strstr($map_height, 'px'))
                $map_height = $map_height . 'px';

            $html .= '			
		<div id="goolge_map_container' . $field_id . '" style="width:100%; max-width: ' . $map_width . '; height: ' . $map_height . ';"></div> 
            ';
            
            $html .= '
            <script>
                function calculateDistance(legs)
                {
                    let distance = 0

                    legs.forEach((v)=>{                        
                        distance += v.distance.value
                    })
                    
                    ' . ($cfg->get('unit_system') == 'kilometers' ? 'distance = (distance/1000).toFixed(3)':'distance = (distance/1609).toFixed(3)') . '

                    if(distance)
                    {   
                        $(".google-map-distance").remove()
                        $("#goolge_map_container' . $field_id . '").after("<div class=\'google-map-distance\' style=\'width: 100%; max-width:' . $map_width . '\'>' . ($cfg->get('unit_system') == 'kilometers' ? TEXT_TOTAL_DISTANCE_IN_KILOMETERS : TEXT_TOTAL_DISTANCE_IN_MILES ). ': "+distance+"</div>")
                            
                        ' . ($cfg->get('save_value_in')>0 ? '
                            $.ajax({
                                  method: "POST",
                                  url: "' . url_for('items/google_map', 'path=' . $options['path'] . '&action=save_value_in') . '",
                                  data: { distance: distance, filed_id: ' . $cfg->get('save_value_in') . ' } 
                                }).done(function(response){
                                    if(response=="UPDATED")
                                    {                                        
                                        $(".form-group-' . $cfg->get('save_value_in') . ' td").html(\'<a href=""><i class="fa fa-refresh" aria-hidden="true"></i></a>\')
                                    }
                                })
                        ':'' ). '
                    }
                }
            </script>
                ';

            return $html;
        } else
        {
            return '';
        }
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
                                        
                    $reference_points[] = [
                        'lng' => $coordinates['lng'],
                        'lat' => $coordinates['lat'],
                        'address'=>$address,                        
                    ];
                }                        
            }
            else
            {
                $lat = $current_reference_points[$item['id']]['lat'];
                $lng = $current_reference_points[$item['id']]['lng'];
                                
                $reference_points[] = [
                        'lng' => $lng,
                        'lat' => $lat,
                        'address'=>$address,                        
                    ];
                
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
        
        $url = "https://maps.google.com/maps/api/geocode/json?key=" . $cfg->get('api_key') . "&address=" . $address;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $result = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($result, true);

        //print_rr($result);

        if (isset($result['error_message']))
        {
            $alerts->add(TEXT_FIELD . ' "' . $fields['name'] . '": ' . $result['error_message'], 'error');
            return false;
        } 
        else
        {
            $lat = $result['results'][0]['geometry']['location']['lat'];
            $lng = $result['results'][0]['geometry']['location']['lng'];

            return [
                'lat' => $lat,
                'lng' => $lng,
            ];

            //echo $value;	  
            //exit();
        }                        
    } 

    

}
