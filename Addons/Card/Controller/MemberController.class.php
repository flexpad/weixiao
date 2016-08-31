<?php

namespace Addons\Card\Controller;

use Addons\Card\Controller\BaseController;

class MemberController extends BaseController {
	var $model;
	function _initialize() {
		$this->model = $this->getModel ( 'card_member' );
		parent::_initialize ();
	}
	// 通用插件的列表模型
	public function lists() {
		// 不显示增加按钮
		$this->assign ( 'add_button', false );
		$this->assign ( 'del_button', false );
		$btn [0] ['title'] = '批量解冻';
		$btn [0] ['is_buttion'] = 1;
		$btn [0] ['class'] = 'ajax-post confirm';
		$btn [0] ['url'] = addons_url ( 'Card://Member/changStatus', array (
				'set_status' => 1 ,
		          'mdm'=>$_GET['mdm']
		) );
		$btn [1] ['title'] = '批量冻结';
		$btn [1] ['is_buttion'] = 1;
		$btn [1] ['class'] = 'ajax-post confirm';
		$btn [1] ['url'] = addons_url ( 'Card://Member/changStatus', array (
				'set_status' => 2,
		          'mdm'=>$_GET['mdm']
		) );
		
		$btn [2] ['title'] = '导出会员';
		$btn [2] ['is_buttion'] = 1;
		$btn [2] ['class'] = 'export_member';
		$btn [2] ['url'] = addons_url ( 'Card://Member/export_member' ,$this->get_param);
		$this->assign ( 'top_more_button', $btn );
		
		$phone = $_REQUEST ['phone'];
		if ($phone) {
			$map ['phone'] = array (
					'like',
					'%' . htmlspecialchars ( $phone ) . '%' 
			);
			unset ( $_REQUEST ['phone'] );
		}
		$number = $_REQUEST ['number'];
		if ($number) {
			$map ['number'] = array (
					'like',
					'%' . htmlspecialchars ( $number ) . '%' 
			);
			unset ( $_REQUEST ['number'] );
		}
		if (!isset($map['number'])){
		    $map['number']=array('neq','');
		}
		$map ['token'] = get_token ();
		session ( 'common_condition', $map );
		$list_data = $this->_get_model_list ( $this->model );
		foreach ( $list_data ['list_data'] as &$vo ) {
			$uInfo = getUserInfo ( $vo ['uid'] );
			$levelInfo = D ( 'CardLevel' )->getCardMemberLevel ( $vo ['uid'] );
			$vo ['score'] = $uInfo ['score'];
			$vo ['level'] = $levelInfo ? $levelInfo ['level'] : "体验卡";
			$vo ['status'] = $vo ['status'] == 1 ? '正常' : '冻结';
		}
		unset ( $list_data ['list_grids'] ['uid'] );
		// dump($uInfo);
		// dump($list_data);
		$this->assign ( $list_data );
		$this->display ();
	}
	function changStatus() {
		$ids = I ( 'ids' );
		if (empty ( $ids )) {
			$this->error ( '请选择要冻结的会员' );
		}
		$setStatus = I ( 'get.set_status' );
		$map ['id'] = array (
				'in',
				$ids 
		);
		if ($setStatus == 1) {
			$data ['status'] = 1;
		} else if ($setStatus == 2) {
			$data ['status'] = 0;
		}
		
		$res = M ( 'card_member' )->where ( $map )->save ( $data );
		if ($res) {
			$this->success ( '操作成功' );
		}
	}
	// 会员充值
	function do_recharge() {
		$param ['mdm'] = $_GET ['mdm'];
		$res ['title'] = '会员管理';
		$res ['url'] = addons_url ( 'Card://Member/lists', $param );
		$res ['class'] = _ACTION == 'lists' ? 'current' : '';
		$nav [] = $res;
		
		$res ['title'] = '会员充值';
		$res ['url'] = addons_url ( 'Card://Member/do_recharge', $param );
		$res ['class'] = _ACTION == 'do_recharge' ? 'current' : '';
		$nav [] = $res;
		$this->assign ( 'nav', $nav );
		$model = $this->getModel ( 'recharge_log' );
		
		if (IS_POST) {
			// if ($_POST['recharge']<0){
			// $this->error('充值金额不能小于0');
			// }
			
			if (empty ( $_POST ['member_id'] )) {
				$this->checkMemberId ();
			}
			
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			if ($Model->create () && $Model->add ()) {
				$recharge = I ( 'recharge' );
				$map1 ['id'] = $member_id = $_POST ['member_id'];
				M ( 'card_member' )->where ( $map1 )->setInc ( 'recharge', $recharge );
				if (is_install("ShopReward")) {
                    // 充值赠送活动
                    $this->_send_reward(I('event_id'), $member_id, 'card_recharge_condition', 'recharge_reward');
                }
				$this->success ( '保存' . $model ['title'] . '成功！', U ( 'lists?model=' . $this->model ['name'],$this->get_param ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$map ['manager_id'] = $this->mid;
			$map ['token'] = get_token ();
			$branch = M ( 'coupon_shop' )->where ( $map )->getFields ( 'id,name' );
			$data ['member_id'] = I ( 'id' );
			if (empty ( $data ['member_id'] )) {
				$map2 ['token'] = get_token ();
				$allNumber = M ( 'card_member' )->where ( $map2 )->getFields ( 'id,number' );
				$this->assign ( 'all_number', $allNumber );
			}
			$this->assign ( 'data', $data );
			$this->assign ( 'shop', $branch );
		}
		
		$this->display ();
	}
	function checkMemberId() {
		if ($_POST ['card_id']) {
			$_POST ['member_id'] = $_POST ['card_id'];
		} else if (empty ( $_POST ['card_id'] ) && empty ( $_POST ['number'] )) {
			$this->error ( '请输入会员卡号' );
		} else if ($_POST ['number']) {
			$map ['number'] = $_POST ['number'];
			$map ['token'] = get_token ();
			$id = M ( 'card_member' )->where ( $map )->getField ( 'id' );
			if (empty ( $id )) {
				$this->error ( '会员卡不存在' );
			} else {
				$_POST ['member_id'] = $id;
			}
		}
	}
	function checkMemberByAjax() {
		$type = I ( 'type', 0 );
		$map ['number'] = I ( 'number' );
		$map ['token'] = get_token ();
		$info = M ( 'card_member' )->where ( $map )->find ();
		
		$data ['status'] = 0;
		if ($info) {
			$data ['status'] = 1;
			$data ['name'] = $info ['username'];
			$data ['event_id'] = 0;
			$data ['event_title'] = '';
			
			$map ['start_time'] = array (
					'lt',
					NOW_TIME 
			);
			$map ['end_time'] = array (
					'gt',
					NOW_TIME 
			);
			if ($type == 0) { // 获取充值赠送活动信息
				$event_info = M ( 'card_recharge' )->where ( $map )->order ( 'id desc' )->find ();
				$data ['event_id'] = $event_info ['id'];
				$data ['event_title'] = $event_info ['title'];
			} elseif ($type == 1) { // 获取消费赠送活动信息
			    if (is_install("ShopReward")) {
                    $event_info = M('shop_reward')->where($map)
                        ->order('id desc')
                        ->find();
                    $data['event_id'] = $event_info['id'];
                    $data['event_title'] = $event_info['title'];
                }
			}
		}
		echo json_encode ( $data );
	}
	
	// 会员消费
	function do_buy() {
		$param ['mdm'] = $_GET ['mdm'];
		$res ['title'] = '会员管理';
		$res ['url'] = addons_url ( 'Card://Member/lists', $param );
		$res ['class'] = _ACTION == 'lists' ? 'current' : '';
		$nav [] = $res;
		
		$res ['title'] = '会员消费';
		$res ['url'] = addons_url ( 'Card://Member/do_buy', $param );
		$res ['class'] = _ACTION == 'do_buy' ? 'current' : '';
		$nav [] = $res;
		$this->assign ( 'nav', $nav );
		$model = $this->getModel ( 'buy_log' );
		
		if (IS_POST) {
			$config = get_addon_config ( 'Card' );
			if (empty ( $_POST ['member_id'] )) {
				$this->checkMemberId ();
			}
			
			if ($config ['managerPassword'] != $_POST ['pay_password']) {
				$this->error ( '消费密码不正确' );
			}
			if ($_POST ['sn_id']) {
				$code = M ( 'sn_code' )->find ( $_POST ['sn_id'] );
				$_POST ['pay'] = $_POST ['pay'] - $code ['prize_title'];
			}
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			if ($Model->create () && $Model->add ()) {
				$map1 ['id'] = $member_id = $_POST ['member_id'];
				if ($_POST ['pay_type'] == 1) {
					$res = M ( 'card_member' )->where ( $map1 )->setDec ( 'recharge', $_POST ['pay'] );
				}
				if ($_POST ['sn_id']) {
					D ( 'Common/SnCode' )->set_use ( $_POST ['sn_id'] );
				}
                if (is_install("ShopReward")) {
                    // 消费赠送活动
                    $this->_send_reward(I('event_id'), $member_id, 'shop_reward_condition', 'shop_reward');
                }
				$this->success ( '保存' . $model ['title'] . '成功！', U ( 'lists?model=' . $this->model ['name'],$this->get_param ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$map ['manager_id'] = $this->mid;
			$map2 ['token'] = $map ['token'] = get_token ();
			$data ['member_id'] = I ( 'id' );
			if (empty ( $data ['member_id'] )) {
				$map2 ['token'] = get_token ();
				$allNumber = M ( 'card_member' )->where ( $map2 )->getFields ( 'id,number' );
				$this->assign ( 'all_number', $allNumber );
			}
			$cardMember = M ( 'card_member' )->find ( $data ['member_id'] );
			if(is_install('ShopCoupon')){
			    $branch = M ( 'coupon_shop' )->where ( $map )->getFields ( 'id,name' );
			    $map2 ['uid'] = $cardMember ['uid'];
			    $map2 ['addon'] = 'ShopCoupon';
			    $map2 ['can_use'] = 1;
			    $snCode = M ( 'sn_code' )->where ( $map2 )->getFields ( 'id,sn,target_id,prize_title' );
			    if ($snCode) {
			        foreach ( $snCode as $s ) {
			            $conponArr [$s ['target_id']] = $s ['target_id'];
			        }
			        $map3 ['id'] = array (
			            'in',
			            $conponArr
			        );
			        $conpons = M ( 'shop_coupon' )->where ( $map3 )->getFields ( 'id,title,member' );
			        foreach ( $snCode as $v ) {
			            if ($conpons[$v['target_id']]){
			                $memberArr=explode(',', $conpons[$v['target_id']]['member']);
			                if (in_array(0, $memberArr) || in_array(-1, $memberArr) || in_array($cardMember['level'], $memberArr)){
			                    $codeArr ['coupon_title'] = $conpons [$v ['target_id']];
			                    $couponData[$v['target_id']]=$conpons[$v['target_id']]['title'];
			                }
			            }
			            	
			        }
			    
			        $this->assign ( 'coupon', $couponData );
			    }
			    $this->assign ( 'shop', $branch );
			}
			
			$this->assign ( 'data', $data );
		}
		
		$this->display ();
	}
	function getSnCode() {
		$targetId = I ( 'target_id' );
		$cardId = I ( 'member_id' );
		$uid = M ( 'card_member' )->where ( array (
				'id' => $cardId 
		) )->getField ( 'uid' );
		$map ['target_id'] = $targetId;
		$map ['uid'] = $uid;
		$map ['addon'] = 'ShopCoupon';
		$map ['can_use'] = 1;
		$map ['token'] = get_token ();
		$snCode = M ( 'sn_code' )->where ( $map )->field ( 'id,prize_title' )->select ();
		$this->ajaxReturn ( $snCode );
	}
	
	// 手动添加积分
	function update_score() {
		$param ['mdm'] = $_GET ['mdm'];
		$res ['title'] = '会员管理';
		$res ['url'] = addons_url ( 'Card://Member/lists', $param );
		$res ['class'] = _ACTION == 'lists' ? 'current' : '';
		$nav [] = $res;
		
		$res ['title'] = '手动修改积分';
		$res ['url'] = addons_url ( 'Card://Member/update_score', $param );
		$res ['class'] = _ACTION == 'update_score' ? 'current' : '';
		$nav [] = $res;
		$this->assign ( 'nav', $nav );
		$model = $this->getModel ( 'update_score_log' );
		
		if (IS_POST) {
			// if ($_POST['recharge']<0){
			// $this->error('充值金额不能小于0');
			// }
			if (empty ( $_POST ['member_id'] )) {
				$this->checkMemberId ();
			}
			
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			
			if ($Model->create () && $id = $Model->add ()) {
				
				$uid = M ( 'card_member' )->where ( array (
						'id' => $_POST ['member_id'] 
				) )->getField ( 'uid' );
				$data ['uid'] = $uid;
				$data ['score'] = $_POST ['score'];
				$data ['experience'] = 0;
				$data ['title'] ='手动修改' ;
				add_credit ( 'card_member_update_score', 5, $data, $id );
				
				$this->success ( '保存' . $model ['title'] . '成功！', U ( 'lists?model=' . $this->model ['name'] ,$this->get_param) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			$map ['manager_id'] = $this->mid;
			$map ['token'] = get_token ();
			$branch = M ( 'coupon_shop' )->where ( $map )->getFields ( 'id,name' );
			$data ['member_id'] = I ( 'id' );
			if (empty ( $data ['member_id'] )) {
				$map2 ['token'] = get_token ();
				$allNumber = M ( 'card_member' )->where ( $map2 )->getFields ( 'id,number' );
				$this->assign ( 'all_number', $allNumber );
			}
			
			$this->assign ( 'data', $data );
			$this->assign ( 'shop', $branch );
		}
		
		$this->display ();
	}
	// 通用插件的编辑模型
	public function edit() {
		$model= $this->model;
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
			if ($Model->create () && $Model->save ()) {
				//$this->_saveKeyword ( $model, $id );
				D('Addons://Card/Card')->updateERPMember($_POST,$data['uid']);
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
			
			$this->display (  );
		}
	}
	
	// 通用插件的增加模型
	public function add() {
		parent::common_add ( $this->model );
	}
	
	// 通用插件的删除模型
	public function del() {
		parent::common_del ( $this->model );
	}
	
	// 导出会员
	function export_member() {
		$ids = I ( 'ids' );
		
		if (! empty ( $ids )) {
			$map ['id'] = array (
					'in',
					$ids 
			);
		}
		$map ['token'] = get_token ();
		// session ( 'common_condition', $map );
		// $list_data = $this->_get_model_list ( $this->model );
		$fieldName = I ( 'names' );
		// dump($fieldName);
		$fieldStr = '';
		foreach ( $fieldName as $ff ) {
			if ($ff != 'score' && $ff != 'level') {
				$fieldStr .= $ff . ',';
			}
		}
		//dump($fieldName);exit;
		$fieldStr = substr ( $fieldStr, 0, strlen ( $fieldStr ) - 1 );
		$fieldStr.=",uid";
		//dump($fieldStr);exit;
		// die;
		// $fieldStr='number,username,phone,recharge,cTime,status';
		$list_data = M ( 'card_member' )->where ( $map )->field ( $fieldStr )->select ();
		//dump($list_data);exit;
		//$uInfo = getUserInfo ( $this->mid );
		//dump($uInfo);exit;
		//$levelInfo = D ( 'CardLevel' )->getCardMemberLevel ( $this->mid );
		
		foreach ( $list_data as &$vo ) {
			$uInfo = getUserInfo ( $vo ['uid'] );
			$levelInfo = D ( 'CardLevel' )->getCardMemberLevel ( $vo ['uid'] );
			if (in_array ( 'cTime', $fieldName )) {
				$vo ['cTime'] = time_format ( $vo ['cTime'] );
			}
			if (in_array ( 'status', $fieldName )) {
				$vo ['status'] = $vo ['status'] == 1 ? '正常' : '冻结';
			}
			if (in_array ( 'score', $fieldName )) {
				$vo ['score'] = $uInfo ['score'];
			}
			if (in_array ( 'level', $fieldName )) {
				$vo ['level'] = $levelInfo ? $levelInfo ['level'] : "没有等级";
			}
			
			if (isset ( $vo ['number'] )) {
				$fieldArr ['number'] = '卡号';
			}
			if (isset ( $vo ['username'] )) {
				$fieldArr ['username'] = '姓名';
			}
			if (isset ( $vo ['phone'] )) {
				$fieldArr ['phone'] = '手机号';
			}
			if (isset ( $vo ['recharge'] )) {
				$fieldArr ['recharge'] = '余额';
			}
			if (isset ( $vo ['cTime'] )) {
				$fieldArr ['cTime'] = '加入时间';
			}
			if (isset ( $vo ['status'] )) {
				$fieldArr ['status'] = '状态';
			}
			
				$fieldArr ['score'] = '剩余积分';
			
			
			if (isset ( $vo ['level'] )) {
				$fieldArr ['level'] = '会员等级';
			}
			
			
		}
		
		// foreach ($fieldName as $f){
		// if ($f=='number'){
		// $fieldArr['number']='卡号';
		// }else if($f == 'username'){
		// $fieldArr['username']='姓名';
		// }else if($f == 'phone'){
		// $fieldArr['phone']='手机号';
		// }else if($f == 'recharge'){
		// $fieldArr['recharge']='余额';
		// }else if($f == 'cTime'){
		// $fieldArr['cTime']='加入时间';
		// }else if($f == 'status'){
		// $fieldArr['status']='状态';
		// }else if($f == 'score'){
		// $fieldArr['score']='剩余积分';
		// }else if($f == 'level'){
		// $fieldArr['level']='会员等级';
		// }
		// }
		// // dump($list_data);
		// $fieldArr = array (
		// 'number' => '卡号',
		// 'username' => '姓名',
		// 'phone' => '手机号',
		// 'recharge' => '余额',
		// 'cTime' => '加入时间',
		// 'status' => '状态',
		// 'score' => '剩余积分',
		// 'level' => '等级'
		// );
		foreach ( $fieldArr as $k => $vv ) {
			$fields [] = $k;
			$titleArr [] = $vv;
		}
		$dataArr [] = $titleArr;
		//dump($list_data);exit;
		// dump($fieldArr);exit;
		// die;
		foreach ( $list_data as $v ) {
			unset($v['uid']);
			$dataArr [] = $v;
		}
		// vendor ( 'out-csv' );
		// export_csv ( $dataArr, 'card_member' );
		
		outExcel ( $dataArr, 'card_member' );
	}
	
	// 活动赠送
	function _send_reward($event_id, $member_id, $table, $credit_type) {
	    if (!is_install("ShopReward")) {
	        return false;
	    }
		if (empty ( $event_id )) {
			return false;
		}
		
		$con_map ['reward_id'] = $event_id;
		$con_map ['condition'] = array (
				'elt',
				$recharge 
		);
		$reward = M ( $table )->where ( $con_map )->order ( '`condition` desc' )->find ();
		if (! $reward) {
			return false;
		}
		if ($reward ['money']) {
			$map1 ['id'] = $member_id;
			M ( 'card_member' )->where ( $map1 )->setInc ( 'recharge', $reward ['money_param'] );
		}
		if ($reward ['score']) { // 送积分
			$credit ['score'] = intval ( $reward ['score_param'] );
			add_credit ( $credit_type, 0, $credit );
		}
		if ($reward ['shop_coupon'] && is_install("ShopCoupon")) { // 送优惠券
			D ( 'Addons://ShopCoupon/Coupon' )->sendCoupon ( $reward ['shop_coupon_param'], $this->mid );
		}
	}
}
