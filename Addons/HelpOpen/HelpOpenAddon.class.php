<?php

namespace Addons\HelpOpen;
use Common\Controller\Addon;

/**
 * 帮拆礼包插件
 * @author 凡星
 */

    class HelpOpenAddon extends Addon{

        public $info = array(
            'name'=>'HelpOpen',
            'title'=>'帮拆礼包',
            'description'=>'可创建一个帮拆活动，指定需要多个好友帮拆开才能得到礼包里的礼品',
            'status'=>1,
            'author'=>'凡星',
            'version'=>'0.1',
            'has_adminlist'=>1
        );

	public function install() {
		$install_sql = './Addons/HelpOpen/install.sql';
		if (file_exists ( $install_sql )) {
			execute_sql_file ( $install_sql );
		}
		return true;
	}
	public function uninstall() {
		$uninstall_sql = './Addons/HelpOpen/uninstall.sql';
		if (file_exists ( $uninstall_sql )) {
			execute_sql_file ( $uninstall_sql );
		}
		return true;
	}

        //实现的weixin钩子方法
        public function weixin($param){

        }

    }