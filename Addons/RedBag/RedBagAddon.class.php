<?php

namespace Addons\RedBag;

use Common\Controller\Addon;

/**
 * 微信红包插件
 *
 * @author 凡星
 */
class RedBagAddon extends Addon {
	public $info = array (
			'name' => 'RedBag',
			'title' => '微信红包',
			'description' => '实现微信红包的金额设置，红包领取，红包素材下载等',
			'status' => 1,
			'author' => '凡星',
			'version' => '0.1',
			'has_adminlist' => 1,
			'type' => 1 
	);
	public function install() {
		$install_sql = './Addons/RedBag/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/RedBag/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}
	
	// 实现的weixin钩子方法
	public function weixin($param) {
	}
}