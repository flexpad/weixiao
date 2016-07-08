DELETE FROM `wp_attribute` WHERE `model_name`='SignIn_Log';
DELETE FROM `wp_model` WHERE `name`='SignIn_Log' ORDER BY id DESC LIMIT 1;
DROP TABLE IF EXISTS `wp_signin_log`;


