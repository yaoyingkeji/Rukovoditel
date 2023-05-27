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
    
    try 
    {
        if(strlen($port))
        {
            mysqli_real_connect($$link, $server, $username, $password, $database, $port);
        }
        else
        {
            mysqli_real_connect($$link, $server, $username, $password, $database);
        }
    }
    catch (mysqli_sql_exception $e) 
    {
        $error = 'Database Error: ' . $e->getCode() . ' - ' . htmlspecialchars($e->getMessage());
        header('Location: index.php?step=database_config&db_error=' . urlencode($error) . '&lng=' .(isset($params['lng']) ? $params['lng']:''). '&params=' . base64_encode(json_encode($params)));
        exit();
    }
           
    db_query("SET sql_mode = ''");

    return $$link;    
  }

  function db_query($query, $link = 'db_link') {
    global $$link;
    
    $result = mysqli_query($$link, $query ) or die($query . '<br>' . mysqli_errno($$link) . '<br>' . mysqli_error($$link));
            
    return $result;
  }
  
  function db_fetch_array($result) 
  {
    return mysqli_fetch_array($result, MYSQLI_ASSOC);
  }
  
  
  function db_error($error,$params = array())
  {
    header('Location: index.php?step=database_config&db_error=' . urlencode($error) . '&lng=' .(isset($params['lng']) ? $params['lng']:''). '&params=' . base64_encode(json_encode($params)));
    exit();
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
