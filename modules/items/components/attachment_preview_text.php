<?php

$content = file_get_contents($file['file_path']);

$content = mb_convert_encoding($content,'utf-8',['windows-1251','utf-8']);

$html = '
    <div class="attachment-previw-window-text' . (is_mobile() ? '-mobile':''). '">
        <textarea id="text_preview" class="form-control select-all" readonly="readonly">' . htmlspecialchars($content) . '</textarea>
        
    </div>
    ';

//resize textarea
if(count(explode("\n",$content))>23)
{        
    $html .= '    
        <script>    
            $("#text_preview").css("height",($(window).height()-200)+"px")
        </script>
    ';
}

//add controls
$html .= '
        <div id="img_previw_menu_box" style="display:none">
            <div class="img-preview-menu">                
                <a title="' . TEXT_DOWNLOAD . '" class="btn btn-default" href="' . $download_url . '"><i class="las la-download"></i></a>
                <a title="' . TEXT_COPY . '" class="btn btn-default btn-copy" href="#"><i class="las fa-files-o"></i></a>
            </div>
        </div>
    <script>
        $(function(){
            $(".fancybox-inner").before($("#img_previw_menu_box").html())
            $("#img_previw_menu_box").remove();
            
            $(".btn-copy").click(function(){
                $("#text_preview").select()
                document.execCommand("copy");
                
                $(this).attr("title","' . addslashes(TEXT_TEXT_COPIED). '")
                $(this).tooltip({placement:"right"}).tooltip("show")
                
                setTimeout(function(){                    
                    $(".btn-copy").tooltip("hide")
                },1000)
                                
                return false;
            })  
            
            if(is_mobile)
            {
                $("#text_preview").css("width",($(window).width()-100)+"px").css("height",($(window).height()-150)+"px")
            }
        })                
    </script>    
    ';


echo $html;

