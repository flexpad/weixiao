CREATE TABLE IF NOT EXISTS `wp_help_open` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`title`  varchar(100) NULL  COMMENT '活动名称',
`start_time`  int(10) NULL  COMMENT '开始时间',
`end_time`  int(10) NULL  COMMENT '过期时间',
`limit_num`  int(10) NULL  DEFAULT 5 COMMENT '帮拆人数限制',
`content`  text  NULL  COMMENT '活动规则',
`token`  varchar(50) NULL  COMMENT 'token',
`manager_id`  int(10) NULL  COMMENT 'manager_id',
`prize_num`  int(10) NULL  COMMENT '大礼包数量',
`status`  tinyint(2) NULL  DEFAULT 1 COMMENT '是否开启',
`collect_tips`  text NULL  COMMENT '领取说明',
`share_icon`  int(10) UNSIGNED NULL  COMMENT '分享图标',
`share_title`  varchar(100) NULL  COMMENT '分享标题',
`share_intro`  varchar(255) NULL  COMMENT '分享简介',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('help_open','帮拆礼包','0','','1','["title","start_time","end_time","limit_num","content","prize_num","status","collect_tips","share_icon","share_title","share_intro"]','1:基础','','','','','title:礼包名称\r\nstatus:状态\r\nprize_num:大礼包总数\r\ncollect_num:大礼包领取\r\nlimit_num:分享要求\r\ntotal:领取总数\r\nstart_time:有效期\r\nid:操作:[EDIT]|编辑,prize_lists?id=[id]|获奖查询,share_lists?id=[id]|分享记录,sncode_lists?id=[id]|核销记录,[DELETE]|删除,preview?id=[id]|预览,index&_addons=HelpOpen&_controller=Wap&id=[id]&invite_uid=1|复制链接','10','title:请输入活动名称','','1443108580','1446429456','1','MyISAM','HelpOpen');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('title','活动名称','varchar(100) NULL','string','','','1','','0','help_open','1','1','1443111611','1443111611','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('start_time','开始时间','int(10) NULL','datetime','','','1','','0','help_open','0','1','1443111673','1443111673','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('end_time','过期时间','int(10) NULL','datetime','','','1','','0','help_open','0','1','1443111693','1443111693','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('limit_num','帮拆人数限制','int(10) NULL','num','5','多少好友帮拆开礼包才有效','1','','0','help_open','0','1','1443111821','1443111821','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('content','活动规则','text  NULL','textarea','','','1','','0','help_open','0','1','1445078360','1443111915','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('token','token','varchar(50) NULL','string','','','0','','0','help_open','0','1','1443148889','1443148889','','3','','regex','get_token','1','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('manager_id','manager_id','int(10) NULL','num','','','0','','0','help_open','0','1','1443148912','1443148912','','3','','regex','get_mid','1','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('prize_num','大礼包数量','int(10) NULL','num','','','1','','0','help_open','0','1','1445078232','1445057624','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('status','是否开启','tinyint(2) NULL','bool','1','','1','0:禁用\r\n1:启用','0','help_open','0','1','1445072096','1445072096','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('collect_tips','领取说明','text NULL','textarea','','','1','','0','help_open','0','1','1445072703','1445072703','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('share_icon','分享图标','int(10) UNSIGNED NULL','picture','','','1','','0','help_open','0','1','1445078407','1445078407','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('share_title','分享标题','varchar(100) NULL','string','','','1','','0','help_open','0','1','1445078427','1445078427','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('share_intro','分享简介','varchar(255) NULL','string','','','1','','0','help_open','0','1','1445078450','1445078450','','3','','regex','','3','function');
UPDATE `wp_attribute` a, wp_model m SET a.model_id = m.id WHERE a.model_name=m.`name`;


CREATE TABLE IF NOT EXISTS `wp_help_open_user` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`invite_uid`  int(10) NULL  COMMENT '邀请人ID',
`friend_uid`  int(10) NULL  COMMENT '帮拆人ID',
`help_id`  int(10) NULL  COMMENT '活动ID',
`cTime`  int(10) NULL  COMMENT '创建时间',
`sn_id`  int(10) NULL  COMMENT 'sn',
`join_count`  int(10) NULL  DEFAULT 0 COMMENT '领取数量',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('help_open_user','帮拆参与人记录','0','','1','["cTime","join_count"]','1:基础','','','','','userface: 用户头像\r\nnickname:分享用户\r\njoin_count:获取数量\r\ncTime:分享时间\r\nids:操作:collect_lists?invite_uid=[invite_uid]&help_id=[help_id]|获取人列表','10','title:请输入分享用户名称','','1443141956','1464421255','1','MyISAM','HelpOpen');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('invite_uid','邀请人ID','int(10) NULL','num','','','0','','0','help_open_user','0','1','1443141991','1443141991','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('friend_uid','帮拆人ID','int(10) NULL','num','','','0','','0','help_open_user','0','1','1443142036','1443142036','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('help_id','活动ID','int(10) NULL','num','','','0','','0','help_open_user','0','1','1443142057','1443142057','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('cTime','创建时间','int(10) NULL','num','','','1','','0','help_open_user','0','1','1443142208','1443142208','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('sn_id','sn','int(10) NULL','num','','','0','','0','help_open_user','0','1','1445240730','1444462169','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('join_count','领取数量','int(10) NULL','num','0','','1','','0','help_open_user','0','1','1445240488','1445240488','','3','','regex','','3','function');
UPDATE `wp_attribute` a, wp_model m SET a.model_id = m.id WHERE a.model_name=m.`name`;


CREATE TABLE IF NOT EXISTS `wp_help_open_prize` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
`help_id`  int(10) NULL  COMMENT '活动ID',
`sort`  int(10) NULL  COMMENT '序号',
`name`  varchar(100) NULL  COMMENT '奖项名称',
`prize_type`  tinyint(1) NULL  DEFAULT 0 COMMENT '奖项类型',
`coupon_id`  int(10) NULL  COMMENT '优惠券ID',
`shop_coupon_id`  int(10) NULL  COMMENT '代金券ID',
`money`  decimal(11,2) NULL  COMMENT '返现金额',
`is_del`  int(10) NULL  DEFAULT 0 COMMENT '是否删除',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci CHECKSUM=0 ROW_FORMAT=DYNAMIC DELAY_KEY_WRITE=0;
INSERT INTO `wp_model` (`name`,`title`,`extend`,`relation`,`need_pk`,`field_sort`,`field_group`,`attribute_list`,`template_list`,`template_add`,`template_edit`,`list_grid`,`list_row`,`search_key`,`search_list`,`create_time`,`update_time`,`status`,`engine_type`,`addon`) VALUES ('help_open_prize','礼包奖项','0','','1','["sort","name","prize_type","coupon_id","shop_coupon_id","money"]','1:基础','','','','','userface:获得用户\r\nnickname:名称\r\ntype:类型\r\nprize:获得礼包\r\ncTime:获取时间\r\ndeal:操作','10','title:请输入用户名称','','1445080823','1445241336','1','MyISAM','HelpOpen');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('help_id','活动ID','int(10) NULL','num','','','0','','0','help_open_prize','0','1','1445080858','1445080858','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('sort','序号','int(10) NULL','num','','','1','','0','help_open_prize','0','1','1445080918','1445080918','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('name','奖项名称','varchar(100) NULL','string','','','1','','0','help_open_prize','0','1','1445080939','1445080939','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('prize_type','奖项类型','tinyint(1) NULL','radio','0','','1','0:请选择\r\n1:优惠券\r\n2:代金券\r\n3:返现','0','help_open_prize','0','1','1445081030','1445081030','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('coupon_id','优惠券ID','int(10) NULL','num','','','1','','0','help_open_prize','0','1','1445081094','1445081094','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('shop_coupon_id','代金券ID','int(10) NULL','num','','','1','','0','help_open_prize','0','1','1445081136','1445081136','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('money','返现金额','decimal(11,2) NULL','num','','','1','','0','help_open_prize','0','1','1445081198','1445081198','','3','','regex','','3','function');
INSERT INTO `wp_attribute` (`name`,`title`,`field`,`type`,`value`,`remark`,`is_show`,`extra`,`model_id`,`model_name`,`is_must`,`status`,`update_time`,`create_time`,`validate_rule`,`validate_time`,`error_info`,`validate_type`,`auto_rule`,`auto_time`,`auto_type`) VALUES ('is_del','是否删除','int(10) NULL','num','0','','0','','0','help_open_prize','0','1','1462331012','1462331012','','3','','regex','','3','function');
UPDATE `wp_attribute` a, wp_model m SET a.model_id = m.id WHERE a.model_name=m.`name`;


