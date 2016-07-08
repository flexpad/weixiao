CREATE TABLE IF NOT EXISTS `wp_card_privilege` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`title`  varchar(255) NULL  COMMENT '特权标题',
`grade`  varchar(100) NULL  COMMENT '适用人群',
`start_time`  int(10) NULL  COMMENT '开始时间',
`end_time`  int(10) NULL  COMMENT '结束时间',
`intro`  text NULL  COMMENT '使用说明',
`token`  varchar(255) NULL  COMMENT 'token',
`enable`  tinyint(2) NULL  DEFAULT 1 COMMENT '是否启用',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('card_privilege','会员卡特权','0','','1','["title","grade","start_time","end_time","intro","enable"]','1:基础','','','','','start_time|time_format:特权开始时间\r\nend_time|time_format:特权结束时间\r\ntitle:特权标题\r\ngrade:适用人群\r\nintro:特权内容\r\nenable|get_name_by_status:是否开启\r\nstatus:状态\r\nid:操作:[EDIT]|编辑,[DELETE]|删除','10','','','1443153625','1443167441','1','MyISAM','Card');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('title','特权标题','varchar(255) NULL','string','','','1','','0','card_privilege','0','1','1443153661','1443153661','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('grade','适用人群','varchar(100) NULL','checkbox','','','1','','0','card_privilege','0','1','1443165742','1443153832','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('start_time','开始时间','int(10) NULL','datetime','','','1','','0','card_privilege','0','1','1443153870','1443153870','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('end_time','结束时间','int(10) NULL','datetime','','','1','','0','card_privilege','0','1','1443153895','1443153895','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('intro','使用说明','text NULL','textarea','','','1','','0','card_privilege','0','1','1443153964','1443153964','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('token','token','varchar(255) NULL','string','','','0','','0','card_privilege','0','1','1443154036','1443154036','','3','','regex','get_token','1','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('enable','是否启用','tinyint(2) NULL','bool','1','','1','1:启用\r\n0:禁用','0','card_privilege','0','1','1443164479','1443164437','','3','','regex','','3','function');
UPDATE `wp_attribute` a, wp_model m SET a.model_id = m.id WHERE a.model_name=m.`name`;


CREATE TABLE IF NOT EXISTS `wp_card_level` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`level`  varchar(255) NULL  COMMENT '会员等级',
`score`  int(10) NULL  COMMENT '累计积分',
`recharge`  int(10) NULL  COMMENT '累计充值',
`discount`  int(10) NULL  COMMENT '折扣率',
`token`  varchar(255) NULL  COMMENT 'token',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('card_level','会员等级','0','','1','["level","score","recharge","discount"]','1:基础','','','','','level:会员等级\r\nscore:累计积分\r\nrecharge:累计充值\r\ndiscount:享受折扣\r\nids:操作:[EDIT]|编辑,[DELETE]|删除','10','','','1443163048','1443164698','1','MyISAM','Card');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('level','会员等级','varchar(255) NULL','string','','','1','','0','card_level','0','1','1443163097','1443163097','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('score','累计积分','int(10) NULL','num','','','1','','0','card_level','0','1','1443163223','1443163223','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('recharge','累计充值','int(10) NULL','num','','','1','','0','card_level','0','1','1443163252','1443163252','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('discount','折扣率','int(10) NULL','num','','例如10代表优惠10%，即打9折','1','','0','card_level','0','1','1443163384','1443163384','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('token','token','varchar(255) NULL','string','','','0','','0','card_level','0','1','1443163422','1443163422','','3','','regex','get_token','1','function');
UPDATE `wp_attribute` a, wp_model m SET a.model_id = m.id WHERE a.model_name=m.`name`;


CREATE TABLE IF NOT EXISTS `wp_card_coupons` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`give_type`  tinyint(2) NOT NULL  DEFAULT 0 COMMENT '发放方式',
`title`  varchar(255) NOT NULL  COMMENT '优惠券标题',
`end_date`  int(10) NULL  COMMENT '结束时间',
`start_date`  int(10) NOT NULL  COMMENT '开始时间',
`content`  text NOT NULL  COMMENT '使用说明',
`cTime`  int(10) NULL  COMMENT '发布时间',
`token`  varchar(100) NOT NULL  COMMENT 'Token',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('card_coupons','会员卡优惠券','0','','1','{"1":["title","give_type","start_date","end_date","content"]}','1:基础','','','','','title:标题\r\ngive_type|get_name_by_status:发放方式\r\nstart_date|time_format:开始时间\r\nend_date|time_format:结束时间\r\ncTime|time_format:发布时间\r\nid:操作:[EDIT]|编辑,[DELETE]|删除','10','title','','1395485774','1395486719','1','MyISAM','Card');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('give_type','发放方式','tinyint(2) NOT NULL','bool','0','人工发放是指管理员要会员管理列表手工进行发放','0','0:自动发放\r\n1:人工发放','0','card_coupons','0','1','1395487734','1395486034','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('title','优惠券标题','varchar(255) NOT NULL','string','','','1','','0','card_coupons','0','1','1395485828','1395485828','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('end_date','结束时间','int(10) NULL','datetime','','','1','','0','card_coupons','0','1','1395486188','1395486188','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('start_date','开始时间','int(10) NOT NULL','datetime','','','1','','0','card_coupons','0','1','1395486135','1395486135','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('content','使用说明','text NOT NULL','editor','','','1','','0','card_coupons','0','1','1395486307','1395486307','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('cTime','发布时间','int(10) NULL','datetime','','','0','','0','card_coupons','0','1','1395486839','1395486801','','3','','regex','time','1','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('token','Token','varchar(100) NOT NULL','string','','','0','','0','card_coupons','0','1','1395912079','1395912079','','3','','regex','get_token','1','function');
UPDATE `wp_attribute` a, wp_model m SET a.model_id = m.id WHERE a.model_name=m.`name`;


CREATE TABLE IF NOT EXISTS `wp_card_notice` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`cTime`  int(10) NULL  COMMENT '发布时间',
`content`  text NOT NULL  COMMENT '通知内容',
`title`  varchar(255) NOT NULL  COMMENT '标题',
`token`  varchar(100) NOT NULL  COMMENT 'Token',
`img`  int(10) UNSIGNED NULL  COMMENT '通知图片',
`grade`  varchar(100) NULL  DEFAULT 0 COMMENT '适用人群',
`to_uid`  int(10) NULL  DEFAULT 0 COMMENT '指定用户',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('card_notice','会员卡通知','0','','1','["title","img","grade","content"]','1:基础','','','','','title:标题\r\ncTime|time_format:发布时间\r\nid:操作:[EDIT]|编辑,[DELETE]|删除','10','title','','1395485156','1444798538','1','MyISAM','Card');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('cTime','发布时间','int(10) NULL','datetime','','','0','','0','card_notice','0','1','1395485303','1395485303','','3','','regex','time','1','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('content','通知内容','text NOT NULL','editor','','','1','','0','card_notice','1','1','1444471509','1395485247','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('title','标题','varchar(255) NOT NULL','string','','','1','','0','card_notice','1','1','1444471517','1395485192','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('token','Token','varchar(100) NOT NULL','string','','','0','','0','card_notice','0','1','1395911896','1395911896','','3','','regex','get_token','1','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('img','通知图片','int(10) UNSIGNED NULL','picture','','','1','','0','card_notice','0','1','1444472055','1444470114','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('grade','适用人群','varchar(100) NULL','checkbox','0','','1','0:所有会员','0','card_notice','0','1','1444478807','1444471427','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('to_uid','指定用户','int(10) NULL','num','0','','0','','0','card_notice','0','1','1444717008','1444716962','','3','','regex','','3','function');
UPDATE `wp_attribute` a, wp_model m SET a.model_id = m.id WHERE a.model_name=m.`name`;


CREATE TABLE IF NOT EXISTS `wp_card_member` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`number`  varchar(50) NULL  COMMENT '卡号',
`cTime`  int(10) NULL  COMMENT '加入时间',
`phone`  varchar(30) NULL  COMMENT '手机号',
`username`  varchar(100) NULL  COMMENT '姓名',
`uid`  int(10) NOT NULL  COMMENT '用户UID',
`token`  varchar(100) NOT NULL  COMMENT 'Token',
`recharge`  int(10) NULL  DEFAULT 0 COMMENT '余额',
`status`  tinyint(2) NULL  DEFAULT 1 COMMENT '会员状态',
`birthday`  int(10) NULL  COMMENT '生日',
`address`  varchar(255) NULL  COMMENT '地址',
`level`  int(10) NULL  DEFAULT 0 COMMENT '会员卡等级',
`sex`  int(10) NULL  COMMENT '性别',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('card_member','会员卡成员','0','','1','["phone","username","recharge"]','1:基础','','','','','number:卡号\r\nuid:uid\r\nusername:姓名\r\nphone:手机号\r\nscore:剩余积分\r\nrecharge:余额\r\nlevel:等级\r\ncTime|time_format:加入时间\r\nstatus|get_name_by_status:状态\r\nid:操作:[EDIT]|编辑,[DELETE]|删除,do_recharge&id=[id]|会员充值,do_buy&id=[id]|会员消费,update_score&id=[id]|手动修改积分','10','username:请输入姓名','','1395482804','1444633312','1','MyISAM','Card');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('number','卡号','varchar(50) NULL','string','','','3','','0','card_member','0','1','1395484806','1395483310','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('cTime','加入时间','int(10) NULL','datetime','','','0','','0','card_member','0','1','1395484366','1395484366','','3','','regex','time','1','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('phone','手机号','varchar(30) NULL','string','','','1','','0','card_member','0','1','1395483248','1395483248','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('username','姓名','varchar(100) NULL','string','','','1','','0','card_member','0','1','1395483048','1395483048','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('uid','用户UID','int(10) NOT NULL','num','','','0','','0','card_member','0','1','1395482973','1395482973','','3','','regex','get_mid','1','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('token','Token','varchar(100) NOT NULL','string','','','0','','0','card_member','0','1','1395973788','1395912028','','3','','regex','get_token','1','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('recharge','余额','int(10) NULL','num','0','','1','','0','card_member','0','1','1443171423','1443170806','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('status','会员状态','tinyint(2) NULL','bool','1','','0','1:正常\r\n0:冻结','0','card_member','0','1','1444271533','1444271533','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('birthday','生日','int(10) NULL','date','','','0','','0','card_member','0','1','1444634831','1444634831','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('address','地址','varchar(255) NULL','string','','','0','','0','card_member','0','1','1444634852','1444634852','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('level','会员卡等级','int(10) NULL','num','0','','1','','0','card_member','0','1','1444714615','1444714615','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('sex','性别','int(10) NULL','num','','','1','1:男\r\n2:女','0','card_member','0','1','1444721537','1444720977','','3','','regex','','3','function');
UPDATE `wp_attribute` a, wp_model m SET a.model_id = m.id WHERE a.model_name=m.`name`;


CREATE TABLE IF NOT EXISTS `wp_recharge_log` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`recharge`  float(10) NULL  COMMENT '充值金额',
`branch_id`  int(10) NULL  DEFAULT 0 COMMENT '充值门店',
`operator`  varchar(255) NULL  COMMENT '操作员',
`cTime`  int(10) NULL  COMMENT '创建时间',
`token`  varchar(255) NULL  COMMENT 'token',
`member_id`  int(10) NULL  COMMENT '会员id',
`manager_id`  int(10) NULL  COMMENT '管理员id',
`type`  tinyint(2) NULL  DEFAULT 1 COMMENT '充值方式',
`remark`  text NULL  COMMENT '备注',
`uid`  int(10) NULL  COMMENT '用户ID',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('recharge_log','会员充值记录','0','','1','["recharge","branch_id","operator","cTime","token","manager_id"]','1:基础','','','','','member_id:会员卡号\r\ntruename:姓名\r\nphone:手机号\r\nrecharge:充值金额\r\ncTime|time_format:充值时间\r\nbranch_id:充值门店\r\noperator:操作员','10','operator:请输入姓名或手机号或操作员','','1444275985','1444387901','1','MyISAM','Card');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('recharge','充值金额','float(10) NULL','num','','','1','','0','recharge_log','1','1','1444286201','1444276045','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('branch_id','充值门店','int(10) NULL','num','0','','1','','0','recharge_log','0','1','1444276408','1444276408','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('operator','操作员','varchar(255) NULL','string','','','1','','0','recharge_log','0','1','1444287439','1444276506','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('cTime','创建时间','int(10) NULL','datetime','','','1','','0','recharge_log','0','1','1444276539','1444276539','','3','','regex','time','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('token','token','varchar(255) NULL','string','','','1','','0','recharge_log','0','1','1444276565','1444276565','','3','','regex','get_token','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('member_id','会员id','int(10) NULL','num','','','4','','0','recharge_log','0','1','1444285683','1444285649','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('manager_id','管理员id','int(10) NULL','num','','','1','','0','recharge_log','0','1','1444287330','1444287330','','3','','regex','get_mid','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('type','充值方式','tinyint(2) NULL','bool','1','','0','0:系统自动\r\n1:管理员手工','0','recharge_log','0','1','1445251928','1445251928','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('remark','备注','text NULL','textarea','','','0','','0','recharge_log','0','1','1445251962','1445251962','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('uid','用户ID','int(10) NULL','num','','','0','','0','recharge_log','0','1','1445252123','1445252123','','3','','regex','','3','function');
UPDATE `wp_attribute` a, wp_model m SET a.model_id = m.id WHERE a.model_name=m.`name`;


CREATE TABLE IF NOT EXISTS `wp_buy_log` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`pay`  float(10) NULL  COMMENT '消费金额',
`sn_id`  int(10) NULL  COMMENT '优惠卷',
`pay_type`  char(10) NULL  COMMENT '支付方式',
`branch_id`  int(10) NULL  DEFAULT 0 COMMENT '消费门店',
`member_id`  int(10) NULL  COMMENT '会员卡id',
`cTime`  int(10) NULL  COMMENT '创建时间',
`token`  varchar(255) NULL  COMMENT 'token',
`manager_id`  int(10) NULL  COMMENT '管理员ID',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('buy_log','会员消费记录','0','','1','["pay","pay_type","branch_id","cTime","token","manager_id","sn_id"]','1:基础','','','','','member_id:会员名称\r\nphone:电话\r\ncTime|time_format:消费时间\r\nbranch_id:消费门店\r\npay:消费金额\r\nsn_id:优惠金额\r\npay_type|get_name_by_status:消费方式','10','member:请输入会员名称或手机号','','1444289843','1444392724','1','MyISAM','Card');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('pay','消费金额','float(10) NULL','num','','','1','','0','buy_log','1','1','1444296855','1444289970','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('sn_id','优惠卷','int(10) NULL','num','','','1','','0','buy_log','0','1','1444297432','1444290217','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('pay_type','支付方式','char(10) NULL','radio','','','1','1:会员卡余额消费\r\n2:现金或POS机消费','0','buy_log','1','1','1444296840','1444290385','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('branch_id','消费门店','int(10) NULL','num','0','','1','','0','buy_log','0','1','1444296901','1444290445','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('member_id','会员卡id','int(10) NULL','num','','','4','','0','buy_log','0','1','1444290484','1444290472','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('cTime','创建时间','int(10) NULL','datetime','','','1','','0','buy_log','0','1','1444290512','1444290512','','3','','regex','time','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('token','token','varchar(255) NULL','string','','','1','','0','buy_log','0','1','1444290535','1444290535','','3','','regex','get_token','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('manager_id','管理员ID','int(10) NULL','num','','','1','','0','buy_log','0','1','1444297606','1444290558','','3','','regex','get_mid','3','function');
UPDATE `wp_attribute` a, wp_model m SET a.model_id = m.id WHERE a.model_name=m.`name`;


CREATE TABLE IF NOT EXISTS `wp_card_marketing` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`title`  varchar(255) NULL  COMMENT '活动名称',
`start_time`  int(10) NULL  COMMENT '开始时间',
`end_time`  int(10) NULL  COMMENT '结束时间',
`status`  tinyint(2) NULL  DEFAULT 0 COMMENT '状态',
`type`  char(50) NULL  COMMENT '活动类型',
`give_type`  char(10) NULL  COMMENT '赠送类型',
`give`  int(10) NULL  COMMENT '赠送数据',
`condition`  int(10) NULL  COMMENT '赠送条件',
`branch_id`  int(10) NULL  COMMENT '充值门店',
`grade`  int(10) NULL  COMMENT '适用人群',
`exchange_count`  int(10) NULL  COMMENT '兑换次数',
`open_give_rule`  tinyint(2) NULL  DEFAULT 0 COMMENT '启用赠送规则',
`enjoy_power`  tinyint(2) NULL  DEFAULT 0 COMMENT '消费享受权限',
`token`  varchar(255) NULL  COMMENT 'token',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('card_marketing','会员营销活动','0','','1','','1:基础','','','','','','10','','','1444482353','1444482364','1','MyISAM','Card');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('title','活动名称','varchar(255) NULL','string','','','1','','0','card_marketing','1','1','1444482518','1444482518','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('start_time','开始时间','int(10) NULL','datetime','','','1','','0','card_marketing','1','1','1444482549','1444482549','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('end_time','结束时间','int(10) NULL','datetime','','','1','','0','card_marketing','1','1','1444482572','1444482572','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('status','状态','tinyint(2) NULL','bool','0','','1','0:关闭\r\n1:开启','0','card_marketing','0','1','1444482635','1444482635','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('type','活动类型','char(50) NULL','select','','','1','1:开卡即送\r\n2:积分兑换\r\n3:充值赠送\r\n4:消费赠送','0','card_marketing','0','1','1444482777','1444482777','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('give_type','赠送类型','char(10) NULL','radio','','','1','1:积分\r\n2:优惠券\r\n3:现金','0','card_marketing','0','1','1444482866','1444482866','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('give','赠送数据','int(10) NULL','num','','','1','','0','card_marketing','0','1','1444482894','1444482894','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('condition','赠送条件','int(10) NULL','num','','','1','','0','card_marketing','0','1','1444482918','1444482918','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('branch_id','充值门店','int(10) NULL','num','','','1','','0','card_marketing','0','1','1444482965','1444482952','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('grade','适用人群','int(10) NULL','num','','','1','','0','card_marketing','0','1','1444482986','1444482986','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('exchange_count','兑换次数','int(10) NULL','num','','','1','','0','card_marketing','0','1','1444483035','1444483035','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('open_give_rule','启用赠送规则','tinyint(2) NULL','bool','0','','1','','0','card_marketing','0','1','1444483107','1444483107','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('enjoy_power','消费享受权限','tinyint(2) NULL','bool','0','','1','0:不限\r\n1:使用券消费的用户不享受','0','card_marketing','0','1','1444483206','1444483206','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('token','token','varchar(255) NULL','string','','','1','','0','card_marketing','0','1','1444483238','1444483238','','3','','regex','get_token','3','function');
UPDATE `wp_attribute` a, wp_model m SET a.model_id = m.id WHERE a.model_name=m.`name`;


CREATE TABLE IF NOT EXISTS `wp_card_reward` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`manager_id`  int(10) NULL  COMMENT '管理员ID',
`token`  varchar(50) NULL  COMMENT 'Token',
`cTime`  int(10) NULL  COMMENT '创建时间',
`start_time`  int(10) NULL  COMMENT '开始时间',
`end_time`  int(10) NULL  COMMENT '过期时间',
`title`  varchar(100) NULL  COMMENT '活动名称',
`type`  tinyint(2) NULL  DEFAULT 0 COMMENT '活动策略',
`score`  int(10) NULL  DEFAULT 0 COMMENT '积分数',
`coupon_id`  int(10) NULL  COMMENT '商城优惠券',
`is_show`  tinyint(2) NULL  DEFAULT 0 COMMENT '是否在用户领卡界面展示',
`content`  text NULL  COMMENT '活动说明',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('card_reward','开卡即送活动','0','','1','["title","start_time","end_time","type","score","is_show","content","coupon_id"]','1:基础','','','','','title:活动名称\r\ntype:活动策略\r\nstart_time:有效期\r\nstatus:活动状态\r\nid:操作:[EDIT]|编辑,[DELETE]|删除','10','title:请输入活动名称搜索','','1442457808','1444640724','1','MyISAM','Card');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('manager_id','管理员ID','int(10) NULL','num','','','0','','0','card_reward','0','1','1442458516','1442458516','','3','','regex','get_mid','1','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('token','Token','varchar(50) NULL','string','','','0','','0','card_reward','0','1','1442458480','1442458480','','3','','regex','get_token','1','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('cTime','创建时间','int(10) NULL','datetime','','','0','','0','card_reward','0','1','1442458439','1442458439','','3','','regex','time','1','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('start_time','开始时间','int(10) NULL','datetime','','','1','','0','card_reward','1','1','1442457879','1442457879','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('end_time','过期时间','int(10) NULL','datetime','','','1','','0','card_reward','1','1','1442457902','1442457902','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('title','活动名称','varchar(100) NULL','string','','','1','','0','card_reward','1','1','1442457852','1442457852','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('type','活动策略','tinyint(2) NULL','bool','0','','1','0:送积分\r\n1:送优惠券','0','card_reward','0','1','1444622808','1444622808','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('score','积分数','int(10) NULL','num','0','','1','','0','card_reward','0','1','1444622853','1444622853','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('coupon_id','商城优惠券','int(10) NULL','num','','','1','','0','card_reward','0','1','1444634724','1444622932','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('is_show','是否在用户领卡界面展示','tinyint(2) NULL','bool','0','','1','0:否\r\n1:是','0','card_reward','0','1','1444622979','1444622979','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('content','活动说明','text NULL','textarea','','','1','','0','card_reward','0','1','1444623015','1444623015','','3','','regex','','3','function');
UPDATE `wp_attribute` a, wp_model m SET a.model_id = m.id WHERE a.model_name=m.`name`;


CREATE TABLE IF NOT EXISTS `wp_card_score` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`manager_id`  int(10) NULL  COMMENT '管理员ID',
`token`  varchar(50) NULL  COMMENT 'Token',
`cTime`  int(10) NULL  COMMENT '创建时间',
`num_limit`  int(10) NULL  DEFAULT 0 COMMENT '兑换次数限制',
`coupon_id`  int(10) NULL  COMMENT '商城优惠券',
`score_limit`  int(10) NULL  DEFAULT 0 COMMENT '所需积分',
`start_time`  int(10) NULL  COMMENT '开始时间',
`end_time`  int(10) NULL  COMMENT '过期时间',
`title`  varchar(100) NULL  COMMENT '活动名称',
`member`  varchar(100) NULL  DEFAULT 0 COMMENT '适用人群',
`coupon_type`  int(10) NULL  DEFAULT 0 COMMENT '优惠券类型',
`cover_id`  int(10) UNSIGNED NULL  COMMENT '活动图片',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('card_score','积分兑换活动','0','','1','["title","start_time","end_time","num_limit","coupon_id","score_limit","member","coupon_type"]','1:基础','','','','','title:活动名称\r\ncoupon_id:兑换内容\r\nstart_time:有效期\r\nstatus:活动状态\r\nmember:适用人群\r\nid:操作:[EDIT]|编辑,[DELETE]|删除','10','title:请输入活动名称搜索','','1442457808','1444798256','1','MyISAM','Card');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('manager_id','管理员ID','int(10) NULL','num','','','0','','0','card_score','0','1','1442458516','1442458516','','3','','regex','get_mid','1','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('token','Token','varchar(50) NULL','string','','','0','','0','card_score','0','1','1442458480','1442458480','','3','','regex','get_token','1','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('cTime','创建时间','int(10) NULL','datetime','','','0','','0','card_score','0','1','1442458439','1442458439','','3','','regex','time','1','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('num_limit','兑换次数限制','int(10) NULL','num','0','','1','','0','card_score','0','1','1444639490','1444622853','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('coupon_id','商城优惠券','int(10) NULL','num','','','1','','0','card_score','0','1','1444634724','1444622932','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('score_limit','所需积分','int(10) NULL','num','0','','1','','0','card_score','0','1','1444639428','1444622979','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('start_time','开始时间','int(10) NULL','datetime','','','1','','0','card_score','1','1','1442457879','1442457879','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('end_time','过期时间','int(10) NULL','datetime','','','1','','0','card_score','1','1','1442457902','1442457902','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('title','活动名称','varchar(100) NULL','string','','','1','','0','card_score','1','1','1442457852','1442457852','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('member','适用人群','varchar(100) NULL','checkbox','0','','1','0:所有用户\r\n-1:所有会员卡成员','0','card_score','0','1','1446107007','1444622808','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('coupon_type','优惠券类型','int(10) NULL','num','0','','1','0:代金券\r\n1:优惠券','0','card_score','0','1','1444727785','1444727785','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('cover_id','活动图片','int(10) UNSIGNED NULL','picture','','','1','','0','card_score','0','1','1444801906','1444801906','','3','','regex','','3','function');
UPDATE `wp_attribute` a, wp_model m SET a.model_id = m.id WHERE a.model_name=m.`name`;


CREATE TABLE IF NOT EXISTS `wp_card_recharge` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`manager_id`  int(10) NULL  COMMENT '管理员ID',
`token`  varchar(50) NULL  COMMENT 'Token',
`cTime`  int(10) NULL  COMMENT '创建时间',
`goods_ids`  text NULL  COMMENT '指定商品ID串',
`is_all_goods`  tinyint(2) NULL  DEFAULT 0 COMMENT '适用的活动商品',
`is_mult`  tinyint(2) NULL  DEFAULT 0 COMMENT '多级优惠',
`start_time`  int(10) NULL  COMMENT '开始时间',
`end_time`  int(10) NULL  COMMENT '过期时间',
`title`  varchar(100) NULL  COMMENT '活动名称',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('card_recharge','充值赠送活动','0','','1','["title","start_time","end_time","is_mult","is_all_goods"]','1:基础','','','','','title:活动名称\r\nstart_time:有效期\r\nstatus:活动状态\r\nid:操作:[EDIT]|编辑,[DELETE]|删除','10','title:请输入活动名称搜索','','1442457808','1442544407','1','MyISAM','Card');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('manager_id','管理员ID','int(10) NULL','num','','','0','','0','card_recharge','0','1','1442458516','1442458516','','3','','regex','get_mid','1','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('token','Token','varchar(50) NULL','string','','','0','','0','card_recharge','0','1','1442458480','1442458480','','3','','regex','get_token','1','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('cTime','创建时间','int(10) NULL','datetime','','','0','','0','card_recharge','0','1','1442458439','1442458439','','3','','regex','time','1','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('goods_ids','指定商品ID串','text NULL','textarea','','','0','','0','card_recharge','0','1','1442540989','1442458406','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('is_all_goods','适用的活动商品','tinyint(2) NULL','bool','0','','1','0:全部商品参与\r\n1:指定商品参与','0','card_recharge','0','1','1442458365','1442458365','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('is_mult','多级优惠','tinyint(2) NULL','bool','0','多级情况下每级优惠不累积叠加','1','0:否\r\n1:是','0','card_recharge','0','1','1442458033','1442458011','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('start_time','开始时间','int(10) NULL','datetime','','','1','','0','card_recharge','1','1','1442457879','1442457879','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('end_time','过期时间','int(10) NULL','datetime','','','1','','0','card_recharge','1','1','1442457902','1442457902','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('title','活动名称','varchar(100) NULL','string','','','1','','0','card_recharge','1','1','1442457852','1442457852','','3','','regex','','3','function');
UPDATE `wp_attribute` a, wp_model m SET a.model_id = m.id WHERE a.model_name=m.`name`;


CREATE TABLE IF NOT EXISTS `wp_card_recharge_condition` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`money_param`  decimal(11,2) NULL  COMMENT '现金参数',
`money`  tinyint(2) NULL  COMMENT '现在开关',
`reward_id`  int(10) NULL  COMMENT '活动ID',
`sort`  int(10) NULL  DEFAULT 0 COMMENT '排序号',
`condition`  decimal(11,2) NULL  COMMENT '条件',
`score`  tinyint(2) NULL  DEFAULT 0 COMMENT '积分开关',
`score_param`  int(10) NULL  COMMENT '积分参数',
`shop_coupon`  tinyint(2) NULL  DEFAULT 0 COMMENT '优惠券开关',
`shop_coupon_param`  int(10) NULL  COMMENT '优惠券ID',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('card_recharge_condition','充值赠送条件','0','','1','["postage","money_param","sort","condition","score","score_param","shop_coupon","shop_coupon_param"]','1:基础','','','','','','10','','','1442458767','1444641566','1','MyISAM','Card');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('money_param','现金参数','decimal(11,2) NULL','num','','','1','','0','card_recharge_condition','0','1','1442542160','1442542160','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('money','现在开关','tinyint(2) NULL','bool','','','0','0:关\r\n1:开','0','card_recharge_condition','0','1','1442542127','1442542127','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('reward_id','活动ID','int(10) NULL','num','','','0','','0','card_recharge_condition','0','1','1442458906','1442458906','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('sort','排序号','int(10) NULL','num','0','','1','','0','card_recharge_condition','0','1','1442544909','1442544909','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('condition','条件','decimal(11,2) NULL','num','','满多少元','1','','0','card_recharge_condition','1','1','1442458834','1442458834','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('score','积分开关','tinyint(2) NULL','bool','0','','1','0:关\r\n1:开','0','card_recharge_condition','0','1','1442542268','1442542268','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('score_param','积分参数','int(10) NULL','num','','','1','','0','card_recharge_condition','0','1','1442542292','1442542292','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('shop_coupon','优惠券开关','tinyint(2) NULL','bool','0','','1','0:关\r\n1:开','0','card_recharge_condition','0','1','1442542329','1442542329','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('shop_coupon_param','优惠券ID','int(10) NULL','num','','','1','','0','card_recharge_condition','0','1','1442542366','1442542366','','3','','regex','','3','function');
UPDATE `wp_attribute` a, wp_model m SET a.model_id = m.id WHERE a.model_name=m.`name`;


CREATE TABLE IF NOT EXISTS `wp_card_custom` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`manager_id`  int(10) NULL  COMMENT '管理员ID',
`token`  varchar(50) NULL  COMMENT 'Token',
`cTime`  int(10) NULL  COMMENT '创建时间',
`score`  int(10) NULL  DEFAULT 0 COMMENT '积分数',
`coupon_id`  int(10) NULL  COMMENT '商城优惠券',
`is_show`  tinyint(2) NULL  DEFAULT 0 COMMENT '是否在会员卡界面展示',
`start_time`  int(10) NULL  COMMENT '节日时间',
`end_time`  int(10) NULL  COMMENT '赠送时间',
`title`  varchar(100) NULL  COMMENT '活动名称',
`type`  tinyint(2) NULL  DEFAULT 0 COMMENT '活动策略',
`content`  text NULL  COMMENT '活动说明',
`member`  int(10) NULL  COMMENT '适用人群',
`is_birthday`  tinyint(2) NULL  DEFAULT 0 COMMENT '节日类型',
`before_day`  tinyint(2) NULL  DEFAULT 1 COMMENT '生日前',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('card_custom','客户关怀活动','0','','1','["title","start_time","end_time","type","score","is_show","content","coupon_id","member","is_birthday","before_day"]','1:基础','','','','','title:节日名称\r\nstart_time:节日时间\r\nmember:目标人群\r\nend_time:赠送时间\r\ntype:赠送内容\r\nid:操作:[EDIT]|编辑,[DELETE]|删除','10','title:请输入活动名称搜索','','1442457808','1444647144','1','MyISAM','Card');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('manager_id','管理员ID','int(10) NULL','num','','','0','','0','card_custom','0','1','1442458516','1442458516','','3','','regex','get_mid','1','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('token','Token','varchar(50) NULL','string','','','0','','0','card_custom','0','1','1442458480','1442458480','','3','','regex','get_token','1','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('cTime','创建时间','int(10) NULL','datetime','','','0','','0','card_custom','0','1','1442458439','1442458439','','3','','regex','time','1','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('score','积分数','int(10) NULL','num','0','','1','','0','card_custom','0','1','1444622853','1444622853','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('coupon_id','商城优惠券','int(10) NULL','num','','','1','','0','card_custom','0','1','1444634724','1444622932','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('is_show','是否在会员卡界面展示','tinyint(2) NULL','bool','0','','1','0:否\r\n1:是','0','card_custom','0','1','1444646159','1444622979','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('start_time','节日时间','int(10) NULL','datetime','','','1','','0','card_custom','0','1','1444647869','1442457879','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('end_time','赠送时间','int(10) NULL','datetime','','','1','','0','card_custom','0','1','1444647885','1442457902','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('title','活动名称','varchar(100) NULL','string','','','1','','0','card_custom','1','1','1442457852','1442457852','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('type','活动策略','tinyint(2) NULL','bool','0','','1','0:送积分\r\n1:送优惠券','0','card_custom','0','1','1444622808','1444622808','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('content','活动说明','text NULL','textarea','','','1','','0','card_custom','0','1','1444623015','1444623015','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('member','适用人群','int(10) NULL','num','','','1','','0','card_custom','0','1','1444646000','1444646000','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('is_birthday','节日类型','tinyint(2) NULL','bool','0','','1','0:公历节日\r\n1:会员生日','0','card_custom','0','1','1444646065','1444646065','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('before_day','生日前','tinyint(2) NULL','num','1','','1','','0','card_custom','0','1','1444646117','1444646117','','3','','regex','','3','function');
UPDATE `wp_attribute` a, wp_model m SET a.model_id = m.id WHERE a.model_name=m.`name`;


CREATE TABLE IF NOT EXISTS `wp_score_exchange_log` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`card_score_id`  int(10) NULL  COMMENT '兑换活动id',
`token`  varchar(255) NULL  COMMENT 'token',
`uid`  int(10) NULL  COMMENT 'uid',
`ctime`  int(10) NULL  COMMENT 'ctime',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('score_exchange_log','兑换记录','0','','1','','1:基础','','','','','','10','','','1444731340','1444731340','1','MyISAM','Card');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('card_score_id','兑换活动id','int(10) NULL','num','','','1','','0','score_exchange_log','0','1','1444731420','1444731420','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('token','token','varchar(255) NULL','string','','','1','','0','score_exchange_log','0','1','1444799211','1444731443','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('uid','uid','int(10) NULL','num','','','1','','0','score_exchange_log','0','1','1444731483','1444731483','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('ctime','ctime','int(10) NULL','datetime','','','1','','0','score_exchange_log','0','1','1444731520','1444731520','','3','','regex','','3','function');
UPDATE `wp_attribute` a, wp_model m SET a.model_id = m.id WHERE a.model_name=m.`name`;


CREATE TABLE IF NOT EXISTS `wp_share_log` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`uid`  int(10) NULL  COMMENT '用户id',
`sTime`  int(10) NULL  COMMENT '分享时间',
`token`  varchar(255) NULL  COMMENT 'token',
`score`  int(10) NULL  COMMENT '积分',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('share_log','分享记录','0','','1','','1:基础','','','','','','10','','','1444789662','1444789662','1','MyISAM','Card');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('uid','用户id','int(10) NULL','num','','','1','','0','share_log','0','1','1444789735','1444789735','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('sTime','分享时间','int(10) NULL','datetime','','','1','','0','share_log','0','1','1444789762','1444789762','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('token','token','varchar(255) NULL','string','','','1','','0','share_log','0','1','1444789785','1444789785','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('score','积分','int(10) NULL','num','','','1','','0','share_log','0','1','1444789813','1444789813','','3','','regex','','3','function');
UPDATE `wp_attribute` a, wp_model m SET a.model_id = m.id WHERE a.model_name=m.`name`;


