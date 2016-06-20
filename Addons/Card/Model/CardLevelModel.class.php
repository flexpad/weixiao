<?php

namespace Addons\Card\Model;
use Think\Model;

/**
 * Card模型
 */
class CardLevelModel extends Model{    
	function getCardMemberLevel($uid){
		$map1['token'] = get_token();
		$levelList = M('card_level')->where($map1)->select();
// 		$creditInfo = D('Common/Credit')->getAllCreditInfo($uid);
		$creditInfo=get_userinfo($uid);
		$map1['uid']=$uid;
		$cardMember = M('card_member')->where($map1)->find();
		if ($cardMember['level']==0){
		    $data=array(
		        'id'=>0,
		        'level'=>'体验卡',
		        'score'=>'0',
		        'recharge'=>'0',
		        'discount'=>'0'
		    );
		    return $data;
		}
		$cardLevel=M('card_level')->find($cardMember['level']);
		foreach($levelList as $vo){
		    if (empty($cardLevel) || $vo['score']>$cardLevel['score'] || $vo['recharge'] >$cardLevel['recharge']){
		        if ($vo['score'] && $vo['recharge']){
		            if($vo['score']<=$creditInfo ['score'] && $vo['recharge']<=$cardMember['recharge']){
		                $levelInfo = $vo;
		            }
		        }else if($vo['score']){
		            if($vo['score']<=$creditInfo ['score']){
		                $levelInfo = $vo;
		            }
		        }else if($vo['recharge']){
		            if( $vo['recharge']<=$cardMember['recharge']){
		                $levelInfo = $vo;
		            }
		        }
		    }
		}
		if ($levelInfo){
			$cardMember['level']=$levelInfo['id'];
		    $is_erp=D('Addons://Card/Card')->updateERPMember($cardMember,$uid,'',$levelInfo);
		    if ($is_erp != 0){
		        $save['level']=$levelInfo['id'];
		        $map['id']=$cardMember['id'];
		        M('card_member')->where($map)->save($save);
		    }
		    return $levelInfo;
		}else{
		    return $cardLevel;
		    
		}
	}
	
	//获取会员最初等级
	function get_first_level(){
	    $map['token']=get_token();
	    $info=$this->where($map)->order('score asc,recharge asc')->limit(1)->select();
	    return $info[0]['id'];	   
	}
}
