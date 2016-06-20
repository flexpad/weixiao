<?php

namespace Addons\SingIn;
use Common\Controller\Addon;

/**
 * 签到插件
 * @author 淡然
 * QQ: 9585216
 */

    class SingInAddon extends Addon{

        public $info = array(
            'name'=>'SingIn',
            'title'=>'签到',
            'description'=>'粉丝每天签到可以获得积分。',
            'status'=>1,
            'author'=>'淡然',
            'version'=>'1.11',
            'has_adminlist'=>1,
            'type'=>1         
        );

	public function install() {
		$install_sql = './Addons/SingIn/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/SingIn/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }