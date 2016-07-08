DELETE FROM `wp_attribute` WHERE `model_name`='weiba_category';
DELETE FROM `wp_model` WHERE `name`='weiba_category' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_weiba_category`;


DELETE FROM `wp_attribute` WHERE `model_name`='weiba';
DELETE FROM `wp_model` WHERE `name`='weiba' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_weiba`;


DELETE FROM `wp_attribute` WHERE `model_name`='weiba_post';
DELETE FROM `wp_model` WHERE `name`='weiba_post' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_weiba_post`;


