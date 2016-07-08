DELETE FROM `wp_attribute` WHERE `model_name`='card_vouchers';
DELETE FROM `wp_model` WHERE `name`='card_vouchers' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_card_vouchers`;


