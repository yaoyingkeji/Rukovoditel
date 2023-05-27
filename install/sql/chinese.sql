CREATE TABLE IF NOT EXISTS `app_access_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `is_default` tinyint(1) DEFAULT NULL,
  `is_ldap_default` tinyint(1) DEFAULT NULL,
  `ldap_filter` text NOT NULL,
  `sort_order` int(11) NOT NULL,
  `notes` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4;

INSERT INTO app_access_groups VALUES
('4','经理','1','0','','2',''),
('5','开发者','0','0','','1',''),
('6','客户','0','0','','0','');

CREATE TABLE IF NOT EXISTS `app_access_rules` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entities_id` int(10) unsigned NOT NULL,
  `fields_id` int(10) unsigned NOT NULL,
  `choices` text NOT NULL,
  `users_groups` text NOT NULL,
  `access_schema` text NOT NULL,
  `fields_view_only_access` text NOT NULL,
  `comments_access_schema` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_fields_id` (`fields_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_access_rules_fields` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entities_id` int(10) unsigned NOT NULL,
  `fields_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_fields_id` (`fields_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_approved_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `items_id` int(11) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `signature` text NOT NULL,
  `date_added` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_items_id` (`items_id`),
  KEY `idx_fields_id` (`fields_id`),
  KEY `idx_users_id` (`users_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_attachments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `form_token` varchar(64) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `date_added` date NOT NULL,
  `container` varchar(16) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_backups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL,
  `is_auto` tinyint(1) NOT NULL,
  `description` text NOT NULL,
  `filename` varchar(64) NOT NULL,
  `date_added` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_users_id` (`users_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `items_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `description` text NOT NULL,
  `attachments` text NOT NULL,
  `date_added` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_items_id` (`items_id`),
  KEY `idx_created_by` (`created_by`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_comments_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `access_groups_id` int(11) NOT NULL,
  `access_schema` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_access_groups_id` (`access_groups_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4;

INSERT INTO app_comments_access VALUES
('4','21','6','view,create'),
('5','21','5','view,create'),
('6','21','4','view,create,update,delete'),
('7','22','5','view,create'),
('8','22','4','view,create,update,delete'),
('9','23','6','view,create'),
('10','23','4','view,create,update,delete'),
('11','24','5','view,create'),
('12','24','4','view,create,update,delete');

CREATE TABLE IF NOT EXISTS `app_comments_forms_tabs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_comments_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `comments_id` int(11) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `fields_value` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_comments_id` (`comments_id`),
  KEY `idx_fields_id` (`fields_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_configuration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `configuration_name` varchar(255) NOT NULL,
  `configuration_value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_custom_php` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `is_folder` tinyint(1) NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` longtext NOT NULL,
  `notes` text NOT NULL,
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_dashboard_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) NOT NULL,
  `sections_id` int(11) NOT NULL,
  `type` varchar(16) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `name` varchar(255) NOT NULL,
  `icon` varchar(64) NOT NULL,
  `description` text NOT NULL,
  `color` varchar(16) NOT NULL,
  `users_fields` text NOT NULL,
  `users_groups` text NOT NULL,
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_created_by` (`created_by`),
  KEY `idx_sections_id` (`sections_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_dashboard_pages_sections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `grid` tinyint(1) NOT NULL,
  `sort_order` smallint(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_emails_on_schedule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_added` bigint(20) unsigned NOT NULL,
  `email_to` varchar(255) NOT NULL,
  `email_to_name` varchar(255) NOT NULL,
  `email_subject` varchar(255) NOT NULL,
  `email_body` text NOT NULL,
  `email_from` varchar(255) NOT NULL,
  `email_from_name` varchar(255) NOT NULL,
  `email_attachments` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_entities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `notes` text NOT NULL,
  `display_in_menu` tinyint(1) DEFAULT 0,
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_group_id` (`group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4;

INSERT INTO app_entities VALUES
('1','0','0','用户','','0','10'),
('21','0','0','项目','','0','1'),
('22','21','0','任务','','0','1'),
('23','21','0','技术支持','','0','2'),
('24','21','0','讨论','','0','3');

CREATE TABLE IF NOT EXISTS `app_entities_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `access_groups_id` int(11) NOT NULL,
  `access_schema` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_access_groups_id` (`access_groups_id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4;

INSERT INTO app_entities_access VALUES
('28','21','6','view_assigned'),
('29','21','5','view_assigned,reports'),
('30','21','4','view,create,update,delete,reports'),
('31','22','6',''),
('32','22','5','view,create,update,reports'),
('33','22','4','view,create,update,delete,reports'),
('34','23','6','view_assigned,create,update,reports'),
('35','23','5',''),
('36','23','4','view,create,update,delete,reports'),
('37','24','6',''),
('38','24','5','view_assigned,create,update,delete,reports'),
('39','24','4','view,create,update,delete,reports');

CREATE TABLE IF NOT EXISTS `app_entities_configuration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `configuration_name` varchar(255) NOT NULL,
  `configuration_value` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4;

INSERT INTO app_entities_configuration VALUES
('11','1','menu_title','用户'),
('12','1','listing_heading','用户'),
('13','1','window_heading','用户信息'),
('14','1','insert_button','添加用户'),
('15','1','use_comments','0'),
('25','21','menu_title',' 项目'),
('26','21','listing_heading','项目'),
('27','21','window_heading','项目信息'),
('28','21','insert_button','添加项目'),
('29','21','email_subject_new_item','新项目:'),
('30','21','use_comments','1'),
('31','21','email_subject_new_comment','新建项目注释:'),
('32','22','menu_title','任务'),
('33','22','listing_heading','任务'),
('34','22','window_heading','任务信息'),
('35','22','insert_button','添加任务'),
('36','22','email_subject_new_item','新任务'),
('37','22','use_comments','1'),
('38','22','email_subject_new_comment','新建任务注释:'),
('39','23','menu_title','技术支持'),
('40','23','listing_heading','技术支持'),
('41','23','window_heading','技术支持信息'),
('42','23','insert_button','添加技术支持'),
('43','23','email_subject_new_item','新技术支持:'),
('44','23','use_comments','1'),
('45','23','email_subject_new_comment','新建技术支持注释'),
('46','24','menu_title','讨论'),
('47','24','listing_heading','讨论'),
('48','24','window_heading','讨论信息'),
('49','24','insert_button','添加讨论'),
('50','24','email_subject_new_item','新讨论:'),
('51','24','use_comments','1'),
('52','24','email_subject_new_comment','新建讨论注释:'),
('53','21','use_editor_in_comments','0'),
('54','22','use_editor_in_comments','0'),
('55','23','use_editor_in_comments','0'),
('56','24','use_editor_in_comments','0');

CREATE TABLE IF NOT EXISTS `app_entities_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `sort_order` smallint(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_entities_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `icon` varchar(64) NOT NULL,
  `icon_color` varchar(7) NOT NULL,
  `bg_color` varchar(7) NOT NULL,
  `entities_list` text NOT NULL,
  `reports_list` text NOT NULL,
  `pages_list` text NOT NULL,
  `type` varchar(16) DEFAULT 'entity',
  `url` varchar(255) NOT NULL,
  `users_groups` text NOT NULL,
  `assigned_to` text NOT NULL,
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_entity_1` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `parent_id` int(11) NOT NULL DEFAULT 0,
  `parent_item_id` int(11) NOT NULL DEFAULT 0,
  `linked_id` int(11) NOT NULL DEFAULT 0,
  `date_added` bigint(20) NOT NULL DEFAULT 0,
  `date_updated` bigint(20) NOT NULL DEFAULT 0,
  `created_by` int(11) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `password` varchar(255) NOT NULL,
  `multiple_access_groups` varchar(64) NOT NULL,
  `is_email_verified` tinyint(1) NOT NULL DEFAULT 1,
  `field_5` tinyint(1) NOT NULL,
  `field_6` int(11) NOT NULL,
  `field_7` varchar(255) NOT NULL,
  `field_8` varchar(255) NOT NULL,
  `field_9` varchar(255) NOT NULL,
  `field_10` varchar(255) NOT NULL,
  `field_12` varchar(255) NOT NULL,
  `field_13` varchar(64) NOT NULL,
  `field_14` varchar(64) NOT NULL,
  `field_201` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_parent_id` (`parent_id`),
  KEY `idx_parent_item_id` (`parent_item_id`),
  KEY `idx_client_id` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_entity_1_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `items_id` int(11) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `value` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_items_id` (`items_id`),
  KEY `idx_fields_id` (`fields_id`),
  KEY `idx_items_fields_id` (`items_id`,`fields_id`),
  KEY `idx_value_id` (`value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_entity_21` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT 0,
  `parent_item_id` int(11) DEFAULT 0,
  `linked_id` int(11) DEFAULT 0,
  `date_added` bigint(20) NOT NULL DEFAULT 0,
  `date_updated` bigint(20) NOT NULL DEFAULT 0,
  `created_by` int(11) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `field_156` text NOT NULL,
  `field_157` text NOT NULL,
  `field_158` text NOT NULL,
  `field_159` bigint(20) NOT NULL DEFAULT 0,
  `field_160` text NOT NULL,
  `field_161` text NOT NULL,
  `field_162` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_parent_id` (`parent_id`),
  KEY `idx_parent_item_id` (`parent_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_entity_21_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `items_id` int(11) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `value` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_items_id` (`items_id`),
  KEY `idx_fields_id` (`fields_id`),
  KEY `idx_items_fields_id` (`items_id`,`fields_id`),
  KEY `idx_value_id` (`value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_entity_22` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT 0,
  `parent_item_id` int(11) DEFAULT 0,
  `linked_id` int(11) DEFAULT 0,
  `date_added` bigint(20) NOT NULL DEFAULT 0,
  `date_updated` bigint(20) NOT NULL DEFAULT 0,
  `created_by` int(11) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `field_167` text NOT NULL,
  `field_168` text NOT NULL,
  `field_169` text NOT NULL,
  `field_170` text NOT NULL,
  `field_171` text NOT NULL,
  `field_172` text NOT NULL,
  `field_173` varchar(64) NOT NULL,
  `field_174` varchar(64) NOT NULL,
  `field_175` bigint(20) NOT NULL DEFAULT 0,
  `field_176` bigint(20) NOT NULL DEFAULT 0,
  `field_177` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_parent_id` (`parent_id`),
  KEY `idx_parent_item_id` (`parent_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_entity_22_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `items_id` int(11) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `value` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_items_id` (`items_id`),
  KEY `idx_fields_id` (`fields_id`),
  KEY `idx_items_fields_id` (`items_id`,`fields_id`),
  KEY `idx_value_id` (`value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_entity_23` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT 0,
  `parent_item_id` int(11) DEFAULT 0,
  `linked_id` int(11) DEFAULT 0,
  `date_added` bigint(20) NOT NULL DEFAULT 0,
  `date_updated` bigint(20) NOT NULL DEFAULT 0,
  `created_by` int(11) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `field_182` text NOT NULL,
  `field_183` text NOT NULL,
  `field_184` text NOT NULL,
  `field_185` text NOT NULL,
  `field_186` text NOT NULL,
  `field_194` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_parent_id` (`parent_id`),
  KEY `idx_parent_item_id` (`parent_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_entity_23_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `items_id` int(11) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `value` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_items_id` (`items_id`),
  KEY `idx_fields_id` (`fields_id`),
  KEY `idx_items_fields_id` (`items_id`,`fields_id`),
  KEY `idx_value_id` (`value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_entity_24` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT 0,
  `parent_item_id` int(11) DEFAULT 0,
  `linked_id` int(11) DEFAULT 0,
  `date_added` bigint(20) NOT NULL DEFAULT 0,
  `date_updated` bigint(20) NOT NULL DEFAULT 0,
  `created_by` int(11) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `field_191` text NOT NULL,
  `field_192` text NOT NULL,
  `field_193` text NOT NULL,
  `field_195` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_parent_id` (`parent_id`),
  KEY `idx_parent_item_id` (`parent_item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_entity_24_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `items_id` int(11) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `value` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_items_id` (`items_id`),
  KEY `idx_fields_id` (`fields_id`),
  KEY `idx_items_fields_id` (`items_id`,`fields_id`),
  KEY `idx_value_id` (`value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_favorites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL,
  `entities_id` int(11) NOT NULL,
  `items_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_users_id` (`users_id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_items_Id` (`items_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `forms_tabs_id` int(11) NOT NULL,
  `comments_forms_tabs_id` int(11) NOT NULL DEFAULT 0,
  `forms_rows_position` varchar(255) NOT NULL,
  `type` varchar(64) NOT NULL,
  `name` varchar(255) NOT NULL,
  `short_name` varchar(64) NOT NULL,
  `is_heading` tinyint(1) DEFAULT 0,
  `tooltip` text NOT NULL,
  `tooltip_display_as` varchar(16) NOT NULL,
  `tooltip_in_item_page` tinyint(1) NOT NULL DEFAULT 0,
  `tooltip_item_page` text NOT NULL,
  `notes` text NOT NULL,
  `is_required` tinyint(1) DEFAULT 0,
  `required_message` text NOT NULL,
  `configuration` text NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `listing_status` tinyint(4) NOT NULL DEFAULT 0,
  `listing_sort_order` int(11) NOT NULL DEFAULT 0,
  `comments_status` tinyint(1) NOT NULL DEFAULT 0,
  `comments_sort_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_form_tabs_id` (`forms_tabs_id`),
  KEY `idx_comments_forms_tabs_id` (`comments_forms_tabs_id`),
  KEY `idx_type` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=207 DEFAULT CHARSET=utf8mb4;

INSERT INTO app_fields VALUES
('1','1','1','0','','fieldtype_action','','',NULL,'','','0','','',NULL,'','',NULL,'1','0','0','0'),
('2','1','1','0','','fieldtype_id','','',NULL,'','','0','','',NULL,'','',NULL,'1','1','0','0'),
('3','1','1','0','','fieldtype_date_added','','',NULL,'','','0','','',NULL,'','',NULL,'0','0','0','0'),
('4','1','1','0','','fieldtype_created_by','','',NULL,'','','0','','',NULL,'','',NULL,'0','0','0','0'),
('5','1','1','0','','fieldtype_user_status','','',NULL,'','','0','','',NULL,'','','0','1','7','0','0'),
('6','1','1','0','','fieldtype_user_accessgroups','','',NULL,'','','0','','',NULL,'','','1','1','2','0','0'),
('7','1','1','0','','fieldtype_user_firstname','','',NULL,'','','0','','',NULL,'','{\"allow_search\":\"1\"}','3','1','4','0','0'),
('8','1','1','0','','fieldtype_user_lastname','','',NULL,'','','0','','',NULL,'','{\"allow_search\":\"1\"}','4','1','5','0','0'),
('9','1','1','0','','fieldtype_user_email','','',NULL,'','','0','','',NULL,'','{\"allow_search\":\"1\"}','6','1','6','0','0'),
('10','1','1','0','','fieldtype_user_photo','','',NULL,'','','0','','',NULL,'','','5','0','0','0','0'),
('12','1','1','0','','fieldtype_user_username','','','1','','','0','','',NULL,'','{\"allow_search\":\"1\"}','2','1','3','0','0'),
('13','1','1','0','','fieldtype_user_language','','','0','','','0','','','0','','','7','0','0','0','0'),
('14','1','1','0','','fieldtype_user_skin','','','0','','','0','','','0','','','0','0','0','0','0'),
('152','21','24','0','','fieldtype_action','','','0','','','0','','','0','','','0','1','0','0','0'),
('153','21','24','0','','fieldtype_id','','','0','','','0','','','0','','','0','1','1','0','0'),
('154','21','24','0','','fieldtype_date_added','','','0','','','0','','','0','','','0','1','6','0','0'),
('155','21','24','0','','fieldtype_created_by','','','0','','','0','','','0','','','0','1','7','0','0'),
('156','21','24','0','','fieldtype_dropdown','优先事项','','0','','','0','','','1','','{\"width\":\"input-medium\"}','0','1','2','1','0'),
('157','21','24','0','','fieldtype_dropdown','状态','','0','','','0','','','1','','{\"width\":\"input-medium\"}','1','1','4','1','1'),
('158','21','24','0','','fieldtype_input','名字','','1','','','0','','','1','','{\"allow_search\":\"1\",\"width\":\"input-xlarge\"}','2','1','3','0','0'),
('159','21','24','0','','fieldtype_input_date','开始日期','','0','','','0','','','0','','','3','1','5','0','0'),
('160','21','24','0','','fieldtype_textarea_wysiwyg','描述','','0','','','0','','','0','','{\"allow_search\":\"1\"}','4','0','0','0','0'),
('161','21','25','0','','fieldtype_users','团队成员','','0','','','0','','','0','','{\"display_as\":\"checkboxes\"}','0','0','0','0','0'),
('162','21','24','0','','fieldtype_attachments','附件','','0','','','0','','','0','','','5','0','0','0','0'),
('163','22','26','0','','fieldtype_action','','','0','','','0','','','0','','','0','1','0','0','0'),
('164','22','26','0','','fieldtype_id','','','0','','','0','','','0','','','0','1','1','0','0'),
('165','22','26','0','','fieldtype_date_added','','','0','','','0','','','0','','','0','1','10','0','0'),
('166','22','26','0','','fieldtype_created_by','','','0','','','0','','','0','','','0','1','11','0','0'),
('167','22','26','0','','fieldtype_dropdown','类别','','0','','','0','','','1','','{\"width\":\"input-medium\"}','1','1','3','0','0'),
('168','22','26','0','','fieldtype_input','名字','','1','','','0','','','1','','{\"allow_search\":\"1\",\"width\":\"input-xlarge\"}','2','1','4','0','0'),
('169','22','26','0','','fieldtype_dropdown','状态','','0','','','0','','','1','','{\"width\":\"input-large\"}','3','1','5','1','0'),
('170','22','26','0','','fieldtype_dropdown','优先事项','','0','','','0','','','1','','{\"width\":\"input-medium\"}','4','1','2','1','1'),
('171','22','26','0','','fieldtype_users','指派给','','0','','','0','','','0','','{\"display_as\":\"checkboxes\"}','5','1','6','0','0'),
('172','22','26','0','','fieldtype_textarea_wysiwyg','描述','','0','','','0','','','0','','{\"allow_search\":\"1\"}','6','0','0','0','0'),
('173','22','27','0','','fieldtype_input_numeric','估计用时','','0','','','0','','','0','','{\"width\":\"input-small\",\"number_format\":\"2/./*\"}','1','1','7','0','0'),
('174','22','27','0','','fieldtype_input_numeric_comments','所用用时','','0','','','0','','','0','','','2','1','8','1','2'),
('175','22','27','0','','fieldtype_input_date','开始日期','','0','','','0','','','0','','','3','0','0','0','0'),
('176','22','27','0','','fieldtype_input_date','结束日期','','0','','','0','','','0','','','4','1','9','0','0'),
('177','22','26','0','','fieldtype_attachments','附件','','0','','','0','','','0','','','7','0','0','0','0'),
('178','23','28','0','','fieldtype_action','','','0','','','0','','','0','','','0','1','0','0','0'),
('179','23','28','0','','fieldtype_id','','','0','','','0','','','0','','','0','1','1','0','0'),
('180','23','28','0','','fieldtype_date_added','','','0','','','0','','','0','','','0','1','6','0','0'),
('181','23','28','0','','fieldtype_created_by','','','0','','','0','','','0','','','0','1','7','0','0'),
('182','23','28','0','','fieldtype_grouped_users','部门','','0','','','0','','','1','','','0','1','4','1','0'),
('183','23','28','0','','fieldtype_dropdown','类型','','0','','','0','','','1','','{\"width\":\"input-large\"}','2','1','2','1','1'),
('184','23','28','0','','fieldtype_input','主题','','1','','','0','','','1','','{\"allow_search\":\"1\",\"width\":\"input-xlarge\"}','3','1','3','0','0'),
('185','23','28','0','','fieldtype_textarea_wysiwyg','描述','','0','','','0','','','0','','{\"allow_search\":\"1\"}','4','0','0','0','0'),
('186','23','28','0','','fieldtype_dropdown','状态','','0','','','0','','','1','','{\"width\":\"input-large\"}','1','1','5','1','2'),
('187','24','29','0','','fieldtype_action','','','0','','','0','','','0','','','0','1','0','0','0'),
('188','24','29','0','','fieldtype_id','','','0','','','0','','','0','','','0','1','1','0','0'),
('189','24','29','0','','fieldtype_date_added','','','0','','','0','','','0','','','0','1','4','0','0'),
('190','24','29','0','','fieldtype_created_by','','','0','','','0','','','0','','','0','1','5','0','0'),
('191','24','29','0','','fieldtype_input','名字','','1','','','0','','','1','','{\"allow_search\":\"1\",\"width\":\"input-xlarge\"}','1','1','3','0','0'),
('192','24','29','0','','fieldtype_textarea_wysiwyg','描述','','0','','','0','','','0','','{\"allow_search\":\"1\"}','2','0','0','0','0'),
('193','24','29','0','','fieldtype_dropdown','状态','','0','','','0','','','0','','{\"width\":\"input-medium\"}','0','1','2','1','0'),
('194','23','28','0','','fieldtype_attachments','附件','','0','','','0','','','0','','','5','0','0','0','0'),
('195','24','29','0','','fieldtype_attachments','附件','','0','','','0','','','0','','','3','0','0','0','0'),
('196','1','1','0','','fieldtype_parent_item_id','','',NULL,'','','0','','',NULL,'','',NULL,'1','100','0','0'),
('197','21','24','0','','fieldtype_parent_item_id','','',NULL,'','','0','','',NULL,'','',NULL,'1','100','0','0'),
('198','22','26','0','','fieldtype_parent_item_id','','',NULL,'','','0','','',NULL,'','',NULL,'1','100','0','0'),
('199','23','28','0','','fieldtype_parent_item_id','','',NULL,'','','0','','',NULL,'','',NULL,'1','100','0','0'),
('200','24','29','0','','fieldtype_parent_item_id','','',NULL,'','','0','','',NULL,'','',NULL,'1','100','0','0'),
('201','1','1','0','','fieldtype_user_last_login_date','','','0','','','0','','','0','','','0','0','0','0','0'),
('202','1','1','0','','fieldtype_date_updated','','','0','','','0','','','0','','','3','0','0','0','0'),
('203','21','24','0','','fieldtype_date_updated','','','0','','','0','','','0','','','3','0','0','0','0'),
('204','22','26','0','','fieldtype_date_updated','','','0','','','0','','','0','','','3','0','0','0','0'),
('205','23','28','0','','fieldtype_date_updated','','','0','','','0','','','0','','','3','0','0','0','0'),
('206','24','29','0','','fieldtype_date_updated','','','0','','','0','','','0','','','3','0','0','0','0');

CREATE TABLE IF NOT EXISTS `app_fields_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `access_groups_id` int(11) NOT NULL,
  `entities_id` int(11) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `access_schema` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_fields_id` (`fields_id`),
  KEY `idx_access_groups_id` (`access_groups_id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_fields_choices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT 0,
  `fields_id` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `name` varchar(255) NOT NULL,
  `is_default` tinyint(1) DEFAULT NULL,
  `bg_color` varchar(16) NOT NULL,
  `sort_order` int(11) DEFAULT NULL,
  `users` text NOT NULL,
  `value` varchar(64) NOT NULL,
  `filename` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_fields_id` (`fields_id`),
  KEY `idx_parent_id` (`parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8mb4;

INSERT INTO app_fields_choices VALUES
('34','0','156','1','紧急','0','','1','','',''),
('35','0','156','1','高','0','','2','','',''),
('37','0','157','1','新项目','0','','1','','',''),
('38','0','157','1','打开','0','','2','','',''),
('39','0','157','1','等待','0','','3','','',''),
('40','0','157','1','关闭','0','','4','','',''),
('41','0','157','1','取消','0','','5','','',''),
('42','0','167','1','任务','1','','1','','',''),
('43','0','167','1','更改','0','','2','','',''),
('44','0','167','1','错误','0','#ff7a00','3','','',''),
('45','0','167','1','想法','0','','0','','',''),
('46','0','169','1','新任务','1','','0','','',''),
('47','0','169','1','打开','0','','2','','',''),
('48','0','169','1','等待','0','','3','','',''),
('49','0','169','1','完成','0','','4','','',''),
('50','0','169','1','关闭','0','','5','','',''),
('51','0','169','1','付费','0','','6','','',''),
('52','0','169','1','取消','0','','7','','',''),
('53','0','170','1','紧急','0','#ff0000','1','','',''),
('54','0','170','1','高','0','','2','','',''),
('55','0','170','1','中','1','','3','','',''),
('56','0','182','1','技术支持','0','','0','','',''),
('57','0','183','1','请求更改','0','','1','','',''),
('58','0','183','1','报告错误','0','','2','','',''),
('59','0','183','1','提问','0','','3','','',''),
('60','0','186','1','新技术支持','1','','0','','',''),
('61','0','186','1','打开','0','','2','','',''),
('62','0','186','1','等待客户','0','','3','','',''),
('63','0','186','1','关闭','0','','4','','',''),
('64','0','186','1','取消','0','','5','','',''),
('65','0','193','1','打开','0','','1','','',''),
('66','0','193','1','关闭','0','','2','','',''),
('67','0','193','1','新','1','','0','','','');

CREATE TABLE IF NOT EXISTS `app_filters_panels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `type` varchar(64) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `is_active_filters` tinyint(1) NOT NULL,
  `position` varchar(16) NOT NULL,
  `users_groups` text NOT NULL,
  `width` tinyint(1) NOT NULL,
  `sort_order` smallint(6) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_filters_panels_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `panels_id` int(11) NOT NULL,
  `entities_id` int(11) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `title` varchar(64) NOT NULL,
  `width` varchar(16) NOT NULL,
  `height` varchar(16) NOT NULL,
  `display_type` varchar(32) NOT NULL,
  `search_type_match` tinyint(1) NOT NULL,
  `exclude_values` text NOT NULL,
  `sort_order` smallint(6) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_panels_id` (`panels_id`),
  KEY `idx_fields_id` (`fields_id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_forms_fields_rules` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entities_id` int(10) unsigned NOT NULL,
  `fields_id` int(10) unsigned NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `choices` text NOT NULL,
  `visible_fields` text NOT NULL,
  `hidden_fields` text NOT NULL,
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_fields_id` (`fields_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_forms_rows` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `forms_tabs_id` int(11) NOT NULL,
  `columns` tinyint(4) NOT NULL,
  `column1_width` tinyint(4) NOT NULL,
  `column2_width` tinyint(4) NOT NULL,
  `column3_width` tinyint(4) NOT NULL,
  `column4_width` tinyint(4) NOT NULL,
  `column5_width` tinyint(4) NOT NULL,
  `column6_width` tinyint(4) NOT NULL,
  `field_name_new_row` tinyint(1) NOT NULL,
  `sort_order` smallint(6) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `entities_id` (`entities_id`),
  KEY `forms_tabs_id` (`forms_tabs_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_forms_tabs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL DEFAULT 0,
  `is_folder` tinyint(1) NOT NULL DEFAULT 0,
  `name` varchar(64) NOT NULL,
  `description` text NOT NULL,
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4;

INSERT INTO app_forms_tabs VALUES
('1','1','0','0','信息','','0'),
('24','21','0','0','信息','','0'),
('25','21','0','0','团队','','1'),
('26','22','0','0','信息','','0'),
('27','22','0','0','时间','','1'),
('28','23','0','0','信息','','0'),
('29','24','0','0','信息','','0');

CREATE TABLE IF NOT EXISTS `app_global_lists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `notes` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_global_lists_choices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT 0,
  `lists_id` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `name` varchar(255) NOT NULL,
  `is_default` tinyint(1) DEFAULT NULL,
  `bg_color` varchar(16) NOT NULL,
  `value` varchar(64) NOT NULL,
  `sort_order` int(11) DEFAULT NULL,
  `users` text NOT NULL,
  `notes` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_parent_id` (`parent_id`),
  KEY `idx_lists_id` (`lists_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_global_vars` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `is_folder` tinyint(1) NOT NULL,
  `name` varchar(64) NOT NULL,
  `value` varchar(255) NOT NULL,
  `notes` text NOT NULL,
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_help_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `type` varchar(16) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `name` varchar(255) NOT NULL,
  `icon` varchar(64) NOT NULL,
  `start_date` int(11) NOT NULL,
  `end_date` int(11) NOT NULL,
  `description` text NOT NULL,
  `color` varchar(16) NOT NULL,
  `position` varchar(16) NOT NULL,
  `users_groups` text NOT NULL,
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_created_by` (`created_by`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_holidays` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_image_map_labels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `map_id` int(11) NOT NULL,
  `choices_id` int(11) NOT NULL,
  `x` int(11) NOT NULL,
  `y` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_map_id` (`map_id`),
  KEY `idx_choices_id` (`choices_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_image_map_markers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `items_id` int(11) NOT NULL,
  `map_id` int(11) NOT NULL,
  `x` int(11) NOT NULL,
  `y` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_items_id` (`items_id`),
  KEY `idx_map_id` (`map_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_image_map_markers_nested` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `items_id` int(11) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `x` int(11) NOT NULL,
  `y` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_items_id` (`items_id`),
  KEY `idx_fields_id` (`fields_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_items_export_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `templates_fields` text NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `cidx` (`entities_id`,`users_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_listing_highlight_rules` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entities_id` int(10) unsigned NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `fields_id` int(10) unsigned NOT NULL,
  `fields_values` text NOT NULL,
  `bg_color` varchar(7) NOT NULL,
  `sort_order` int(11) NOT NULL,
  `notes` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_fields_id` (`fields_id`),
  KEY `entities_id` (`entities_id`),
  KEY `fields_id` (`fields_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_listing_sections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `listing_types_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `fields` text NOT NULL,
  `display_as` varchar(16) NOT NULL,
  `display_field_names` tinyint(1) NOT NULL,
  `text_align` varchar(16) NOT NULL,
  `width` varchar(16) NOT NULL,
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_listing_types_id` (`listing_types_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_listing_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `type` varchar(16) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `is_default` tinyint(4) NOT NULL,
  `width` smallint(6) NOT NULL,
  `settings` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_mind_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `items_id` int(11) DEFAULT NULL,
  `fields_id` int(11) DEFAULT NULL,
  `reports_id` int(11) DEFAULT NULL,
  `mm_id` varchar(64) NOT NULL,
  `mm_parent_id` varchar(64) NOT NULL,
  `mm_text` varchar(255) NOT NULL,
  `mm_layout` varchar(16) NOT NULL,
  `mm_shape` varchar(16) NOT NULL,
  `mm_side` varchar(16) NOT NULL,
  `mm_color` varchar(16) NOT NULL,
  `mm_icon` varchar(32) NOT NULL,
  `mm_collapsed` varchar(1) NOT NULL,
  `mm_value` varchar(64) NOT NULL,
  `mm_items_id` int(11) DEFAULT 0,
  `parent_entity_item_id` int(11) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_items_id` (`items_id`),
  KEY `idx_fields_id` (`fields_id`),
  KEY `idx_reports_id` (`reports_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_portlets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `users_id` int(11) NOT NULL,
  `is_collapsed` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_name` (`name`,`users_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_records_visibility_rules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `users_groups` text NOT NULL,
  `merged_fields` text NOT NULL,
  `merged_fields_empty_values` text NOT NULL,
  `notes` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT 0,
  `entities_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `reports_type` varchar(64) NOT NULL,
  `name` varchar(64) NOT NULL,
  `menu_icon` varchar(64) NOT NULL,
  `icon_color` varchar(7) NOT NULL,
  `bg_color` varchar(7) NOT NULL,
  `in_menu` tinyint(1) NOT NULL DEFAULT 0,
  `in_dashboard` tinyint(4) NOT NULL DEFAULT 0,
  `in_dashboard_counter` tinyint(1) NOT NULL DEFAULT 0,
  `in_dashboard_icon` tinyint(1) NOT NULL,
  `in_dashboard_counter_color` varchar(16) NOT NULL,
  `in_dashboard_counter_bg_color` varchar(16) NOT NULL,
  `in_dashboard_counter_fields` varchar(255) NOT NULL,
  `dashboard_counter_hide_count` tinyint(1) NOT NULL DEFAULT 0,
  `dashboard_counter_hide_zero_count` tinyint(1) NOT NULL,
  `dashboard_counter_sum_by_field` int(11) NOT NULL,
  `in_header` tinyint(1) NOT NULL DEFAULT 0,
  `in_header_autoupdate` tinyint(1) NOT NULL,
  `dashboard_sort_order` int(11) DEFAULT NULL,
  `header_sort_order` int(11) NOT NULL DEFAULT 0,
  `dashboard_counter_sort_order` int(11) NOT NULL DEFAULT 0,
  `listing_order_fields` text NOT NULL,
  `users_groups` text NOT NULL,
  `assigned_to` text NOT NULL,
  `displays_assigned_only` tinyint(1) NOT NULL DEFAULT 0,
  `parent_entity_id` int(11) NOT NULL DEFAULT 0,
  `parent_item_id` int(11) NOT NULL DEFAULT 0,
  `fields_in_listing` text NOT NULL,
  `rows_per_page` int(11) NOT NULL DEFAULT 0,
  `notification_days` varchar(32) NOT NULL,
  `notification_time` varchar(255) NOT NULL,
  `listing_type` varchar(16) NOT NULL,
  `listing_col_width` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_created_by` (`created_by`),
  KEY `idx_parent_id` (`parent_id`),
  KEY `idx_parent_entity_id` (`parent_entity_id`),
  KEY `idx_parent_item_id` (`parent_item_id`),
  KEY `idx_reports_type` (`reports_type`),
  KEY `idx_in_dashboard` (`in_dashboard`),
  KEY `idx_in_dashboard_counter` (`in_dashboard_counter`)
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8mb4;

INSERT INTO app_reports VALUES
('59','0','21','0','default','','','','','0','0','0','0','','','','0','0','0','0','0',NULL,'0','0','','','','0','0','0','','0','','','',''),
('61','0','22','0','default','','','','','0','0','0','0','','','','0','0','0','0','0',NULL,'0','0','','','','0','0','0','','0','','','',''),
('63','0','23','0','default','','','','','0','0','0','0','','','','0','0','0','0','0',NULL,'0','0','','','','0','0','0','','0','','','','');

CREATE TABLE IF NOT EXISTS `app_reports_filters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reports_id` int(11) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `filters_values` text NOT NULL,
  `filters_condition` varchar(64) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_reports_id` (`reports_id`),
  KEY `idx_fields_id` (`fields_id`)
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4;

INSERT INTO app_reports_filters VALUES
('68','59','157','37,38,39','include','1'),
('70','61','169','46,47,48','include','1'),
('72','63','186','60,61,62','include','1');

CREATE TABLE IF NOT EXISTS `app_reports_filters_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fields_id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `filters_values` text NOT NULL,
  `filters_condition` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cidx` (`fields_id`,`users_id`),
  KEY `idx_fields_id` (`fields_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_reports_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `menu_icon` varchar(64) NOT NULL,
  `icon_color` varchar(7) NOT NULL,
  `bg_color` varchar(7) NOT NULL,
  `in_menu` tinyint(1) NOT NULL,
  `in_dashboard` tinyint(1) NOT NULL,
  `sort_order` smallint(6) NOT NULL,
  `counters_list` text NOT NULL,
  `reports_list` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `is_common` tinyint(1) NOT NULL DEFAULT 0,
  `users_groups` text NOT NULL,
  `assigned_to` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_reports_sections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_by` int(11) NOT NULL,
  `count_columns` tinyint(1) NOT NULL DEFAULT 2,
  `reports_groups_id` int(11) NOT NULL,
  `report_left` varchar(64) NOT NULL,
  `report_right` varchar(64) NOT NULL,
  `sort_order` smallint(6) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_reports_groups_id` (`reports_groups_id`),
  KEY `idx_created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_sessions` (
  `sesskey` varchar(32) NOT NULL,
  `expiry` bigint(20) unsigned NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`sesskey`),
  KEY `idx_expiry` (`expiry`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_user_filters_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filters_id` int(11) NOT NULL,
  `reports_id` int(11) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `filters_values` text NOT NULL,
  `filters_condition` varchar(64) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_filters_id` (`filters_id`),
  KEY `idx_reports_id` (`reports_id`),
  KEY `idx_fields_id` (`fields_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_user_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `sort_order` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_fields_id` (`fields_id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_user_roles_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_roles_id` int(11) NOT NULL,
  `fields_id` int(11) NOT NULL,
  `entities_id` int(11) NOT NULL,
  `access_schema` varchar(255) NOT NULL,
  `comments_access` varchar(64) NOT NULL,
  `fields_access` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_fields_id` (`fields_id`),
  KEY `idx_user_roles_id` (`user_roles_id`),
  KEY `entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_user_roles_to_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fields_id` int(11) NOT NULL,
  `entities_id` int(11) NOT NULL,
  `items_id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `roles_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_roles_id` (`roles_id`),
  KEY `idx_users_id` (`users_id`),
  KEY `idx_items_id` (`items_id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_fields_id` (`fields_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_users_alerts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_active` tinyint(1) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `type` varchar(16) NOT NULL,
  `location` varchar(16) NOT NULL,
  `start_date` bigint(20) unsigned NOT NULL,
  `end_date` bigint(20) unsigned NOT NULL,
  `assigned_to` text NOT NULL,
  `users_groups` text NOT NULL,
  `created_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_users_alerts_viewed` (
  `users_id` int(11) NOT NULL,
  `alerts_id` int(11) NOT NULL,
  KEY `idx_ueser_alerts` (`users_id`,`alerts_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_users_configuration` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL,
  `configuration_name` varchar(255) NOT NULL,
  `configuration_value` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_users_id` (`users_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_users_filters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reports_id` int(11) NOT NULL,
  `users_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `fields_in_listing` text NOT NULL,
  `listing_order_fields` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_reports_id` (`reports_id`),
  KEY `idx_users_id` (`users_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_users_login_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) DEFAULT NULL,
  `username` varchar(255) NOT NULL,
  `identifier` varchar(255) NOT NULL,
  `is_success` tinyint(1) NOT NULL,
  `date_added` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_users_id` (`users_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_users_notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL,
  `entities_id` int(11) NOT NULL,
  `items_id` int(11) NOT NULL,
  `name` text NOT NULL,
  `type` varchar(16) NOT NULL,
  `date_added` bigint(20) unsigned NOT NULL,
  `created_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_users_id` (`users_id`),
  KEY `idx_entities_id` (`entities_id`),
  KEY `idx_items_id` (`items_id`),
  KEY `idx_uei` (`users_id`,`entities_id`) USING BTREE,
  KEY `idx_created_by` (`created_by`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_users_search_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `users_id` int(11) NOT NULL,
  `reports_id` int(11) NOT NULL,
  `configuration_name` varchar(255) NOT NULL,
  `configuration_value` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_users_id` (`users_id`),
  KEY `idx_users_reports_id` (`users_id`,`reports_id`),
  KEY `idx_reports_id` (`reports_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `app_logs` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `users_id` int(11) UNSIGNED NOT NULL,
  `ip_address` varchar(64) NOT NULL,
  `log_type` varchar(16) NOT NULL,
  `date_added` int(11) NOT NULL,
  `http_url` varchar(255) NOT NULL,
  `is_ajax` tinyint(1) NOT NULL,
  `description` text NOT NULL,
  `seconds` decimal(11,4) NOT NULL,
  `errno` int(10) UNSIGNED NOT NULL,
  `filename` varchar(255) NOT NULL,
  `linenum` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_users_id` (`users_id`),
  KEY `idx_date_added` (`date_added`),
  KEY `idx_ip_address` (`ip_address`),
  KEY `idx_log_type` (`log_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `app_records_visibility_rules` ADD `mysql_query` TEXT NOT NULL AFTER `notes`;

ALTER TABLE `app_sessions` CHANGE `value` `value` LONGTEXT;

ALTER TABLE `app_reports` ADD `description` TEXT NOT NULL AFTER `name`;

CREATE TABLE IF NOT EXISTS `app_nested_entities_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entities_id` int(11) NOT NULL,
  `is_active` tinyint(4) NOT NULL,
  `name` varchar(64) NOT NULL,
  `entities` varchar(255) NOT NULL,
  `icon` varchar(64) NOT NULL,
  `icon_color` varchar(10) NOT NULL,
  `sort_order` smallint(6) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_entities_id` (`entities_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `app_records_visibility_rules` ADD `php_code` TEXT NOT NULL AFTER `mysql_query`;
ALTER TABLE `app_filters_panels_fields` ADD `exclude_values_not_in_listing` TINYINT(1) NOT NULL DEFAULT '0' AFTER `exclude_values`;

