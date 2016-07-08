DELETE FROM `wp_attribute` WHERE `model_name`='qr_admin';
DELETE FROM `wp_model` WHERE `name`='qr_admin' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_qr_admin`;


