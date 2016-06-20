<?php

namespace Addons\Card\Controller;

use Addons\Card\Controller\BaseController;

class CardSetController extends BaseController {
	function _initialize() {
		parent::_initialize();
		
		$controller = strtolower ( _CONTROLLER );
		// 子导航
		$action = strtolower ( _ACTION );
		
		$res ['title'] = '卡片设置';
		$res ['url'] = addons_url ( 'Card://Card/config',array('mdm'=>I('mdm')) );
		$res ['class'] = $controller =='card' && $action == 'config' ? 'cur' : '';
		$nav [] = $res;
		
		$res ['title'] = '特权设置';
		$res ['url'] = addons_url ( 'Card://CardPrivilege/lists',array('mdm'=>I('mdm')) );
		$res ['class'] = $controller == 'cardprivilege' ? 'cur' : '';
		$nav [] = $res;
		
		$res ['title'] = '等级设置';
		$res ['url'] = addons_url ( 'Card://CardLevel/lists',array('mdm'=>I('mdm')) );
		$res ['class'] = $controller == 'cardlevel' ? 'cur' : '';
		$nav [] = $res;
		
		$this->assign ( 'sub_nav', $nav );	
	}
}
