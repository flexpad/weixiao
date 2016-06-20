<?php

namespace Addons\Card\Controller;

use Addons\Card\Controller\BaseController;

class CardPrivilegeController extends CardSetController {
	var $model;
	function _initialize() {
		$this->model = $this->getModel ( 'card_privilege' );
		parent::_initialize ();

	}
	function lists(){
		$list_data = $this->_get_model_list ( $this->model );
		$levels = M('card_level') ->where($levelMap)->select();
		foreach($list_data['list_data'] as &$vo){
			$ids = explode(',',$vo['grade']);
			$gradeArr = array();
			foreach($ids as $id){
				if((int)$id>0){
					foreach($levels as $level){
						if($level['id']==(int)trim($id)){
							$gradeArr[] = $level['level'];
						}
					}
				}else{
					$gradeArr[] = "全部用户";
				}
			}
			$vo['grade'] = implode('<br/>',$gradeArr);
			if($vo['start_time']<time() && time()<$vo['end_time'] && $vo['enable']==1){
				$vo['status'] = "<span style='color:green'>进行中</span>";

			}else if($vo['start_time']>time()){
				$vo['status'] = "<span style='color:red'>未开始</span>";
			}else if($vo['end_time']<time()){
				$vo['status'] = "<span style='color:gray'>已结束</span>";
			}else{
				$vo['status'] = "<span style='color:red'>不可用</span>";
			}
		}
		$this -> assign($list_data);
		$this->display();
	}

	function add(){
		$this->checkPostData ();
		$model = $this -> model;
		if (IS_POST) {
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
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
			$levelMap['token'] = get_token();
			$extra = M('card_level') ->where($levelMap)->select();
			$extraStr = '0:全部用户';
			foreach($extra as $vo){
				$extraStr = $extraStr .'
				'. $vo['id'].':'.$vo['level'];
			}
			$fields['grade']['extra'] = $extraStr;
			$this->assign ( 'fields', $fields );
			//$templateFile || $templateFile = $model ['template_add'] ? $model ['template_add'] : '';
			$this->display ();
		}
	}
	function edit(){
		$this->checkPostData ();
	    $id = I('id');
	    $model = $this -> model;
	    // 获取数据
	    $data = M(get_table_name($model['id']))->find($id);
	    $data || $this->error('数据不存在！');

	    if (IS_POST) {
	        $Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
	        // 获取模型的字段信息
	        $Model = $this->checkAttr ( $Model, $model ['id'] );
	       	$res = false;
			$Model->create () && $res=$Model->save ();
			if ($res !== false) {

	            // 清空缓存
	            method_exists ( $Model, 'clear' ) && $Model->clear ( $id, 'edit' );

	            $this->success ( '保存' . $model ['title'] . '成功！', U ( 'lists?model=' . $model ['name'], $this->get_param ) );
	        } else {
	            $this->error ( $Model->getError () );
	        }
	    } else {
	        $fields = get_model_attribute ( $model ['id'] );
	        $levelMap['token'] = get_token();
	        $extra = M('card_level') ->where($levelMap)->select();
	        $extraStr = '0:全部用户';
	        foreach($extra as $vo){
	            $extraStr = $extraStr .'
				'. $vo['id'].':'.$vo['level'];
	        }
	        $fields['grade']['extra'] = $extraStr;
	        $this->assign ( 'fields', $fields );
	        $this->assign('data', $data);

	        //$templateFile || $templateFile = $model ['template_add'] ? $model ['template_add'] : '';
	        $this->display ();
	    }
	}
	function checkPostData() {
		if (strtotime ( I ( 'post.start_time' ) ) > strtotime ( I ( 'post.end_time' ) )) {
			$this->error ( '开始时间不能大于结束时间' );
		}
	}

}
