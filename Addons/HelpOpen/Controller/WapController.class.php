<?php

namespace Addons\HelpOpen\Controller;

use Home\Controller\AddonsController;

class WapController extends AddonsController {
	function _initialize() {
		parent::_initialize ();
	}
	// 活动首页
	function index() {
		$id = $user_map ['help_id'] = $prmap ['help_id'] = I ( 'id', 0, 'intval' );
		if (empty ( $id )) {
			$this->error ( '参数非法' );
		}
		// 获取活动信息
		$info = D ( 'HelpOpen' )->getInfo ( $id );
		$this->error_tips ( $info );
		$this->assign ( 'info', $info );
		
		// 推荐人信息
		$invite_uid = $user_map ['invite_uid'] = I ( 'invite_uid', 0, 'intval' );
		
		$param ['id'] = $id;
		$param ['invite_uid'] = $this->mid;
		$param ['token'] = get_token ();
		$my_url = U ( 'index', $param );
		if (empty ( $invite_uid )) {
			redirect ( $my_url );
		}
		
		$this->assign ( 'invite_uid', $invite_uid );
		$this->assign ( 'invite_nickname', get_nickname ( $invite_uid ) );
		
		// 如果是首次进来，自动报名参与活动
		$user_dao = D ( 'HelpOpenUser' );
		$openid = $user_dao->where ( $user_map )->getField ( 'id' );
		if (! $openid) {
			$data ['invite_uid'] = $invite_uid;
			$data ['help_id'] = $id;
			$data ['friend_uid'] = 0;
			$data ['cTime'] = NOW_TIME;
			$user_dao->add ( $data );
		}
		
		// 当前用户是否关注公众号
		$map ['token'] = get_token ();
		$map ['uid'] = $this->mid;
		$has_subscribe = intval ( M ( 'public_follow' )->where ( $map )->getField ( 'has_subscribe' ) );
		$this->assign ( 'has_subscribe', $has_subscribe );
		if ($has_subscribe == 0) { // 获取需要关注的公众号二维码
			$res = D ( 'Home/QrCode' )->add_qr_code ( 'QR_SCENE', 'HelpOpen', $id, $invite_uid );
			$this->assign ( 'qrcode', $res );
		}
		// 当前用户参与信息
		$join = D ( 'HelpOpenUser' )->checkJoin ( $id, $this->mid, $invite_uid );
		$this->assign ( 'join', $join );
		
		// 已经帮助过当前推荐人拆过，并且自己也参与的情况下，直接跳转到我参与的界面就行
		$join_info = D ( 'HelpOpenUser' )->join_info ( $id, $this->mid );
		$has_join = $join_info ['id'] > 0 ? 0 : 1;
		$this->assign ( 'has_join', $has_join );
		$this->assign ( 'join_info', $join_info );
		if ($invite_uid != $this->mid && $join ['has_help'] && $has_join) {
			redirect ( $my_url );
		}
		
		$invite_left = intval ( $info ['limit_num'] - $join ['invite_count'] );
		$invite_left < 0 && $invite_left = 0;
		$progress = $invite_left == 0 ? 100 : ceil ( $join ['invite_count'] * 100 / $info ['limit_num'] );
		
		$this->assign ( 'invite_count', $join ['invite_count'] );
		$this->assign ( 'invite_left', $invite_left );
		$this->assign ( 'progress', $progress );
		
		$is_use = 1;
		if ($invite_left == 0 && $invite_uid == $this->mid) {
			$is_use = D ( 'HelpOpenUser' )->check_use ( $id, $invite_uid );
		}
		$this->assign ( 'is_use', $is_use );
		
		// 大奖信息
		$prmap ['sort'] = 0;
		$prmap ['is_del'] = 0;
		$big = M ( 'help_open_prize' )->where ( $prmap )->find ();
		$this->assign ( 'big', $big );
		
		if ($invite_uid == $this->mid) {
			$prize_name = $big ['name'];
		} else {
			$sn = D ( 'Common/SnCode' )->getInfoById ( $join ['sn_id'] );
			$samll = M ( 'help_open_prize' )->where ( $prmap )->find ( $sn ['prize_id'] );
			$prize_name = $samll ['name'];
		}
		$this->assign ( 'prize_name', $prize_name );
		
		$this->display ();
	}
	function do_help() {
		$id = I ( 'id', 0, 'intval' );
		$invite_uid = I ( 'invite_uid', 0, 'intval' );
		if (empty ( $id ) || empty ( $invite_uid )) {
			$this->error ( '参数不能为空' );
		}
		if ($this->mid == $invite_uid) {
			$this->error ( '不能拆自己的礼包' );
		}
		
		$info = D ( 'HelpOpen' )->getInfo ( $id );
		if ($info ['status'] == 0) {
			$this->error ( '活动已经禁用' );
		}
		if ($info ['start_time'] > NOW_TIME) {
			$this->error ( '活动还没开始' );
		}
		if ($info ['end_time'] < NOW_TIME) {
			$this->error ( '活动已经结束' );
		}
		$data1 ['is_del']=0;
		$data1 ['help_id'] =$data ['help_id'] = $mapou ['help_id'] = $id;
		// 获取奖品列表
		$list = M ( 'help_open_prize' )->where ( $data1 )->order ( 'sort asc' )->select ();
		foreach ( $list as $k => $vv ) {
			$flat = true;
			if ($k == 0) {
				$big_prize = $vv;
			} else {
				$uid=$this->mid;
				if ($vv ['prize_type'] == 1) { // 优惠券 OPENTM200474379 优惠券领取成功通知
					$couponinfo = D ( 'Addons://Coupon/Coupon' )->getInfo ( $vv ['coupon_id'] );
					$snMap['target_id']=$vv ['coupon_id'];
					$snMap['addon']="Coupon";
					$snMap['can_use']=1;
					$couponinfo['collect_count']=D ( 'Common/SnCode' )->where($snMap)->count();
					if ($couponinfo ['collect_count'] >= $couponinfo ['num']) {
						$flat = false;
					} else if (! empty ( $couponinfo ['start_time'] ) && $couponinfo ['start_time'] > NOW_TIME) {
						$flat = false;
					} else if (! empty ( $couponinfo ['end_time'] ) && $couponinfo ['end_time'] < NOW_TIME) {
						$flat = false;
					}
					$list = D ( 'Common/SnCode')->getMyList ( $uid, $vv['coupon_id'], 'Coupon' );
					$my_count = count ( $list );
					if ($couponinfo ['max_num'] > 0 && $my_count >= $couponinfo ['max_num']) {
						$flat = false;
					}
				} elseif ($vv ['prize_type'] == 2 && is_install("ShopCoupon")) { // 代金券 TM00483 获得代金券通知
					// 金额
					$scouponinfo = D ( 'Addons://ShopCoupon/Coupon' )->getInfo ( $vv ['shop_coupon_id'] );
					$snMap['target_id']=$vv ['shop_coupon_id'];
					$snMap['addon']="ShopCoupon";
					$snMap['can_use']=1;
					$scouponinfo['collect_count']=D ( 'Common/SnCode' )->where($snMap)->count();
					if ($scouponinfo ['collect_count'] >= $scouponinfo ['num']) {
						$flat = false;
					} else if (! empty ( $scouponinfo ['start_time'] ) && $scouponinfo ['start_time'] > NOW_TIME) {
						$flat = false;
					} else if (! empty ( $scouponinfo ['end_time'] ) && $scouponinfo ['end_time'] < NOW_TIME) {
						$flat = false;
					}
					$list = D ( 'Common/SnCode')->getMyList ( $uid, $vv['shop_coupon_id'], 'ShopCoupon' );
					$my_count = count ( $list );
				
					if ($scouponinfo ['limit_num'] > 0 && $my_count >= $info ['limit_num']) {
						$flat = false;
					}
				}
				if ($flat){
					$prize_ids [$vv ['id']] = $vv ['id'];
					$small_prize [$vv ['id']] = $vv;
				}
			}
		}
		
		$data ['friend_uid'] = $this->mid;
		if (D ( 'HelpOpenUser' )->where ( $data )->find ()) {
			$this->error ( '您已经帮拆过一次' );
		}
		
		// 当前好友获得随机小礼品一份
		$prize_id = array_rand ( $prize_ids );
		if (empty($small_prize [$prize_id])){
			$this->error('礼包已被领取完！');
		}
		$sn = $this->_sendPrize ( $id, $this->mid, $small_prize [$prize_id] );
		$data ['sn_id'] = $sn ['id'];
		$data ['cTime'] = NOW_TIME;
		$data ['invite_uid'] = $invite_uid;
		D ( 'HelpOpenUser' )->add ( $data );
		
		$mapou ['invite_uid'] = $invite_uid;
		$mapou ['friend_uid'] = 0;
		D ( 'HelpOpenUser' )->where ( $mapou )->setInc ( 'join_count' );
		
		$uname = get_nickname ( $this->mid );
		
		$join = D ( 'HelpOpenUser' )->checkJoin ( $id, $this->mid, $invite_uid );

		$config=get_addon_config('HelpOpen');

		$invite_left = intval ( $info ['limit_num'] - $join ['invite_count'] );
		if ($invite_left > 0) { // 小礼包领取通知 OPENTM200977411
			if ($config['is_close']==0){
				$content = "您的好友{$uname}领取了您推荐的爱心分享，您还需要{$invite_left}位好友帮助领取才能获得大礼包，别忘了感谢他！";
				// 			D ( 'Common/Custom' )->replyText ( $invite_uid, $content );
				D ( 'Common/TemplateMessage' )->replyGiftNotice ( $invite_uid, $uname,$first='',$orderId='',$content,'',$config['libaoling']);
			}
		} else { // 小礼包领取通知 OPENTM200977411
// 			$content = "您的好友{$uname}领取了您推荐的爱心分享，您的人气指数直接爆表！";
// 			D ( 'Common/Custom' )->replyText ( $invite_uid, $content );
			if ($config['is_close']==0){
				D ( 'Common/TemplateMessage' )->replyGiftNotice ( $invite_uid, $uname,'','','','',$config['libaoling']);
				 
			}
			// 判断大礼品发放过没有，没有的话给他发放一份
			$sn_id = intval ( D ( 'HelpOpenUser' )->where ( $mapou )->getField ( 'sn_id' ) );
			if ($sn_id <= 0) {
				// 先判断大礼包是否被领取完
				$hgmap ['help_id'] = $info ['id'];
				$hgmap ['friend_uid'] = 0;
				$hgmap ['sn_id'] = array (
						'gt',
						0 
				);
				$has_get = D ( 'HelpOpenUser' )->where ( $hgmap )->count ();
				
				if ($info ['prize_num'] > 0 && $has_get >= $info ['prize_num']) {
					if ($sn_id == 0) { // 只提示一次 礼包发放失败通知 TM00384
// 						$content = "很抱歉，大礼包已被抢完!";
// 						D ( 'Common/Custom' )->replyText ( $invite_uid, $content );
						if ($config['is_close'] == 0){
							D ( 'Common/TemplateMessage' )->replyGiftFail ( $invite_uid, $info['title'],'大礼包已被抢完','大礼包','','','',$config['libaoshi'] );
						}
						D ( 'HelpOpenUser' )->where ( $mapou )->setField ( 'sn_id', - 1 );
					}
				} else {
					$sn = $this->_sendPrize ( $id, $invite_uid, $big_prize );
					D ( 'HelpOpenUser' )->where ( $mapou )->setField ( 'sn_id', $sn ['id'] );
				}
			}
		}
		
		$this->success ( '帮TA拆开成功' );
	}
	function _sendPrize($help_id, $uid, $prize) {
		if ($prize ['prize_type'] == 0)
			return false;
			
			// 通过客服接口发送礼包通知
		$url = U ( 'my_prize', array (
				'help_id' => $help_id,
				'uid' => $uid,
				'publicid' => get_token_appinfo ( '', 'id' ) 
		) );
		if ($prize ['prize_type'] == 1) { // 优惠券 OPENTM200474379 优惠券领取成功通知		
			$content = "恭喜您获得优惠券大礼包，已发到您会员账号，<a href='$url'>点击查看我的礼包</a>";
			$data ['target_id'] = $prize ['coupon_id'];
			$data ['addon'] = 'Coupon';
			$data ['prize_title'] = $prize ['name'];
			
		} elseif ($prize ['prize_type'] == 2 && is_install("ShopCoupon")) { // 代金券 TM00483 获得代金券通知
			$content = "恭喜您获得代金券大礼包，已发到您会员账号，<a href='$url'>点击查看我的礼包</a>";
			$data ['target_id'] = $prize ['shop_coupon_id'];
			$data ['addon'] = 'ShopCoupon';
			
			// 金额
			$info = D ( 'Addons://ShopCoupon/Coupon' )->getInfo ( $prize ['shop_coupon_id'] );
			$data ['prize_title'] = $info ['money'];
			if ($info ['is_money_rand']) {
				$data ['prize_title'] = rand ( $info ['money'] * 100, $info ['money_max'] * 100 ) / 100;
			}
			
			
		} else { // 返现 OPENTM205223929 返现到账通知
			$money = $prize ['money'];
			$content = "恭喜您获得{$money}元返现，已充值您会员卡余额中，<a href='$url'>点击查看我的礼包</a>";
			$data ['target_id'] = $help_id;
			$data ['addon'] = 'HelpOpen';
			$data ['prize_title'] = $prize ['name'];
			
			// 自动充值到账户
			$log ['type'] = 0; // 系统自动充值
			$log ['remark'] = "参加9+1红包获得{$money}元返现"; // 系统自动充值
			add_money ( $uid, $money, $log );
		}
		
		$data ['uid'] = $uid;
		$data ['sn'] = uniqid ();
		$data ['cTime'] = NOW_TIME;
		$data ['token'] = get_token ();
		$data ['prize_id'] = $prize ['id'];
		
		$data ['id'] = D ( 'Common/SnCode' )->add ( $data );
		$config=get_addon_config('HelpOpen');

		if ($prize ['prize_type'] == 1){
		    //优惠券
		    $couponInfo = D ( 'Addons://Coupon/Coupon' )->getInfo ( $prize ['coupon_id'] );
		    $remark="礼包已发到您的会员卡个人中心的优惠券处，点击详情查看我的礼包";
		    if ($config['is_close']==0){
		    	D ( 'Common/TemplateMessage' )->replyCouponSuccess($uid, $couponInfo['title'],$data ['sn'],time_format($couponInfo['over_time'],'Y-m-d'),$remark,$first='恭喜您获得优惠券大礼包！',$url,$config['youhuijuan']);
		    }
		   
		}else if($prize ['prize_type'] == 2){
		    
		    $remark = "礼包已发到您的会员卡个人中心的代金券处，点击详情查看我的礼包";
		    if ($config['is_close']==0 && is_install("ShopCoupon")){
		    	D ( 'Common/TemplateMessage' )->replyShopCouponSuccess($uid, $data ['prize_title'],time_format($info['end_time'],'Y-m-d'),$remark,$first='恭喜您获得代金券大礼包',$url,$config['daijinjuan']) ;
		    }
		   
		}else{
		    $remark = "已充值到您会员卡余额中，点击详情查看我的礼包";
		    $content="参加9+1红包获得{$money}元返现";
		    if ($config['is_close']==0){
		    	D ( 'Common/TemplateMessage' )->replyReturnMoney($uid, $money.'元',$content,$remark,$first="恭喜您获得{$money}元返现",$url,$config['fangxian']) ;
		    }
		}
		
		
// 		D ( 'Common/Custom' )->replyText ( $uid, $content );
		return $data;
	}
	function lists() {
		$model = $this->getModel ( 'help_open' );
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
		
		$this->display ();
	}
	function store_list() {
		$id = $param ['id'] = I ( 'id', 0, 'intval' );
		
		$info = D ( 'HelpOpen' )->getInfo ( $id );
		
		$shop_ids = wp_explode ( $info ['shop_ids'] );
		if (! empty ( $shop_ids )) {
			$map_shop ['id'] = array (
					'in',
					$shop_ids 
			);
			$shop_list = M ( 'coupon_shop' )->where ( $map_shop )->select ();
			$this->assign ( 'shop_list', $shop_list );
		}
		$this->display ();
	}
	function content() {
		$id = $param ['id'] = I ( 'id', 0, 'intval' );
		
		$info = D ( 'HelpOpen' )->getInfo ( $id );
		
		$this->assign ( 'info', $info );
		$this->display ();
	}
	
