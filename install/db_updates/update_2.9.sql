ALTER TABLE `app_entity_1` ADD `multiple_access_groups` VARCHAR(64) NOT NULL AFTER `password`;
ALTER TABLE `app_entity_1` ADD `client_id` BIGINT UNSIGNED NOT NULL AFTER `id`, ADD INDEX `idx_client_id` (`client_id`);
ALTER TABLE `app_listing_types` ADD `settings` TEXT NOT NULL AFTER `width`;