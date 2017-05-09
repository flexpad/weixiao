<?php

namespace Addons\Course;
use Common\Controller\Addon;

/**
 * 课程展示插件
 * @author qiaoc
 */

    class CourseAddon extends Addon{

        public $info = array(
            'name'=>'Course',
            'title'=>'课程展示',
            'description'=>'展示培训课程的插件。',
            'status'=>1,
            'author'=>'qiaoc',
            'version'=>'0.1',
            'has_adminlist'=>1
        );

	public function install() {
		$install_sql = './Addons/Course/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/Course/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }