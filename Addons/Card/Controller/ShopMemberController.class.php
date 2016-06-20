<?php

namespace Addons\Card\Controller;

use Addons\Card\Controller\BaseController;

class ShopMemberController extends BaseController {
	var $model;
	function _initialize() {
		$this->model = $this->getModel ( 'shop_card_member' );
		parent::_initialize ();
	}
	// 通用插件的列表模型
	public function lists() {
		// 不显示增加按钮
		$this->assign ( 'add_button', false );
		$this->assign('del_button',false);
		$this->assign('check_all',false);
		
		$btn[0]['title']='导入会员';
		$btn[0]['class']='ajax-post';
		$btn[0]['url']=addons_url('Card://ShopMember/import',array('mdm'=>$_GET['mdm']));
		$btn[1]['title']='导出会员';
		$btn[1]['class']='ajax-post';
		$btn[1]['url']=addons_url('Card://ShopMember/output',array('mdm'=>$_GET['mdm']));
		
		$this->assign('top_more_button',$btn);
		$list_data = $this->_get_model_list ( $this->model );
// 		dump($list_data);
		foreach($list_data['list_data'] as &$vo){
// 		    $vo['birthday']=time_format($vo['birthday'],'Y-m-d');
		}
		//dump($uInfo);
// 		dump($list_data);
		$this -> assign($list_data);
		$this->display();
		
// 		parent::common_lists ( $this->model );
	}
	//导入会员
	function import(){
	  
	    $model = $this->getModel ( 'import' );
	    if (IS_POST) {
	        $column = array (
	            'A' => 'username',
	            'B' => 'phone',
	            'C' => 'sex',
	            'D'=>'birthday',
	            'E'=>'address',
	            'F'=>'card_number',
	            'G'=>'score',
	            'H'=>'shop_code'
	        );
	        	
	        $attach_id = I ( 'attach', 0 );
	        $dateCol=array('D');
	        $res = importFormExcel ( $attach_id, $column,$dateCol );
	        if ($res ['status'] == 0) {
	            $this->error ( $res ['data'] );
	        }
	        $total = count ( $res ['data'] );
	        foreach ( $res ['data'] as $vo ) {
	            if (empty ( $vo ['username'] )) {
	                $this->error ( '姓名不能为空' );
	            }
	            if (empty ( $vo ['phone'] )) {
	                $this->error ( '手机号不能为空' );
	            }
	            $data['username']=$vo['username'];
	            $data['phone']=$vo['phone'];
	            $data['sex']=$vo['sex'];
	            $data['address']=$vo['address'];
	            $data['birthday']=strtotime($vo['birthday']);
	            $data ['token'] = get_token ();
	            $data['ctime']=time();
	            $data['shop_code']=$vo['shop_code'];
	            $data['score']=$vo['score'];
	            $data['card_number']=$vo['card_number'];
	            //获取会员卡号
	            if ($data['card_number']){
	                $cardMap['token']=get_token();
	            	$cardMap['number']=$data['card_number'];
	            }
	            if ($data['phone']){
	                $cardMap['token']=get_token();
	            	$cardMap['phone']=$data['phone'];
	            }
	            if ($cardMap){
	                $cardInfo=M('card_member')->where($cardMap)->find();
	            }
	            $credit=null;
	            if ($cardInfo){
	            	$saveCard['is_bind']=1;
	                $saveCard['phone'] = $cardInfo['phone']=$data['phone'];
	                $saveCard['username'] = $cardInfo['username']=$data['username'];
	                $saveCard['sex'] = $cardInfo['sex']=$data['sex']=='男'?1:2;
	                $saveCard['birthday'] = $cardInfo['birthday']=$data['birthday'];
	                $saveCard['shop_code'] = $cardInfo['shop_code']=$data['shop_code'];
	                $resData=D('Addons://Card/Card')->updateERPMember($cardInfo);
	                $res1=$resData['res'];
	                if ($res1 !=0 && $res1 !=-1){
	                    M('card_member')->where($cardMap)->save($saveCard);
	                    if ($data['score']){
	                    	$credit['title']='绑定实体店会员卡';
	                    	$credit['score']=intval($data['score']);
	                    	$credit['uid']=$cardInfo['uid'];
	                    	add_credit('shop_card_member',0,$credit);
	                    }
	                }else{
	                	$this->error($resData['msg']);
	                }
//                     $credit['credit_name']='shop_card_member';
// 	                D('Common/Credit')->addCredit($credit);
// 	                die;
	            }
	            $datas[]=$data;
	        }
	        $r = M ( 'shop_card_member')->addAll($datas);
	        $msg = "共导入" . $total . "条记录";
	        // dump($arr);
	        // $msg = trim ( $msg, ', ' );
	        // dump($msg);exit;
	        $this->success ( $msg, U ( 'lists',array('mdm'=>$_GET['mdm'])) );
	    } else {
	        $fields = get_model_attribute ( $model ['id'] );
	        $this->assign ( 'fields', $fields );
	        	
	        $this->assign ( 'post_url', U ( 'import' ) );
	        $this->assign ( 'import_template', 'shop_card_member.xls' );
	        	
	        $this->display ( T ( 'Addons/import' ) );
	    }
	}
	
	// 通用插件的编辑模型
	public function edit() {
		parent::common_edit ( $this->model );
	}
	
	// 通用插件的增加模型
	public function add() {
		parent::common_add ( $this->model );
	}
	
	// 通用插件的删除模型
	public function del() {
		parent::common_del ( $this->model );
	}
	//改变领取状态
	function changeGet(){
	    $id=I('id');
	    $is_get=I('is_get');
	    $save['is_get']=1-$is_get;
	    $res=M('shop_card_member')->where(array('id'=>$id))->save($save);
	    if ($res){
	        $this->success('修改成功！');
	    }
	}
	function output() {
	    $model =$this->model ;
	    parent::common_export ( $model );
	}
}
