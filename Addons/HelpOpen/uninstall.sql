DELETE FROM `wp_attribute` WHERE `model_name`='help_open';
DELETE FROM `wp_model` WHERE `name`='help_open' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_help_open`;


DELETE FROM `wp_attribute` WHERE `model_name`='help_open_user';
DELETE FROM `wp_model` WHERE `name`='help_open_user' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_help_open_user`;


DELETE FROM `wp_attribute` WHERE `model_name`='help_open_prize';
DELETE FROM `wp_model` WHERE `name`='help_open_prize' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_help_open_prize`;


