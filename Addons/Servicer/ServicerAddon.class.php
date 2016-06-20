<?php

namespace Addons\Servicer;
use Common\Controller\Addon;

/**
 * 工作授权插件
 * @author jacy
 */

    class ServicerAddon extends Addon{

        public $info = array(
            'name'=>'Servicer',
            'title'=>'工作授权',
            'description'=>'关注公众号后，扫描授权二维码，获取工作权限',
            'status'=>1,
            'author'=>'jacy',
            'version'=>'0.1',
            'has_adminlist'=>1
        );

	public function install() {
		$install_sql = './Addons/Servicer/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/Servicer/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }