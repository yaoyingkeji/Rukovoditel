<?php

$report_info = db_find('app_ext_calendar',str_replace('calendar_reminder','',$app_redirect_to));

$breadcrumb = array();

$breadcrumb[] = '<li>' . link_to(TEXT_EXT_CALENDAR_REPORT,url_for('ext/calendar/configuration_reports')) . '<i class="fa fa-angle-right"></i></li>';

$breadcrumb[] = '<li>' . $report_info['name'] . '<i class="fa fa-angle-right"></i></li>';

$breadcrumb[] = '<li>' . $entity_info['name'] . '<i class="fa fa-angle-right"></i></li>';

$breadcrumb[] = '<li>' . TEXT_FILTERS . '</li>';

