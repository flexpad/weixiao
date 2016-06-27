<?php

namespace Addons\SingIn\Model;

use Home\Model\WeixinModel;

/**
 * SingIn的微信模型
 */
class WeixinAddonModel extends WeixinModel {
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'SingIn' ); // 获取后台插件的配置参数
		$openid = $dataArr['FromUserName'];
		$token = $dataArr['ToUserName'];
		$umap ['openid'] = $openid;
		$umap ['token'] = $token;
	    $uid = M ( 'public_follow' )->where ( $umap )->getField ( 'uid' );
		$done = $config ['done'];
		$reply = $config ['reply'];
		$notstart = $config ['notstart'];
		
		// 检查是否已到开始时间
		$hour = $config ['hour'];
		$minute = $config ['minute'];
		$startTime = $this->getToday () + $hour * 3600 + $minute * 60;
		if (time () < $startTime) {
			$notstart = str_replace ( '[开始时间]', date ( 'H:i', $startTime ), $notstart );
			$notstart = str_replace ( '[当前时间]', date ( 'H:i', time () ), $notstart );
			$this->replyText ( $notstart );
			return true;
		}
		
		// 检查是否已签到
		if ($this->check_SignIn ( $uid, $token )) {
			$this->replyText ( $done );
			return true;
		}
		
		// 计算积分
		$score = 0;
		if ($config ['random'] == 1) {
			// 固定积分
			$score = ( int ) $config ['score'];
		} else {
			// 随机积分
			$score = rand ( ( int ) $config ['score1'], ( int ) $config ['score2'] );
		}
		
		// 记录日志
		$data ['uid'] = $uid;
		$data ['token'] = $token;
		$data ['sTime'] = time ();
		$data ['score'] = $score;
		$res = M ( 'signin_log' )->add ( $data );
		
		if ($res) {
			$credit['score']=$score;
			$credit['uid']=$uid;
			add_credit('signin',0,$credit);
			// 组装回复内容
			
			$reply = str_replace ( '[本次积分]', $score, $reply );
			$reply = str_replace ( '[签到时间]', date ( 'Y-m-d H:i:s', time () ), $reply );
			
			// 积分余额
			if (stripos ( '..' . $reply, '[积分余额]' ) >= 1) {
				$total = $this->getScore ();
                $total = get_userinfo($uid,'score');
				$reply = str_replace ( '[积分余额]', $total, $reply );
			}
			
			// 排名
			if (stripos ( '..' . $reply, '[排名]' ) >= 1) {
				$w1 ['token'] = $token;
				$w1 ['sTime'] = array (
						'egt',
						$this->getToday () 
				);
				$cnt = M ( 'signin_log' )->where ( $w1 )->count ( 'id' );
				$reply = str_replace ( '[排名]', $cnt, $reply );
			}
			
			// 排行榜
			if (stripos ( '..' . $reply, '[排行榜]' ) >= 1) {
				$w1 ['token'] = $token;
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
							//$vo ['nickname'] = getUserInfo($user [$vo ['uid']], 'nickname');
						    $vo ['nickname'] = getUserInfo($vo ['uid'], 'nickname');
						}
					}
					
					// 组装排行榜
					$top5_content = '';
					$i = 1;
					foreach ( $top5 as $vo1 ) {
						$top5_content .= sprintf ( "第%s名  %s  %s\n", $i, empty ( $vo1 ['nickname'] ) ? '匿名' : $vo1 ['nickname'], date ( 'H:i:s', $vo1 ['sTime'] ) );
						$i ++;
					}
					$reply = str_replace ( '[排行榜]', $top5_content, $reply );
				}
			}
			if ($config['continue_day']==1){
				$credit['score']=$config['continue_score'];
				$credit['uid']=$uid;
				$credit['title'] = '连续签到'.$config['continue_day'].'天';
				add_credit('signin',0,$credit);
			}else{
				$amap['token']=get_token();
				$amap['uid'] = $uid;
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
			
			$this->replyText ( $reply );
			return;
		} else {
			$this->replyText ( '签到失败,请联系客服!' );
			return;
		}
	}
	private function check_SignIn($uid, $token) {
		$result = false;
		
		$date = date ( 'Y-m-d 00:00:00', time () );
		$temp = explode ( " ", $date );
		$temp1 = explode ( "-", $temp [0] );
		$temp2 = explode ( ":", $temp [1] );
		$today = mktime ( $temp2 [0], $temp2 [1], $temp2 [2], $temp1 [1], $temp1 [2], $temp1 [0] );
		
		$map ['uid'] = $uid;
		$map ['token'] = $token;
		$map ['sTime'] = array (
				'egt',
				$today 
		);
		$cnt = M ( 'signin_log' )->where ( $map )->count ( 'id' );
		if ($cnt >= 1) {
			$result = true;
		}
		return $result;
	}
	
	// 获得用户积分
	private function getScore() {
		return intval(getUserInfo($this->mid, 'score'));
	}
	private function getToday() {
		$date = date ( 'Y-m-d 00:00:00', time () );
		$temp = explode ( " ", $date );
		$temp1 = explode ( "-", $temp [0] );
		$temp2 = explode ( ":", $temp [1] );
		$today = mktime ( $temp2 [0], $temp2 [1], $temp2 [2], $temp1 [1], $temp1 [2], $temp1 [0] );
		
		return $today;
	}
}