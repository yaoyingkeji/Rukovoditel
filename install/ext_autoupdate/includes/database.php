<?php

  function db_connect($server, $username, $password,$database,$port, $link = 'db_link',$params = array()) {    
    global $$link;
      
    $$link = mysqli_init();
    
    if (!$$link) {
        db_error('mysqli_init failed',$params);
    }

    if (!mysqli_options($$link, MYSQLI_OPT_CONNECT_TIMEOUT, 5)) {
        db_error('Setting MYSQLI_OPT_CONNECT_TIMEOUT failed',$params);
    }
    
    if (!mysqli_options($$link, MYSQLI_INIT_COMMAND, 'SET NAMES utf8')) {
        db_error('Setting MYSQLI_INIT_COMMAND failed',$params);
    }
    
    if(strlen($port))
    {
    	if (!@mysqli_real_connect($$link, $server, $username, $password, $database, $port)) {
    		db_error('Error: (' . mysqli_connect_errno() . ') ' . mysqli_connect_error(),$params);
    	}
    }
    else
    {
    	if (!@mysqli_real_connect($$link, $server, $username, $password, $database)) {
    		db_error('Error: (' . mysqli_connect_errno() . ') ' . mysqli_connect_error(),$params);
    	}
    }
    
    db_query("SET sql_mode = ''");

    return $$link;    
  }
    

  function db_query($query, $link = 'db_link') {
    global $$link;
    
    $result = mysqli_query($$link, $query ) or die($query . '<br>' . mysqli_errno($$link) . '<br>' . mysqli_error($$link));
            
    return $result;
  }
  
  function db_query_from_content($content)
  {
    $content = explode(';',$content);
    
    foreach($content as $query)
    {
      if(strlen(trim($query))>0)
      {
        db_query(trim($query));
      }
    }
  }
  
  function db_fetch_array($result) 
  {
    return mysqli_fetch_array($result, MYSQLI_ASSOC);
  }
  
  
  function db_error($error,$params = array())
  {
    die('<div class="alert alert-danger">' . $error . '</div>');    
  }
  
  function db_input($string, $link = 'db_link') {
    global $$link;
                                                        
    if (function_exists('mysqli_real_escape_string')) {
      return mysqli_real_escape_string($$link,$string);
    } elseif (function_exists('mysqli_escape_string')) {
      return mysqli_escape_string($$link,$string);
    }

    return addslashes($string);
  }  
