DELETE FROM `wp_attribute` WHERE `model_name`='auto_reply';
DELETE FROM `wp_model` WHERE `name`='auto_reply' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_auto_reply`;


