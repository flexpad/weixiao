<?php

namespace Addons\Servicer\Controller;
use Home\Controller\AddonsController;

class ServicerController extends AddonsController{
	var $model;
	function _initialize() {
		$this->model = $this->getModel ( 'servicer' );
		parent::_initialize ();
		$controller = strtolower ( _CONTROLLER );
		
		$res ['title'] = '授权列表';
		$res ['url'] = addons_url ( 'Servicer://Servicer/lists' );
		$res ['class'] = ($controller == 'servicer' && _ACTION == "lists") ? 'current' : '';
		$nav [0] = $res;
		if($controller == 'servicer' && _ACTION == "add"){
			$res ['title'] = '添加授权';
			$res ['url'] = '#';
			$res ['class'] = ($controller == 'servicer' && _ACTION == "add") ? 'current' : '';
			$nav [1] = $res;
		}else if($controller == 'servicer' && _ACTION == "edit"){
			$res ['title'] = '编辑授权';
			$res ['url'] = '#';
			$res ['class'] = ($controller == 'servicer' && _ACTION == "edit") ? 'current' : '';
			$nav [1] = $res;
		}
		$this->assign ( 'nav', $nav );
	}
	// 通用插件的列表模型
	public function lists() {
		$model = $this->model;
		$map ['token'] = get_token ();
		session ( 'common_condition', $map );
		
		// 解析列表规则
		$list_data = $this->_list_grid ( $model );
		$fields = $list_data ['fields'];
		
		// 搜索条件
		$map = $this->_search_map ( $model, $fields );
		
		// 读取模型数据列表
		$name = parse_name ( get_table_name ( $model ['id'] ), true );
		$data = M ( $name )->field ( true )->where ( $map )->order ( $order )->select ();
		
		/* 查询记录总数 */
		
		$list_data ['list_data'] = $data;
		
		$fieldsAttr = get_model_attribute ( $model ['id'] );
		$roleAttr = parse_field_attr($fieldsAttr['role']['extra']);
		foreach($list_data['list_data'] as &$vo){
			$uInfo = getUserInfo($vo['uid']);
			$vo['nickname'] = '<img width="60" src="'.$uInfo['headimgurl'].'"/><br/>'.$uInfo['nickname'];
			$qrCode = 'http://qr.liantu.com/api.php?text='.addons_url('Servicer://Wap/do_login',array('id'=>$vo['id'],'publicid'=>$this->mid));
			$vo['uid'] = '<img class="list_img" width="100" style="width:100px" src="'.$qrCode.'"/>';
			$roles = explode(',',$vo['role']);
			$roleStr = '';
			foreach($roles as $r){
				if(empty($roleStr)){
					$roleStr = $roleAttr[$r];
				}else{
					$roleStr = $roleStr .'<br/>'. $roleAttr[$r];
				}
			}
			$vo['role'] = $roleStr;
// 			$vo['enable'] = $vo['enable']?"启用":"未启用";
			
		}
		
		$this->assign ( $list_data );
		$templateFile = $this->model ['template_list'] ? $this->model ['template_list'] : '';
		$this->display ( $templateFile );
	}
	function set_enable() {
		$save ['enable'] = 1 - I ( 'enable' );
		$map ['id'] = I ( 'id' );
		$res = M ( 'Servicer' )->where ( $map )->save ( $save );
		
		$this->success ( '操作成功' );
	}
}
