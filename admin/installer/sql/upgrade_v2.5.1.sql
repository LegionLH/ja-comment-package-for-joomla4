ALTER TABLE `#__jacomment_items` ADD `latitude` CHAR(255) COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `p0`;
ALTER TABLE `#__jacomment_items` ADD `longitude` CHAR(255) COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `latitude`;
ALTER TABLE `#__jacomment_items` ADD `address` TEXT COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `longitude`;