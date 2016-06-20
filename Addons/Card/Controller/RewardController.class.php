<?php

namespace Addons\Card\Controller;

use Home\Controller\AddonsController;
// 开卡即送活动
class RewardController extends AddonsController {
	function _initialize() {
		parent::_initialize ();
		
		$type = I ( 'type', 0, 'intval' );
		$param['mdm']=$_GET['mdm'];
		$param ['type'] = 0;
		$res ['title'] = '所有的开卡即送活动';
		$res ['url'] = addons_url ( 'Card://Reward/lists', $param );
		$res ['class'] = $type == $param ['type'] ? 'current' : '';
		$nav [] = $res;
		
		$param ['type'] = 1;
		$res ['title'] = '未开始';
		$res ['url'] = addons_url ( 'Card://Reward/lists', $param );
		$res ['class'] = $type == $param ['type'] ? 'current' : '';
		$nav [] = $res;
		
		$param ['type'] = 2;
		$res ['title'] = '进行中';
		$res ['url'] = addons_url ( 'Card://Reward/lists', $param );
		$res ['class'] = $type == $param ['type'] ? 'current' : '';
		$nav [] = $res;
		
		$param ['type'] = 3;
		$res ['title'] = '已结束';
		$res ['url'] = addons_url ( 'Card://Reward/lists', $param );
		$res ['class'] = $type == $param ['type'] ? 'current' : '';
		$nav [] = $res;
		
		$this->assign ( 'nav', $nav );
	}
	function lists() {
		$model = $this->getModel ( 'card_reward' );
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
			if ($vo ['type'] == 0) {
				$vo ['type'] = '送 ' . $vo ['score'] . '积分';
			} else {
				$vo ['type'] = '送： ' . M ( 'shop_coupon' )->where ( "id='{$vo[coupon_id]}'" )->getField ( 'title' );
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
		$model = $this->getModel ( 'card_reward' );
		
		if (IS_POST) {
		    if (empty($_POST['title'])){
		        $this->error('请填写活动名称');
		    }
		    if (empty($_POST['start_time'])){
		        $this->error('请填写开始时间');
		    }
		    if (empty($_POST['end_time'])){
		        $this->error('请填写结束时间');
		    }
		    if ($_POST['start_time'] >= $_POST['end_time']){
		        $this->error('活动开始时间必须小于结束时间');
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
			$this->assign ( 'data', $data );
			
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
}
