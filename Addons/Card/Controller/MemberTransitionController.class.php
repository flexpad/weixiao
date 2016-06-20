<?php

namespace Addons\Card\Controller;

use Addons\Card\Controller\BaseController;

class MemberTransitionController extends BaseController {
	var $model;
	function _initialize() {
		$this->model = $this->getModel ( 'card_member' );
		parent::_initialize ();
	}
	// 通用插件的列表模型
	public function lists() {
		$this->display ();
	}
	
	// 会员充值列表
	function recharge_lists() {
		$param ['mdm'] = $_GET ['mdm'];
		$res ['title'] = '会员交易';
		$res ['url'] = addons_url ( 'Card://MemberTransition/lists', $param );
		$res ['class'] = _ACTION == 'lists' ? 'current' : '';
		$nav [] = $res;
		
		$res ['title'] = '充值查询';
		$res ['url'] = addons_url ( 'Card://MemberTransition/recharge_lists', $param );
		$res ['class'] = _ACTION == 'recharge_lists' ? 'current' : '';
		$nav [] = $res;
		$this->assign ( 'nav', $nav );
		
		$this->assign ( 'add_button', false );
		$this->assign ( 'del_button', false );
		$this->assign ( 'check_all', false );
		$this->assign ( 'search_url', addons_url ( "Card://MemberTransition/recharge_lists", array (
				'mdm' => $_GET ['mdm'] 
		) ) );
		$map ['manager_id'] = $this->mid;
		$map ['token'] = get_token ();
		$branch = M ( 'coupon_shop' )->where ( $map )->getFields ( 'id,name' );
		$this->assign ( 'shop', $branch );
		
		$search = $_REQUEST ['operator'];
		if ($search) {
			$this->assign ( 'search', $search );
			$map1 ['username'] = array (
					'like',
					'%' . htmlspecialchars ( $search ) . '%' 
			);
			$map1 ['token'] = $map2 ['token'] = get_token ();
			$u_card_id = D ( 'card_member' )->where ( $map1 )->getFields ( 'id' );
			$u_card_id = implode ( ',', $u_card_id );
			
			$map2 ['phone'] = array (
					'like',
					'%' . htmlspecialchars ( $search ) . '%' 
			);
			
			$p_card_id = D ( 'card_member' )->where ( $map2 )->getFields ( 'id' );
			$p_card_id = implode ( ',', $p_card_id );
			if (! empty ( $u_card_id )) {
				$map ['member_id'] = array (
						'exp',
						' in (' . $u_card_id . ') ' 
				);
			} else if (! empty ( $p_card_id )) {
				$map ['member_id'] = array (
						'exp',
						' in (' . $p_card_id . ') ' 
				);
			} else {
				$map ['operator'] = array (
						'like',
						'%' . htmlspecialchars ( $search ) . '%' 
				);
			}
			unset ( $_REQUEST ['operator'] );
		}
		
		$shop_id = I ( 'pay_shop' );
		if ($shop_id != null) {
			$map ['branch_id'] = $shop_id;
		}
		$isRecharge = I ( 'is_recharge' );
		if ($isRecharge) {
			$minVal = I ( 'min_value', 0, 'intval' );
			$maxVal = I ( 'max_value', 0, 'intval' );
			if ($minVal && $maxVal) {
				$minVal < $maxVal && $map ['recharge'] = array (
						'between',
						array (
								$minVal,
								$maxVal 
						) 
				);
				$minVal > $maxVal && $map ['recharge'] = array (
						'between',
						array (
								$maxVal,
								$minVal 
						) 
				);
				$minVal == $maxVal && $map ['recharge'] = $minVal;
			} else if (! empty ( $minVal )) {
				$map ['recharge'] = array (
						'egt',
						$minVal 
				);
			} else if (! empty ( $maxVal ) || $maxVal == 0) {
				$map ['recharge'] = array (
						'elt',
						$maxVal 
				);
			}
		}
		
		$isCTime = I ( 'is_ctime' );
		if ($isCTime) {
			$startVal = I ( 'start_ctime', 0, 'strtotime' );
			$endVal = I ( 'end_ctime', 0, 'strtotime' );
			$endVal = $endVal == 0 ? 0 : $endVal + 86400 - 1;
			if ($startVal && $endVal) {
				$startVal < $endVal && $map ['cTime'] = array (
						'between',
						array (
								$startVal,
								$endVal 
						) 
				);
				$startVal > $endVal && $map ['cTime'] = array (
						'between',
						array (
								$startVal,
								$endVal 
						) 
				);
				$startVal == $endVal && $map ['cTime'] = array (
						'egt',
						$startVal 
				);
			} else if (! empty ( $startVal )) {
				$map ['cTime'] = array (
						'egt',
						$startVal 
				);
			} else if (! empty ( $endVal )) {
				$map ['cTime'] = array (
						'elt',
						$endVal 
				);
			}
		}
		$map ['token'] = get_token ();
		session ( 'common_condition', $map );
		$model = $this->getModel ( 'recharge_log' );
		$list_data = $this->_get_model_list ( $model );
		// $uInfo = getUserInfo($this->mid);
		// $levelInfo = D('CardLevel')->getCardMemberLevel($this->mid);
		
		$map ['manager_id'] = $this->mid;
		$map ['token'] = get_token ();
		$branch = M ( 'coupon_shop' )->where ( $map )->getFields ( 'id,name' );
		
		$cardMemberDao = M ( 'card_member' );
		foreach ( $list_data ['list_data'] as &$vo ) {
			$cardMember = $cardMemberDao->find ( $vo ['member_id'] );
			$vo ['member_id'] = $cardMember ['number'];
			$vo ['truename'] = $cardMember ['username'];
			$vo ['phone'] = $cardMember ['phone'];
			$vo ['branch_id'] = $vo ['branch_id'] == 0 ? '商店总部' : $branch [$vo ['branch_id']];
		}
		
		// dump($uInfo);
		// dump($list_data);
		$this->assign ( $list_data );
		$this->display ();
	}
	// 会员消费列表
	function buy_lists() {
		$param ['mdm'] = $_GET ['mdm'];
		$res ['title'] = '会员交易';
		$res ['url'] = addons_url ( 'Card://MemberTransition/lists', $param );
		$res ['class'] = _ACTION == 'lists' ? 'current' : '';
		$nav [] = $res;
		
		$res ['title'] = '消费查询';
		$res ['url'] = addons_url ( 'Card://MemberTransition/buy_lists', $param );
		$res ['class'] = _ACTION == 'buy_lists' ? 'current' : '';
		$nav [] = $res;
		$this->assign ( 'nav', $nav );
		
		$this->assign ( 'add_button', false );
		$this->assign ( 'del_button', false );
		$this->assign ( 'check_all', false );
		$this->assign ( 'search_url', addons_url ( "Card://MemberTransition/buy_lists", array (
				'mdm' => $_GET ['mdm'] 
		) ) );
		$map ['manager_id'] = $this->mid;
		$map ['token'] = get_token ();
		$branch = M ( 'coupon_shop' )->where ( $map )->getFields ( 'id,name' );
		$this->assign ( 'shop', $branch );
		$search = $_REQUEST ['member'];
		if ($search) {
			$this->assign ( 'search', $search );
			$map1 ['username'] = array (
					'like',
					'%' . htmlspecialchars ( $search ) . '%' 
			);
			$map1 ['token'] = $map2 ['token'] = get_token ();
			$u_card_id = D ( 'card_member' )->where ( $map1 )->getFields ( 'id' );
			$u_card_id = implode ( ',', $u_card_id );
			
			$map2 ['phone'] = array (
					'like',
					'%' . htmlspecialchars ( $search ) . '%' 
			);
			
			$p_card_id = D ( 'card_member' )->where ( $map2 )->getFields ( 'id' );
			$p_card_id = implode ( ',', $p_card_id );
			if (! empty ( $u_card_id )) {
				$map ['member_id'] = array (
						'exp',
						' in (' . $u_card_id . ') ' 
				);
			} else if (! empty ( $p_card_id )) {
				$map ['member_id'] = array (
						'exp',
						' in (' . $p_card_id . ') ' 
				);
			} else {
				$map ['id'] = 0;
			}
			unset ( $_REQUEST ['member'] );
		}
		$shop_id = I ( 'pay_shop' );
		if ($shop_id != null) {
			$map ['branch_id'] = $shop_id;
		}
		$isRecharge = I ( 'is_recharge' );
		if ($isRecharge) {
			$minVal = I ( 'min_value', 0, 'intval' );
			$maxVal = I ( 'max_value', 0, 'intval' );
			if ($minVal && $maxVal) {
				$minVal < $maxVal && $map ['pay'] = array (
						'between',
						array (
								$minVal,
								$maxVal 
						) 
				);
				$minVal > $maxVal && $map ['pay'] = array (
						'between',
						array (
								$maxVal,
								$minVal 
						) 
				);
				$minVal == $maxVal && $map ['pay'] = $minVal;
			} else if (! empty ( $minVal )) {
				$map ['pay'] = array (
						'egt',
						$minVal 
				);
			} else if (! empty ( $maxVal ) || $maxVal == 0) {
				$map ['pay'] = array (
						'elt',
						$maxVal 
				);
			}
		}
		
		$isCTime = I ( 'is_ctime' );
		if ($isCTime) {
			$startVal = I ( 'start_ctime', 0, 'strtotime' );
			$endVal = I ( 'end_ctime', 0, 'strtotime' );
			$endVal = $endVal == 0 ? 0 : $endVal + 86400 - 1;
			if ($startVal && $endVal) {
				$startVal < $endVal && $map ['cTime'] = array (
						'between',
						array (
								$startVal,
								$endVal 
						) 
				);
				$startVal > $endVal && $map ['cTime'] = array (
						'between',
						array (
								$startVal,
								$endVal 
						) 
				);
				$startVal == $endVal && $map ['cTime'] = array (
						'egt',
						$startVal 
				);
			} else if (! empty ( $startVal )) {
				$map ['cTime'] = array (
						'egt',
						$startVal 
				);
			} else if (! empty ( $endVal )) {
				$map ['cTime'] = array (
						'elt',
						$endVal 
				);
			}
		}
		
		$map ['token'] = get_token ();
		session ( 'common_condition', $map );
		$model = $this->getModel ( 'buy_log' );
		$list_data = $this->_get_model_list ( $model );
		if (! empty ( $list_data ['list_data'] )) {
			// $uInfo = getUserInfo($this->mid);
			// $levelInfo = D('CardLevel')->getCardMemberLevel($this->mid);
			
			$map ['manager_id'] = $this->mid;
			$map ['token'] = get_token ();
			$branch = M ( 'coupon_shop' )->where ( $map )->getFields ( 'id,name' );
			
			$cardMemberDao = M ( 'card_member' );
			
			foreach ( $list_data ['list_data'] as $vo ) {
				$snArr [$vo ['sn_id']] = $vo ['sn_id'];
			}
			$map2 ['id'] = array (
					'in',
					$snArr 
			);
			$prizeData = M ( 'sn_code' )->where ( $map2 )->getFields ( 'id,prize_title' );
			
			foreach ( $list_data ['list_data'] as &$vo ) {
				$cardMember = $cardMemberDao->find ( $vo ['member_id'] );
				// $vo['member_id']=$cardMember['number'];
				$vo ['member_id'] = $cardMember ['username'];
				$vo ['phone'] = $cardMember ['phone'];
				$vo ['branch_id'] = $vo ['branch_id'] == 0 ? '商店总部' : $branch [$vo ['branch_id']];
				$vo ['sn_id'] = floatval ( $prizeData [$vo ['sn_id']] );
			}
		}
		// dump($uInfo);
		// dump($list_data);
		$this->assign ( $list_data );
		$this->display ();
	}
	
