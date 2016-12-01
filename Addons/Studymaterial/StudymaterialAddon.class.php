<?php

namespace Addons\Studymaterial;
use Common\Controller\Addon;

/**
 * 学习资料插件
 * @author Chen Qiao
 */

    class StudymaterialAddon extends Addon{

        public $info = array(
            'name'=>'Studymaterial',
            'title'=>'学习资料',
            'description'=>'学习资料微信订阅和邮件自动分发插件。',
            'status'=>1,
            'author'=>'Chen Qiao',
            'version'=>'0.1',
            'has_adminlist'=>1
        );

	public function install() {
		$install_sql = './Addons/Studymaterial/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/Studymaterial/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }