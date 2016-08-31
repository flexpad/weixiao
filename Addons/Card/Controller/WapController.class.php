<?php

namespace Addons\Card\Controller;

use Addons\Card\Controller\BaseController;

class WapController extends BaseController {

	function index() {
		$map ['uid'] = $this->mid;
		$map['token']=get_token();
		$info = M ( 'card_member' )->where ( $map )->find ();
// 		$shopInfo=D('Addons://Shop/Shop')->getInfo(0,true);
// 		$gpsArr=wp_explode($shopInfo['gps']);
//  		$shopInfo['gps']=$gpsArr[1].','.$gpsArr['0'];
		// dump(M ( 'card_member' )->getLastSql());
		$map ['token'] = get_token ();
		$map ['uid'] = $this->mid;
		$has_subscribe = intval ( M ( 'public_follow' )->where ( $map )->getField ( 'has_subscribe' ) );
		$this->assign ( 'has_subscribe', $has_subscribe );
		if ($has_subscribe == 0) { // 获取需要关注的公众号二维码
 			$res = D ( 'Home/QrCode' )->add_qr_code ( 'QR_SCENE', 'Card', $map['uid'], $map['uid'] );
 			$this->assign ( 'qrcode', $res );
		}
		if ($info['number'] && $info['phone']) {
			//已领取
			$tpl = 'card_center';
			$this->assign ( 'info', $info );
			//会员消费
			$model=$this->getModel('buy_log');
	     	$map['manager_id']= session ( 'manager_id');
			$map2['token']=$map['token']=get_token();
			
			$data['member_id']= $this->mid;
			if (empty($data['member_id'])){
				$map2['token']=get_token();
				$allNumber=M('card_member')->where($map2)->getFields('id,number');
				$this->assign('all_number',$allNumber);
			}
			if (is_install('ShopCoupon')){
			    $branch=M('coupon_shop')->where($map)->getFields('id,name');
			    $cardMember=M('card_member')->find($data['member_id']);
			    $map2['uid']=$this->mid;
			    $map2['addon']='ShopCoupon';
			    $map2['can_use']=1;
			    $snCode=M('sn_code')->where($map2)->getFields('id,sn,target_id,prize_title');
			    if ($snCode){
			        foreach ($snCode as $s){
			            $conponArr[$s['target_id']]=$s['target_id'];
			        }
			        $map3['id']=array('in',$conponArr);
			        $conpons=M('shop_coupon')->where($map3)->getFields('id,title,member');
			        foreach ($snCode as &$v){
			    
			            $memberArr=explode(',', $conpons[$v['target_id']]['member']);
			            if (in_array(0, $memberArr)|| in_array(-1, $memberArr) || in_array($cardMember['lev'], $memberArr)){
			                $v['target_name']=$conpons[$v['target_id']]['title'];
			                $codeArr['coupon_title']=$conpons[$v['target_id']]['title'];
			                $couponData[$v['id']]=$v;
			            }
			        }
			        $this->assign('coupon',$couponData);
			    }
			    $this->assign('shops',$branch);
			}
		}
		else if($info['number'] && !$info['phone'] && $info['status']==2){
		    //status: 2 体验卡， 1：正常， 0：冻结
		    redirect (U('get_success',$this->get_param));
		}
		else{
			//未领取
			$tpl = 'unget';
		}
// 		$this->assign('shop',$shopInfo);
		//dump($shopInfo);
		$this->display ($tpl);
	}
	function do_buy(){
	       $model = $this->getModel ( 'buy_log' );
			$config=get_addon_config('Card');
			if ($config['managerPassword']!=$_POST['pay_password']){
				$this->error('消费密码不正确');
			}
			if ($_POST['coupon_id']){
			    $_POST['sn_id']=$_POST['coupon_id'];
			   $code= M('sn_code')->find($_POST['coupon_id']);
			   $_POST['pay']=$_POST['pay'] - $code['prize_title'];
			}
			$Model = D ( parse_name ( get_table_name ( $model ['id'] ), 1 ) );
			// 获取模型的字段信息
			$Model = $this->checkAttr ( $Model, $model ['id'] );
			if ($Model->create () && $Model->add ()) {
			    if ($_POST['member_id']){
			        $map1['id']=$_POST['member_id'];
			        $info = M ( 'card_member' )->find($map1['id']);
			    }else{
			        $map ['uid'] = $this->mid;
			        $map['token']=get_token();
			        $info = M ( 'card_member' )->where($map)->find();
			        $map1['id']=$info['id'];
			    }
				if ($_POST['pay_type']==1){
					if ($info['recharge']<$_POST['pay']){
					    $this->error ('余额不足，你的余额为：'.$info['recharge']);
					}else {
					    $res =M('card_member')->where($map1)->setDec('recharge',$_POST['pay']);
					}
				}
				if ($_POST['sn_id']){
					D ( 'Common/SnCode' )->set_use ( $_POST['coupon_id'] );
				}
				if (is_install("ShopReward")) {
                    $this->_send_reward($_POST['member_id'], 'shop_reward_condition', 'shop_reward', $_POST['pay']);
                }
				$this->success ( '消费成功' );
				redirect (U('index',$this->get_param));
			} else {
				$this->error ('消费失败' );
			}
	}
	// 活动赠送
	function _send_reward( $member_id, $table, $credit_type,$recharge) {
	    if (!is_install("ShopReward")) {
	        return false;
	    }
	    $map ['start_time'] = array (
	        'lt',
	        NOW_TIME
	    );
	    $map ['end_time'] = array (
	        'gt',
	        NOW_TIME
	    );
	    $event_info = M ( 'shop_reward' )->where ( $map )->order ( 'id desc' )->find ();
	    $event_id = $event_info ['id'];
	    $data ['event_title'] = $event_info ['title'];
	    
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
	
	function get_success(){
		$map['start_time'] = array('elt',time()); 
		$map['end_time'] = array('egt',time());
		$map['token'] = get_token();
		$map['is_show']=1;
		$info = M('card_reward')->where($map)->find();
		$this -> assign('info',$info);
		$map1['uid']=$this->mid;
		$map1['token']=get_token();
		$cardInfo=M('card_member')->where($map1)->find();
		$save['status']=I('status');
		if (empty($cardInfo) && $save['status'] == 2){
		    //获取体验卡
		    $uid=$this->mid;
		    $cardid=D('Addons://Card/Card')->init_card_member('',$uid);
// 		    if ($cardid ==0){
// 		        $this->error('ERP同步保存错误！');
//                $this->assign('is_error','ERP同步保存错误！');
// 		    }
		    $cardInfo=M('card_member')->find($cardid);
		}
		
		if (empty($cardInfo)){
		    $this->error('领取失败');
		    $this->assign('is_error','领取失败');
		}
		$this->assign('card_info',$cardInfo);
		$this -> display();
	}
	function me(){
		$map ['uid'] = $this->mid;
		$userInfo = getUserInfo($this->mid);
		$info = M ( 'card_member' )->where ( $map )->find ();
		$levelInfo = D('CardLevel')->getCardMemberLevel($this->mid);
		// dump(M ( 'card_member' )->getLastSql());
		if ($info) {
			//已领取
			$this->assign ( 'info', $info );
			$this->assign ( 'userInfo', $userInfo );
			$this->assign('levelInfo',$levelInfo);
				// 获取优惠券数量
			$CouponCount = D ( 'Common/SnCode' )->getUserCount ( $this->mid );
			$this->assign ( 'shop_coupon_count', intval ( $CouponCount ['ShopCoupon'] ['left_count'] ) );
			$this->assign ( 'coupon_count', intval ( $CouponCount ['Coupon'] ['left_count'] ) );
		}else{
			redirect (U('index',$this->get_param));
			return;
		}
		$this->display ();
	}
	
	// 使用介绍
	function introduction() {
		$this->display ();
	}
	// 适用门店
	function storeList() {
	    $is_list=I('is_list',0,'intval');
	    
	    if ($is_list !=0){
// 	        vendor ( 'jsApi.jssdk' );
// 	        $info = get_token_appinfo ();
// 	        $jssdk = new \JSSDK ( $info ['appid'], $info ['secret'] );
// 	        $jsapiParams = $jssdk->GetjsapiParams ();
// 	        $this->assign ( 'jsapiParams', $jsapiParams );
// 	        $this->assign('coupon_shop',$couponShop);2141018
// 	    }else{
	        $latitude=I('latitude');//位置114.057045,22.6302
	        $longitude=I('longitude');
	        $map['token']=get_token();
	        $couponShop=M('coupon_shop')->where($map)->getFields('id,name,address,gps,phone,img');
	        foreach ($couponShop as &$v){
	            if ($v['gps']){
	                $gpsArr=wp_explode($v['gps']);
	                $v['gps']=$gpsArr[1].','.$gpsArr['0'];
	                $location[$v['id']]=$v;
	                $weidu=$gpsArr[1];
	                $jingdu=$gpsArr[0];
	                $juli[$v['id']]=$this->getDistance($latitude, $longitude, $weidu, $jingdu);
	            }else{
	                $endata[]=$v;
	            }
	        }
	        asort($juli);
	        foreach ($juli as $k =>$vo){
	            $dd=$location[$k];
	            $gl=$vo/1000;
	            $dd['juli']=round($gl,2);
	            if ($dd['juli']== 0){
	                $dd['juli']=$vo .' 米';
	            }else{
	                $dd['juli'].=' 公里';
	            }
	           $data[]=$dd;
	        }
	        if (empty($data) && $endata){
	        	$data = $endata;
	        }else if ($endata){
	           $data=array_merge($data,$endata);
	       }
	        $this->assign('coupon_shop',$data);
	    }
	    $this->assign('is_list',$is_list);
		$this->display ();
	}
	/**
	 * @desc 根据两点间的经纬度计算距离
	 * @param float $lat 纬度值
	 * @param float $lng 经度值
	 */
	function getDistance($lat1, $lng1, $lat2, $lng2)
	{
	    $earthRadius = 6367000; //地球半径
	    $lat1 = ($lat1 * pi() ) / 180;
	    $lng1 = ($lng1 * pi() ) / 180;
	    $lat2 = ($lat2 * pi() ) / 180;
	    $lng2 = ($lng2 * pi() ) / 180;
	    $calcLongitude = $lng2 - $lng1;
	    $calcLatitude = $lat2 - $lat1;
	    $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
	    $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
	    $calculatedDistance = $earthRadius * $stepTwo;
	
	    return round($calculatedDistance);
	}
	
	
	
	// 填写会员卡资料
	function writeCardInfo() {
	    $map['token']=get_token();
		$uid=$map ['uid'] = $this->mid;
		$info = M ( 'card_member' )->where ( $map )->find ();
		$config=get_addon_config('Card');
		$this->assign('need_verify',$config['need_verify']);
		
		$isEdit=I('isEdit');
		$this->assign('isEdit',$isEdit);
		if (IS_POST) {
			
		    $data ['status']=1;
			$data ['username'] = I ( 'post.username' );
			$data ['phone'] = I ( 'post.phone' );
			$data['birthday']=strtotime(I('post.birthday'));
			$data['address']=I('post.address');
			$data['sex']=I('sex');
			$userSave['truename']=$data['username'];
			$userSave['mobile']=$data['phone'];
			$verifyCode = I('post.verifyCode');
			
			if($verifyCode && $verifyCode!=""){
				$verifyRes = D('Addons://Sms/Sms')->checkSms($data['phone'],$verifyCode);
				if($verifyRes['result']!=1){
					$this -> error($verifyRes['msg']);
				} 
			}
			if (intval($info['level'])==0){
			    $data['level']= D('Addons://Card/CardLevel')->get_first_level();
			}
			if ($info) {
			    if ($info['number']){
			        $cardInfo=$data;
			        $cardInfo['number']=$info['number'];
			        $erpData=D('Addons://Card/Card')->updateERPMember($cardInfo,$map ['uid']);
			        $is_erp=$erpData['res'];
			        if ($is_erp !=0 && $is_erp != -1){
			            $res = M ( 'card_member' )->where ( $map )->save ( $data );
			        }else {
			        	if ($erpData['msg']){
			        		$this->error($erpData['msg']) ;
			        	}else{
			        		$this->error('ERP同步失败') ;
			        	}
			        	
			        }
			    }else {
			        $config = getAddonConfig ( 'Card' );
			        $cardLength=intval($config['length']);
			        $map_token ['token'] = get_token ();
			        $map_token['number']=array('egt',$cardLength);
			        $data ['number'] = M ( 'card_member' )->where ( $map_token )->getField ( "max(number) as number" );
			        if (empty ( $data ['number'] )) {
			            $data ['number'] = $config ['length'];
			        } else {
			            $data ['number'] += 1;
			        }
			        $data ['cTime'] = time ();
			        $cardInfo=$data;
			        $erpData=D('Addons://Card/Card')->updateERPMember($cardInfo,$map ['uid']);
			        $is_erp=$erpData['res'];
			        if ($is_erp !=0 && $is_erp != -1){
			            $res = M ( 'card_member' )->where ( $map )->save ( $data );
			            $this->_do_card_reward();
			        }else {
			        	
			        	if ($erpData['msg']){
			        		$this->error($erpData['msg']) ;
			        	}else{
			        		$this->error('ERP同步失败') ;
			        	}
			        	
			        }
			    }
				
			} else {
				$config = getAddonConfig ( 'Card' );
				$cardLength=intval($config['length']);
				$map_token ['token'] = get_token ();
				$map_token['number']=array('egt',$cardLength);
				$data ['number'] = M ( 'card_member' )->where ( $map_token )->getField ( "max(number) as number" );
				if (empty ( $data ['number'] )) {
					$data ['number'] = $config ['length'];
				} else {
					$data ['number'] += 1;
				}
				
				$data ['uid'] = $map2 ['id'] = $this->mid;
				$data ['cTime'] = time ();
				$data ['token'] = get_token ();
				//对接erp
				$erpData=D('Addons://Card/Card')->addERPMember($data,$data['uid']);
				$is_erp = $erpData['res'];
				if ($is_erp !=0 && $is_erp != -1){
				    $res = M ( 'card_member' )->add ( $data );
				    $userSave['status']=3;
				    $this->_do_card_reward();
				}else {
			        	if ($erpData['msg']){
			        		$this->error($erpData['msg']) ;
			        	}else{
			        		$this->error('ERP同步失败') ;
			        	}
			        	
			    }
// 				$managerId=session ( 'manager_id');
// 				$userInfo=get_userinfo($managerId);
// 				if ($userInfo['secretID'] && $userInfo['secretKey']){
// 				    if(empty($openid)){
// 				        $openid=getOpenidByUid($uid);
// 				    }
// 				    $param ['OpenID'] = $openid;
// 				    $param ['ShopCode'] = ''; // 898554
// 				    $param ['MemberID'] = $data ['number']; // 100856
// 				    $param ['Mobile'] = $data ['phone']; // 13510455105
// 				    $levelname=M('card_level')->where(array('id'=>$data['level']))->getField('level');
// 				    $param ['CustType'] = $levelname; // 1
// 				    $param ['CustomerName'] = $data['truename']; // 韦小宝
// 				    $param ['Gender'] = $data['sex']==1?'男':'女'; // 男
// 				    $param ['Birthday'] = time_format($data['birthday'],'Y-m-d'); // 2015-10-15
// 				    $param ['MarryDay'] =''; // 2015-09-15
// 				    $res1=D ( 'Common/Server' )->InsertMemberInfo ($openid, $param);
// 				    if(strstr($res1['Rows'][0]['状态'],'保存失败')){
// 				        //保存失败
// 				        $this->error('ERP 同步保存失败！') ;
// 				    }else{
// 				        $res = M ( 'card_member' )->add ( $data );
// 				        $userSave['status']=3;
// 				        $this->_do_card_reward();
// 				    }
// 				}else {
// 				    $res = M ( 'card_member' )->add ( $data );
// 				    $userSave['status']=3;
// 				    $this->_do_card_reward();
// 				}
				
			}
			D ( 'Common/User' )->updateInfo($this->mid,$userSave);
			redirect ( addons_url ( 'Card://Wap/index' ,$this->get_param) );
		}
		$this->assign ( 'info', $info );
		$this->display ( 'write_cardinfo' );
	}
	//发送短信验证
	function send_sms_code(){
		$phone = I('get.phone');
		$res = D('Addons://Sms/Sms')->sendSms($phone,'card');
		$this -> ajaxReturn($res,'JSON');
	}
	
	function _do_card_reward(){
	    // 开卡即送活动
	    $map ['start_time'] = array (
	        'lt',
	        NOW_TIME
	    );
	    $map ['end_time'] = array (
	        'gt',
	        NOW_TIME
	    );
	    $map['token']=get_token();
	    $event_info = M ( 'card_reward' )->where ( $map )->order ( 'id desc' )->find ();
	    if ($event_info) {
	        if ($event_info ['type'] == 0) { // 送积分
	        	$credit ['title'] = empty($event_info['title'])?'开卡即送':$event_info['title'];
	            $credit ['score'] = intval ( $event_info ['score'] );
	            add_credit ( 'card_reward', 0, $credit );
	        } else { // 送代金券
	            if (is_install("ShopCoupon")) {
                    D('Addons://ShopCoupon/Coupon')->sendCoupon($event_info['coupon_id'], $this->mid);
                }
	        }
	    }
	    
	    // 增加积分
	    add_credit ( 'card_bind' );
	}
	// 绑定实体会员卡
	function bindCard() {
		$config=get_addon_config('Card');
		$this->assign('need_verify',$config['need_verify']);
		$this->display ( 'bind_card' );
	}
	function do_bind_card() {
	    if (!is_install("Shop")){
	        $returnData['status']=0;
	        $returnData['msg']='绑定失败 ！';
	        $this->ajaxReturn($returnData);
	        exit();
	    }
	    $phone=I('phone');
	    $cardNumber=I('card_number');
	    if ($cardNumber){
// 	        $map['card_number']=$cardNumber;
	    }
	    $map['phone']=$phone;
	    $map['token']=get_token();
	    $map ['is_get'] =1;
	    $shopMember=M('shop_card_member')->where($map)->order('id desc')->find();
// 	    $sql= M ()->getLastSql () ;    
	    if ($shopMember){
	        $data['phone']=$shopMember['phone'];
	        $data['cTime']=time();
	        $data['username']=$shopMember['username'];
	       
	        $data['birthday']=$shopMember['birthday'];
	        $data['sex']=$shopMember['sex']=='男'?1:2;
	        $data['address']=$shopMember['address'];
	        $data['shop_code']=$shopMember['shop_code'];
	        $data['status'] =1;
	        $data['is_bind']=1;
	        $config = getAddonConfig ( 'Card' );
	        $cardLength=intval($config['length']);
	        $map_token ['token'] = get_token ();
	        $map_token['number']=array('egt',$cardLength);
	        
	        
	        $map2['token']=get_token();
	        $map2 ['uid'] = $this->mid;
	        $info = M ( 'card_member' )->where ( $map2 )->find ();
	        if ($shopMember['card_number']){
	        	$data['number']=$shopMember['card_number'];
	        }else if(!empty($cardNumber)){
	        	$data['number']=$cardNumber;
	        }else if($info['number']){
	        	$data['number']=$info['number'];
	        }else{
	        	$data['number']= M ( 'card_member' )->where ( $map_token )->getField ( "max(number) as number" );
	        	if (empty ( $data ['number'] )) {
	        		$data ['number'] = $config ['length'];
	        	} else {
	        		$data ['number'] += 1;
	        	}
	        }
				// 增加积分
			if ($info) {
				$erpData = D ( 'Addons://Card/Card' )->updateERPMember ( $data, $map2 ['uid'] );
				$res1 = $erpData['res'];
				if ($res1 != 0 && $res1 != - 1) {
					$res = M ( 'card_member' )->where ( $map2 )->save ( $data );
				}else{
					$msg= $erpData['msg'];
					$res = 0;
				} 
				
			}else {
			    $erpData = D ( 'Addons://Card/Card' )->addERPMember ( $data, $map2 ['uid'] );
			    $res1 = $erpData['res'];
				if ($res1 != 0 && $res1 != - 1) {
					$data ['uid'] = $this->mid;
					$data ['token'] = get_token ();
					$res = M ( 'card_member' )->add ( $data );
				} else{
					$msg= $erpData['msg'];
					$res = 0;
				} 
			    // 开卡即送活动
			    $map1 ['start_time'] = array (
			        'lt',
			        NOW_TIME
			    );
			    $map1 ['end_time'] = array (
			        'gt',
			        NOW_TIME
			    );
			    $map1['token']=get_token();
			    $event_info = M ( 'card_reward' )->where ( $map1 )->order ( 'id desc' )->find ();
			    if ($event_info) {
			        if ($event_info ['type'] == 0) { // 送积分
			        	$credit ['title'] = empty($event_info['title'])?'开卡即送':$event_info['title'];
			            $credit ['score'] = intval ( $event_info ['score'] );
			            add_credit ( 'card_reward', 0, $credit );
			        } else { // 送代金券
			            if (is_install("ShopCoupon")) {
                            D('Addons://ShopCoupon/Coupon')->sendCoupon($event_info['coupon_id'], $this->mid);
                        }
			        }
			    }
			    add_credit ( 'card_bind' );
			    	
			    $userSave['truename']=$data['username'];
			    $userSave['mobile']=$data['phone'];
			    $userSave['status']=3;
			    D ( 'Common/User' )->updateInfo($this->mid,$userSave);
			}
			
			if ($res){
			    $returnData['status']=1;
			    $returnData['msg']='绑定成功！';
			}else{
			    $returnData['status']=0;
			    if ($msg){
			        $returnData['msg']=$msg;
			    }else{
			        $returnData['msg']='绑定失败 ！';
			    }
			}
			
	    }else {
	        $returnData['status']=0;
	        $returnData['msg']='该手机号不存在 ！';
//             $returnData['msg']='绑定失败';
	    }
	    if ($returnData['status'] == 1 && $shopMember['score']){
	    	$credit['title']='绑定实体店会员卡';
	    	$credit['score']=intval($shopMember['score']);
	    	$credit['uid']=$this->mid;
	    	add_credit('shop_card_member',0,$credit);
	    	
	    }
	    $this->ajaxReturn($returnData);
	}
		// 积分
	function score() {
		$userInfo = get_userinfo ( $this->mid );
		$this->assign ( 'user', $userInfo );
		
		$year = I ( 'get.year' );
		$month = I ( 'get.month' );
		$year = $year ? $year : time_format ( NOW_TIME, 'Y' );
		$month = $month ? $month : time_format ( NOW_TIME, 'm' );
		$map1 ['uid'] = $this->mid;
		// $is_ajax=I('is_ajax');
		$map1 ['token'] = $map2 ['token'] = get_token ();
		$month = intval ( $month );
		// $day=$this->getDays($year, $month);
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
		$map1['score']=array('neq',0);
		$isERP=false;//is_ERP();
		if ($isERP){
			$openid=get_openid();
			$detail=D ( 'Common/Server' )->getMarksUsageDetail ( $openid );
			foreach ($detail['Rows'] as $vvv){
				$strtime=$vvv['日期'];
				$timedate=strtotime($strtime);
				$dyear=intval(time_format($timedate,'Y'));
				$dmonth=intval(time_format($timedate,'m'));
				if ($dyear == $year && $dmonth == $month){
					$dd['credit_name']=$vvv['备注']?$vvv['备注']:$vvv['礼品描述'];
					$dd['allday']=$vvv['日期'];
					$dd['score']=$vvv['积分'];
					$get_data[]=$dd;
				}
			}
		}else {
			$get_data = M ( 'credit_data' )->where ( $map1 )->field ( "from_unixtime(cTime,'%Y-%m-%d') allday,credit_name,score,credit_title" )->order ( 'id desc' )->select ();
			$creditTitle = M ( 'credit_config' )->getFields ( 'name,title' );
			foreach ( $get_data as &$vo ) {
				if ($vo ['credit_name'] == 'card_member_update_score') {
					if ($vo ['score'] >= 0) {
						$vo ['credit_name'] = '手动添加';
					} else {
						$vo ['credit_name'] = '手动扣除';
					}
				}else if( !$creditTitle [$vo ['credit_name']]) {
					$vo['credit_name']=$vo['credit_title'];
				} else {
					$vo ['credit_name'] = $vo ['credit_name'] == 'auto_add' ? $vo ['credit_title'] : $creditTitle [$vo ['credit_name']];
				}
			}
		}
		
		$this->assign ( 'score_data', $get_data );
		$this->assign ( 'year', $year );
		$this->assign ( 'month', $month );
		$this->display ();
	}
	// 兑换
	function exchange() {
		$userInfo = get_userinfo ( $this->mid );
		$this->assign ( 'user', $userInfo );
		
		$year = I ( 'get.year' );
		$month = I ( 'get.month' );
		$year = $year ? $year : time_format ( NOW_TIME, 'Y' );
		$month = $month ? $month : time_format ( NOW_TIME, 'm' );
		$map1 ['uid'] = $this->mid;
		// $is_ajax=I('is_ajax');
		$map1 ['token'] = $map2 ['token'] = get_token ();
		$month = intval ( $month );
		// $day=$this->getDays($year, $month);
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
		$map1 ['score'] = array (
				'lt',
				0 
		);
		$get_data = M ( 'credit_data' )->where ( $map1 )->field ( "from_unixtime(cTime,'%Y-%m-%d') allday,credit_name,score,credit_title" )->order ( 'id desc' )->select ();
		$creditTitle = M ( 'credit_config' )->getFields ( 'name,title' );
		foreach ( $get_data as &$vo ) {
			if ($vo ['credit_name'] == 'card_member_update_score') {
				if ($vo ['score'] >= 0) {
					$vo ['credit_name'] = '手动添加';
				} else {
					$vo ['credit_name'] = '手动扣除';
				}
			}else if( !$creditTitle [$vo ['credit_name']]) {
			        $vo['credit_name']=$vo['credit_title'];
			}else {
				$vo ['credit_name'] = $vo ['credit_name'] == 'auto_add' ? $vo ['credit_title'] : $creditTitle [$vo ['credit_name']];
			}
		}
		$this->assign ( 'score_data', $get_data );
		$this->assign ( 'year', $year );
		$this->assign ( 'month', $month );
		$this->display ();
	}
	// 会员特权
	function privilege() {
		$levelInfo = D('CardLevel')->getCardMemberLevel($this->mid);
		$map['token'] = get_token();
		$lists = M('card_privilege')->where($map)->select(); 
		foreach($lists as $v){
			
			$v['intro']=str_replace("\n","<br/>",$v['intro']);
			$gradeArr = explode(',',$v['grade']);
			
			if(in_array($levelInfo['id'],$gradeArr) || in_array(0,$gradeArr)||in_array(-1, $gradeArr)){
				$privilege[] = $v;
			}
		}

		$this -> assign('lists',$privilege);
		//dump($privilege);
		$this->display ();
	}
	//余额
	function recharge(){
	    $map['uid']=$this->mid;
	    $map3['token']= $map2['token']=$map1['token']=$map['token']=get_token();
	    $card_id=M('card_member')->where($map)->getField('id');
	    if ($card_id){
	        
	        $map3['member_id']=$map2['member_id']=$map1['member_id']=$card_id;
	        
	        $year=I('get.year');
	        $month=I('get.month');
	        $year= $year?$year:time_format(NOW_TIME,'Y');
	        $month=$month?$month:time_format(NOW_TIME,'m');
	        $start_date=$year.'-'.$month;
	        $end_month=$month+1;
	        $end_date=$year.'-'.$end_month;
	        $start_date=strtotime($start_date);
	        $end_date=strtotime($end_date);
	        
	        //总
	        $map1['recharge']=array('egt',0);
	        $rechargeTotal=M('recharge_log')->where($map1)->field("sum(recharge) total_recharge")->select();
	        
	        //充值时，负数为 手动扣除金额数 
	        $map2['recharge']=array('elt',0);
	        $delRecharge=M('recharge_log')->where($map2)->field("sum(recharge) total_recharge")->select();
	        
	        $buyTotal=M('buy_log')->where($map3)->field("sum(pay) total_recharge")->select();

	        $totalData['all_recharge']=$rechargeTotal[0]['total_recharge'];
	        $totalData['all_buy']=$buyTotal[0]['total_recharge']- $delRecharge[0]['total_recharge'];
	        
	        //充值
	        $map1['cTime']= $map3['cTime']=$map2['cTime']=array('between',array($start_date,$end_date));
	        $rechargeData=M('recharge_log')->where($map1)->field("from_unixtime(cTime,'%Y-%m-%d') allday,recharge,remark")->order('id desc')->select();

	        //扣除
	        $delRechargeData=M('recharge_log')->where($map2)->field("from_unixtime(cTime,'%Y-%m-%d') allday,recharge,remark")->select();
	        //消费
	        $buyData=M('buy_log')->where($map3)->field("from_unixtime(cTime,'%Y-%m-%d') allday,pay")->select();
	        foreach ($rechargeData as $r){
	            if (empty($r['remark'])){
	                $r['remark']='手动充值';
	            }
	            $data[]=$r;
	        }
	        foreach ($delRechargeData as $dr){
	            if (empty($dr['remark'])){
	               $dr['remark']='手动扣除';
	            }
	            $data[]=$dr;
	        }
	        foreach ($buyData as $b){
	            $b['remark']='消费扣除';
	            $b['pay']=0-$b['pay'];
	            $data[]=$b;
	        }
	    }
	    $this->assign('totalData',$totalData);
	    $this->assign('data',$data);
	    $this->assign('year',$year);
	    $this->assign('month',$month);
	    $this->display ();
	    
	}
	
	// 签到
	function signin() {
	    $user=get_userinfo($this->mid);
	   
	    $map['token']=get_token();
	    $map['uid']=$this->mid;
	    $sDay=M('signin_log')->where($map)->getFields('sTime');
	    $count=count($sDay);
	    //1:上个月，2：下个月
	    $next=I('next');
	    $month=I('month');
	    $year=I('year');
	    if (!$year){
	        $year=time_format(time(),'Y');
	    }
	    if (!$month){
	        $month=time_format(time(),'m');
	    }
	    if ($next ==1){
	        $month--;
	    }else if($next ==2){
	        $month++;
	    }
	    if ($month >12){
	        $month=1;
	        $year++;
	    }else if($month <1){
	        $month=12;
	        $year --;
	    }
	    //判断今天是否已签到
	    $now_time=strtotime(time_format(NOW_TIME,'Y-m-d'));
	    $map['sTime']=array('egt',$now_time);
	    $hasLog=M('signin_log')->where($map)->getField('id');
	    $mDay='[';
	    foreach ($sDay as $s){
	        $m = time_format($s,'Y-m');
	        if ($year.'-'.$month == $m){
	            $mDayArr[]=time_format($s,'d');
	        }
	    }
	    $mDay.=implode(',', $mDayArr);
	    $mDay.=']';
	    $this->assign('mDays',$mDay);
	    $this->assign('has_log',$hasLog);
// 	    if (!empty($hasLog)){
	        $config=get_addon_config('SingIn');
	        $remsg = $config['done'];
// 	    }
	    $this->assign('has_msg',$remsg);
	    $this->assign('year',$year);
	    $this->assign('month',$month);
	    $this->assign('user',$user);
	    $this->assign('day_count',$count);
	    $this->assign('sDay',$sDay);
		$this->display ();
	}
	function do_signin(){
	    $map1['token']=get_token();
	    $now_time=strtotime(time_format(NOW_TIME,'Y-m-d'));
	    $map1['sTime']=array('egt',$now_time);
	    
	    $uid=$this->mid;
	    $user=get_userinfo($uid);
	    $config=get_addon_config('SingIn');
	    
	    if ($config['random']==1){
	        $get_score=$config['score'];
	    }else {
	        $get_score=rand($config['score1'], $config['score2']);
	    }
	    //模板替换
	    $hour=intval($config['hour']);
	    $min=intval($config['minute']);
	    $is_can=0;
	    if ($hour || $min){
	        $start_day=time_format(time(),'Y-m-d');
	        $start_time_str=$start_day.' '.$hour.':'.$min;
	        $start_time=strtotime($start_time_str);
	        if (NOW_TIME < $start_time){
	            //未开始签到回复模板:
	            $searchArr['[开始时间]']=$start_time_str;
	            $searchArr['[当前时间]']=time_format(NOW_TIME);
	        }else {
	            $is_can=1;
	        }
	    }else {
	        $is_can=1;
	    }
	     
	    if ($is_can ==1){
	        $addData['score']=$get_score;
	        $addData['token']=get_token();
	        $addData['sTime']=NOW_TIME;
	        $addData['uid']=$uid;
	        $res=M('signin_log')->add($addData);
	        // 排行榜
	        if (stripos ( '..' . $config['reply'], '[排行榜]' ) >= 1) {
	            $w1 ['token'] = $map1['token'];
	            $w1 ['sTime'] = array (
	                'egt',
	                $this->getToday ()
	            );
	            $top5 = M ( 'signin_log' )->where ( $w1 )->order ( 'id ASC' )->limit ( 5 )->select ();
	            if ($top5) {
	                // 获取相关的用户信息
	                $uids = getSubByKey ( $top5, 'uid' );
	                $uids = array_filter ( $uids );
	                $uids = array_unique ( $uids );
	                if (! empty ( $uids )) {
	                    $w2 ['openid'] = array (
	                        'in',
	                        $uids
	                    );
	                    $w2 ['token'] = get_token ();
	                    $members = M ( 'public_follow' )->where ( $w2 )->field ( 'uid,openid' )->select ();
	                    foreach ( $members as $m ) {
	                        $user [$m ['openid']] = $m['uid'];
	                    }
	                    foreach ( $top5 as &$vo ) {
	                        $vo ['nickname'] = get_username($vo['uid']);
	                    }
	                }
	                // 组装排行榜
	                $top5_content = '<br/>';
	                $i = 1;
	                foreach ( $top5 as $vo1 ) {
	                    $top5_content .= sprintf ( "第%s名  %s  %s\n", $i, empty ( $vo1 ['nickname'] ) ? '匿名' : $vo1 ['nickname'], date ( 'H:i:s', $vo1 ['sTime'] ) ).'<br/>';
	                    $i ++;
	                }
	            }
	        }
	        $searchArr['[本次明豆]']=$get_score;
	        $searchArr['[明豆余额]']=$user['score'];
	        $searchArr['[签到时间]']=time_format(NOW_TIME);
	        //获取今天签到数
	        $allLog=M('signin_log')->where($map1)->count();
	        $searchArr['[排名]']=$allLog ;
	        $searchArr['[排行榜]']=$top5_content;
	        $msg=strtr($config['reply'],$searchArr);
	        
	       
	        $credit['score']=$get_score;
	        $credit['uid']=$uid;
	        add_credit('signin',5,$credit);
	        if ($res){
	            $returnData['status']=1;
	            $returnData['msg']=$msg;
	        }
	        //连续签到
	        if ($config['continue_day']==1){
	        	$credit['score']=$config['continue_score'];
	        	$credit['uid']=$uid;
	        	$credit['title'] = '连续签到'.$config['continue_day'].'天';
	        	add_credit('signin',0,$credit);
	        }else{
	        	$amap['token']=get_token();
	        	$amap['uid'] = $this->mid;
	        	$logdata = M('signin_log')->where($amap)->order('sTime desc')->limit($config['continue_day'])->getfields('sTime');
	        	$daycount = 0;
	        	$days = 3600 * 24;
	        	for ($i=0 ;$i<count($logdata);$i++){
	        	    if (empty($logdata[1+$i])){
	        	        break;
	        	    }
	        	    $qtime = strtotime(time_format($logdata[$i],'Y-m-d'));
				    $htime = strtotime(time_format($logdata[1+$i],'Y-m-d'));
					if ($qtime - $htime <= $days){
	        			$daycount++ ;
	        		}else{
	        			$daycount = 0;
	        		}
	        	}
	        	$daycount +=1;
	        	if ($daycount == $config['continue_day'] && $config['continue_day'] !=0){
	        		$credit['score']=$config['continue_score'];
	        		$credit['uid']=$uid;
	        		$credit['title'] = '连续签到'.$daycount.'天';
	        		add_credit('signin',0,$credit);
	        	}
	        }
	        
	    }else {
	        $msg=strtr($config['notstart'],$searchArr);
	        $returnData['status']=0;
	        $returnData['msg']=$msg;
	    }
	     $this->ajaxReturn($returnData);
	    
	}
	private function getToday() {
	    $date = date ( 'Y-m-d 00:00:00', time () );
	    $temp = explode ( " ", $date );
	    $temp1 = explode ( "-", $temp [0] );
	    $temp2 = explode ( ":", $temp [1] );
	    $today = mktime ( $temp2 [0], $temp2 [1], $temp2 [2], $temp1 [1], $temp1 [2], $temp1 [0] );
	
	    return $today;
	}
	//分享
	function share() {
	    $user=get_userinfo($this->mid);
	    $this->assign('user',$user);
		$this->display ();
	}
	function do_share(){
	    $data['uid']=$map['uid']=$this->mid;
	    $data['sTime']=time();
	    $data['token']=get_token();
	    $config=get_addon_config('SingIn');
	    $iscan=1;
	    if ($config['share_limit']){
	        $nowDay=strtotime(time_format(time(),'Y-m-d'));
	        $map['sTime']=array('egt',$nowDay);
	        $count=M('share_log')->where($map)->count();
	        if ($count>=$config['share_limit']){
	            $iscan=0;
	        }
	    }
	    $res=0;
	    if ($iscan){
	        $data['score']=$config['share_score'];
	        $credit['score']=$data['score'];
	        $credit['title']='分享';
	        $credit['uid']=$this->mid;
	        add_credit('share',5,$credit);
	        $res=M('share_log')->add($data);
	    }
	    echo $res;
	}
	
	
	//以下忽略
	/*
	// 返现
	function money() {
		$this->display ();
	}
	// 会员卡密码
	function password() {
		$this->display ();
	}
	//余额赠送
	function recharge_give() {
		$this->display ();
	}
	
	//优惠券
	function stamp() {
		$this->display ();
	}
	*/
	//积分兑换
	function score_exchange(){
	    $map1['token']=$map['token']=get_token();
	    $map1 ['uid'] =  $map ['uid'] =$this->mid;
	    $cardMember = M ( 'card_member' )->where ( $map )->find ();
// 	    $map['member']=array('in',array(0,-1,$cardMember['level']));
	    $dataList=M('card_score')->where($map)->order('id desc')->select();
	    foreach ($dataList as $vo){
	        $memberArr=explode(',', $vo['member']);
	        if (in_array(0, $memberArr)||in_array(-1, $memberArr)||in_array($cardMember['level'], $memberArr)){
	            $data[]=$vo;
	        }
	    }
	    foreach ($data as &$v){
	        $nocount=0;
	        $map1['card_score_id']=$v['id'];
	        $logs=M('score_exchange_log')->where($map1)->count();
	        if ($v['coupon_type']==0 ){
	            if (is_install("ShopCoupon")) {
                    $info = D('Addons://ShopCoupon/Coupon')->getInfo($v['coupon_id']);
                    $list = D('Common/SnCode')->getMyList($map['uid'], $v['coupon_id'], 'ShopCoupon');
                    $my_count = count($list);
                    if ($info['limit_num'] > 0 && $my_count >= $info['limit_num']) {
                        $nocount = 1;
                    }
                    $v['coupon'] = '代金券：' . $info['title'];
                }
// 	            $v['coupon']=M('shop_coupon')->find($v['coupon_id']);
	        }else {
	            $info=D ( 'Addons://Coupon/Coupon' )->getInfo ( $v['coupon_id'] );
	            $list = D ( 'Common/SnCode' )->getMyList ( $map['uid'], $v['coupon_id'], 'Coupon' );
	            $my_count = count ( $list );
	                if ($info ['max_num'] > 0 && $my_count >= $info ['max_num']) {
	                    $nocount=1;
	                }
	            
	            $v['coupon']='优惠券：'.$info['title'];
// 	            $v['coupon']=M('coupon')->find($v['coupon_id']);
	        }
	        if ($info ['collect_count'] >= $info ['num']) {
	            $nocount=1;
	        } else if (! empty ( $info ['start_time'] ) && $info ['start_time'] > NOW_TIME) {
	             $nocount=1;
	        } else if (! empty ( $info ['end_time'] ) && $info ['end_time'] < NOW_TIME) {
	             $nocount=1;
	        }
	      
	        
	        if ($logs >= $v['num_limit']){
	            $nocount=1;
	        }
	        $v['no_count']=$nocount;
	        
	    }
	    
	    $this->assign('score_exchange',$data);
		$this -> display();
	}
	function do_score_exchange(){
	    $id=I('get.id');
	    $card_score=M('card_score')->find($id);
	    $coupon_id=I('get.coupon_id');
	    if ($card_score['coupon_type']==0){
	        if (is_install("ShopCoupon")) {
                $res = D('Addons://ShopCoupon/Coupon')->sendCoupon($coupon_id, $this->mid);
            }
	    }else {
	       $res=D ( 'Addons://Coupon/Coupon' )->sendCoupon ( $coupon_id, $this->mid );
	    }
	    if ($res){
	        $user=get_userinfo($this->mid);
	        if ($user['score']<$card_score['score_limit']){
	            echo -1;
	            exit();
	        }
	        $credit['uid']=$this->mid;
	        $credit['score']=0-$card_score['score_limit'];
	        $credit['title']='积分兑换';
	        add_credit('card_score',5,$credit);
	         
	        $data['ctime']=time();
	        $data['token']=get_token();
	        $data['card_score_id']=$id;
	        $data['uid']=$this->mid;
	        $res=M('score_exchange_log')->add($data);
	        if ($res){
	            echo 1;
	        }else{
	            echo 0;
	        }
	    }else {
	        echo 0;
	    }
	   
	}
	//积分记录
	function sign_list(){
	    $map['uid']=$this->mid;
	    $map['token']=get_token();
	    $data=M('signin_log')->where($map)->getFields('id,score,sTime');
	    $this->assign('signin_log',$data);
		$this -> display();
	}
	//积分攻略
	function score_method(){
		$config=get_addon_config('SingIn');
		$this -> assign('config',$config);
		$this -> display();	
	}
	//每日推荐
	function day_recommend(){
		$this -> display();
	}
	//生日特权
	function custom_privilege(){
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
		$levelInfo = D('CardLevel')->getCardMemberLevel($this->mid);
// 		$map['member'] = array('in','0,-1,'.$levelInfo['level']);
		$map['member'] = array('in',array(0,-1,$levelInfo['level']));
		$row = empty ( $model ['list_row'] ) ? 20 : $model ['list_row'];
		
		// 读取模型数据列表
		$name = parse_name ( get_table_name ( $model ['id'] ), true );
		$data = M ( $name )->field ( true )->where ( $map )->order ( 'id desc' )->page ( $page, $row )->select ();
		foreach ($data as $ddd){
			$idsarr[$ddd['id']]=$ddd['id'];
		}
		$clog['uid']=$this->mid;
// 		$clog['uid']=11884;

		if ($idsarr){
			$clog['custom_id']=array('in',$idsarr);
			$customLog=M('card_custom_log')->where($clog)->getFields('custom_id,id,is_send');
		}
		
		foreach ( $data as &$vo ) {
			
			if ($vo ['is_birthday']) {
				$vo ['start_time'] = '会员生日';
				$vo ['end_time'] = '生日前' . $vo ['before_day'] . ' 天';
				time_format ( $vo ['end_time'] );
			} else {
				$vo ['start_time'] = time_format ( $vo ['start_time'] );
				$vo ['end_time'] = time_format ( $vo ['end_time'] );
			}
			if ($vo ['member'] == 0) {
				$vo ['member'] = '所有用户';
			} elseif ($vo ['member'] == '-1') {
				$vo ['member'] = '所有会员卡成员';
			} else {
				$level_map ['id'] = $vo ['member'];
				$vo ['member'] = M ( 'card_level' )->where ( $level_map )->getField ( 'level' );
			}
			
			if ($vo ['type'] == 0) {
				$vo ['type'] = '送 ' . $vo ['score'] . '积分';
			} else {
				$vo ['type'] = '送： ' . M ( 'shop_coupon' )->where ( "id='{$vo[coupon_id]}'" )->getField ( 'title' );
			}
			if ($customLog[$vo['id']]){
				$vo['custom_log']=$customLog[$vo['id']]['id'];
				$vo['is_send']=$customLog[$vo['id']]['is_send'];
			}else{
				$vo['custom_log']=0;
				$vo['is_send']=-1;
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
		//dump($list_data);
		
		$this -> display();
	}
	//等级介绍
	function level_intro(){
		$map['token'] = get_token();
		$data[]=array(
		    'id'=>0,
		    'level'=>'体验卡',
		    'score'=>'0',
		    'recharge'=>'0',
		    'discount'=>'0'
		);
		$list = M('card_level')->where($map)->select();
		foreach ($list as $v){
		    $data[]=$v;
		}
		$this -> assign('list',$data);
		$this -> display();
	}
	//积分规则
	function score_rule(){
	    $map['token']=get_token();
	    $creditTitle =  D ( 'Common/Credit' )->getCreditByName ();
	    $this -> assign('list',$creditTitle);
	    $this -> display();
	}
	function check_ERP_member(){
	    $mobile=I('phone');
	    $managerId=session ( 'manager_id');
	    $userInfo=get_userinfo($managerId);
	    if ($userInfo['secretID'] && $userInfo['secretKey']){
	        $res = D ( 'Common/Server' )->checkMemberByMobile ( $mobile );
	        $data['status']=$res['Rows'][0]['状态'];
	        $data['card_number']=trim($res['Rows'][0]['会员卡号']);
	    }else{
	        $data['status']=0;
	        $data['card_number']="";
	    }
	    $this->ajaxReturn($data);
	}
	//领取客户关怀奖品
	function get_privilege(){
		$id=I('id');
		$title=I('title');
		$sataus=0;
		$msg='';
		if (empty($id)){
			$msg='领取失败';
		}
		if (empty($msg)){
			$uid=$this->mid;
// 			$uid=11884;
			$logData=M('card_custom_log')->find($id);
			if ($logData['score']){
				$sataus=1;
				//送积分
				$credit ['title'] = '[客户关怀]'.$title;
				$credit ['score'] = intval ( $logData['score'] );
				add_credit ( 'card_custom', 0, $credit );
			}else if($logData['coupon_id']){
				$res=D('Addons://Coupon/Coupon')->sendCoupon($logData['coupon_id'],$uid);
				if (!$res){
					$msg='优惠券发放失败！';
				}else{
					$sataus=1;
				}
			}
		}
		if (empty($msg)){
			$save['is_send']=1;
			$rr=M('card_custom_log')->where(array('id'=>$id))->save($save);
			if (!$rr){
				$msg='领取失败!';
			}else{
				$sataus=1;
				$msg='领取成功！请到"我的"个人中心查询！';
			}
		}
		$returndata['status']=$sataus;
		$returndata['msg']=$msg;
		$this->ajaxReturn($returndata);
	}
	
}
