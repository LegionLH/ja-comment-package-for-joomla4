ALTER TABLE `#__jacomment_items` ADD `children` INT( 11 ) DEFAULT '0' AFTER `date_active`;
ALTER TABLE `#__jacomment_items` ADD `active_children` INT( 11 ) DEFAULT '0' AFTER `children`;