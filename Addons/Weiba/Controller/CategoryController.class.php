<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace Addons\Weiba\Controller;

use Home\Controller\AddonsController;

class CategoryController extends AddonsController {
	var $model;
	function _initialize() {
		$this->model = $this->getModel ( 'weiba_category' );
	}
	public function lists() {
		parent::common_lists ( $this->model );
	}
	public function del() {
		parent::common_del ( $this->model );
	}
	
	// 通用插件的编辑模型
	public function edit() {
		$model =$this->getModel ( 'weiba_category' );
		is_array ( $model ) || $model = $this->getModel ( $model );
		$id || $id = I ( 'id' );
		
		// 获取数据
		$data = M ( get_table_name ( $model ['id'] ) )->find ( $id );
		$data || $this->error ( '数据不存在！' );
		
		$token = get_token ();
		if (isset ( $data ['token'] ) && $token != $data ['token'] && defined ( 'ADDON_PUBLIC_PATH' )) {
		    $this->error ( '非法访问！' );
		}
		
		if (IS_POST) {
		    $Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
		    // 获取模型的字段信息
		    $Model = $this->checkAttr ( $Model, $model ['id'] );
		    $this->checkTitle($_POST['name'],$id);
		    if ($Model->create () && $Model->save ()) {
		        $this->_saveKeyword ( $model, $id );
		
		        // 清空缓存
		        method_exists ( $Model, 'clear' ) && $Model->clear ( $id, 'edit' );
		
		        $this->success ( '保存' . $model ['title'] . '成功！', U ( 'lists?model=' . $model ['name'], $this->get_param ) );
		    } else {
		        $this->error ( $Model->getError () );
		    }
		} else {
		    $fields = get_model_attribute ( $model ['id'] );
		    $this->assign ( 'fields', $fields );
		    $this->assign ( 'data', $data );
		    $this->display();
		}
	}
	
	// 通用插件的增加模型
	public function add() {
	    $model =$this->getModel ( 'weiba_category' );
		if (IS_POST) {
		    $Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
		    // 获取模型的字段信息
		    $Model = $this->checkAttr ( $Model, $model ['id'] );
		    $this->checkTitle($_POST['name']);
		    if ($Model->create () && $id = $Model->add ()) {
		        $this->_saveKeyword ( $model, $id );
		
		        // 清空缓存
		        method_exists ( $Model, 'clear' ) && $Model->clear ( $id, 'add' );
		
		        $this->success ( '添加' . $model ['title'] . '成功！', U ( 'lists?model=' . $model ['name'], $this->get_param ) );
		    } else {
		        $this->error ( $Model->getError () );
		    }
		} else {
		    $fields = get_model_attribute ( $model ['id'] );
		    $this->assign ( 'fields', $fields );
		    	
		  $this->display();
		}
	}
	public function checkTitle($title,$id){
	    $map['name']=$title;
	    $map['token']=get_token();
	    if($id){
	        $map['id']=array('neq',$id);
	    }
	    
	    $res =M('weiba_category')->where($map)->find();
	   
	    if($res){
	        $this->error('分类已存在');
	    }
	}
}