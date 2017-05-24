<?php

namespace Addons\Weizk;
use Common\Controller\Addon;

/**
 * 微中考插件
 * @author weixiao team
 */

    class WeizkAddon extends Addon{

        public $info = array(
            'name'=>'Weizk',
            'title'=>'微中考',
            'description'=>'微中考，提供信息，测评和在线咨询',
            'status'=>1,
            'author'=>'weixiao team',
            'version'=>'0.1',
            'has_adminlist'=>1
        );

	public function install() {
		$install_sql = './Addons/Weizk/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/Weizk/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }