<?php

$field_id = _GET('field_id');
$reports_id = isset($_GET['reports_id']) ? _GET('reports_id') : 0;

if(!isset_field($current_entity_id,$field_id) or $app_fields_cache[$current_entity_id][$field_id]['type']!='fieldtype_process_button')
{
    app_exit();    
}

$cfg = new fields_types_cfg($app_fields_cache[$current_entity_id][$field_id]['configuration']);

$buttons_urls = [];
$buttons_css = '';

$process_button = is_array($cfg->get('process_button')) ? $cfg->get('process_button'): [0];

$processes = new processes($current_entity_id);
$processes->items_id = $current_item_id;
$buttons_list = $processes->get_buttons_list('',implode(',',$process_button));

//print_rr($buttons_list);

foreach($buttons_list as $buttons)
{
    $check_buttons_filters = $processes->check_buttons_filters($buttons);   
            
    $is_dialog = ((strlen($buttons['confirmation_text']??'') or $buttons['allow_comments']==1 or $buttons['preview_prcess_actions']==1 or $processes->has_enter_manually_fields($buttons['id'])) ? true:false);                 
    $params = (!$is_dialog ? '&action=run':'') . ((isset($_GET['page']) and $_GET['page']>1) ? '&gotopage[' . $reports_id . ']=' . $_GET['page'] :'');
    $css = (!$is_dialog ? ' prevent-double-click':'');

    $rdirect_to = ((isset($_GET['redirect_to']) and strlen($_GET['redirect_to'])) ? $_GET['redirect_to']:'items');

    $path = $app_path;
    
    if(!$reports_id)
    {
        $rdirect_to = 'items_info';
    }           
        
    if($rdirect_to=='parent_item_info_page')
    {
        $path_info = items::get_path_info(explode('-',$path)[0], explode('-',$path)[1]);
        $path_info = items::parse_path($path_info['full_path']);
        $rdirect_to = 'item_info_page' .$path_info['parent_entity_id'] . '-' . $path_info['parent_entity_item_id'];
    }

    //buttons url
    $url_color = (strlen($buttons['button_color']??'') ? 'style="color: ' . $buttons['button_color']  . '"': '');                                

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
             
}

//print_rr($buttons_urls);

if(count($buttons_urls))
{
    echo '<li>' . implode('</li><li>',$buttons_urls) . '<li>';
}
else
{
    echo  '<li><a href="#" onClick="return false">'. (strlen($cfg->get('empty_text')) ? $cfg->get('empty_text') : TEXT_NONE) . '</a></li>';
}

app_exit();