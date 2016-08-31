<?php

namespace Addons\HelpOpen\Controller;

use Home\Controller\AddonsController;

class HelpOpenController extends AddonsController {
	function _initialize() {
		parent::_initialize ();
		$type = I ( 'type', 0, 'intval' );
		
		if (_ACTION == 'prize_lists') {
			$type = 10;
			$res ['title'] = '获奖查询';
			$res ['url'] = '###';
			$res ['class'] = 'current';
			$nav [] = $res;
		}
		if (_ACTION == 'share_lists') {
			$type = 10;
			$res ['title'] = '分享记录';
			$res ['url'] = '###';
			$res ['class'] = 'current';
			$nav [] = $res;
		}
		if (_ACTION == 'collect_lists') {
			$type = 10;
			$res ['title'] = '领取人列表';
			$res ['url'] = '###';
			$res ['class'] = 'current';
			$nav [] = $res;
		}
		if (_ACTION == 'sncode_lists') {
			$type = 10;
			$res ['title'] = '核销记录';
			$res ['url'] = '###';
			$res ['class'] = 'current';
			$nav [] = $res;
		}
		$param['mdm']=$_GET['mdm'];
		$param ['type'] = 0;
		$res ['title'] = '所有的9+1红包活动';
		$res ['url'] = addons_url ( 'HelpOpen://HelpOpen/lists', $param );
		$res ['class'] = $type == $param ['type'] && _ACTION == 'lists' ? 'current' : '';
		$nav [] = $res;
		
		$param ['type'] = 1;
		$res ['title'] = '未开始';
		$res ['url'] = addons_url ( 'HelpOpen://HelpOpen/lists', $param );
		$res ['class'] = $type == $param ['type'] ? 'current' : '';
		$nav [] = $res;
		
		$param ['type'] = 2;
		$res ['title'] = '进行中';
		$res ['url'] = addons_url ( 'HelpOpen://HelpOpen/lists', $param );
		$res ['class'] = $type == $param ['type'] ? 'current' : '';
		$nav [] = $res;
		
		$param ['type'] = 3;
		$res ['title'] = '已结束';
		$res ['url'] = addons_url ( 'HelpOpen://HelpOpen/lists', $param );
		$res ['class'] = $type == $param ['type'] ? 'current' : '';
		$nav [] = $res;
		
		$res ['title'] = '模板消息配置';
		$res ['url'] = addons_url ( 'HelpOpen://HelpOpen/config', array('mdm'=>$_GET['mdm']) );
		$res ['class'] = _ACTION == config ? 'current' : '';
		$nav [] = $res;
		$this->assign ( 'nav', $nav );
	}
	function lists() {

	    $isAjax = I ( 'isAjax' );
	    $isRadio = I ( 'isRadio' );
		$model = $this->getModel ( 'help_open' );
		$page = I ( 'p', 1, 'intval' ); // 默认显示第一页数据
		                                
		// 解析列表规则
		$list_data = $this->_list_grid ( $model );
		
		// 搜索条件
		$map = $this->_search_map ( $model, $list_data ['fields'] );
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
		$data = M ( $name )->field ( true )->where ( $map )->order ( 'id desc' )->page ( $page, $row )->select ();
		
		$dao = D ( 'HelpOpenUser' );
		foreach ( $data as &$vo ) {
			if ($vo ['status']) {
				if ($vo ['start_time'] > NOW_TIME) {
					$vo ['status'] = '未开始';
				} elseif ($vo ['end_time'] < NOW_TIME) {
					$vo ['status'] = '已结束';
				} else {
					$vo ['status'] = '进行中';
				}
			} else {
				$vo ['status'] = '已禁用';
			}
			$vo ['start_time'] = time_format ( $vo ['start_time'] ) . ' 至<br/>' . time_format ( $vo ['end_time'] );
			
			$user_map ['help_id'] = $vo ['id'];
			$user_map ['friend_uid'] = 0;
			$join_list = $dao->where ( $user_map )->field ( 'invite_uid,sn_id,join_count' )->select ();
			
			$collect_num = $total = 0;
			foreach ( $join_list as $jo ) {
				$total += $jo ['join_count'];
				if ($jo ['sn_id'] > 0){
						$collect_num += 1;
				}
			}
			
			$vo ['collect_num'] = $collect_num; // 已经达到领取大礼包要求的人数
			$vo ['total'] = $total + $collect_num; // 领取小礼包和大礼包的总数
		}
		
		/* 查询记录总数 */
		$count = M ( $name )->where ( $map )->count ();
		$list_data ['list_data'] = $data;
		$this->assign('search_url',U('lists',array('type'=>$type ,'mdm'=>$_GET['mdm'])));
		// 分页
		if ($count > $row) {
			$page = new \Think\Page ( $count, $row );
			$page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
			$list_data ['_page'] = $page->show ();
		}
		if ($isAjax) {
		    $this->assign('isRadio',$isRadio);
		    $this->assign ( $list_data );
		    $this->display ( 'ajax_lists_data' );
		} else {
		    $this->assign ( $list_data );
		    // dump($list_data);
		    
		    $this->display ();
		}
		
	}
	function add() {
		$this->_prize_list ();
		$this->_get_coupon ();
		
		$this->display ( 'edit' );
	}
	function edit() {
		$id = I ( 'id' );
		$model = $this->getModel ( 'help_open' );
		
		if (IS_POST) {
			$act = empty ( $id ) ? 'add' : 'save';
			$_POST ['shop_ids'] = implode ( ',', array_filter ( $_POST ['shop_ids'] ) );
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			$res = false;
			$Model->create () && $res = $Model->$act ();
			if ($res !== false) {
				$act == 'add' && $id = $res;
				
				$this->setPrize ( $id, $_POST );
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
			
			$this->_prize_list ( $id );
			$this->_get_coupon ();
			
			$this->display ();
		}
	}
	// 获取代金券列表
	function _get_shop_coupon() {
	    if (!is_install("ShopCoupon")){
	        return false;
	    }
		$list = D ( 'Addons://ShopCoupon/Coupon' )->getSelectList ();
		$this->assign ( 'shop_conpon_list', $list );
	}
	// 获取优惠券列表
	function _get_coupon() {
		$list = D ( 'Addons://Coupon/Coupon' )->getSelectList ();
		$this->assign ( 'conpon_list', $list );
	}
	function preview() {
		$id = I ( 'id', 0, 'intval' );
		$url = addons_url ( 'HelpOpen://Wap/index', array (
				'id' => $id,
				'invite_uid' => $this->mid 
		) );
		$this->assign ( 'url', $url );
		$this->display ( SITE_PATH . '/Application/Home/View/default/Addons/preview.html' );
	}
	function _prize_list($help_id = 0) {
		if ($help_id) {
			$map ['help_id'] = $help_id;
			$map ['is_del']= 0;
			$list = M ( 'help_open_prize' )->where ( $map )->order ( 'sort asc' )->select ();
			foreach ( $list as $k => &$vo ) {
				if ($k == 0) {
					$vo ['title'] = '大礼包';
				} else {
					$vo ['title'] = '小礼包' . $k;
				}
			}
		} else {
			$list [0] = array (
					'title' => '大礼包',
					'sort' => 0 
			);
			for($i = 1; $i < 7; $i ++) {
				$list [$i] = array (
						'title' => '小礼包' . $i,
						'sort' => $i 
				);
			}
		}
		
		$this->assign ( 'prize_list', $list );
	}
	// 保存优惠信息
	function setPrize($help_id, $data) {
		$dao = M ( 'help_open_prize' );
		$hmap ['is_del']= 0;
		$hmap ['help_id'] = $help_id;
		$list = $dao->where ( $hmap )->select ();
		foreach ( $list as $v ) {
			$arr [$v ['sort']] = $v ['id'];
		}
		
		foreach ( $data ['name'] as $key => $val ) {
			if (empty ( $val )) {
				if ($key == 0) {
					$this->error ( '请配置大礼包的数据' );
				} elseif ($key == 1) {
					$this->error ( '请配置小礼包1的数据' );
				} else {
					continue;
				}
			}
			
			$save ['help_id'] = $help_id;
			$save ['name'] = $val;
			$save ['sort'] = $key;
			$save ['prize_type'] = intval ( $data ['prize_type'] [$key] );
			$save ['money'] = intval ( $data ['money'] [$key] );
			$save ['shop_coupon_id'] = intval ( $data ['shop_coupon_id'] [$key] );
			$save ['coupon_id'] = intval ( $data ['coupon_id'] [$key] );
			
			if (! empty ( $arr [$val] )) {
				$ids [] = $map ['id'] = $arr [$val];
				$dao->where ( $map )->save ( $save );
			} else {
				$ids [] = $dao->add ( $save );
			}
			unset ( $save );
		}
		
		$diff = array_diff ( $arr, $ids );
		if (! empty ( $diff )) {
			$map2 ['id'] = array (
					'in',
					$diff 
			);
			$del['is_del']=1;
			$dao->where ( $map2 )->save($del);
		}
		
		$this->success ( '保存成功！', U ( 'lists' ) );
	}
	// 获奖查询
	function prize_lists() {
		$this->assign ( 'add_button', false );
		$this->assign ( 'del_button', false );
		$this->assign ( 'check_all', false );
		
		$model = $this->getModel ( 'help_open_prize' );
		$page = I ( 'p', 1, 'intval' ); // 默认显示第一页数据
		                                
		// 解析列表规则
		$list_data = $this->_list_grid ( $model );
		
		// 搜索条件
		$map['is_del']=0;
		$map ['help_id'] = $hmap ['id'] = I ( 'id' );
		$title = I ( 'title' );
		if (! empty ( $title )) {
			$map ['uid'] = array (
					'in',
					D ( 'Common/User' )->searchUser ( $title ) 
			);
		}
		$where = 'sn_id>0';
		$row=20;
		// 读取模型数据列表
		$data = M ( 'help_open_user' )->field ( true )->where ( $map )->where ( $where )->order ( 'id desc' )->page ( $page, $row )->select ();
		
		$sdao = M ( 'sn_code' );
		foreach ( $data as &$vo ) {
			if ($vo ['friend_uid'] == 0) {
				$user = getUserInfo ( $vo ['invite_uid'] );
				$vo ['userface'] = url_img_html ( $user ['headimgurl'] );
				$vo ['nickname'] = $user ['nickname'];
				$vo ['type'] = '大礼包';
				$url = U ( 'collect_lists', array (
						'help_id' => $map ['help_id'],
						'invite_uid' => $vo ['invite_uid'] 
				) );
				$vo ['deal'] = "<a href='$url'>领取人列表</a>";
			} else {
				$user = getUserInfo ( $vo ['friend_uid'] );
				$vo ['userface'] = url_img_html ( $user ['headimgurl'] );
				$vo ['nickname'] = $user ['nickname'];
				$vo ['type'] = '小礼包';
				$vo ['deal'] = '';
			}
			$vo ['cTime'] = time_format ( $vo ['cTime'] );
			
			$smap ['id'] = $vo ['sn_id'];
			$vo ['prize'] = $sdao->where ( $smap )->getField ( 'prize_title' );
		}
		
		/* 查询记录总数 */
		$count = M ( 'help_open_user' )->where ( $map )->where ( $where )->count ();
		$list_data ['list_data'] = $data;
		
		// 分页
		if ($count > $row) {
			$page = new \Think\Page ( $count, $row );
			$page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
			$list_data ['_page'] = $page->show ();
		}
		
		$this->assign ( $list_data );
		// dump($list_data);
		
		$this->display ( 'lists' );
	}
	function share_lists() {
		$this->assign ( 'add_button', false );
		$this->assign ( 'del_button', false );
		$this->assign ( 'check_all', false );
		
		$model = $this->getModel ( 'help_open_user' );
		$page = I ( 'p', 1, 'intval' ); // 默认显示第一页数据
		                                
		// 解析列表规则
		$list_data = $this->_list_grid ( $model );
		// 搜索条件
		$map ['help_id'] = $hmap ['id'] = I ( 'id' );
		$title = I ( 'title' );
		if (! empty ( $title )) {
		    $uids = D ( 'Common/User' )->searchUser ( $title ) ;
		    if ($uids){
		        $map ['invite_uid'] = array (
		            'in',
		            $uids
		        );
		    }
		}
		if (!isset($map['invite_uid'])){
		    $map ['invite_uid'] = array (
		        'gt',
		        0
		    );
		}
		$map ['friend_uid'] = 0;
		$row=20;
		// 读取模型数据列表
		$data = M ( 'help_open_user' )->field ( true )->where ( $map )->order ( 'id desc' )->page ( $page, $row )->select ();
		foreach ( $data as &$vo ) {
			$user = getUserInfo ( $vo ['invite_uid'] );
			$vo ['userface'] = url_img_html ( $user ['headimgurl'] );
			$vo ['nickname'] = $user ['nickname'];
			$vo ['cTime'] = time_format ( $vo ['cTime'] );
		}
		
		/* 查询记录总数 */
		$count = M ( 'help_open_user' )->where ( $map )->count ();
		$list_data ['list_data'] = $data;
		
		// 分页
		if ($count > $row) {
			$page = new \Think\Page ( $count, $row );
			$page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
			$list_data ['_page'] = $page->show ();
		}
		
		$this->assign ( $list_data );
		$this->assign('search_key','title');
		$this->assign('placeholder','请输入分享用户名称');
		$this->assign('search_url',U('share_lists',array('id'=>$hmap ['id'] ,'mdm'=>$_GET['mdm'])));
		$this->display ( 'lists' );
	}
	function collect_lists() {
		$list_data ["list_grids"] = array (
				"userface" => array (
						"field" => "userface",
						"title" => " 用户头像" 
				),
				"nickname" => array (
						"field" => "nickname",
						"title" => "领取用户" 
				),
				"join_count" => array (
						"field" => "sn_id",
						"title" => "奖品名称" 
				),
				"cTime" => array (
						"field" => "cTime",
						"title" => "领取时间" 
				) 
		);
		
		$this->assign ( 'add_button', false );
		$this->assign ( 'del_button', false );
		$this->assign ( 'check_all', false );
		$this->assign ( 'search_button', false );
		
		$page = I ( 'p', 1, 'intval' ); // 默认显示第一页数据
		                                
		// 搜索条件
		$map ['help_id'] = $hmap ['id'] = I ( 'help_id' );
		$map ['invite_uid'] = I ( 'invite_uid' );
		$map ['friend_uid'] = array (
				'neq',
				0 
		);
		
		// 读取模型数据列表
		$data = M ( 'help_open_user' )->field ( true )->where ( $map )->order ( 'id desc' )->page ( $page, 20 )->select ();
		
		foreach ( $data as &$vo ) {
			$user = getUserInfo ( $vo ['friend_uid'] );
			$vo ['userface'] = url_img_html ( $user ['headimgurl'] );
			$vo ['nickname'] = $user ['nickname'];
			$vo ['cTime'] = time_format ( $vo ['cTime'] );
			$sn = D ( 'Common/SnCode' )->getInfoById ( $vo ['sn_id'] );
			$vo ['sn_id'] = $sn ['prize_title'];
		}
		
		/* 查询记录总数 */
		$count = M ( 'help_open_user' )->where ( $map )->count ();
		$list_data ['list_data'] = $data;
		
		// 分页
		if ($count > $row) {
			$page = new \Think\Page ( $count, $row );
			$page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
			$list_data ['_page'] = $page->show ();
		}
		
		$this->assign ( $list_data );
		// dump($list_data);
		
		$this->display ( 'lists' );
	}
	function sncode_lists() {
		$help_id = $hpmap ['help_id'] = I ( 'id', 0, 'intval' );
// 		$hpmap['is_del'] = 0;
		$hp_list = D ( 'HelpOpenPrize' )->where ( $hpmap )->select ();
		
		foreach ( $hp_list as $vo ) {
			$hp_arr [$vo ['id']] = $vo;
			if ($vo['is_del']==0){
				$hp_data[]=$vo;
			}
		}
		$this->assign ( 'hp_list', $hp_data );
		$row=20;
		$list_data ["list_grids"] = array (
				"nickname" => array (
						"field" => "nickname",
						"title" => "用户" 
				),
				"content" => array (
						"field" => "content",
						"title" => " 详细信息" 
				),
				"money" => array (
						"field" => "money",
						"title" => " 金额" 
				),
				"admin_uid" => array (
						"field" => "admin_uid",
						"title" => "工作人员" 
				),
				"use_time" => array (
						"field" => "use_time",
						"title" => "核销时间" 
				) 
		);
		
		$px = C ( 'DB_PREFIX' );
		$page = I ( 'p', 1, 'intval' ); // 默认显示第一页数据
		                                
		// 搜索条件
		$where = "u.help_id='{$help_id}' AND u.sn_id>0 AND s.is_use=1 AND s.addon IN ('ShopCoupon','Coupon')";
		
		$start_time = I ( 'start_time' );
		if ($start_time) {
			$where .= " AND s.use_time>" . strtotime ( $start_time );
			$this->assign ( 'start_time', $start_time );
		}
		
		$end_time = I ( 'end_time' );
		if ($end_time) {
			$where .= " AND s.use_time<" . strtotime ( $end_time );
			$this->assign ( 'end_time', $start_time );
		}
		
		$search_nickname = I ( 'search_nickname' );
		if (! empty ( $search_nickname )) {
			$where .= " AND s.uid IN(" . D ( 'Common/User' )->searchUser ( $search_nickname ) . ")";
			
			$this->assign ( 'search_nickname', $search_nickname );
		}
		
		$search_prize_id = I ( 'search_prize_id', 0, 'intval' );
		if ($search_prize_id) {
			$where .= " AND s.prize_id=" . $search_prize_id;
			$this->assign ( 'search_prize_id', $search_prize_id );
		}
		
		// 读取模型数据列表
		$dao = M ()->table ( "{$px}help_open_user u" )->join ( "{$px}sn_code s ON u.sn_id=s.id" );
		$data = $dao->field ( 's.*' )->where ( $where )->order ( 's.use_time DESC' )->page ( $page, 20 )->select ();
		foreach ( $data as &$vo ) {
			$vo ['nickname'] = get_nickname ( $vo ['uid'] );
			$vo ['use_time'] = time_format ( $vo ['use_time'] );
			$vo ['admin_uid'] = get_nickname ( $vo ['admin_uid'] );
			
			$prize = $hp_arr [$vo ['prize_id']];
			if ($vo ['addon'] == 'Coupon') {
				$info = D ( 'Addons://Coupon/Coupon' )->getInfo ( $prize ['coupon_id'] );
				$vo ['content'] = '核销优惠券： ' . $info ['title'] . ', 奖项名： ' . $prize ['name'];
				$vo ['money'] = '0.0';
			} else {
                if (is_install("ShopCoupon")) {
                    $info = D('Addons://ShopCoupon/Coupon')->getInfo($prize['coupon_id']);
                    $vo['content'] = '核销代金券： ' . $info['title'] . ', 奖项名： ' . $prize['name'];
                    $vo['money'] = $vo['prize_title'];
                }
			}
		}
		/* 查询记录总数 */
		$dao = M ()->table ( "{$px}help_open_user u" )->join ( "{$px}sn_code s ON u.sn_id=s.id" );
		$count = $dao->where ( $where )->count ();
		$list_data ['list_data'] = $data;
		
		// 分页
		if ($count > $row) {
			$page = new \Think\Page ( $count, $row );
			$page->setConfig ( 'theme', '%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%' );
			$list_data ['_page'] = $page->show ();
		}
		
		$this->assign ( $list_data );
		// dump($list_data);
		
		$this->display ();
	}
	function export() {
		set_time_limit ( 0 );
		
		$help_id = $hpmap ['help_id'] = I ( 'id', 0, 'intval' );
// 		$hpmap ['is_del'] = 0;
		$hp_list = D ( 'HelpOpenPrize' )->where ( $hpmap )->select ();
		$this->assign ( 'hp_list', $hp_list );
		foreach ( $hp_list as $vo ) {
			$hp_arr [$vo ['id']] = $vo;
		}
		
		$dataArr [0] = array (
				0 => "用户",
				1 => " 详细信息",
				2 => " 金额",
				3 => "工作人员",
				4 => "核销时间" 
		);
		
		$px = C ( 'DB_PREFIX' );
		$page = I ( 'p', 1, 'intval' ); // 默认显示第一页数据
		                                
		// 搜索条件
		$where = "u.help_id='{$help_id}' AND u.sn_id>0 AND s.is_use=1 AND s.addon IN ('ShopCoupon','Coupon')";
		
		$start_time = I ( 'start_time' );
		if ($start_time) {
			$where .= " AND s.use_time>" . strtotime ( $start_time );
			$this->assign ( 'start_time', $start_time );
		}
		
		$end_time = I ( 'end_time' );
		if ($end_time) {
			$where .= " AND s.use_time<" . strtotime ( $end_time );
			$this->assign ( 'end_time', $start_time );
		}
		
		$search_nickname = I ( 'search_nickname' );
		if (! empty ( $search_nickname )) {
			$where .= " AND s.uid IN(" . D ( 'Common/User' )->searchUser ( $search_nickname ) . ")";
			
			$this->assign ( 'search_nickname', $search_nickname );
		}
		
		$search_prize_id = I ( 'search_prize_id', 0, 'intval' );
		if ($search_prize_id) {
			$where .= " AND s.prize_id=" . $search_prize_id;
			$this->assign ( 'search_prize_id', $search_prize_id );
		}
		
		// 读取模型数据列表
		$dao = M ()->table ( "{$px}help_open_user u" )->join ( "{$px}sn_code s ON u.sn_id=s.id" );
		$data = $dao->field ( 's.*' )->where ( $where )->order ( 's.use_time DESC' )->limit ( 5000 )->select ();
		// dump ( $data );
		foreach ( $data as $k => $vo ) {
			$prize = $hp_arr [$vo ['prize_id']];
			if ($vo ['addon'] == 'Coupon') {
				$info = D ( 'Addons://Coupon/Coupon' )->getInfo ( $prize ['coupon_id'] );
				$vo ['content'] = '核销优惠券： ' . $info ['title'] . ', 奖项名： ' . $prize ['name'];
				$vo ['money'] = '0.0';
			} else {
                if (is_install("ShopCoupon")) {
                    $info = D('Addons://ShopCoupon/Coupon')->getInfo($prize['coupon_id']);
                    $vo['content'] = '核销代金券： ' . $info['title'] . ', 奖项名： ' . $prize['name'];
                    $vo['money'] = $vo['prize_title'];
                }
			}
			
			$dataArr [$k + 1] = array (
					0 => get_nickname ( $vo ['uid'] ),
					1 => $vo ['content'],
					2 => $vo ['money'],
					3 => get_nickname ( $vo ['admin_uid'] ),
					4 => time_format ( $vo ['use_time'] ) 
			);
		}
		vendor ( 'out-csv' );
		export_csv ( $dataArr, 'HelpOpen_' . $help_id );
// 		outExcel ( $dataArr, 'HelpOpen_' . $help_id );
	}
	function checkDate(){
		// 判断时间选择是否正确
		if (! I ( 'post.start_time' )) {
			$this->error ( '请选择开始时间' );
		} else if (! I ( 'post.end_time' )) {
			$this->error ( '请选择结束时间' );
		} else if (strtotime ( I ( 'post.start_time' ) ) > strtotime ( I ( 'post.end_time' ) )) {
			$this->error ( '开始时间不能大于结束时间' );
		}
	}
}
