DELETE FROM `wp_attribute` WHERE `model_name`='redbag';
DELETE FROM `wp_model` WHERE `name`='redbag' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_redbag`;


DELETE FROM `wp_attribute` WHERE `model_name`='redbag_follow';
DELETE FROM `wp_model` WHERE `name`='redbag_follow' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_redbag_follow`;


