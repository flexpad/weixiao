<?php

namespace Addons\Weiba\Controller;

use Home\Controller\AddonsController;

class WeibaController extends AddonsController {
	var $model;
	function _initialize() {
		$this->model = $this->getModel ( 'weiba' );
	}
	public function lists() {
		$public_id = get_token_appinfo ( '', 'id' );
		$this->assign('normal_tips','微社区首页链接:'.addons_url('Weiba://Wap/index',array('publicid'=>$public_id)).'&nbsp;&nbsp;&nbsp;<a class="btn" style="padding:2px 10px" id="copyLink" href="javascript:;" data-clipboard-text="'.addons_url('Weiba://Wap/index',array('publicid'=>$public_id)).'">复制链接</a><script>$.WeiPHP.initCopyBtn(\'copyLink\');
</script>');
		$map ['token'] = get_token ();
		$cateArr = M ( 'weiba_category' )->where ( $map )->getFields ( 'id,name' );
		$list_data = $this->_get_model_list ( $this->model );
		foreach ( $list_data ['list_data'] as &$data ) {
			$data ['cid'] = $cateArr [$data ['cid']];
		}
		$this->assign ( $list_data );
		
		$this->display ();
	}
	public function edit() {
	    $model =$this->getModel ( 'weiba' );
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
	        $this->checkTitle($_POST['weiba_name'],$_POST['cid'],$id);
	        $where['id']=$id;
	       // dump($_POST['admin_uid']);
	        if(is_array($_POST['admin_uid'])){
	        $_POST['admin_uid']=implode($_POST['admin_uid']);
	        }
	      //dump($_POST['admin_uid']);
	       // M('weiba')->where()->setField('admin_uid',$admin_uid);
	        $res=false;
	        $res=$Model->save ($_POST);
	        if ($res!=false) {
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
	        //dump($data);
	        //$data['admin_uid'] =explode(',',$data['admin_uid']);
	        $this->assign ( 'data', $data );
	        $this->display();
	    }
	}
	
	// 通用插件的增加模型
	public function add() {
	    $model =$this->getModel ( 'weiba' );
	    $id=I('id');
	    if (IS_POST) {
	        $Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
	        // 获取模型的字段信息
	        $Model = $this->checkAttr ( $Model, $model ['id'] );
	        $this->checkTitle($_POST['weiba_name'],$_POST['cid'],$id);
	        if(is_array($_POST['admin_uid'])){
	        $_POST['admin_uid']=implode($_POST['admin_uid']);
	        }
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
	public function checkTitle($title,$cid){
	    $map['weiba_name']=$title;
	    $map['token']=get_token();
	    $map['cid'] =$cid; 
	    $map['id']=array('neq',$id);
	    //dump($map);
	    $res =M('weiba')->where($map)->find();
	    if($res){
	        $this->error('版块名已存在');
	    }
	}
}
