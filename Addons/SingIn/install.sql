CREATE TABLE IF NOT EXISTS `wp_signin_log` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`score`  int(10) NOT NULL  COMMENT '积分',
`token`  varchar(255) NOT NULL  COMMENT 'Token',
`sTime`  int(10) UNSIGNED NOT NULL  COMMENT '签到时间',
`uid`  varchar(255) NOT NULL  COMMENT '用户ID',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('SignIn_Log','签到记录','0','','1','{"1":["uid","score"]}','1:基础','','','','','uid:用户ID\r\nnickname:呢称\r\nsTime|time_format:签到时间\r\nscore:积分\r\n','10','uid','','1396061373','1404694493','1','MyISAM','SingIn');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('score','积分','int(10) NOT NULL','num','','','1','','0','SignIn_Log','0','1','1404694456','1404694456','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('token','Token','varchar(255) NOT NULL','string','','','0','','0','SignIn_Log','0','1','1396602871','1396602859','','3','','regex','get_token','1','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('sTime','签到时间','int(10) UNSIGNED NOT NULL','datetime','','','0','','0','SignIn_Log','1','1','1404631787','1396075102','','3','','regex','time','1','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('uid','用户ID','varchar(255) NOT NULL','textarea','','','1','','0','SignIn_Log','1','1','1404631866','1396062093','','3','','regex','','3','function');
UPDATE `wp_attribute` a, wp_model m SET a.model_id = m.id WHERE a.model_name=m.`name`;


