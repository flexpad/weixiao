<?php

namespace Addons\Card\Model;

use Think\Model;

/**
 * Card模型
 */
class CardModel extends Model {
	var $tableName = 'card_member';
	function addMoney($uid, $money, $log) {
		$map ['uid'] = $uid;
		$map ['token'] = get_token ();
		
		$member = M ( 'card_member' )->where ( $map )->find ();
		if ($member) {
		    $cardId=$member['id'];
			$recharge = $member ['recharge'] + $money;
			$res = M ( 'card_member' )->where ( $map )->setField ( 'recharge', $recharge );
		} else {
			$map ['recharge'] = $money;
			$cardId = $res = M ( 'card_member' )->add ( $map );
		}
		
		// 记录充值日志
		if ($res) {
			$log ['recharge'] = $money;
			$log ['uid'] = $uid;
			$log ['cTime'] = NOW_TIME;
			$log ['token'] = get_token ();
			$log ['member_id'] = $cardId;
			M ( 'recharge_log' )->add ( $log );
		}
		return $res;
	}
	
	//关注初始化会员 
	function init_card_member($openid='',$uid=0){
	    $token=get_token();
	    if ($openid){
	        $dataMap ['token'] = $token;
	        $dataMap ['openid'] = $openid;
	        $uid = M ( 'public_follow' )->where ( $dataMap )->getField ( 'uid' );
	    }
	    if ($uid <= 0){
	        $uid =get_uid_by_openid(true,$openid);
	    }
	    if ($uid <= 0){
	        return  0;
	    }
	    //判断是否已用会员卡号
	    $map['token']=$token;
	    $map['uid']=$uid;
	    $info=$this->where($map)->find();
	    $act='add';
	    $has_card=0;
	    if ($info){
	        if ($info['number']){
	            $has_card=1;
	        }else {
	            $has_card=0;
	            $act='save';
	        }
	    }else {
	        $has_card=0;
	        $act='add';
	        $data ['cTime'] = time ();
	        $data['uid']=$uid;
	        $data['token']=get_token();
	    }
	    if ($has_card == 0){
	        $config = getAddonConfig ( 'Card' );
	        $cardLength=intval($config['length']);
	        $map_token ['token'] = get_token ();
	        $map_token['number']=array('egt',$cardLength);
	        $data ['number'] = $this->where ( $map_token )->getField ( "max(number) as number" );
	        if (empty ( $data ['number'] )) {
	            $data ['number'] = $config ['length'];
	        } else {
	            $data ['number'] += 1;
	        }
	        $data['status']=2;
	        //对接erp
	        $managerId=session ( 'manager_id');
	        $userInfo=get_userinfo($managerId);
	        
	        if ($act == 'add'){
// 	            $res2=$this->addERPMember($data,$uid,$openid);
// 	            if ($res2 !=0){
	                $res = M ( 'card_member' )->add ( $data );
	                $this->_do_card_reward($uid);
// 	            }else{
// 	               $res=0;
// 	            }
	        }else {
// 	            $res1=$this->updateERPMember($info,$uid,$openid);
// 	            if ($res1 != 0){
	                M ( 'card_member' )->where ( $map )->save ( $data );
	                $res=$info['id'];
// 	            }else {
// 	                return $res1;
// 	            }
	        }
	    }
	    return $res;
	}
	
