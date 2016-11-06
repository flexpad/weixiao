<?php

namespace Addons\Student;
use Common\Controller\Addon;

/**
 * 学生中心插件
 * @author Qiao
 */

    class StudentAddon extends Addon{

        public $info = array(
            'name'=>'Student',
            'title'=>'学生中心',
            'description'=>'学生中心功能：
1. 微信粉丝绑定学生；
2. 学生资料录入和查询',
            'status'=>1,
            'author'=>'Qiao',
            'version'=>'0.1',
            'has_adminlist'=>1
        );

	public function install() {
		$install_sql = './Addons/Student/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/Student/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }