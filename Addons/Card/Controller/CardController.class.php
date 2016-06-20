<?php

namespace Addons\Card\Controller;

use Addons\Card\Controller\BaseController;

class CardController extends CardSetController {
	function config() {
		//$normal_tips = '配置完保存后，在微信里回复: 会员卡，即可看到效果。';
		//$this->assign ( 'normal_tips', $normal_tips );
		
		$this->getModel ();
		
		if (IS_POST) {
			if ($_POST ['config'] ['background'] == 11) {
				$_POST ['config'] ['background_custom'] = get_cover_url ( $_POST ['config'] ['bg'] );
				$_POST ['config'] ['bg_id'] =  $_POST ['config'] ['bg'] ;
			}
			if ($_POST ['config'] ['back_background'] == 11) {
				$_POST ['config'] ['back_background_custom'] = get_cover_url ( $_POST ['config'] ['backbg'] );
				$_POST ['config'] ['backbg_id'] =  $_POST ['config'] ['backbg'] ;
			}
			
			$flag = D ( 'Common/AddonConfig' )->set ( _ADDONS, $_POST ['config'] );
			
			if ($flag !== false) {
				$this->success ( '保存成功' );
			} else {
				$this->error ( '保存失败' );
			}
		}
		
		$map ['name'] = _ADDONS;
		$addon = M ( 'Addons' )->where ( $map )->find ();
		if (! $addon)
			$this->error ( '插件未安装' );
		
		$addon_class = get_addon_class ( $addon ['name'] );
		
		$data = new $addon_class ();
		$addon ['addon_path'] = $data->addon_path;
		$addon ['custom_config'] = $data->custom_config;
		$db_config = D ( 'Common/AddonConfig' )->get ( _ADDONS );
		$addon ['config'] = include $data->config_file;
		if ($db_config) {
			foreach ( $addon ['config'] as $key => $value ) {
				if ($value ['type'] != 'group') {
					! isset ( $db_config [$key] ) || $addon ['config'] [$key] ['value'] = $db_config [$key];
				} else {
					foreach ( $value ['options'] as $gourp => $options ) {
						foreach ( $options ['options'] as $gkey => $value ) {
							! isset ( $db_config [$key] ) || $addon ['config'] [$key] ['options'] [$gourp] ['options'] [$gkey] ['value'] = $db_config [$gkey];
						}
					}
				}
			}
		}
		$this->assign ( 'data', $addon );
		//var_dump('$addon');
		
		$this->display ();
	}
	function preview() {
		$url = addons_url ( 'Card://Wap/index', array (
				'publicid' => get_token_appinfo ( '', 'id' ) 
		) );
		$this->assign ( 'url', $url );
		$this->display ( SITE_PATH . '/Application/Home/View/default/Addons/preview.html' );
	}
}