	function _do_card_reward($uid){
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
                    D('Addons://ShopCoupon/Coupon')->sendCoupon($event_info['coupon_id'], $uid);
                }
	        }
	    }
	     
	    // 增加积分
	    add_credit ( 'card_bind' );
	}
	//同步ERP会员信息 
	function updateERPMember($cardInfo,$uid=0,$openid='',$levelInfo){
		$msg='';
	    //对接erp
	    $managerId=session ( 'manager_id');
	    $userInfo=get_userinfo($managerId);
	    if ($userInfo['secretID'] && $userInfo['secretKey']){
	    	if ($uid==0){
	    		$uid=$cardInfo['uid'];
	    	}
	        if(empty($openid)){
	            $openid=getOpenidByUid($uid);
	        }
	        if (!$cardInfo['phone']){
	        	$resdata['res']=0;
	        	$resdata['msg']='手机号不能为空';
	        	return $resdata;
	        }
	        $member=D ( 'Common/Server' )->checkMemberByMobile ($cardInfo['phone']);
			$member2=D ( 'Common/Server' )->checkMemberByOpenID ($openid);
			if ($member['Rows'][0]['状态']==0 && $member2['Rows'][0]['状态']==0){
	        	return $this->addERPMember($cardInfo,$uid,$openid);
	        	
	        }else if($member['Rows'][0]['状态']==1 ||$member2['Rows'][0]['状态']!=0){
	        	$newCardNumber=trim($member['Rows'][0]['会员卡号']);
	        	if ($member['Rows'][0]['状态']!=0 && $newCardNumber != $cardInfo['number'] && !empty($newCardNumber)){
	        		$resdata['res']=0;
	        		$resdata['msg']='保存失败！该手机号已是其他用户的手机号！';
	        		return $resdata;
	        	}
	        	// 通过手机号同步OPENID
				$bindstatus=D ( 'Common/Server' )->updateOpenIDByMobile($cardInfo['phone'], $openid);
				if(strstr($bindstatus['Rows'][0]['状态'],'绑定成功')){
					$param ['OpenID'] = $openid;
					$param ['ShopCode'] = $cardInfo['shop_code']; // 898554
					$param ['MemberID'] = $cardInfo ['number']; // 100856
					$param ['Mobile'] = $cardInfo['phone']; // 13510455105
					$level=$levelInfo;
					$param ['CustType'] = $level['level']; // 1
					$param ['CustomerName'] =empty($cardInfo['username'])? get_username($uid):$cardInfo['username']; // 韦小宝
					$param ['Gender'] = $cardInfo['sex']== 1?'男':'女'; // 男
					$param ['Birthday'] = time_format($cardInfo['birthday'],'Y-m-d'); // 2015-10-15
					$param ['MarryDay'] =''; // 2015-09-15
					$res1=D ( 'Common/Server' )->updateMemberInfoByOpenID ($openid, $param);
					//dump($res1);
					//exit;
					if(strstr($res1['Rows'][0]['状态'],'保存失败')){
						//保存失败
						$msg=$res1['Rows'][0]['状态'];
						$res =0;
					}else{
						//初始化积分
						 $userInfos=D ( 'Common/User' )->getUserInfo ( $uid );
	               		 $user_score=$userInfos['score'];

// 						$user_score=get_userinfo($uid,'score');
						if ($user_score > 0){
							$this->synchro_score($openid, $user_score);
						}
						$res=1;
					}
				}else{
					//保存失败
					$msg=$bindstatus['Rows'][0]['状态'];
					$res =0;
				}
	        }else{
	        	$msg='ERP端存在多个该手机号，请到店里转人工服务处理！';
	        	$res=-1;
	        }
	    }else{
	        $res = 2;
	    }
	    $resdata['res']=$res;
	    $resdata['msg']=$msg;
	    return $resdata;
	}
	function addERPMember($cardInfo,$uid=0,$openid=''){
		$msg='';
	    //对接erp
	    $managerId=session ( 'manager_id');
	    $userInfo=get_userinfo($managerId);
	    if ($userInfo['secretID'] && $userInfo['secretKey']){
	        if ($uid==0){
	            $uid=$cardInfo['uid'];
	        }
	        if(empty($openid)){
	            $openid=getOpenidByUid($uid);
	        }
	        if (!$cardInfo['phone']){
	        	$resdata['res']=0;
	        	$resdata['msg']='手机号不能为空';
	        	return $resdata;
	        }
	        $member=D ( 'Common/Server' )->checkMemberByMobile ($cardInfo['phone']);
	        $member2=D ( 'Common/Server' )->checkMemberByOpenID ($openid);
	        if ($member['Rows'][0]['状态']==0 && $member2['Rows'][0]['状态']==0){
	            //不存在,新增会员
	            $param ['OpenID'] = $openid;
	            $param ['ShopCode'] = $cardInfo['shop_code']; // 898554
	            $param ['MemberID'] = $cardInfo ['number']; // 100856
	            $param ['Mobile'] = $cardInfo['phone']; // 13510455105
	            $level=D('Addons://Card/CardLevel')->getCardMemberLevel($uid);
	            $param ['CustType'] = $level['level']; // 1
	            $param ['CustomerName'] = empty($cardInfo['username'])? get_username($uid):$cardInfo['username']; // 韦小宝
	            $param ['Gender'] = $cardInfo['sex']== 1?'男':'女'; // 男
	            $param ['Birthday'] = time_format($cardInfo['birthday'],'Y-m-d'); // 2015-10-15
	            $param ['MarryDay'] =''; // 2015-09-15
	            $res1=D ( 'Common/Server' )->InsertMemberInfo ($openid, $param);
	            if(strstr($res1['Rows'][0]['状态'],'保存失败')){
	                //保存失败
	            	$msg=$res1['Rows'][0]['状态'];
	            	$res=0;
	            }else{
	                //初始化积分
	                $userInfos=D ( 'Common/User' )->getUserInfo ( $uid );
	                $user_score=$userInfos['score'];
// 					$user_score=get_userinfo($uid,'score');
	                if ($user_score > 0){
	                    $this->synchro_score($openid, $user_score);
	                }
	                $res=1;
	            }
	        }else if($member['Rows'][0]['状态']==1 || $member2['Rows'][0]['状态'] != 0){
	           return  $this->updateERPMember($cardInfo,$uid,$openid);
	        }else {
	        	$msg='ERP端存在多个该手机号，请到店里转人工服务处理！';
	        	$res=-1;
	        }
	    }else{
	       $res=2;
	    }
	    $resdata['res']=$res;
	    $resdata['msg']=$msg;
	    return $resdata;
	}
	//同步积分
	function synchro_score($openid,$total_score){
	    $mark=D ( 'Common/Server' )->getMemberMarksByOpenID ($openid);
	    $markData=$mark['Rows'][0];
	    if ($markData['状态'] == 1){
	        $erp_score=$markData['可用积分'];
	        if ($total_score > $erp_score){
	            $sec=$total_score-$erp_score;
	            //将不同的积分的同步到ERP
	            D ( 'Common/Server' )->AddMemberMarksByOpenID($openid,'微信积分',$sec,'微信初始积分');
	        }
	    }else if ($markData['状态'] == 0){
	        //添加积分
	        D ( 'Common/Server' )->AddMemberMarksByOpenID($openid,'微信积分',$total_score,'微信初始积分');
	    }
	}
}
