<?php
  
  $ch = curl_init();
  
  curl_setopt($ch, CURLOPT_URL, "https://www.rukovoditel.net/current_version/version.txt");  
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
  curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
  curl_setopt($ch, CURLOPT_TIMEOUT, 3);  
  $response = curl_exec($ch);
  curl_close($ch);
      
  if(strlen($response)>0 and strlen($response)<10)
  {  	  
  	$app_current_version = $response;
  }
  else
  {      
      $app_current_version = PROJECT_VERSION;
  }    
    
  
  
  