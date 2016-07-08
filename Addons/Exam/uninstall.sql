DELETE FROM `wp_attribute` WHERE `model_name`='exam';
DELETE FROM `wp_model` WHERE `name`='exam' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_exam`;


DELETE FROM `wp_attribute` WHERE `model_name`='exam_question';
DELETE FROM `wp_model` WHERE `name`='exam_question' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_exam_question`;


DELETE FROM `wp_attribute` WHERE `model_name`='exam_answer';
DELETE FROM `wp_model` WHERE `name`='exam_answer' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_exam_answer`;


