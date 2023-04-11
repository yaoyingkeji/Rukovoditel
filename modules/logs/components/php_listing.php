<?php

 // Get the error type from the error number 
    $errortype = [
        1 => "Error",
        2 => "Warning",
        4 => "Parsing Error",
        8 => "Notice",
        16 => "Core Error",
        32 => "Core Warning",
        64 => "Compile Error",
        128 => "Compile Warning",
        256 => "User Error",
        512 => "User Warning",
        1024 => "User Notice",
        2048 => "Error Strict",
        8192 => "Deprecated"
        ];
    
$order_by = $_POST['order_by']??'';    

$html = '
    <div class="table-scrollable">
        <table class="table table-striped table-bordered table-hover">
            <thead>
                <tr>						                    
                    <th class="listing_order ' . (strstr($order_by,'date_added') ? (strstr($order_by,'asc') ? 'listing_order_asc' : 'listing_order_desc'):'' ) . '" data_order_by="date_added">'
        . '             <div>' . TEXT_DATE_ADDED . '</div>
                    </th>
                    <th>' . TEXT_USERNAME . '</th>
                    <th>' . TEXT_TYPE . '</th>    
                    <th width="100%">' . TEXT_DESCRIPTION . '</th>                    
                </tr>
            </thead>
            <tbody>';

$where_sql = "";

$filters = $_POST['filters']??[];

foreach($filters as $filter)
{
    if(strlen($filter['value']) > 0)
    {
        $filter['value'] = db_input($filter['value']);
        
        switch($filter['name'])
        {
            case 'from':
                $where_sql .= " and FROM_UNIXTIME(l.date_added,'%Y-%m-%d %H:%i')>='" . $filter['value'] . "'";
                break;
            case 'to':
                $where_sql .= " and FROM_UNIXTIME(l.date_added,'%Y-%m-%d %H:%i')<='" . $filter['value'] . "'";
                break;
            case 'search':
                $where_sql .= " and (u.field_12 like ('%" . $filter['value'] . "%') or l.description like ('%" . $filter['value'] . "%')  or l.http_url like ('%" . $filter['value'] . "%'))";
                break;            
        }
    }
}

$order_by_sql = (strlen($order_by) ? 'l.' . $order_by : 'l.date_added desc');

$listing_sql = "select l.*,u.field_12 as username from app_logs l left join app_entity_1 u on u.id=l.users_id where log_type='php' {$where_sql} order by {$order_by_sql}";
$listing_split = new split_page($listing_sql,'log_listing','',50);
$items_query = db_query($listing_split->sql_query, false);								
while($item = db_fetch_array($items_query))
{
    $errlevel = isset($errortype[$item['errno']]) ? $errortype[$item['errno']] : $errortype[2];
    switch($item['errno'])
    {
        case 2:
            $errlevel = '<span class="label label-warning">' . $errlevel . '</span>';
            break;
        case 8:
            $errlevel = '<span class="label label-info">' . $errlevel . '</span>';
            break;
        case 8192:
            $errlevel = '<span class="label label-default">' . $errlevel . '</span>';
            break;       
    }
    
    $url_label = ($item['is_ajax'] ? '<span style="color: #c7254e">[AJAX]</span>': (strstr($item['http_url'],'action=') ? '<span style="color: #c7254e">[ACTION]</span>':''));
    
    if(strlen($url_label))
    {
        $http_url = $item['http_url'] . ' ' . $url_label;
    }
    else
    {
        $http_url = link_to($item['http_url'],'//' . $_SERVER['HTTP_HOST'] . $item['http_url'],['target'=>'_blank']);
    }
    
    $username = $item['username'];
    
    if($item['users_id']>0 and $item['users_id']!=$app_user['id'])
    {
        $username = link_to_modalbox('<i class="fa fa-sign-in"></i> ' . $username, url_for('users/login_as','users_id=' . $item['users_id']));
    }
    
    $html .= '
        <tr>
            <td>' . format_date_time($item['date_added']). '</td>
            <td>' . $username . '</td>
            <td>' . $errlevel . '</td>
            <td class="white-space-normal">
                <div>' . $item['description'] . '</div>
                <div style="font-size: 11px; color: #737373; padding-top: 3px;">
                    <div>' . str_replace(DIR_FS_CATALOG,'',$item['filename']) . ':<font color="#c7254e">' . $item['linenum'] . '</font></div>
                    <div>' . $http_url . '</div>
                </div>
            </td>
        </tr>
        ';
}

if($listing_split->number_of_rows==0)
{
    $html .= '
            <tr>
              <td colspan="4">' . TEXT_NO_RECORDS_FOUND . '</td>
            </tr>
          ';
}


$html .= '
            </tbody>
        </table>
    </div>    
    ';

//add pager
$html .= '
    <table width="100%">
      <tr>
        <td>' . $listing_split->display_count() . '</td>
        <td align="right">' . $listing_split->display_links(). '</td>
      </tr>
    </table>
  ';

echo $html;
