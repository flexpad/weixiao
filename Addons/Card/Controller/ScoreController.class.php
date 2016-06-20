<?php

namespace Addons\Card\Controller;

use Home\Controller\AddonsController;
// 积分兑换活动
class ScoreController extends AddonsController {
	function _initialize() {
		parent::_initialize ();
		
		$type = I ( 'type', 0, 'intval' );
		$param['mdm']=$_GET['mdm'];
		$param ['type'] = 0;
		$res ['title'] = '所有的积分兑换活动';
		$res ['url'] = addons_url ( 'Card://Score/lists', $param );
		$res ['class'] = $type == $param ['type'] ? 'current' : '';
		$nav [] = $res;
		
		$param ['type'] = 1;
		$res ['title'] = '未开始';
		$res ['url'] = addons_url ( 'Card://Score/lists', $param );
		$res ['class'] = $type == $param ['type'] ? 'current' : '';
		$nav [] = $res;
		
		$param ['type'] = 2;
		$res ['title'] = '进行中';
		$res ['url'] = addons_url ( 'Card://Score/lists', $param );
		$res ['class'] = $type == $param ['type'] ? 'current' : '';
		$nav [] = $res;
		
		$param ['type'] = 3;
		$res ['title'] = '已结束';
		$res ['url'] = addons_url ( 'Card://Score/lists', $param );
		$res ['class'] = $type == $param ['type'] ? 'current' : '';
		$nav [] = $res;
		
		$this->assign ( 'nav', $nav );
	}
	function lists() {
		$model = $this->getModel ( 'card_score' );
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
			$vo ['coupon_id'] = $vo ['score_limit'] . '积分兑换 ' . M ( 'shop_coupon' )->where ( "id='{$vo[coupon_id]}'" )->getField ( 'title' );
			
			$vo ['start_time'] = time_format ( $vo ['start_time'] ) . ' 至<br/>' . time_format ( $vo ['end_time'] );
			
			if ($vo ['member'] == 0) {
				$vo ['member'] = '所有用户';
			} elseif ($vo ['member'] == -1) {
				$vo ['member'] = '所有会员卡成员';
			} else {
			    $memberArr=explode(',', $vo['member']);
				$card_map ['id'] = array('in',$memberArr);
				$level_name= M ( 'card_level' )->where ( $card_map )->getFields ( 'level' );
				$vo['member']=implode('<br/>', $level_name);
			}
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
		$this->_card_level ();
		$this->display ( 'edit' );
	}
	function edit() {
		$id = I ( 'id' );
		$model = $this->getModel ( 'card_score' );
		
		if (IS_POST) {
		    if (empty($_POST['title'])){
		        $this->error('活动名称必须');
		    }
		    if (empty($_POST['start_time'])){
		        $this->error('开始时间必须');
		    }
		    if (empty($_POST['end_time'])){
		        $this->error('结束时间必须');
		    }
			$score_limit = intval($_POST['score_limit']);
		    if (empty($score_limit)){
		        $this->error('兑换所需积分必须');
		    }
		    if (empty($_POST['coupon_id'])){
		        $this->error('请选择兑换券，添加兑换券可到奖品库添加！');
		    }
			$_POST ['is_show'] = intval ( $_POST ['is_show'] );
			
			$act = empty ( $id ) ? 'add' : 'save';
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			
			if ($Model->create () && $res = $Model->$act ()) {
				$act == 'add' && $id = $res;
				
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
			$data['member']=explode(',', $data['member']);
			$this->assign ( 'data', $data );
			
			$this->_get_card_conpon ( $data ['coupon_type'] );
			$this->_card_level ();
			
			$this->display ( 'edit' );
		}
	}
	// 获取优惠券列表
	function _get_card_conpon($type = 0) {
		$map ['end_time'] = array (
				'gt',
				NOW_TIME 
		);
		$map ['token'] = get_token ();
		if ($type == 0) {
			$list = M ( 'shop_coupon' )->where ( $map )->field ( 'id,title' )->order ( 'id desc' )->select ();
		} else {
			$list = M ( 'coupon' )->where ( $map )->field ( 'id,title' )->order ( 'id desc' )->select ();
		}
		$this->assign ( 'shop_conpon_list', $list );
	}
	function _card_level() {
		if (M ( 'addons' )->where ( 'name="Card"' )->find ()) {
			$map ['token'] = get_token ();
			$list = M ( 'card_level' )->where ( $map )->select ();
			$this->assign ( 'card_level', $list );
		}
	}
	function ajax_get_card_conpon() {
		$type = I ( 'type' );
		$map ['end_time'] = array (
				'gt',
				NOW_TIME 
		);
		$map ['token'] = get_token ();
		if ($type == 0) {
			$list = M ( 'shop_coupon' )->where ( $map )->field ( 'id,title' )->order ( 'id desc' )->select ();
		} else {
			$list = M ( 'coupon' )->where ( $map )->field ( 'id,title' )->order ( 'id desc' )->select ();
		}
		$this->ajaxReturn ( $list );
	}
}
