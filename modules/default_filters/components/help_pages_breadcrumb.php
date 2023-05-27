<?php

$page_info = db_find('app_help_pages',str_replace('help_pages','',$app_redirect_to));

$breadcrumb = array();

$breadcrumb[] = '<li>' . link_to(TEXT_HELP_SYSTEM,url_for('help_pages/pages','entities_id=' . $page_info['entities_id'])) . '<i class="fa fa-angle-right"></i></li>';

$breadcrumb[] = '<li>' . $entity_info['name'] . '<i class="fa fa-angle-right"></i></li>';

$breadcrumb[] = '<li>' . (strlen($page_info['name']) ? $page_info['name'] : app_truncate_text($page_info['description'])) . '<i class="fa fa-angle-right"></i></li>';



$breadcrumb[] = '<li>' . TEXT_FILTERS . '</li>';

