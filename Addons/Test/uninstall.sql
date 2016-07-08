DELETE FROM `wp_attribute` WHERE `model_name`='test';
DELETE FROM `wp_model` WHERE `name`='test' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_test`;


DELETE FROM `wp_attribute` WHERE `model_name`='test_question';
DELETE FROM `wp_model` WHERE `name`='test_question' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_test_question`;


DELETE FROM `wp_attribute` WHERE `model_name`='test_answer';
DELETE FROM `wp_model` WHERE `name`='test_answer' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_test_answer`;


