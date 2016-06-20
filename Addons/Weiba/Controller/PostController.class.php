<?php

namespace Addons\Weiba\Controller;

use Home\Controller\AddonsController;

class PostController extends AddonsController {
	var $model;
	function _initialize() {
		$this->model = $this->getModel ( 'weiba_post' );
	}
	public function lists() {
		$map ['token'] = get_token ();
		$cateArr = M ( 'weiba' )->where ( $map )->getFields ( 'id,weiba_name' );
		$list_data = $this->_get_model_list ( $this->model );
		foreach ( $list_data ['list_data'] as &$data ) {
			$data ['weiba_id'] = $cateArr [$data ['weiba_id']];
			$data ['post_uid'] = get_nickname ( $data ['post_uid'] );
		}
		$this->assign ( $list_data );
		
		$this->display ();
	}
	public function del() {
		parent::common_del ( $this->model );
	}
	
	// 通用插件的编辑模型
	public function edit() {
		parent::common_edit ( $this->model );
	}
	
	// 通用插件的增加模型
	public function add($model = null) {
		parent::common_add ( $this->model );
	}
	//首页帖子
	public function indexLists(){
		$add_url = U('edit_index',array('mdm'=>I('mdm')));
		$del_button = false;
		$this->assign('add_url',$add_url);
		$this->assign('del_button',$del_button);
		$map ['token'] = get_token ();
		
		$cateArr = M ( 'weiba' )->where ( $map )->getFields ( 'id,weiba_name' );
		session ( 'common_condition' ,array('is_index'=>1));
		$list_data = $this->_get_model_list ( $this->model );
		foreach ( $list_data ['list_data'] as &$data ) {
			$data ['weiba_id'] = $cateArr [$data ['weiba_id']];
			$data ['post_uid'] = get_nickname ( $data ['post_uid'] );
		}
		$list_data['list_grids']['ids']['href'] = "edit_index&id=[id]|编辑首页帖子,del_index&id=[id]|删除首页帖子,";
		//dump($list_data);
		$this->assign ( $list_data );
		$this->display (SITE_PATH.'/Application/Home/View/default/Addons/lists.html');
	}
	//编辑首页帖子
	public function edit_index(){
		
		$id = intval($_GET['id']);
		if($id){
			$post = D('weiba_post')->where(array('id'=>$id))->find();
			$data['id'] = $id;
			$data['title']=$post['title'];
			$data['index_img'] = $post['index_img'];
			$this->assign('data',$data);
		}
		if(IS_POST){
			$id = intval($_POST['id']);
			$data['is_index'] = 1;
			$data['index_img'] = $_POST['index_img'];
			$data['title']=$_POST['title'];
			
			$res = D('weiba_post')->where(array('id'=>$id))->save($data);
			if($res){
				$this->success ( '编辑成功',addons_url('Weiba://Post/indexLists',array('mdm'=>I('mdm'))) );
			} else {
				$this->error ( '编辑失败！' );
			}
		}
		$this->display();
		
	}
	//编辑首页帖子
	public function del_index(){
		$id = intval($_GET['id']);
		$res = D('weiba_post')->where(array('id'=>$id))->setField('is_index',0);
		if(res){
			$this->success ( '删除成功' );
		} else {
			$this->error ( '删除失败！' );
		}
	}
	public function post_data(){
	    $page = I ( 'p', 1, 'intval' );
	    //$wmodel =get_model('weiba');
	    $map ['token'] = get_token ();
	    $map['is_index'] =array('neq',1);
	    $cateArr = M ( 'weiba_category' )->where ( $map )->getFields ( 'id,name' );
	    //$list_data = $this->_get_model_list ( $wmodel );
	    $list_data =M('weiba_post')->where($map)->order ( 'id DESC' )->page ( $page, 20 )->selectPage ( 20 );
	    foreach ( $list_data ['list_data'] as &$data ) {
	        $data ['cid'] = $cateArr [$data ['cid']];
	    }
	    $this->ajaxReturn( $list_data ,'JSON');
	}
}

     