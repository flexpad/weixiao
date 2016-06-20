<?php

namespace Addons\Card\Controller;

use Home\Controller\AddonsController;
// 充值赠送活动
class RechargeController extends AddonsController {
	function _initialize() {
		parent::_initialize ();

		$type = I ( 'type', 0, 'intval' );
		$param['mdm']=$_GET['mdm'];
		$param ['type'] = 0;
		$res ['title'] = '所有的充值赠送活动';
		$res ['url'] = addons_url ( 'Card://Recharge/lists', $param );
		$res ['class'] = $type == $param ['type'] ? 'current' : '';
		$nav [] = $res;

		$param ['type'] = 1;
		$res ['title'] = '未开始';
		$res ['url'] = addons_url ( 'Card://Recharge/lists', $param );
		$res ['class'] = $type == $param ['type'] ? 'current' : '';
		$nav [] = $res;

		$param ['type'] = 2;
		$res ['title'] = '进行中';
		$res ['url'] = addons_url ( 'Card://Recharge/lists', $param );
		$res ['class'] = $type == $param ['type'] ? 'current' : '';
		$nav [] = $res;

		$param ['type'] = 3;
		$res ['title'] = '已结束';
		$res ['url'] = addons_url ( 'Card://Recharge/lists', $param );
		$res ['class'] = $type == $param ['type'] ? 'current' : '';
		$nav [] = $res;

		$this->assign ( 'nav', $nav );
	}
	function lists() {
		$model = $this->getModel ( 'card_recharge' );
		$page = I ( 'p', 1, 'intval' ); // 默认显示第一页数据

		// 解析列表规则
		$list_data = $this->_list_grid ( $model );

		// 搜索条件
		$map = $this->_search_map ( $model, $fields );
		$type = I ( 'type', 0, 'intval' );
		if ($type == 1) {
			$map ['start_time'] = array (
					'gt',
					NOW_TIME
			);
		} elseif ($type == 2) {
			$map ['start_time'] = array (
					'lt',
					NOW_TIME
			);
			$map ['end_time'] = array (
					'gt',
					NOW_TIME
			);
		} elseif ($type == 3) {
			$map ['end_time'] = array (
					'lt',
					NOW_TIME
			);
		}

		$row = empty ( $model ['list_row'] ) ? 20 : $model ['list_row'];

		// 读取模型数据列表
		$name = parse_name ( get_table_name ( $model ['id'] ), true );
		$data = M ( $name )->field ( true )->where ( $map )->order ( $order )->page ( $page, $row )->select ();

		foreach ( $data as &$vo ) {
			if ($vo ['start_time'] > NOW_TIME) {
				$vo ['status'] .= '未开始';
			} elseif ($vo ['end_time'] < NOW_TIME) {
				$vo ['status'] .= '已结束';
			} else {
				$vo ['status'] .= '进行中';
			}

			$vo ['start_time'] = time_format ( $vo ['start_time'] ) . ' 至<br/>' . time_format ( $vo ['end_time'] );
		}

		/* 查询记录总数 */
		$count = M ( $name )->where ( $map )->count ();

		$list_data ['list_data'] = $data;

		// 分页
		if ($count > $row) {
			$page = new \Think\Page ( $count, $row );
			$page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
			$list_data ['_page'] = $page->show ();
		}

		$this->assign ( $list_data );
		// dump($list_data);

		$this->display ( $templateFile );
	}
	function add() {
		$this->_get_card_conpon ();
		$this->display ( 'edit' );
	}
	function edit() {
		$id = I ( 'id' );
		$model = $this->getModel ( 'card_recharge' );

		if (IS_POST) {
			$this->checkPostData();
			$_POST ['is_show'] = intval ( $_POST ['is_show'] );

			$act = empty ( $id ) ? 'add' : 'save';
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			$Model->create () && $res = $Model->$act ();
			if ($res !== false) {
				$act == 'add' && $id = $res;
				$this->setCondition ( $id, $_POST );

				$this->success ( '保存' . $model ['title'] . '成功！', U ( 'lists?model=' . $model ['name'], $this->get_param ) );
			} else {
				$this->error ( $Model->getError () );
			}
		} else {
			// 获取数据
			$data = M ( get_table_name ( $model ['id'] ) )->find ( $id );
			$data || $this->error ( '数据不存在！' );

			$token = get_token ();
			if (isset ( $data ['token'] ) && $token != $data ['token'] && defined ( 'ADDON_PUBLIC_PATH' )) {
				$this->error ( '非法访问！' );
			}
			$this->assign ( 'data', $data );

			// 优惠信息
			$map ['reward_id'] = $id;
			$list = M ( 'card_recharge_condition' )->where ( $map )->order ( 'sort asc' )->select ();
			$this->assign ( 'cd', $list [0] );

			if ($data ['is_mult'] == 1) {
				unset ( $list [0] );
				$this->assign ( 'condition_list', $list );
			}

			$this->_get_card_conpon ();

			$this->display ( 'edit' );
		}
	}
	// 获取优惠券列表
	function _get_card_conpon() {
		$map ['end_time'] = array (
				'gt',
				NOW_TIME
		);
		$map ['token'] = get_token ();

		$list = M ( 'shop_coupon' )->where ( $map )->field ( 'id,title' )->order ( 'id desc' )->select ();
		$this->assign ( 'shop_conpon_list', $list );
	}

	// 保存优惠信息
	function setCondition($reward_id, $data) {
		$dao = M ( 'card_recharge_condition' );
		$save ['reward_id'] = $reward_id;

		$list = $dao->where ( $save )->select ();
		foreach ( $list as $v ) {
			$arr [$v ['condition']] = $v ['id'];
		}

		foreach ( $data ['condition'] as $key => $val ) {
			$save ['condition'] = $val;
			$save ['money'] = intval ( $data ['money'] [$key] );
			$save ['money_param'] = safe ( $data ['money_param'] [$key] );
			$save ['score'] = intval ( $data ['score'] [$key] );
			$save ['score_param'] = intval ( $data ['score_param'] [$key] );
			$save ['shop_coupon'] = intval ( $data ['shop_coupon'] [$key] );
			$save ['shop_coupon_param'] = intval ( $data ['shop_coupon_param'] [$key] );
			$save ['sort'] = $key;

			if (! empty ( $arr [$val] )) {
				$ids [] = $map ['id'] = $arr [$val];
				$dao->where ( $map )->save ( $save );
			} else {
				$ids [] = $dao->add ( $save );
			}
		}

		$diff = array_diff ( $arr, $ids );
		if (! empty ( $diff )) {
			$map2 ['id'] = array (
					'in',
					$diff
			);
			$dao->where ( $map2 )->delete ();
		}
	}
	function checkPostData(){
		//判断活动名称是否填写
		if (! I ( 'post.title' )) {
			$this->error ( '活动名称必填' );
		}
		// 判断时间选择是否正确
		$start_time = I( 'post.start_time' );
		$end_time = I( 'post.end_time' );
        if(empty($start_time)||empty($end_time)){
        	$this->error('有效期不能为空');
        }
		if (strtotime ( $start_time ) >= strtotime ( $end_time )) {
			$this->error ( '开始时间不能大于或等于结束时间' );
		}

	}
}
