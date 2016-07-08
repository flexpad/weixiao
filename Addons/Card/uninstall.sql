DELETE FROM `wp_attribute` WHERE `model_name`='card_privilege';
DELETE FROM `wp_model` WHERE `name`='card_privilege' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_card_privilege`;


DELETE FROM `wp_attribute` WHERE `model_name`='card_level';
DELETE FROM `wp_model` WHERE `name`='card_level' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_card_level`;


DELETE FROM `wp_attribute` WHERE `model_name`='card_coupons';
DELETE FROM `wp_model` WHERE `name`='card_coupons' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_card_coupons`;


DELETE FROM `wp_attribute` WHERE `model_name`='card_notice';
DELETE FROM `wp_model` WHERE `name`='card_notice' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_card_notice`;


DELETE FROM `wp_attribute` WHERE `model_name`='card_member';
DELETE FROM `wp_model` WHERE `name`='card_member' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_card_member`;


DELETE FROM `wp_attribute` WHERE `model_name`='recharge_log';
DELETE FROM `wp_model` WHERE `name`='recharge_log' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_recharge_log`;


DELETE FROM `wp_attribute` WHERE `model_name`='buy_log';
DELETE FROM `wp_model` WHERE `name`='buy_log' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_buy_log`;


DELETE FROM `wp_attribute` WHERE `model_name`='card_marketing';
DELETE FROM `wp_model` WHERE `name`='card_marketing' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_card_marketing`;


DELETE FROM `wp_attribute` WHERE `model_name`='card_reward';
DELETE FROM `wp_model` WHERE `name`='card_reward' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_card_reward`;


DELETE FROM `wp_attribute` WHERE `model_name`='card_score';
DELETE FROM `wp_model` WHERE `name`='card_score' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_card_score`;


DELETE FROM `wp_attribute` WHERE `model_name`='card_recharge';
DELETE FROM `wp_model` WHERE `name`='card_recharge' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_card_recharge`;


DELETE FROM `wp_attribute` WHERE `model_name`='card_recharge_condition';
DELETE FROM `wp_model` WHERE `name`='card_recharge_condition' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_card_recharge_condition`;


DELETE FROM `wp_attribute` WHERE `model_name`='card_custom';
DELETE FROM `wp_model` WHERE `name`='card_custom' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_card_custom`;


DELETE FROM `wp_attribute` WHERE `model_name`='score_exchange_log';
DELETE FROM `wp_model` WHERE `name`='score_exchange_log' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_score_exchange_log`;


DELETE FROM `wp_attribute` WHERE `model_name`='share_log';
DELETE FROM `wp_model` WHERE `name`='share_log' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_share_log`;


