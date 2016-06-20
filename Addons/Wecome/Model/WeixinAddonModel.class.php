<?php

namespace Addons\Wecome\Model;

use Home\Model\WeixinModel;

/**
 * Wecome模型
 */
class WeixinAddonModel extends WeixinModel {
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'Wecome' ); // 获取后台插件的配置参数
		
		if ($dataArr ['Content'] == 'subscribe') {
			
			$uid = D ( 'Common/Follow' )->init_follow ( $dataArr ['FromUserName'] );
			D ( 'Common/Follow' )->set_subscribe ( $dataArr ['FromUserName'], 1 );
			// 增加积分
			session ( 'mid', $uid );
			add_credit ( 'subscribe' );
			
			// //关注公众号获取会员卡号
			// D('Addons://Card/Card')->init_card_member($dataArr['FromUserName']);
			
			$this->material_reply ( $config ['stype'] );
			
			$has_return = false;
			if (! empty ( $dataArr ['EventKey'] )) {
				$has_return = $this->scan ( $dataArr, $keywordArr );
			}
			if ($has_return)
				return true;
		} elseif ($dataArr ['Content'] == 'scan') {
			$this->scan ( $dataArr, $keywordArr, $config );
		} elseif ($dataArr ['Content'] == 'unsubscribe') {
			D ( 'Common/Follow' )->set_subscribe ( $dataArr ['FromUserName'], 0 );
			// 增加积分
			$map1 ['openid'] = $dataArr ['FromUserName'];
			$map1 ['token'] = get_token ();
			$map2 ['uid'] = $uid = M ( 'public_follow' )->where ( $map1 )->getField ( 'uid' );
			$credit ['uid'] = $uid;
			add_credit ( 'unsubscribe', 0, $credit );
			
			// 如果需要用户取消关系时系统自动物理删除该用户，把下面三行开启即可
			// M ( 'public_follow' )->where ( $map1 )->delete ();
			// M ( 'user' )->where ( $map2 )->delete ();
			// M ( 'credit_data' )->where ( $map2 )->delete ();
			session ( 'mid', null );
			
			$key = 'getUserInfo_' . $map2 ['uid'];
			S ( $key, NULL );
		} elseif ($dataArr ['Content'] == '获取内测码') {
			$map ['openid'] = $dataArr ['FromUserName'];
			$code = M ( 'invite_code' )->where ( $map )->getField ( 'code' );
			if (! $code) {
				$code = $map ['code'] = substr ( uniqid (), - 5 );
				M ( 'invite_code' )->add ( $map );
			}
			$this->replyText ( '您的内测码是：' . $code . ', 注意：内测码只能使用一次，再次注册时需要重新获取内测码' );
		} elseif ($dataArr ['Content'] == '自动检测') {
			$this->replyText ( 'auto_check' );
		}
	}
	function scan($dataArr, $keywordArr = array(), $config = array()) {
		$map ['scene_id'] = ltrim ( $dataArr ['EventKey'], 'qrscene_' );
		$map ['token'] = get_token ();
		$qr = M ( 'qr_code' )->where ( $map )->find ();
		if ($qr ['addon'] == 'UserCenter') { // 设置用户分组
			$uid = $GLOBALS ['mid'];
			if (empty ( $uid )) {
				$map1 ['openid'] = $dataArr ['FromUserName'];
				$map1 ['token'] = get_token ();
				$uid = M ( 'public_follow' )->where ( $map1 )->getField ( 'uid' );
			}
			$group = D ( 'Home/AuthGroup' )->move_group ( $uid, $qr ['aim_id'] );
			
			$this->replyText ( '您已加入' . $group ['title'] );
			return true; // 告诉上面的关注方法，不需要再回复欢迎语了
		} else if ($qr ['addon'] == 'QrAdmin') { // 扫码管理
			$qr_admin = M ( 'qr_admin' )->find ( $qr ['aim_id'] );
			
			$uid = $GLOBALS ['mid'];
			if (empty ( $uid )) {
				$map1 ['openid'] = $dataArr ['FromUserName'];
				$map1 ['token'] = get_token ();
				$uid = M ( 'public_follow' )->where ( $map1 )->getField ( 'uid' );
			}
			
			// 加入用户组
			if (! empty ( $qr_admin ['group_id'] )) {
				D ( 'Home/AuthGroup' )->move_group ( $uid, $qr_admin ['group_id'] );
			}
			
			// 增加用户标签
			if (! empty ( $qr_admin ['tag_ids'] )) {
				D ( 'Common/Tag' )->addTags ( $uid, $qr_admin ['tag_ids'] );
			}
			D ( 'Common/User' )->getUserInfo ( $uid, true );
			
			// 回复内容
			if (! empty ( $qr_admin ['material'] )) {
				$this->material_reply ( $qr_admin ['material'] );
				return true; // 告诉上面的关注方法，不需要再回复欢迎语了
			}
		} else if ($qr ['addon'] == 'Shop') {
			$savedata ['openid'] = $map1 ['openid'] = $dataArr ['FromUserName'];
			$map1 ['token'] = get_token ();
			$followId = M ( 'public_follow' )->where ( $map1 )->getField ( 'uid' );
			
			$savedata ['duid'] = $qr ['aim_id'];
			$savedata ['uid'] = $followId;
			$res1 = M ( 'shop_statistics_follow' )->where ( $map1 )->getField ( 'id' );
			if (! $res1) {
				$savedata ['ctime'] = time ();
				$savedata ['token'] = get_token ();
				M ( 'shop_statistics_follow' )->add ( $savedata );
			}
		} elseif ($qr ['addon'] == 'HelpOpen') {
			$user = getUserInfo ( $qr ['extra_int'] );
			$url = addons_url ( 'HelpOpen://Wap/index', array (
					'invite_uid' => $qr ['extra_int'],
					'id' => $qr ['aim_id'] 
			) );
			$this->replyText ( "关注成功，<a href='{$url}'>请点击这里继续帮{$user[nickname]}领取奖品</a>" );
			return true; // 告诉上面的关注方法，不需要再回复欢迎语了
		} elseif ($qr ['addon'] == 'Draw') {
			$url = addons_url ( 'Draw://Wap/index', array (
					'games_id' => $qr ['aim_id'] 
			) );
			$this->replyText ( "关注成功，<a href='{$url}'>请点击这里继续抽奖游戏</a>" );
			return true; // 告诉上面的关注方法，不需要再回复欢迎语了
		} elseif ($qr ['addon'] == 'CouponShop') {
			// 门店二维码
			// 触发会员卡图文
			$config = getAddonConfig ( 'Card' ); // 获取后台插件的配置参数
			$articles [0] = array (
					'Title' => '点击进入免费领取微会员哦~',
					'Description' => $config ['title'],
					'PicUrl' => SITE_URL . "/Addons/Card/View/default/Public/cover_pic.png",
					'Url' => addons_url ( 'Card://Wap/index', array (
							'token' => get_token () 
					) ) 
			);
			$res = $this->replyNews ( $articles );
			return true; // 告诉上面的关注方法，不需要再回复欢迎语了
		} elseif ($qr ['addon'] == 'ScanLogin') {
			$user = D ( 'Common/User' )->getUserInfo ( $GLOBALS ['mid'] );
			S ( $qr ['extra_text'], $user, 120 );
			
			$m_map ['uid'] = $GLOBALS ['mid'];
			if (! M ( 'manager' )->where ( $m_map )->find ()) {
				M ( 'manager' )->add ( $m_map );
			}
			
			$res = $this->replyText ( '登录成功' );
			return true; // 告诉上面的关注方法，不需要再回复欢迎语了
		} elseif ($qr ['addon'] == 'ScanBindLogin') {
			$map1 ['openid'] = $dataArr ['FromUserName'];
			$map1 ['token'] = get_token ();
			$map2 ['uid'] = D ( 'public_follow' )->where ( $map1 )->getField ( 'uid' );
			D ( 'public_follow' )->where ( $map1 )->setField ( 'uid', $qr ['extra_int'] );
			M ( 'user' )->where ( $map2 )->delete ();
			M ( 'credit_data' )->where ( $map2 )->delete ();
			
			S ( $qr ['extra_text'], 1, 120 );
			
			$res = $this->replyText ( '绑定成功' );
			return true; // 告诉上面的关注方法，不需要再回复欢迎语了
		} elseif ($qr ['addon'] == 'MiniLive') {
			$map1 ['openid'] = $dataArr ['FromUserName'];
			$map1 ['token'] = get_token ();
			$uid = M ( 'public_follow' )->where ( $map1 )->getField ( 'uid' );
			// 微现场二维码
			$info = D ( 'Addons://MiniLive/MiniLive' )->getLive ();
			if ($info) {
				$monitor = D ( 'Addons://MiniLive/MiniMonitor' )->getInfo ( $info ['id'] );
				$shakeCount = $monitor ['shake_count'] + 1;
				// $userAttend= D('Addons://MiniLive/MiniShake')->get_user_attend($info['id'],$info['shake_id'],$shakeCount,$uid);
				$userAttend = D ( 'Addons://MiniLive/MiniShake' )->getUserShake ( $info ['id'], $info ['shake_id'], $shakeCount, $uid );
				
				if ($userAttend ['user_num'] == 0) {
					$addUA ['token'] = get_token ();
					$addUA ['live_id'] = $info ['id'];
					$addUA ['shake_count'] = $shakeCount;
					$addUA ['uid'] = $uid;
					$addUA ['join_count'] = 0;
					$addUA ['shake_id'] = $info ['shake_id'];
					M ( 'shake_user_attend' )->add ( $addUA );
				}
				$rr = D ( 'Addons://MiniLive/MiniLive' )->isUpUser ( $uid, $info ['id'], 1, $dataArr ['FromUserName'] );
				if ($monitor ['msgwall_state'] == 1) {
					// 上墙，处理为可上场
					if ($rr == 1) {
						$content = D ( 'Addons://MiniLive/MiniLive' )->_str_rand ();
						$this->replyText ( $content . $info ['up_push'] );
					}
				} else if ($monitor ['game_state'] == 1) {
					$url1 = addons_url ( 'MiniLive://Wap/shake', array (
							'token' => get_token (),
							'live_id' => $info ['id'] 
					) );
					if ($info ['game_msg_title']) {
						$articles [0] = array (
								'Title' => $info ['game_msg_title'],
								'Description' => $info ['game_msg_intro'],
								'PicUrl' => get_cover_url ( $info ['game_msg_img'] ),
								'Url' => $url1 
						);
						$res = $this->replyNews ( $articles );
					} else {
						$text = "游戏即将开始，<a href='{$url1}'>马上点击参与 >></a>";
					}
				} else if ($monitor ['game_state'] == 2) {
					$url1 = addons_url ( 'MiniLive://Wap/shake', array (
							'token' => get_token (),
							'live_id' => $info ['id'] 
					) );
					if ($info ['game_msg_title']) {
						$articles [0] = array (
								'Title' => $info ['game_msg_title'],
								'Description' => $info ['game_msg_intro'],
								'PicUrl' => get_cover_url ( $info ['game_msg_img'] ),
								'Url' => $url1 
						);
						$res = $this->replyNews ( $articles );
					} else {
						$this->replyText ( "游戏进行中，<a href='{$url1}'>马上点击参与...</a>" );
					}
				} else if ($monitor ['playback_state'] == 1) {
					if ($info ['review_msg_title']) {
						$articles [0] = array (
								'Title' => $info ['review_msg_title'],
								'Description' => $info ['review_msg_intro'] 
						);
						$res = $this->replyNews ( $articles );
					}
					return true;
				} else {
					$this->replyText ( '上墙还没开始或已结束！' );
				}
			} else {
				// 活动尚未启用,推送欢迎语内容
				$config = getAddonConfig ( 'Wecome' );
				$param ['token'] = get_token ();
				$param ['openid'] = get_openid ();
				$sreach = array (
						'[follow]',
						'[website]',
						'[token]',
						'[openid]' 
				);
				$replace = array (
						addons_url ( 'UserCenter://Wap/bind', $param ),
						addons_url ( 'WeiSite://WeiSite/index', $param ),
						$param ['token'],
						$param ['openid'] 
				);
				$config ['description'] = str_replace ( $sreach, $replace, $config ['description'] );
				switch ($config ['type']) {
					case '3' :
						if ($config ['appmsg_id']) {
							$res = D ( 'Common/Custom' )->replyNews ( $uid, $config ['appmsg_id'] );
						}
						break;
					case '2' :
						return false;
						break;
					default :
						if ($config ['description']) {
							$res = $this->replyText ( $config ['description'] );
						}
				}
			}
			return true;
		}
	}
}
