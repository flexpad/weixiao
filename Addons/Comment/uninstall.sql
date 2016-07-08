DELETE FROM `wp_attribute` WHERE `model_name`='comment';
DELETE FROM `wp_model` WHERE `name`='comment' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_comment`;


