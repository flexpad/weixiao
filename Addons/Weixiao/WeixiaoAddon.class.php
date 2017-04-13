<?php

namespace Addons\Weixiao;
use Common\Controller\Addon;

/**
 * 微校（全日制）插件
 * @author weixiao team
 */

    class weixiaoAddon extends Addon{

        public $info = array(
            'name'=>'Weixiao',
            'title'=>'微校（全日制）',
            'description'=>'应用于全日制学校的初级教务系统，提供：成绩查询、学生资料管理等功能。',
            'status'=>1,
            'author'=>'weixiao team',
            'version'=>'0.1',
            'has_adminlist'=>1
        );

	public function install() {
		$install_sql = './Addons/Weixiao/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/Weixiao/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }