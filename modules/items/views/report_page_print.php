<?php
    $page = new report_page\report($report_info);
    $page->set_item($current_entity_id,$current_item_id);
    echo $page->get_html();
    
    echo ($report_info['page_orientation']=='landscape' ? '<style type="text/css" media="print"> @page { size: landscape; } </style>':'');
?>

<script>
$(function(){
    window.print()
})
</script>

