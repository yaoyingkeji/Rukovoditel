<?php

$app_user = [
    'id'=>0,
    'group_id'=>0,
    'email'=>CFG_EMAIL_ADDRESS_FROM,
    'name'=>CFG_EMAIL_NAME_FROM,
];

$templates_query = db_query("select * from app_ext_xml_export_templates where id='" . _get::int('id') . "' and is_public=1 and is_active=1");
if(!$templates = db_fetch_array($templates_query))
{
	die(TEXT_PAGE_NOT_FOUND_HEADING);
}
	
$xml_export = new xml_export($templates['id']);
$xml_export->export();

exit();	