<?php 

class fieldtype_jalali_calendar
{
  public $options;
  
  function __construct()
  {
      $this->options = array('title' => TEXT_FIELDTYPE_JALALI_CALENDAR_TITLE);
  }
  
  function get_configuration()
  {
    $cfg = array();
    
    $cfg[] = array('title'=>TEXT_TIME, 'name'=>'time_picker','type'=>'checkbox');
    
    $cfg[] = array('title'=>TEXT_HIDE_FIELD_IF_EMPTY, 'name'=>'hide_field_if_empty','type'=>'checkbox','tooltip_icon'=>TEXT_HIDE_FIELD_IF_EMPTY_TIP);
    
    $cfg[] = array('title'=>TEXT_NOTIFY_WHEN_CHANGED, 'name'=>'notify_when_changed','type'=>'checkbox','tooltip_icon'=>TEXT_NOTIFY_WHEN_CHANGED_TIP);
                
    return $cfg;
  }
  
  static function timestamp_to_jalali($timestamp,$with_hours = false)
  {
      $value = date('Y/m/d' .($with_hours==1 ? ' H:i:s':'') ,$timestamp);
      
      $value = explode(' ',$value);
      
      $date = $value[0];
      $hours = (isset($value[1]) ? $value[1]:'');
      
      $date = explode('/',$date);
      
      return self::gregorian_to_jalali($date[0], $date[1], $date[2]) . (strlen($hours) ? ' ' . $hours : '');
  }
  
  function render($field,$obj,$params = array())
  {
    $cfg =  new fields_types_cfg($field['configuration']);
    
    if(strlen($obj['field_' . $field['id']])>0 and $obj['field_' . $field['id']]!=0)
    {
        $value = self::timestamp_to_jalali($obj['field_' . $field['id']],$cfg->get('time_picker'));                       
    }
    else
    {
        $value = '';
    }
    
    $attributes = array(
        'class'=>'form-control ' . $cfg->get('width') .
        ' fieldtype_input field_' . $field['id'] .
        ($field['is_heading']==1 ? ' autofocus':'') .
        ($field['is_required']==1 ? ' required noSpace':'') .
        ($cfg->get('is_unique')==1 ? ' is-unique':'')
    );
    
    $attributes['data-EnglishNumber']='true';
            
    $html = '
        <div class="input-group input-medium" style="direction: ltr;">
            ' . input_tag('fields[' . $field['id'] . ']',$value,  $attributes ) .'
            <span class="input-group-btn">
                <label class="btn btn-default date-set" type="button"  for="fields_' . $field['id'] . '"><i class="fa fa-calendar"></i></label>
            </span>
        </div>
        ';
    
    $html .= '
        <script>
            $("#fields_' . $field['id'] . '").MdPersianDateTimePicker({EnableTimePicker:' . ($cfg->get('time_picker')==1 ? 'true':'false') . '});
        </script>
        ';
    
    return $html;

  }
  
  static function jalali_date_to_gregorian($value)
  {
      $value = explode(' ',$value);
      
      $date = $value[0];
      $hours = $value[1] ?? '';
      
      $date = explode('/',$date);
      
      return self::jalali_to_gregorian($date[0], $date[1], $date[2]) . (strlen($hours) ? ' ' . $hours : '');
  }
  
  function process($options)
  {      
      $value = (strlen($options['value']) ? $options['value'] : false);
      
      if($value)
      {
          $value = self::jalali_date_to_gregorian($value);
                                         
          $value = (int)get_date_timestamp($value);
      }
                
      return $value;
  }
  
  function output($options)
  {
      $cfg = new fields_types_cfg($options['field']['configuration']);
      
      if($options['value']!=0)
      {
          return self::timestamp_to_jalali($options['value'],$cfg->get('time_picker'));
      }
      else
      {      
          return '';
      }
  }
  
  static function div($a,$b) {
      return (int) ($a / $b);
  } 
  
  static function gregorian_to_jalali ($g_y, $g_m, $g_d,$str=true)
  {
      $g_days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
      $j_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);
      
      
      $gy = $g_y-1600;
      $gm = $g_m-1;
      $gd = $g_d-1;
      
