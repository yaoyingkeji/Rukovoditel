<?php

$reports_info_query = db_query("select * from app_reports where id='" . db_input($_GET['reports_id']) . "'");
if(!$reports_info = db_fetch_array($reports_info_query))
{
    $alerts->add(TEXT_REPORT_NOT_FOUND, 'error');
    redirect_to('dashboard/');
}

$fields_access_schema = users::get_fields_access_schema($reports_info['entities_id'], $app_user['group_id']);

$sorting_fields = array();
$sorting_fields_info = array();

if(strlen($reports_info['listing_order_fields']) > 0)
{
    foreach(explode(',', $reports_info['listing_order_fields']) as $value)
    {
        $v = explode('_', $value);
        $sorting_fields[] = $v[0];
        $sorting_fields_info[$v[0]] = $v[1];
    }
}


switch($app_module_action)
{
    case 'render_fields_for_sorting':
        $html = '';
        if(count($sorting_fields) > 0)
        {
            if(($key = array_search('lastcommentdate', $sorting_fields)) !== false)
            {
                $html .= '<li id="form_fields_lastcommentdate_' . $sorting_fields_info['lastcommentdate'] . '"><div><img rel="' . $sorting_fields_info['lastcommentdate'] . '" src="images/' . ($sorting_fields_info['lastcommentdate'] == 'asc' ? 'arrow_down.png' : 'arrow_up.png') . '" class="condition_icon" id="condition_icon_lastcommentdate"> ' . TEXT_LAST_COMMENT_DATE . '</div></li>';
            }

            if(count($sorting_fields) > 0)
            {
                $fields_query = db_query("select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.id in (" . db_input_in($sorting_fields) . ") and f.type not in ('fieldtype_action') and  f.entities_id='" . db_input($reports_info['entities_id']) . "' and f.forms_tabs_id=t.id order by field(f.id," . db_input_in($sorting_fields) . ")");
                while($v = db_fetch_array($fields_query))
                {
                    //check field access
                    if(isset($fields_access_schema[$v['id']]) and $fields_access_schema[$v['id']] == 'hide')
                    {
                        continue;
                    }

                    $html .= '
                        <li id="form_fields_' . $v['id'] . '_' . $sorting_fields_info[$v['id']] . '">
                            <div>                                
                                <img rel="' . $sorting_fields_info[$v['id']] . '" src="images/' . ($sorting_fields_info[$v['id']] == 'asc' ? 'arrow_down.png' : 'arrow_up.png') . '" class="condition_icon" id="condition_icon_' . $v['id'] . '"> ' . fields_types::get_option($v['type'], 'name', $v['name']) . '
                            </div>
                        </li>';
                }
            }
        }
        
        $html .= '
            <script>
                prepare_condition_icons()
            </script>
            ';

        echo $html;

        app_exit();

        break;
    case 'set_sorting_fields':
        $sorting_fields = !is_array($_POST['sorting_fields']) ? [] : $_POST['sorting_fields'];

        if(!count($sorting_fields))
        {
            db_query("update app_reports set listing_order_fields='' where id='" . $reports_info['id'] . "'");
        }

        $listing_order_fields = strlen($reports_info['listing_order_fields']) ? explode(',', $reports_info['listing_order_fields']) : [];

        foreach($sorting_fields as $field_id)
        {
            if(!in_array($field_id . '_asc', $listing_order_fields) and!in_array($field_id . '_desc', $listing_order_fields))
            {
                $listing_order_fields[] = $field_id . '_asc';
            }
        }

        foreach($listing_order_fields as $k => $v)
        {
            if(!in_array(str_replace(['_asc', '_desc'], '', $v), $sorting_fields))
            {
                unset($listing_order_fields[$k]);
            }
        }

        //print_rr($sorting_fields);
        //print_rr($listing_order_fields);

        db_query("update app_reports set listing_order_fields='" . db_input(implode(',', $listing_order_fields)) . "' where id='" . db_input($reports_info['id']) . "'");

        app_exit();
        break;
    case 'set_sorting':
        $listing_order_fields = array();
        if(strlen($_POST['fields_for_sorting']) > 0)
        {
            foreach(explode(',', str_replace('form_fields_', '', $_POST['fields_for_sorting'])) as $v)
            {
                if(strstr($v, 'lastcommentdate'))
                {
                    $listing_order_fields = array_merge(array($v), $listing_order_fields);
                }
                else
                {
                    $listing_order_fields[] = $v;
                }
            }
        }

        if(count($listing_order_fields) > 0)
        {
            db_query("update app_reports set listing_order_fields='" . db_input(implode(',', $listing_order_fields)) . "' where id='" . db_input($_GET['reports_id']) . "'");
        }
        else
        {
            db_query("update app_reports set listing_order_fields='' where id='" . db_input($_GET['reports_id']) . "'");
        }
        app_exit();
        break;
    case 'set_sorting_condition':
        if($_POST['condition'] == 'desc')
        {
            db_query("update app_reports set listing_order_fields=replace(listing_order_fields,'" . $_POST['field_id'] . "_asc','" . $_POST['field_id'] . "_desc') where id='" . db_input($_GET['reports_id']) . "'");
        }
        else
        {
            db_query("update app_reports set listing_order_fields=replace(listing_order_fields,'" . $_POST['field_id'] . "_desc','" . $_POST['field_id'] . "_asc') where id='" . db_input($_GET['reports_id']) . "'");
        }

        app_exit();
        break;
}