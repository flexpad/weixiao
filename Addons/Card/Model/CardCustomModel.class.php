<?php

namespace Addons\Card\Model;

use Think\Model;

/**
 * 客户关怀模型
 */
class CardCustomModel extends Model {
	function do_send_crons(){
		//判断用户条件
		$map['token']=get_token();
		$key='CardCustom_listData_'.$map['token'];
		$lists_data=S($key);
		if (empty($lists_data)){
			$lists_data=$this->where($map)->select();
			S($key,$lists_data,86400);
		}
		foreach($lists_data as $vo){
			$member=explode(',', $vo['member']);
			$saxArr=null;
			$levelarr=null;
			foreach ($member as $m){
				if ($m == -2){
					$saxArr[]=2;//女
				}else if ($m == -3){
					$saxArr[]=1;//男
				}else if ($m == -1){
					//所有会员用户
					$map['level']=array('neq',0);
				}else if($m !=0){
					$levelarr[]=$m;
				}
			}
			if (!empty($saxArr)){
				$map['sex']=array('in',$saxArr);
			}
			if (!empty($levelarr)){
				$map['level']=array('in',$levelarr);
			}
			//两种方式
			$this->_add_log($vo,$map);
		}
	}
	//会员生日
	function _add_log($data,$map){
		$cardMember=M('card_member')->where($map)->select();
		foreach ($cardMember as $cd){
			$sendlog=null;
			$is_add=0;
			if ($data ['is_birthday'] == 1){
				//生日
				//提前几天
				$strtime="+".$data['before_day']." day";
				$beforeday= date("m-d",strtotime($strtime));
				$birth=time_format($cd['birthday'],'m-d');
				if($birth == $beforeday){
					$is_add=1;
				}
			}else{
				//公历节日
				$oldtime=time_format($data['end_time'],'Y-m-d');
				$nowtime=time_format(time(),'Y-m-d');
				if ($oldtime == $nowtime){
					$is_add=1;
				}
			}
			
			if ($is_add == 1){
				$logMap['uid']=$cd['uid'];
				$logMap['custom_id']=$data['id'];
				$ctime=M('card_custom_log')->where($logMap)->getField('cTime');
				$oldyear=time_format($ctime,'Y');
				$nowyear=time_format(time(),'Y');
				if (!$ctime || $oldyear != $nowyear ){
					$sendlog['custom_id']=$data['id'];
					$sendlog['uid']=$cd['uid'];
					$sendlog['token']=get_token();
					$sendlog['cTime']=time();
					$sendlog['score']=$data['score'];
					$sendlog['coupon_id']=$data['coupon_id'];
					$sendlog['is_send']=0;
					$sendlog['is_birthday']=$data['is_birthday'];
					$sendlogs[]=$sendlog;
					$userarr[$cd['uid']]=$cd['uid'];
				}
			}
		}
		//测试定时数据
		if (!empty($sendlogs)){
			$customDao=D('Common/Custom');
			M('card_custom_log')->addAll($sendlogs);
			if (!empty($userarr)){
				//通知用户领取
				$notice['cTime']=time();
				$notice['title']='客户关怀-'.$data['title'];
				$notice['token']=get_token();
				
				foreach ($userarr as $u){
					if ($data['is_birthday']==1){
						if ($data['notice_mess']){
							$exp["{username}"]=get_username($u);
							$exp["{before_time}"]=$data['before_day'];
							$exp["{title}"]=$data['title'];
							$notice['content']=strtr($data['notice_mess'], $exp);
						}else{
							$notice['content']='您好，再过'.$data['before_day'].'天就是您的破蛋日啦！<br/>我们在"'.$data['title'].'"活动中为您准备份小礼物，请到 会员卡-客户关怀 领取吧！';
						}
					}else{
						if ($data['notice_mess']){
							$exp["{username}"]=get_username($u);
							$exp["{title}"]=$data['title'];
							$notice['content']=strtr($data['notice_mess'], $exp);
						}else {
							$notice['content']='您好，今天是个特殊的日子（'.$data['title'].'），在此我们为您准备份小礼物，请到 会员卡-客户关怀 领取吧！';
						}
					}
					
					$notice['to_uid']=$u;
					$noticeArr[]=$notice;
					
					$key='cardnotic_'.$notice['token'].'_'.$u;
					$rrs=S($key);
					if($rrs > 0){
						$rrs ++;
						S($key,$rrs);
					}else{
						S($key,1);
					}
				}
// 				dump($noticeArr);
				if ($noticeArr){
					M('card_notice')->addAll($noticeArr);
				}
			}
		}
	}
}
