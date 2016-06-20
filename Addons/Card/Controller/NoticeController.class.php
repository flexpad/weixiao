<?php

namespace Addons\Card\Controller;

use Addons\Card\Controller\BaseController;

class NoticeController extends BaseController {
	var $model;
	function _initialize() {
		$this->model = $this->getModel ( 'card_notice' );
		parent::_initialize ();
	}
	// 通用插件的列表模型
	public function lists() {
		$map ['token'] = get_token ();
		session ( 'common_condition', $map );
		
		parent::common_lists ( $this->model );
	}
	
	// 通用插件的编辑模型
	public function edit() {
	    $id = I('id');
	    // 获取数据
	    $data = M(get_table_name($this->model['id']))->find($id);
	    $data || $this->error('数据不存在！');
	    if (IS_POST) {
// 	        $this->_checkdata($_POST);
	        $Model = D(parse_name(get_table_name($this->model['id']), 1));
	        // 获取模型的字段信息
	        $Model = $this->checkAttr($Model, $this->model['id']);
	        if ($Model->create() && $Model->save()) {
	            // 清空缓存
	            method_exists($Model, 'clear') && $Model->clear($id, 'edit');
	            // $url= '<script language=javascript>history.go(-1);</script>';
	            $this->success('保存' . $this->model['title'] . '成功！', U('lists?model=' . $this->model['name'], $this->get_param));
	        } else {
	            $this->error($Model->getError());
	        }
	    } else {
	        $fields = get_model_attribute($this->model['id']);
	        $levelData=$this->get_card_level();
	        if ($levelData){
	            foreach ($fields as $key=>&$v){
	                if ($key=='grade'){
	                    foreach ($levelData as $k=>$l){
	                        $v['extra'].=chr(10).$k.':'.$l;
	                    }
	                }
	            }
	        }
	        $this->assign('fields', $fields);
	        $this->assign('data', $data);
	    
	        $this->display();
	    }
	    
// 		parent::common_edit ( $this->model );
	}
	
	// 通用插件的增加模型
	public function add() {
	    if (IS_POST) {
// 	        $this->_checkdata($_POST);
	        $Model = D(parse_name(get_table_name($this->model['id']), 1));
	        // 获取模型的字段信息
	        $Model = $this->checkAttr($Model, $this->model['id']);
	        if ($Model->create() && $id = $Model->add()) {
	            // 清空缓存
	            method_exists($Model, 'clear') && $Model->clear($id, 'add');
	    
	            $this->success('添加' . $this->model['title'] . '成功！', U('lists?model=' . $this->model['name'], $this->get_param));
	        } else {
	            $this->error($Model->getError());
	        }
	    } else {
	        $fields = get_model_attribute($this->model['id']);
	        $levelData=$this->get_card_level();
	        if ($levelData){
	            foreach ($fields as $key=>&$v){
	                if ($key=='grade'){
	                    foreach ($levelData as $k=>$l){
	                         $v['extra'].=chr(10).$k.':'.$l;
	                    }
	                }
	            }
	        }
	        $this->assign('fields', $fields);
	        $this->display();
	    }
// 		parent::common_add ( $this->model );
	}
	
	//获取会员等级
	function get_card_level(){
	    $map['token']=get_token();
	    $data=M('card_level')->where($map)->getFields('id,level');
	    return $data;
	}
	
	// 通用插件的删除模型
	public function del() {
		parent::common_del ( $this->model );
	}
	function show() {
		$map ['token'] = get_token ();
		$uid=$map['uid']=$this->mid;
		$cardMember=M('card_member')->where($map)->find();
		//用户已点进来查看
		$key='cardnotic_'.get_token().'_'.$uid;
		$rrs=S($key);
		if ($rrs > 0){
			$rrs = 0-$rrs;
			S($key,$rrs);
		}
		$news=abs($rrs);
		$this->assign('newnum',$news);
		
		$list = M ( 'card_notice' )->where ( $map )->order ( 'id desc' )->select ();
		foreach ($list as $v){
		    $gradeArr=explode(',',$v['grade']);
		    if ($v['to_uid']==0){
		        if (in_array(0, $gradeArr) || in_array($cardMember['level'], $gradeArr)){
		            $data[]=$v;
		        }
		    }else if ($v['to_uid']==$uid){
		        $data[]=$v;
		    }
		}
		$this->assign ( 'list', $data );
		$this->display ();
	}
}
