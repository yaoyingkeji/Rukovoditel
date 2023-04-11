<?php

if(is_ext_installed() and $current_entity_id and $current_item_id)
{
    $position = str_replace('item_info_mail_','',$app_redirect_to);
    $mail_related = new mail_related($current_entity_id, $position);
    echo  $mail_related -> render_list($current_item_id);
}

exit();
