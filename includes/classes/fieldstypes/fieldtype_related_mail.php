<?php

class fieldtype_related_mail
{

    public $options;

    function __construct()
    {
        $this->options = array('title' => TEXT_FIELDTYPE_RELATED_MAIL_TITLE);
    }

    function get_configuration()
    {
        $cfg = array();
        
        $cfg[] = array(
            'title' => TEXT_DISPLAY_IN_LISTING,
            'name' => 'display_in_listing',
            'type' => 'dropdown',
            'choices' => [
                'count' => TEXT_COUNT_RELATED_EMAILS, 
                'list' => TEXT_LIST_RELATED_EMAILS,
                'list_unread' => TEXT_UNREAD_EMAILS_ONLY,
                ],
            'params' => array('class' => 'form-control input-large'));

        if(!is_ext_installed())
        {
            $cfg[] = array('html'=>app_alert_warning(TEXT_EXTENSION_REQUIRED),'type'=>'html');
        }

        return $cfg;
    }

    function render($field, $obj, $params = array())
    {
        return '';
    }

    function process($options)
    {
        return '';
    }

    function output($options)
    {
        global $app_user;
        
        $cfg = new fields_types_cfg($options['field']['configuration']);
        
        if(!is_ext_installed() or isset($options['is_export']))
        {
            return '';
        }
                                        
        $entity_id = $options['field']['entities_id'];
        $items_id = $options['item']['id'];
        $field_id = $options['field']['id'];
        $reports_id = $options['reports_id'];
        $page = $_POST['page'];
                
        //check access
        $accounts_entities_query = db_query("select ae.* from app_ext_mail_accounts_entities ae where ae.entities_id='" . $entity_id. "'  and ae.accounts_id in (select au.accounts_id from app_ext_mail_accounts_users au where au.users_id='" . $app_user['id'] . "')");
        if(!$accounts_entities = db_fetch_array($accounts_entities_query))
        {
            return '';
        }                
        
        $related_mails_query = db_query("select mg.id, mg.subject_cropped, (select count(*) from app_ext_mail m3 where m3.groups_id=mg.id) as count_mails, (select count(*) from app_ext_mail m2 where m2.groups_id=mg.id and m2.is_new=1) as is_new,(select date_added from app_ext_mail m1 where m1.groups_id=mg.id order by date_added desc limit 1) as date_added from app_ext_mail_to_items m2i left join app_ext_mail_groups mg on mg.id=m2i.mail_groups_id where m2i.entities_id='" . $entity_id . "' and m2i.items_id='" . $items_id . "'");
        $count_mails = db_num_rows($related_mails_query);                
        
        $html_list = '<ul class="list related-mail-list">';        
        $html_table = '<h4>' . $options['field']['name'] . ' (' . $count_mails . ')</h4><table class="table" style="min-width: 550px;">';
        $has_unread = false;
        while($related_mails = db_fetch_array($related_mails_query))
        { 
            $mail_date = mail_related::render_mail_date($related_mails['date_added']);
            
            
            if(($cfg->get('display_in_listing')=='list_unread' and $related_mails['is_new']>0) or $cfg->get('display_in_listing')=='list')
            {
                $html_list .= '
                    <li class="' . ($related_mails['is_new']>0 ? 'unread-email':'') . '">
                        <a href="' . url_for('ext/mail/info', 'id=' . $related_mails['id']) . '">' . htmlspecialchars($related_mails['subject_cropped']) . ($related_mails['count_mails']>1 ? ' (' . $related_mails['count_mails'] . ')':'') . '
                        <small><i>' . $mail_date. '</i></small>    
                        </a>                        
                   </li>';
            }                           
            
            $html_table .= '
                    <tr class="' . ($related_mails['is_new']>0 ? 'new-email':'') . '">                        
                        <td>
                            <a href="' . url_for('ext/mail/info', 'id=' . $related_mails['id']) . '"><i class="fa fa-envelope-o" aria-hidden="true"></i> ' . htmlspecialchars($related_mails['subject_cropped']) . ($related_mails['count_mails']>1 ? ' (' . $related_mails['count_mails'] . ')':'') . '</a>
                        </td>
                        <td align="right">
                            ' . ($related_mails['is_new']>0 ? '<span class="label label-warning">' . $mail_date . '</span>' : $mail_date). '
                        </td>
                    </tr>';
            
            if($related_mails['is_new'])
            {
                $has_unread = true;
            }
        }
        
        $html_list .= '</ul>';        
        $html_table .= '</table>';
                
        $from_email = $options['item']['field_' . $accounts_entities['from_email']]??'';
        $mail_to = strlen($from_email) ? '&mail_to=' . str_replace("\n",',',$from_email) : '';
        
        
        
        $html_table .= '<a href="javascript: $.fancybox.close(); open_dialog(\'' . url_for('ext/mail/create','redirect_to=listing_' . $reports_id . '_' . $entity_id . '_' . $page . '&path=' . $entity_id . '-' . $options['item']['id'] . $mail_to) . '\')" class="btn btn-default"><i class="fa fa-plus"></i> ' . TEXT_BUTTON_CREATE . '</a>';
        
        $html = '';
        switch($cfg->get('display_in_listing'))
        {
            case 'count':               
                $html = '<a ' . ($has_unread ? 'style="font-weight:bold"':'') .' href="#related_mails_list_' . $items_id . '_' . $field_id . '" class="fancybox" title="' . items::get_heading_field($entity_id, $items_id, $options['item']) . '">' . $count_mails . '</a><div style="display:none" id="related_mails_list_' . $items_id . '_' . $field_id . '">' . $html_table . '</div>';                
                break;
            case 'list':
                $html = $html_list;
                break;
            case 'list_unread':                
                $html = $html_list . '<a href="#related_mails_list_' . $items_id . '_' . $field_id . '" class="fancybox" title="' . items::get_heading_field($entity_id, $items_id, $options['item']) . '">' . TEXT_TOTAL . ' (' . $count_mails . ')</a><div style="display:none" id="related_mails_list_' . $items_id . '_' . $field_id . '">' . $html_table . '</div>';;                
                break;
        }
        
        
        return $html;
    }
    
    function reports_query($options)
    {
        $filters = $options['filters'];
        $sql_query = $options['sql_query'];
        
        //print_rr($filters);
                
        switch($filters['filters_values'])
        {
            case 'has_related_emails':
                $sql_query[] = "(select count(*) from app_ext_mail_to_items m2i left join app_ext_mail_groups mg on mg.id=m2i.mail_groups_id where m2i.entities_id='" . $options['entities_id'] . "' and m2i.items_id=e.id limit 1)>0";
                break;
            case 'no_related_emails':
                $sql_query[] = "(select count(*) from app_ext_mail_to_items m2i left join app_ext_mail_groups mg on mg.id=m2i.mail_groups_id where m2i.entities_id='" . $options['entities_id'] . "' and m2i.items_id=e.id limit 1)=0";
                break;
            case 'has_unread_emails':
                $sql_query[] = "(select count(*) from app_ext_mail_to_items m2i left join app_ext_mail_groups mg on mg.id=m2i.mail_groups_id where m2i.entities_id='" . $options['entities_id'] . "' and m2i.items_id=e.id and (select count(*) from app_ext_mail m2 where m2.groups_id=mg.id and m2.is_new=1)>0 limit 1)>0";
                break;
        }
        
        return $sql_query;
    }

}
