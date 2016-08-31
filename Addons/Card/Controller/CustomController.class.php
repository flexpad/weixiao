<?php

namespace Addons\Card\Controller;

use Addons\Card\Controller\BaseController;
// 客户关怀活动
class CustomController extends BaseController {
	function _initialize() {
		parent::_initialize ();
		
		$type = I ( 'type', 0, 'intval' );
		$param['mdm']=$_GET['mdm'];
		$param ['type'] = 0;
		$res ['title'] = '所有的客户关怀活动';
		$res ['url'] = addons_url ( 'Card://Custom/lists', $param );
		$res ['class'] = $type == $param ['type'] ? 'current' : '';
		$nav [] = $res;
		
		$param ['type'] = 1;
		$res ['title'] = '公历节日';
		$res ['url'] = addons_url ( 'Card://Custom/lists', $param );
		$res ['class'] = $type == $param ['type'] ? 'current' : '';
		$nav [] = $res;
		
		$param ['type'] = 2;
		$res ['title'] = '生日关怀';
		$res ['url'] = addons_url ( 'Card://Custom/lists', $param );
		$res ['class'] = $type == $param ['type'] ? 'current' : '';
		$nav [] = $res;
		
		$this->assign ( 'nav', $nav );
	}
	function lists() {
		$model = $this->getModel ( 'card_custom' );
		$page = I ( 'p', 1, 'intval' ); // 默认显示第一页数据
		                                
		// 解析列表规则
		$list_data = $this->_list_grid ( $model );
		
		// 搜索条件
		$map = $this->_search_map ( $model, $fields );
		$type = I ( 'type', 0, 'intval' );
		if ($type == 1) {
			$map ['is_birthday'] = 0;
		} elseif ($type == 2) {
			$map ['is_birthday'] = 1;
		}

		$row = empty ( $model ['list_row'] ) ? 20 : $model ['list_row'];
		
		// 读取模型数据列表
		$name = parse_name ( get_table_name ( $model ['id'] ), true );
		$data = M ( $name )->field ( true )->where ( $map )->order ( 'id desc' )->page ( $page, $row )->select ();
		$level_map['token']=get_token();
		$levels=M ( 'card_level' )->where ( $level_map )->getFields ( 'id,level' );
		foreach ( $data as &$vo ) {
			
			if ($vo ['is_birthday']) {
				$vo ['start_time'] = '会员生日';
				$vo ['end_time'] = '生日前' . $vo ['before_day'] . ' 天';
				time_format ( $vo ['end_time'] );
			} else {
				$vo ['start_time'] = time_format ( $vo ['start_time'] );
				$vo ['end_time'] = time_format ( $vo ['end_time'] );
			}
			$member=explode(',', $vo['member']);
			$vo['member']='';
			foreach ($member as $mm){
				if ($mm == 0){
					$vo ['member'] .= '所有用户,';
				}else if($mm == -2){
					$vo ['member'] .= '女性用户,';
				}else if($mm == -3){
					$vo ['member'] .= '男性用户,';
				}else if($mm == -1){
					$vo ['member'] .= '所有会员卡成员,';
				}else{
					if ($levels[$mm]){
						$vo['member'].=$levels[$mm].',';
					}
				}
			}
			$vo['member']=substr($vo['member'], 0,strlen($vo['member'])-1);
// 			if ($vo ['member'] == 0) {
// 				$vo ['member'] = '所有用户';
// 			} elseif ($vo ['member'] == '-1') {
// 				$vo ['member'] = '所有会员卡成员';
// 			} else {
// 				$level_map ['id'] = $vo ['member'];
// 				$vo ['member'] = M ( 'card_level' )->where ( $level_map )->getField ( 'level' );
// 			}
			
			if ($vo ['type'] == 0) {
				$vo ['type'] = '送 ' . $vo ['score'] . '积分';
			} else {
				$vo ['type'] = '送： ' . M ( 'shop_coupon' )->where ( "id='{$vo[coupon_id]}'" )->getField ( 'title' );
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
		$model = $this->getModel ( 'card_custom' );
		
		if (IS_POST) {
			$_POST ['is_show'] = intval ( $_POST ['is_show'] );
			
			$act = empty ( $id ) ? 'add' : 'save';
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			if ($Model->create () && $res = $Model->$act ()) {
				$act == 'add' && $id = $res;
				$token=get_token();
				$key='CardCustom_listData_'.$token;
				S($key,null);
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
			$this->_get_card_conpon ();
			$this->_card_level ();
			
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
		
		$list = M ( 'coupon' )->where ( $map )->field ( 'id,title' )->order ( 'id desc' )->select ();
		$this->assign ( 'shop_conpon_list', $list );
	}
	function _card_level() {
		if (M ( 'addons' )->where ( 'name="Card"' )->find ()) {
			$map ['token'] = get_token ();
			$list = M ( 'card_level' )->where ( $map )->select ();
			$this->assign ( 'card_level', $list );
		}
	}
}
