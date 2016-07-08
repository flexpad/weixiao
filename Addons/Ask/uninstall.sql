DELETE FROM `wp_attribute` WHERE `model_name`='ask';
DELETE FROM `wp_model` WHERE `name`='ask' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_ask`;


DELETE FROM `wp_attribute` WHERE `model_name`='ask_answer';
DELETE FROM `wp_model` WHERE `name`='ask_answer' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_ask_answer`;


DELETE FROM `wp_attribute` WHERE `model_name`='ask_question';
DELETE FROM `wp_model` WHERE `name`='ask_question' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_ask_question`;


