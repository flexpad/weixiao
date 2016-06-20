DELETE FROM `wp_attribute` WHERE model_id = (SELECT id FROM wp_model WHERE `name`='redbag' ORDER BY id DESC LIMIT 1);
DELETE FROM `wp_model` WHERE `name`='redbag' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_redbag`;


DELETE FROM `wp_attribute` WHERE model_id = (SELECT id FROM wp_model WHERE `name`='redbag_follow' ORDER BY id DESC LIMIT 1);
DELETE FROM `wp_model` WHERE `name`='redbag_follow' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_redbag_follow`;


