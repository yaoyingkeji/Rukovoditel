<?php
$form_entity = db_find('app_entities',str_replace('form_add_in','',$app_redirect_to));

$breadcrumb = array();

$breadcrumb[] = '<li>' . $form_entity['name'] . '<i class="fa fa-angle-right"></i></li>';

$breadcrumb[] = '<li>' . link_to(TEXT_NAV_FORM_CONFIG,url_for('entities/forms&entities_id=' . $form_entity['id'])) . '<i class="fa fa-angle-right"></i></li>';

$breadcrumb[] = '<li>' . TEXT_FORM_ADD_IN . '<i class="fa fa-angle-right"></i></li>';

$breadcrumb[] = '<li>' . $entity_info['name'] . '<i class="fa fa-angle-right"></i></li>';

$breadcrumb[] = '<li>' . TEXT_FILTERS . '</li>';

$page_description = TEXT_FORM_ADD_IN_FILTERS_TIP;