	// 积分查询
	function score_lists() {
		$param ['mdm'] = $_GET ['mdm'];
		$res ['title'] = '会员交易';
		$res ['url'] = addons_url ( 'Card://MemberTransition/lists', $param );
		$res ['class'] = _ACTION == 'lists' ? 'current' : '';
		$nav [] = $res;
		
		$res ['title'] = '积分查询';
		$res ['url'] = addons_url ( 'Card://MemberTransition/score_lists', $param );
		$res ['class'] = _ACTION == 'score_lists' ? 'current' : '';
		$nav [] = $res;
		$this->assign ( 'nav', $nav );
		
		$this->assign ( 'add_button', false );
		$this->assign ( 'del_button', false );
		// $this->assign ( 'search_button', false );
		$this->assign ( 'check_all', false );
		$this->assign ( 'search_url', addons_url ( "Card://MemberTransition/score_lists", array (
				'mdm' => $_GET ['mdm'] 
		) ) );
		$this->assign ( 'search_key', 'username' );
		$this->assign ( 'placeholder', '请输入用户名或手机号' );
		$grid ['field'] = 'credit_name';
		$grid ['title'] = '交易名称';
		$list_grids [] = $grid;
		
		$grid ['field'] = 'username';
		$grid ['title'] = '用户名';
		$list_grids [] = $grid;
		
		$grid ['field'] = 'phone';
		$grid ['title'] = '手机号码';
		$list_grids [] = $grid;
		
		$grid ['field'] = 'cTime|time_format';
		$grid ['title'] = '交易时间';
		$list_grids [] = $grid;
		
		$grid ['field'] = 'score';
		$grid ['title'] = '积分';
		$list_grids [] = $grid;
		
		$grid ['field'] = 'operator';
		$grid ['title'] = '操作员';
		$list_grids [] = $grid;
		$list_data ['list_grids'] = $list_grids;
		// 获取交易方式信息
		$creditTitle = M ( 'credit_config' )->getFields ( 'name,title' );
		$i = 1;
		foreach ( $creditTitle as $k => $c ) {
			$title ['name'] = $k;
			$title ['title'] = $c;
			$creditArr [$i ++] = $title;
		}
		$title1 ['name'] = 'addAuto';
		$title1 ['title'] = '手动增加';
		$creditArr [$i ++] = $title1;
		$title2 ['name'] = 'delAuto';
		$title2 ['title'] = '手动扣除';
		$creditArr [$i ++] = $title2;
		
		$this->assign ( 'credit_title', $creditArr );
		
		// 交易方式查询
		$creditType = I ( 'credit_type' );
		if ($creditType) {
			foreach ( $creditArr as $key => $cr ) {
				if ($creditType == $key) {
					if ($cr ['name'] == 'addAuto') {
						$map ['credit_name'] = 'card_member_update_score';
						$map ['score'] = array (
								'egt',
								0 
						);
					} else if ($cr ['name'] == 'delAuto') {
						$map ['credit_name'] = 'card_member_update_score';
						$map ['score'] = array (
								'elt',
								0 
						);
					} else {
						$map ['credit_name'] = $cr ['name'];
					}
				}
			}
		}
		// 时间查询
		$isCTime = I ( 'is_ctime' );
		if ($isCTime) {
			$startVal = I ( 'start_ctime', 0, 'strtotime' );
			$endVal = I ( 'end_ctime', 0, 'strtotime' );
			$endVal = $endVal == 0 ? 0 : $endVal + 86400 - 1;
			if ($startVal && $endVal) {
				$startVal < $endVal && $map ['cTime'] = array (
						'between',
						array (
								$startVal,
								$endVal 
						) 
				);
				$startVal > $endVal && $map ['cTime'] = array (
						'between',
						array (
								$startVal,
								$endVal 
						) 
				);
				$startVal == $endVal && $map ['cTime'] = array (
						'egt',
						$startVal 
				);
			} else if (! empty ( $startVal )) {
				$map ['cTime'] = array (
						'egt',
						$startVal 
				);
			} else if (! empty ( $endVal )) {
				$map ['cTime'] = array (
						'elt',
						$endVal 
				);
			}
		}
		// 搜索查询
		$search = $_REQUEST ['username'];
		if ($search) {
			$this->assign ( 'search', $search );
			$map3 ['mobile'] = array (
					'like',
					'%' . htmlspecialchars ( $search ) . '%' 
			);
			$nickname_follow_ids = D ( 'Common/User' )->searchUser ( $search );
			$mobile_follow_ids = M ( 'user' )->where ( $map3 )->getFields ( 'uid' );
			$nickname_follow_ids = implode ( ',', $nickname_follow_ids );
			$mobile_follow_ids = implode ( ',', $mobile_follow_ids );
			if (! empty ( $nickname_follow_ids )) {
				$map ['uid'] = array (
						'exp',
						' in (' . $nickname_follow_ids . ') ' 
				);
			} else if (! empty ( $mobile_follow_ids )) {
				$map ['uid'] = array (
						'exp',
						' in (' . $mobile_follow_ids . ') ' 
				);
			} else {
				$map ['id'] = 0;
			}
			unset ( $_REQUEST ['username'] );
		}
		
		$map ['token'] = get_token ();
		$data = M ( 'credit_data' )->where ( $map )->order ( 'id desc' )->selectPage ();
		$list_data ['list_data'] = $data ['list_data'];
		// dump($list_data['list_data']);
		
		foreach ( $list_data ['list_data'] as &$vo ) {
			if ($vo ['credit_name'] == 'card_member_update_score') {
				if ($vo ['score'] > 0) {
					$vo ['credit_name'] = '手动增加';
				} else {
					$vo ['credit_name'] = '手动扣除';
				}
				if ($vo ['uid'] && $vo ['admin_uid']) {
					$updateData = M ( 'update_score_log' )->find ( $vo ['admin_uid'] );
					$vo ['operator'] = $updateData ['operator'];
				}
			} else {
				$vo ['credit_name'] = $creditTitle [$vo ['credit_name']];
				if ($vo ['uid']) {
					$userInfo = get_userinfo ( $vo ['uid'] );
					$vo ['username'] = $userInfo ['nickname'];
					$vo ['phone'] = $userInfo ['mobile'];
				}
			}
			// 判断是否为会员
			if ($vo ['uid']) {
				$userInfo = get_userinfo ( $vo ['uid'] );
				$vo ['username'] = $userInfo ['truename'] ? $userInfo ['truename'] : $userInfo ['nickname'];
				$vo ['phone'] = $userInfo ['mobile'];
			}
		}
		$this->assign ( $list_data );
		$this->display ();
	}
	// 消费统计
	function buy_tongji() {
		$param ['mdm'] = $_GET ['mdm'];
		$res ['title'] = '会员交易';
		$res ['url'] = addons_url ( 'Card://MemberTransition/lists', $param );
		$res ['class'] = _ACTION == 'lists' ? 'current' : '';
		$nav [] = $res;
		
		$res ['title'] = '消费统计';
		$res ['url'] = addons_url ( 'Card://MemberTransition/buy_tongji', $param );
		$res ['class'] = _ACTION == 'buy_tongji' ? 'current' : '';
		$nav [] = $res;
		$this->assign ( 'nav', $nav );
		
		$year = I ( 'year' );
		$month = I ( 'month' );
		$is_ajax = I ( 'is_ajax' );
		$map ['token'] = get_token ();
		if ($year && $month && $is_ajax) {
			$start_date = $year . '-' . $month;
			$end_month = $month + 1;
			$end_date = $year . '-' . $end_month;
			$start_date = strtotime ( $start_date );
			$end_date = strtotime ( $end_date );
			$map ['cTime'] = array (
					'between',
					array (
							$start_date,
							$end_date 
					) 
			);
		} else {
			$now_month = time_format ( NOW_TIME, 'Y-m' );
			$map ['cTime'] = array (
					'egt',
					strtotime ( $now_month ) 
			);
		}
		// 本月总消费金额
		$totalPay = M ( 'buy_log' )->where ( $map )->field ( "sum(pay) totalPay" )->select ();
		$totalPay = round ( floatval ( $totalPay [0] ['totalPay'] ), 2 );
		$this->assign ( 'total_pay', $totalPay );
		
		$data = M ( 'buy_log' )->where ( $map )->field ( "sum(pay) totalPay,from_unixtime(cTime,'%m-%d') allday" )->group ( "allday" )->select ();
		foreach ( $data as $v ) {
			$allDay [] = $v ['allday'];
			$allPay [] = round ( floatval ( $v ['totalPay'] ), 2 );
		}
		$highcharts ['xAxis'] = $allDay;
		$highcharts ['series'] = $allPay;
		if ($is_ajax) {
			$highcharts ['total_pay'] = $totalPay;
			$this->ajaxReturn ( $highcharts );
		} else {
			$highcharts = json_encode ( $highcharts );
			$this->assign ( 'highcharts', $highcharts );
			$this->display ();
		}
	}
	function getDays($year, $month) {
		if ($month == 2) {
			if ($year % 400 == 0 || $year % 4 == 0 && $year % 100 != 0) {
				$day = 28;
			} else {
				$day = 29;
			}
		} else if ($month == 4 || $month == 6 || $month == 9 || $month == 11) {
			$day = 30;
		} else {
			$day = 31;
		}
		return $day;
	}
	// 积分统计
	// 消费统计
	function score_tongji() {
		$param ['mdm'] = $_GET ['mdm'];
		$res ['title'] = '会员交易';
		$res ['url'] = addons_url ( 'Card://MemberTransition/lists', $param );
		$res ['class'] = _ACTION == 'lists' ? 'current' : '';
		$nav [] = $res;
		
		$res ['title'] = '当月用户积分统计';
		$res ['url'] = addons_url ( 'Card://MemberTransition/score_tongji', $param );
		$res ['class'] = _ACTION == 'score_tongji' ? 'current' : '';
		$nav [] = $res;
		$this->assign ( 'nav', $nav );
		
		$year = I ( 'year' );
		$month = I ( 'month' );
		$is_ajax = I ( 'is_ajax' );
		$map1 ['token'] = $map2 ['token'] = get_token ();
		
		if ($year && $month && $is_ajax) {
			$month = intval ( $month );
			$day = $this->getDays ( $year, $month );
			$start_date = $year . '-' . $month;
			$end_month = $month + 1;
			$end_date = $year . '-' . $end_month;
			$start_date = strtotime ( $start_date );
			$end_date = strtotime ( $end_date );
			$map1 ['cTime'] = $map2 ['cTime'] = array (
					'between',
					array (
							$start_date,
							$end_date 
					) 
			);
		} else {
			$year = time_format ( NOW_TIME, 'Y' );
			$month = intval ( time_format ( NOW_TIME, 'm' ) );
			$day = $this->getDays ( $year, $month );
			$now_month = time_format ( NOW_TIME, 'Y-m' );
			$map1 ['cTime'] = $map2 ['cTime'] = array (
					'egt',
					strtotime ( $now_month ) 
			);
		}
		// 本月总获取积分
		$map1 ['score'] = array (
				'gt',
				0 
		);
		$getTotal = M ( 'credit_data' )->where ( $map1 )->field ( "sum(score) totalScore" )->select ();
		// 本月使用积分
		$map2 ['score'] = array (
				'lt',
				0 
		);
		$useTotal = M ( 'credit_data' )->where ( $map2 )->field ( "sum(score) totalScore" )->select ();
		
		$getTotal = round ( floatval ( $getTotal [0] ['totalScore'] ), 2 );
		$useTotal = 0 - round ( floatval ( $useTotal [0] ['totalScore'] ), 2 );
		
		$this->assign ( 'get_score', $getTotal );
		$this->assign ( 'use_score', $useTotal );
		
		$get_data = M ( 'credit_data' )->where ( $map1 )->field ( "sum(score) totalScore,from_unixtime(cTime,'%m-%d') allday" )->group ( "allday" )->select ();
		$use_data = M ( 'credit_data' )->where ( $map2 )->field ( "sum(score) totalScore,from_unixtime(cTime,'%m-%d') allday" )->group ( "allday" )->select ();
		$month = str_pad ( $month, 2, "0", STR_PAD_LEFT );
		for($i = 1; $i <= $day; $i ++) {
			$i = str_pad ( $i, 2, "0", STR_PAD_LEFT );
			$dateArr [$month . '-' . $i] = $month . '-' . $i;
			$the_date [] = $month . '-' . $i;
		}
		// dump($dateArr);
		// dump($get_data);
		foreach ( $get_data as $v ) {
			// $getAllDay[]=$v['allday'];
			if ($dateArr [$v ['allday']]) {
				$getAllScore [] = round ( floatval ( $v ['totalScore'] ), 2 );
			}
		}
		foreach ( $use_data as $v ) {
			// $useAllDay[]=$v['allday'];
			if ($dateArr [$v ['allday']]) {
				$useAllScore [] = 0 - round ( floatval ( $v ['totalScore'] ), 2 );
			}
		}
		$highcharts ['xAxis'] = $the_date;
		$highcharts ['series1'] = $getAllScore;
		$highcharts ['series2'] = $useAllScore;
		if ($is_ajax) {
			$highcharts ['get_score'] = $getTotal;
			$highcharts ['use_score'] = $useTotal;
			$this->ajaxReturn ( $highcharts );
		} else {
			$highcharts = json_encode ( $highcharts );
			$this->assign ( 'highcharts', $highcharts );
			$this->display ();
		}
	}
	function duihuang() {
		if (IS_POST) {
			if (! $_POST ['sn_id']) {
				$this->error ( '请输入sn码' );
			}
			$map ['sn'] = $_POST ['sn_id'];
			$id = D ( 'Common/SnCode' )->where ( $map )->getField ( 'id' );
			$res = D ( 'Common/SnCode' )->set_use ( $id );
			if ($res) {
				$this->success ( '兑换成功' );
			} else {
				$this->error ( '兑换失败' );
			}
		}
		$this->display ();
	}
}
