<?php

namespace Addons\DailyTime;
use Common\Controller\Addon;

/**
 * 学员考勤插件
 * @author 无名
 */

    class DailyTimeAddon extends Addon{

        public $info = array(
            'name'=>'DailyTime',
            'title'=>'学员考勤',
            'description'=>'学员上学记录',
            'status'=>1,
            'author'=>'无名',
            'version'=>'0.1',
            'has_adminlist'=>1
        );

	public function install() {
		$install_sql = './Addons/DailyTime/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/DailyTime/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }