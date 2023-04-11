<?php

//to create temporarry attachemts to pervio outside
class attachments_viewer 
{
    private $file;
    
    function __construct($file)
    {
        $this->file = $file;
    }
    
    function get_url()
    {
        $tmp_file = urlencode($this->generate_tmp_file());
        
        $tmp_file_url = url_for_file($tmp_file);
        
        switch(CFG_SERVICE_DOCX_PREVIEW)
        {
            case 'docs.google.com':
                $url = 'https://docs.google.com/gview?embedded=true&url=' . $tmp_file_url;
                break;
            case 'docs.yandex.ru':
                $url = 'https://docviewer.yandex.ru/?url=' . $tmp_file_url;
                break;
            case 'microsoft.com':
                $url = 'https://view.officeapps.live.com/op/embed.aspx?src=' . $tmp_file_url;
                break;
        }
        
        return $url;
    }
    
    function generate_tmp_file()
    {
        if(!is_dir(DIR_WS_UPLOADS . 'tmp'))
        {
            mkdir(DIR_WS_UPLOADS . 'tmp');
        }
        
        $today = date('Ymd');
        
        if(!is_dir(DIR_WS_UPLOADS . 'tmp/' . $today))
        {
            mkdir(DIR_WS_UPLOADS . 'tmp/' . $today);
        }
        
        $timestamp = explode('_',$this->file['file'])[0];
        
        if(!is_dir(DIR_WS_UPLOADS . 'tmp/' . $today . '/' . $timestamp))
        {
            mkdir(DIR_WS_UPLOADS . 'tmp/' . $today . '/' . $timestamp);
        }
        
        $url = DIR_WS_UPLOADS . 'tmp/' . $today . '/' . $timestamp . '/' . $this->file['name'];
        
        copy($this->file['file_path'],$url);
        
        return $url;
    }
    
    static function reset_tmp_files()
    {
        $dir = DIR_WS_UPLOADS . 'tmp';
        
        if(is_dir($dir) and $dh = opendir($dir))
        {
            while(($file = readdir($dh)) !== false)
            {
                $delete_dir = true;
                
                if($file!='.' and $file!='..' and filetype($dir .'/'. $file)=='dir' and $dh2 = opendir($dir .'/'. $file))
                {
                    while(($file2 = readdir($dh2)) !== false)
                    {
                        if($file2!='.' and $file2!='..' and  filetype($dir .'/'.  $file .'/'.  $file2)=='dir' and $dh3 = opendir($dir .'/'.  $file .'/'.  $file2))
                        {
                            while(($file3 = readdir($dh3)) !== false)
                            {
                                if($file3!='.' and $file3!='..' and filetype($dir . '/'. $file . '/'. $file2 . '/'. $file3)=='file' )
                                {
                                    $filepath = $dir .'/'. $file . '/'. $file2 . '/'. $file3;
                                    if(time() - filemtime($filepath)>600)
                                    {
                                        unlink($filepath);
                                    }
                                    else
                                    {
                                       $delete_dir = false; 
                                    }
                                    
                                }
                            }
                            
                            if($delete_dir)
                            {
                                rmdir($dir .'/'.  $file .'/'.  $file2);
                            }
                            
                            closedir($dh3);            
                        }
                    }
                    
                    if($delete_dir)
                    {
                        rmdir($dir .'/'.  $file);
                    }
                                                            
                    closedir($dh2);            
                }                                
            }
                
            closedir($dh);            
        }
    }

    static function get_service_choices()
    {
        $choices = [
            '' => '',
            'microsoft.com' => 'microsoft.com',
            'docs.google.com' => 'docs.google.com',
            'docs.yandex.ru' => 'docs.yandex.ru',            
        ];
        
        return $choices;
    }
}