	// 我的奖品
	function my_prize() {
		$map ['help_id'] = I ( 'help_id' );
		$this->assign ( 'help_id', $map ['help_id'] );
		$info = D ( 'HelpOpen' )->getInfo ( $map ['help_id'] );
		$this->assign ( 'info', $info );
		
		$where = "sn_id>0 and ((invite_uid='{$this->mid}' and friend_uid=0) or friend_uid='{$this->mid}')";
		$list = D ( 'HelpOpenUser' )->where ( $map )->where ( $where )->select ();
		foreach ( $list as &$vo ) {
			$sn = D ( 'Common/SnCode' )->getInfoById ( $vo ['sn_id'] );
			$prize = D ( 'HelpOpenPrize' )->getInfo ( $sn ['prize_id'] );
			
			if ($prize ['prize_type'] == '1') {
				$info = M ( 'coupon' )->find ( $prize ['coupon_id'] );
				$vo ['url'] = addons_url ( 'Coupon://Wap/show', array (
						'id' => $prize ['coupon_id'],
						'sn_id' => $vo ['sn_id'] 
				) );
				$vo ['time'] = $this->_time ( $info ['use_start_time'], $info ['over_time'] );
				$vo ['is_use'] = $sn ['is_use'];
			} elseif ($prize ['prize_type'] == '2' && is_install("ShopCoupon")) {
				$info = M ( 'shop_coupon' )->find ( $prize ['shop_coupon_id'] );
				$vo ['url'] = addons_url ( 'ShopCoupon://Wap/show', array (
						'id' => $prize ['shop_coupon_id'],
						'sn_id' => $vo ['sn_id'] 
				) );
				$vo ['time'] = $this->_time ( $info ['start_time'], $info ['end_time'] );
				$vo ['is_use'] = $sn ['is_use'];
			} else {
				$vo ['url'] = addons_url ( 'Card://Wap/recharge' );
			}
			
			$vo ['prize'] = $prize;
			$vo ['sn'] = $sn;
			if ($vo ['friend_uid'] == 0) {
				$vo ['is_big'] = 1;
			} else {
				$vo ['is_big'] = 0;
			}
		}
		// dump ( $list );
		// exit ();
		$this->assign ( 'list', $list );
		
		// 获奖人数
		$where = "sn_id>0";
		$total = D ( 'HelpOpenUser' )->where ( $map )->where ( $where )->count ();
		$this->assign ( 'total', $total );
		
		$this->display ();
	}
	function _time($start_time = '', $end_time = '') {
		if (! empty ( $start_time ) && ! empty ( $end_time )) {
			return time_format ( $start_time ) . ' 至 ' . time_format ( $end_time );
		} elseif (! empty ( $start_time )) {
			return time_format ( $start_time ) . ' 开始';
		} elseif (! empty ( $end_time )) {
			return '至 ' . time_format ( $start_time );
		}
	}
	// 获奖名单
	function prize_log() {
		$page = I ( 'p', 1, 'intval' ); // 默认显示第一页数据
		                                
		// 搜索条件
		$map ['help_id'] = $hmap ['id'] = I ( 'help_id' );
		$where = 'sn_id>0';
		$info = D ( 'HelpOpen' )->getInfo ( $map ['help_id'] );
		$this->assign ( 'info', $info );
		// 读取模型数据列表
		$data = M ( 'help_open_user' )->field ( true )->where ( $map )->where ( $where )->order ( 'id desc' )->page ( $page, 20 )->select ();
		
		$sdao = M ( 'sn_code' );
		$pdao = D ( 'HelpOpenPrize' );
		foreach ( $data as &$vo ) {
			if ($vo ['friend_uid'] == 0) {
				$user = getUserInfo ( $vo ['invite_uid'] );
				$vo ['userface'] = $user ['headimgurl'];
				$vo ['nickname'] = $user ['nickname'];
				$vo ['type'] = '大礼包';
			} else {
				$user = getUserInfo ( $vo ['friend_uid'] );
				$vo ['userface'] = $user ['headimgurl'];
				$vo ['nickname'] = $user ['nickname'];
				$vo ['type'] = '小礼包';
			}
			$vo ['cTime'] = time_format ( $vo ['cTime'] );
			
			$smap ['id'] = $vo ['sn_id'];
			$pmap ['id'] = $sdao->where ( $smap )->getField ( 'prize_id' );
			$vo ['prize'] = $pdao->where ( $pmap )->getField ( 'name' );
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
		
		$this->display ();
	}
	function error_tips($info) {
		$msg = '';
		if ($info ['status'] == 0) {
			$msg = '活动已经禁用';
		}
		if ($info ['start_time'] > NOW_TIME) {
			$msg = '活动还没开始';
		}
		if ($info ['end_time'] < NOW_TIME) {
			$msg = '活动已经结束';
		}
		
		if (! empty ( $msg )) {
			$this->assign ( 'msg', $msg );
			$this->assign ( 'info', $info );
			$this->assign ( 'help_id', I ( 'id' ) );
			$this->display ( 'error_tips' );
			exit ();
		}
	}
}