      $g_day_no = 365*$gy+self::div($gy+3,4)-self::div($gy+99,100)+self::div($gy+399,400);
      
      for ($i=0; $i < $gm; ++$i)
          $g_day_no += $g_days_in_month[$i];
          if ($gm>1 && (($gy%4==0 && $gy%100!=0) || ($gy%400==0)))
              /* leap and after Feb */
              $g_day_no++;
              $g_day_no += $gd;
              
              $j_day_no = $g_day_no-79;
              
              $j_np = self::div($j_day_no, 12053); /* 12053 = 365*33 + 32/4 */
              $j_day_no = $j_day_no % 12053;
              
              $jy = 979+33*$j_np+4*self::div($j_day_no,1461); /* 1461 = 365*4 + 4/4 */
              
              $j_day_no %= 1461;
              
              if ($j_day_no >= 366) {
                  $jy += self::div($j_day_no-1, 365);
                  $j_day_no = ($j_day_no-1)%365;
              }
              
              for ($i = 0; $i < 11 && $j_day_no >= $j_days_in_month[$i]; ++$i)
                  $j_day_no -= $j_days_in_month[$i];
                  $jm = $i+1;
                  $jd = $j_day_no+1;
                  
                  if($jm<10) $jm = '0'.$jm;
                  if($jd<10) $jd = '0'.$jd;
                  
                  if($str) return $jy.'/'.$jm.'/'.$jd ;
                  return array($jy, $jm, $jd);
  }
  
  static function jalali_to_gregorian($j_y, $j_m, $j_d,$str=true)
  {
      $g_days_in_month = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
      $j_days_in_month = array(31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, 29);
      
      
      $jy = (int)($j_y)-979;
      $jm = (int)($j_m)-1;
      $jd = (int)($j_d)-1;
      
      $j_day_no = 365*$jy + self::div($jy, 33)*8 + self::div($jy%33+3, 4);
      
      for ($i=0; $i < $jm; ++$i)
          $j_day_no += $j_days_in_month[$i];
          
          $j_day_no += $jd;
          
          $g_day_no = $j_day_no+79;
          
          $gy = 1600 + 400*self::div($g_day_no, 146097); /* 146097 = 365*400 + 400/4 - 400/100 + 400/400 */
          $g_day_no = $g_day_no % 146097;
          
          $leap = true;
          if ($g_day_no >= 36525) /* 36525 = 365*100 + 100/4 */
          {
              $g_day_no--;
              $gy += 100*self::div($g_day_no,  36524); /* 36524 = 365*100 + 100/4 - 100/100 */
              $g_day_no = $g_day_no % 36524;
              
              if ($g_day_no >= 365)
                  $g_day_no++;
                  else
                      $leap = false;
          }
          
          $gy += 4*self::div($g_day_no, 1461); /* 1461 = 365*4 + 4/4 */
          $g_day_no %= 1461;
          
          if ($g_day_no >= 366) {
              $leap = false;
              
              $g_day_no--;
              $gy += self::div($g_day_no, 365);
              $g_day_no = $g_day_no % 365;
          }
          
          for ($i = 0; $g_day_no >= $g_days_in_month[$i] + ($i == 1 && $leap); $i++)
              $g_day_no -= $g_days_in_month[$i] + ($i == 1 && $leap);
              $gm = $i+1;
              $gd = $g_day_no+1;
              
              if($gm<10) $gm = '0'.$gm;
              if($gd<10) $gd = '0'.$gd;
                  
              if($str) return $gy.'-'.$gm.'-'.$gd ;
              return array($gy, $gm, $gd);
  } 
  
  function reports_query($options)
  {
      $filters = $options['filters'];
      $sql_query = $options['sql_query'];
      
      $sql = reports::prepare_dates_sql_filters($filters,$options['prefix']);
      
      if(count($sql)>0)
      {
          $sql_query[] =  implode(' and ', $sql);
      }
      
      //print_rr($sql_query);
      
      return $sql_query;
  }
}