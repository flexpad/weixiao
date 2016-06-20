<?php

namespace Addons\Weiba;
use Common\Controller\Addon;

/**
 * 微社区插件
 * @author 凡星
 */

    class WeibaAddon extends Addon{

        public $info = array(
            'name'=>'Weiba',
            'title'=>'微社区',
            'description'=>'打造公众号粉丝之间沟通的社区，为粉丝运营提供更多服务',
            'status'=>1,
            'author'=>'凡星',
            'version'=>'0.1',
            'has_adminlist'=>1
        );

	public function install() {
		$install_sql = './Addons/Weiba/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/Weiba/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }