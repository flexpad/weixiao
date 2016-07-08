CREATE TABLE IF NOT EXISTS `wp_qr_admin` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`action_name`  varchar(30) NOT NULL  DEFAULT 'QR_SCENE' COMMENT '类型',
`group_id`  int(10) NULL  DEFAULT 0 COMMENT '用户组',
`tag_ids`  varchar(255) NULL  COMMENT '用户标签',
`qr_code`  varchar(255) NULL  COMMENT '二维码',
`material`  varchar(50) NULL  COMMENT '扫码后的回复内容',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('qr_admin','扫码管理','0','','1','["action_name","group_id","tag_ids"]','1:基础','','','','','qr_code:二维码\r\naction_name:类型\r\ngroup_id:用户组\r\ntag_ids:标签\r\nids:操作:[EDIT]|编辑,[DELETE]|删除','10','','','1463999052','1464002422','1','MyISAM','QrAdmin');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('action_name','类型','varchar(30) NOT NULL','bool','QR_SCENE','临时二维码最长有效期是30天','1','QR_SCENE:临时二维码\r\nQR_LIMIT_SCENE:永久二维码','0','qr_admin','1','1','1463999695','1463999695','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('group_id','用户组','int(10) NULL','dynamic_select','0','','1','table=auth_group&value_field=id&title_field=title&first_option=不选择','0','qr_admin','0','1','1463999863','1463999863','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('tag_ids','用户标签','varchar(255) NULL','dynamic_checkbox','','','1','table=user_tag','0','qr_admin','0','1','1464002088','1464000098','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('qr_code','二维码','varchar(255) NULL','string','','','0','','0','qr_admin','0','1','1464056435','1464056435','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('material','扫码后的回复内容','varchar(50) NULL','material','','','1','','0','qr_admin','0','1','1464060736','1464060509','','3','','regex','','3','function');
UPDATE `wp_attribute` a, wp_model m SET a.model_id = m.id WHERE a.model_name=m.`name`;


