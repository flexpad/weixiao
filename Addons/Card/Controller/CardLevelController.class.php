<?php

namespace Addons\Card\Controller;

use Addons\Card\Controller\BaseController;

class CardLevelController extends CardSetController {
	var $model;
	function _initialize() {
		$this->model = $this->getModel ( 'card_level' );
		parent::_initialize ();
		
	}
	function lists(){
		$list_data = $this->_get_model_list ( $this->model );
		foreach($list_data['list_data'] as &$vo){
			$vo['discount'] = $vo['discount']."%";
		}
		$this -> assign($list_data);
		$this->display();
	}
}
