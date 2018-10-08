ALTER TABLE `#__djrevs_comments` 
CHANGE `object_type` `item_type` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL, 
CHANGE `object_id` `item_id` INT(11) NOT NULL;

ALTER TABLE `#__djrevs_objects` 
CHANGE `object_type` `item_type` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `#__djrevs_objects_items` 
CHANGE `object_id` `item_id` INT(11) NOT NULL; 

ALTER TABLE `#__djrevs_reviews` 
CHANGE `object_type` `item_type` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, 
CHANGE `object_id` `item_id` INT(11) NOT NULL;

