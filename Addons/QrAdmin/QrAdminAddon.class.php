<?php

namespace Addons\QrAdmin;
use Common\Controller\Addon;

/**
 * 扫码管理插件
 * @author 凡星
 */

    class QrAdminAddon extends Addon{

        public $info = array(
            'name'=>'QrAdmin',
            'title'=>'扫码管理',
            'description'=>'在服务号的情况下，可以自主创建一个二维码，并可指定扫码后用户自动分配到哪个用户组，绑定哪些标签',
            'status'=>1,
            'author'=>'凡星',
            'version'=>'0.1',
            'has_adminlist'=>1
        );

	public function install() {
		$install_sql = './Addons/QrAdmin/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/QrAdmin/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }