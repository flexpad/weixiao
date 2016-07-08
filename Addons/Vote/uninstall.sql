DELETE FROM `wp_attribute` WHERE `model_name`='vote';
DELETE FROM `wp_model` WHERE `name`='vote' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_vote`;


DELETE FROM `wp_attribute` WHERE `model_name`='vote_log';
DELETE FROM `wp_model` WHERE `name`='vote_log' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_vote_log`;


DELETE FROM `wp_attribute` WHERE `model_name`='vote_option';
DELETE FROM `wp_model` WHERE `name`='vote_option' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_vote_option`;


DELETE FROM `wp_attribute` WHERE `model_name`='shop_vote';
DELETE FROM `wp_model` WHERE `name`='shop_vote' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_shop_vote`;


DELETE FROM `wp_attribute` WHERE `model_name`='shop_vote_option';
DELETE FROM `wp_model` WHERE `name`='shop_vote_option' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_shop_vote_option`;


DELETE FROM `wp_attribute` WHERE `model_name`='shop_vote_log';
DELETE FROM `wp_model` WHERE `name`='shop_vote_log' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_shop_vote_log`;


