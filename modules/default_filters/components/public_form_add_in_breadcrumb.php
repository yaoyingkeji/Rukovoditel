<?php

$public_form = db_find('app_ext_public_forms',str_replace('public_form_add_in','',$app_redirect_to));

$breadcrumb = array();

$breadcrumb[] = '<li>' . link_to(TEXT_EXT_PUBLIC_FORMS,url_for('ext/public_forms/public_forms')) . '<i class="fa fa-angle-right"></i></li>';

$breadcrumb[] = '<li>' . $public_form['name'] . '<i class="fa fa-angle-right"></i></li>';

$breadcrumb[] = '<li>' . TEXT_FORM_ADD_IN . '<i class="fa fa-angle-right"></i></li>';

$breadcrumb[] = '<li>' . $entity_info['name'] . '<i class="fa fa-angle-right"></i></li>';

$breadcrumb[] = '<li>' . TEXT_FILTERS . '</li>';

