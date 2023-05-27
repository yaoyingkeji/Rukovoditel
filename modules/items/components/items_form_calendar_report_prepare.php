<?php

$calendar_reports_id = str_replace('calendarreport','',$app_redirect_to);
$calendar_reports_query = db_query("select * from app_ext_calendar where id='" . db_input($calendar_reports_id) . "'");
if($calendar_reports = db_fetch_array($calendar_reports_query))
{
  $start_date_timestamp = strtotime($_GET['start']);
  $end_date_timestamp = strtotime($_GET['end']);
              
  if($_GET['view_name']=='dayGridMonth')  
  {
    $obj['field_' . $calendar_reports['start_date']] = $start_date_timestamp;
    $obj['field_' . $calendar_reports['end_date']] = strtotime('-1 day',$end_date_timestamp);
  }  
  else
  { 
    $obj['field_' . $calendar_reports['start_date']] = $start_date_timestamp;
    $obj['field_' . $calendar_reports['end_date']] = $end_date_timestamp;       
  }    
}