ALTER TABLE `app_ext_export_templates` ADD `save_attachments` VARCHAR(255) NOT NULL AFTER `save_as`;

CREATE TABLE IF NOT EXISTS `app_ext_email_rules_blocks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;