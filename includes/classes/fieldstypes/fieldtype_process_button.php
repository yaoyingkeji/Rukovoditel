<?php

class fieldtype_process_button
{
    public $options;
    
    function __construct()
    {
        $this->options = array('title' => TEXT_FIELDTYPE_PROCESS_BUTTON_TITLE);
    }
    
    function get_configuration($params = array())
    {
        $cfg = array();
        
        if(is_ext_installed())
        {
            $choices = [];
            $choices[''] = '';
            $processes_query = db_query("select p.*, e.name as entities_name from app_ext_processes p, app_entities e where e.id=p.entities_id and e.id='" . $params['entities_id'] . "' order by p.sort_order, e.name, p.name");
            while($processes = db_fetch_array($processes_query))
            {
                $choices[$processes['id']] = (($processes['name']==$processes['button_title'] or strlen($processes['button_title'])==0) ? $processes['name'] : $processes['name'] . ' (' . $processes['button_title'] . ')');
            }
            
            $cfg[] = array(
                'title'=>TEXT_EXT_PROCESSES,
                'tooltip_icon'=>TEXT_EXT_SELECT_BUTTONS_TO_DISPLAY,
                'name'=>'process_button',
                'type'=>'dropdown',
                'choices'=>$choices,
                'params'=>array('class'=>'form-control chosen-select','multiple'=>'multiple'));
            
                        
            $cfg[] = array(
                'title'=>TEXT_DISPLAY_AS,
                'tooltip_icon' => TEXT_EXT_MULTIPLE_BUTTONS_DISPLAY_TYPE,
                'name'=>'display_as',
                'type'=>'dropdown',
                'tooltip'=>TEXT_DISPLAY_AS_DROPDOWN_AJAX_TIP,
                'choices'=>['inline'=>TEXT_INLINE_LIST,'inrow'=>TEXT_EXT_EXTRA_ROWS,'grouped'=>TEXT_EXT_BUTTON_GROUP,'dropdown'=>TEXT_FIELDTYPE_DROPDOWN_TITLE,'dropdown_ajax'=>TEXT_FIELDTYPE_DROPDOWN_TITLE . ' (Ajax)'],
                'params'=>array('class'=>'form-control input-medium'));
            
            $cfg[] = array('title' => TEXT_BUTTON_TITLE, 'name' => 'button_title', 'type' => 'input', 'params' => ['class'=>'form-control input-medium'],'tooltip'=>TEXT_DEFAULT . ': ' . TEXT_ACTION, 'form_group'=>['form_display_rules'=>'fields_configuration_display_as:dropdown,dropdown_ajax']);
            
            $cfg[] = array('title' => TEXT_EMPTY_VALUE, 'name' => 'empty_text', 'type' => 'input', 'params' => ['class'=>'form-control input-medium'],'tooltip'=>TEXT_DEFAULT . ': ' . TEXT_NONE, 'form_group'=>['form_display_rules'=>'fields_configuration_display_as:dropdown_ajax']);
            
            $cfg[] = array('title'=>TEXT_EXT_PROCESS_BUTTON_COLOR,
    		'name'=>'button_color',
    		'type'=>'colorpicker',
                'form_group'=>['form_display_rules'=>'fields_configuration_display_as:dropdown,dropdown_ajax']);
                        
        }
        else
        {
            $cfg[] = array('html'=>app_alert_warning(TEXT_EXTENSION_REQUIRED),'type'=>'html');
        }
        
        
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
        global $buttons_css_holder, $current_item_id;
        
        $cfg = new fields_types_cfg($options['field']['configuration']);
                
        
        $html = '';
        $buttons_css = '';        
        $buttons_links = [];
        $buttons_urls = [];
        $btn_group_id = 'filed_' . $options['field']['id'];
        
        if($cfg->get('display_as')=='dropdown_ajax')
        {
            $html = '
                <div class="btn-group btn-group-sm ">
                        <button class="btn btn-primary btn-process-' . $btn_group_id . ' dropdown-toggle dropdown-toggle-ajax btn-process-button-dropdown" type="button" data-toggle="dropdown" 
                        data-toggle-ajax="' . url_for('items/process_button_ajax','path=' . $options['field']['entities_id'] . '-' . $options['item']['id'] . '&field_id=' . $options['field']['id'] . '&page=' . ($_POST['page']??1) . '&redirect_to=' . ($options['redirect_to']??'') . '&reports_id=' . ($options['reports_id']??0)). '" 
                        data-boundary="window" aria-expanded="false">
                        ' . (strlen($cfg->get('button_title')) ? $cfg->get('button_title') : TEXT_ACTION). ' <i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu" role="menu">
                            <li>
                                <a href="#"><i class="fa fa-spinner fa-spin"></i></a>
                            <li>			
                        </ul>
                </div>
                '; 
            if(strlen($cfg->get('button_color')) and !isset($buttons_css_holder[$btn_group_id]))
            {
                $processes = new processes($options['field']['entities_id']);                
                $buttons_css_holder[$btn_group_id] = $processes->prepare_button_css(['id'=>$btn_group_id,'button_color'=>$cfg->get('button_color')]);
                $buttons_css .= $buttons_css_holder[$btn_group_id]; 
            }
        }
        elseif(is_array($cfg->get('process_button')) and count($cfg->get('process_button')))
        {
            $processes = new processes($options['field']['entities_id']);
            $processes->items_id = $options['item']['id'];
            $buttons_list = $processes->get_buttons_list('',implode(',',$cfg->get('process_button')));
                        
            foreach($buttons_list as $buttons)
            {
                $check_buttons_filters = $processes->check_buttons_filters($buttons);                
                                
                $is_dialog = ((strlen($buttons['confirmation_text']) or $buttons['allow_comments']==1 or $buttons['preview_prcess_actions']==1 or $processes->has_enter_manually_fields($buttons['id'])) ? true:false);                 
                $params = (!$is_dialog ? '&action=run':'') . ((isset($options['reports_id']) and isset($_POST['page'])) ? '&gotopage[' . $options['reports_id'] . ']=' . $_POST['page'] :'');
                $css = (!$is_dialog ? ' prevent-double-click':'');
                
                $rdirect_to = ((isset($options['redirect_to']) and strlen($options['redirect_to'])) ? $options['redirect_to']:'items');
                
                if(!isset($options['reports_id'])) $rdirect_to = 'items_info';
                
                $path = $options['path'];
                                                
                if(substr($path,-strlen('-' . $options['item']['id']))!='-' . $options['item']['id']) 
                {
                    if(substr($path,-strlen('-' . $current_item_id))=='-' . $current_item_id)
                    {                        
                        $path =  substr($path,0,-strlen('-' . $current_item_id)) . '-' . $options['item']['id'];
                    }
                    else
                    {
                        $path .= '-' . $options['item']['id'];
                    }
                    
                }
                
                if($rdirect_to=='items_info')
                {
                    //$path = $options['field']['entities_id'] . '-' . $options['item']['id'];
                }
                
                if($rdirect_to=='parent_item_info_page')
                {
                    $path_info = items::parse_path($path);
                    $rdirect_to = 'item_info_page' .$path_info['parent_entity_id'] . '-' . $path_info['parent_entity_item_id'];
                }
                                             
                //buttons list
                if(!$check_buttons_filters)
                {
                    if($processes->button_has_warnign_text($buttons))
                    {
                        $buttons_links[] = button_tag($buttons['button_title'],url_for('items/processes_warning','id=' . $buttons['id'] .  '&path=' . $path),true,array('class'=>'btn btn-primary btn-sm btn-process-' . $buttons['id'] . $css),$buttons['button_icon']);
                    }
                }
                else
                {
                    $buttons_links[] = button_tag($buttons['button_title'],url_for('items/processes','id=' . $buttons['id'] .  '&path=' . $path . '&redirect_to=' . $rdirect_to . $params),$is_dialog,array('class'=>'btn btn-primary btn-sm btn-process-' . $buttons['id'] . $css),$buttons['button_icon']);
                }
                
                //buttons url
                $url_color = (strlen($buttons['button_color']) ? 'style="color: ' . $buttons['button_color']  . '"': '');                                
                
                //check buttons filters
                if(!$check_buttons_filters)
                {
                    if($processes->button_has_warnign_text($buttons))
                    {
                        $buttons_urls[] = '<a ' . $url_color . ' onclick="open_dialog(\'' . url_for('items/processes_warning','id=' . $buttons['id'] .  '&path=' . $path) . '\')" class="link-to-modalbox">'  . app_render_icon($buttons['button_icon'])  . ' ' . $buttons['button_title'] . '</a>';
                    }                    
                }                
                //prepare buttons
                elseif($is_dialog)
                {
                    $buttons_urls[] = '<a ' . $url_color . ' onclick="open_dialog(\'' . url_for('items/processes','id=' . $buttons['id'] .  '&path=' . $path . '&redirect_to=' . $rdirect_to . $params) . '\')" class="link-to-modalbox">'  . app_render_icon($buttons['button_icon'])  . ' ' . $buttons['button_title'] . '</a>';
                }
                else
                {
                   $buttons_urls[] = '<a ' . $url_color . ' href="' . url_for('items/processes','id=' . $buttons['id'] .  '&path=' . $path . '&redirect_to=' . $rdirect_to . $params) . '" class="link-to-modalbox">'  . app_render_icon($buttons['button_icon'])  . ' ' . $buttons['button_title'] . '</a>'; 
                }
                
                //button csss
                if(!isset($buttons_css_holder[$buttons['id']]))
                {
                    $buttons_css_holder[$buttons['id']] = $processes->prepare_button_css($buttons);
                    $buttons_css .= $buttons_css_holder[$buttons['id']];
                }                
            }
                                 
            switch($cfg->get('display_as'))
            {
                case 'inline': $html = implode(' ',$buttons_links);
                    break;
                case 'inrow': $html = implode('<br>',$buttons_links);
                    break; 
                case 'grouped': $html = '<div class="btn-group btn-group-sm" style="display: inline-flex">' . implode('',$buttons_links) . '</div>';
                    break;
                case 'dropdown':  
                    if(count($buttons_urls))
                    {
                        $html = '
                            <div class="btn-group btn-group-sm ">
                                    <button class="btn btn-primary btn-process-' . $btn_group_id . ' dropdown-toggle btn-process-button-dropdown" type="button" data-toggle="dropdown" data-boundary="window" aria-expanded="false">
                                    ' . (strlen($cfg->get('button_title')) ? $cfg->get('button_title') : TEXT_ACTION). ' <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu" role="menu">
                                        <li>
                                            ' . implode('</li><li>',$buttons_urls) . '
                                        <li>			
                                    </ul>
                            </div>
                            ';    
                    }                                        
                    
                    if(strlen($cfg->get('button_color')) and !isset($buttons_css_holder[$btn_group_id]))
                    {
                        $buttons_css_holder[$btn_group_id] = $processes->prepare_button_css(['id'=>$btn_group_id,'button_color'=>$cfg->get('button_color')]);
                        $buttons_css .= $buttons_css_holder[$btn_group_id]; 
                    }
                    break;
            }
                                                                
        }
        
        $html .= $buttons_css;            
        
        return $html;
    }
}