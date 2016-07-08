DELETE FROM `wp_attribute` WHERE `model_name`='sms';
DELETE FROM `wp_model` WHERE `name`='sms' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_sms`;


