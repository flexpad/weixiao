<?php

namespace Addons\Draw\Controller;

use Home\Controller\AddonsController;

class WapController extends AddonsController {
	function _initialize() {
		parent::_initialize ();
	}
	function index() {
		
		// $openid =get_openid();
		// dump($openid);exit;
		$gameId = I ( 'games_id', 0, 'intval' );
		if (empty ( $gameId )) {
			$this->error ( '还没有配置活动' );
		}
		$info = D ( 'Addons://Draw/Games' )->getInfo ( $gameId );
		if ($info ['status'] == 0) {
			$info ['status'] = '已关闭';
		} else {
			if (NOW_TIME >= $info ['end_time']) {
				$info ['status'] = '已结束';
			} else if (NOW_TIME < $info ['start_time']) {
				$info ['status'] = '未开始';
			} else {
				$info ['status'] = '进行中';
			}
		}
		// 分享数据
		$shareData ['title'] = $info ['title'];
		$desc = empty ( $info ['remark'] ) ? $info ['title'] : $info ['remark'];
		$shareData ['desc'] = filter_line_tab ( $desc );
		switch ($info ['game_type']) {
			case 1 :
				$shareData ['imgUrl'] = $info ['cover'] ? get_cover_url ( $info ['cover'] ) : SITE_URL . '/Addons/Draw/View/default/Public/guaguale_cover.jpg';
				break;
			case 2 :
				$shareData ['imgUrl'] = $info ['cover'] ? get_cover_url ( $info ['cover'] ) : SITE_URL . '/Addons/Draw/View/default/Public/dzp_cover.jpg';
				break;
			case 3 :
				$shareData ['imgUrl'] = $info ['cover'] ? get_cover_url ( $info ['cover'] ) : SITE_URL . '/Addons/Draw/View/default/Public/zjd_cover.jpg';
				break;
			case 4 :
				$shareData ['imgUrl'] = $info ['cover'] ? get_cover_url ( $info ['cover'] ) : SITE_URL . '/Addons/Draw/View/default/Public/nine_cover.jpg';
				break;
		}
		
		$publicinfo = get_token_appinfo ();
		
		$shareData ['link'] = U ( 'index', array (
				'games_id' => $info ['id'],
				'publicid' => $publicinfo ['id'] 
		) );
		$this->assign ( 'shareData', $shareData );
		$this->assign ( 'public_info', $publicinfo );
		// 奖品列表
		$awardLists = D ( 'Addons://Draw/LotteryGamesAwardLink' )->getGamesAwardlists ( $gameId );
		foreach ( $awardLists as $v ) {
			$jp ['title'] = $v ['grade'];
			$jp ['pic'] = $v ['img'];
			$jp ['picUrl'] = $v ['img_url'];
			$jp ['award_id'] = $v ['award_id'];
			$jplist [] = $jp;
		}
		// 所有获奖列表
		$luckLists = D ( 'Addons://Draw/LuckyFollow' )->getGamesLuckyLists ( $gameId );
		// 个人获奖列表
		$uid = $this->mid;
		if ($uid > 0) {
			$userLucky = D ( 'Addons://Draw/LuckyFollow' )->getGamesLuckyLists ( $gameId, $uid, 0 );
		}
		$hasPrize = 0;
		if (! empty ( $userLucky )) {
			$hasPrize = 1;
		}
		$tmp = '';
		switch ($info ['game_type']) {
			case 1 :
				// 刮刮乐
				$tmp = 'guaguale';
				break;
			case 2 :
				$jp ['title'] = '加油';
				$jp ['picUrl'] = ADDON_PUBLIC_PATH . '/ungeted_pic.png';
				$jp ['award_id'] = 0;
				$jplist [] = $jp;
				// 大转盘
				$tmp = 'dzp';
				break;
			case 3 :
				// 砸金蛋
				$tmp = 'zajindan';
				break;
			case 4 :
				// 九宫格
				$count = count ( $jplist );
				$num = 10 - $count;
				if ($num > 0) {
					for($i = 0; $i < $num; $i ++) {
						$jp ['title'] = '加油';
						$jp ['picUrl'] = ADDON_PUBLIC_PATH . '/ungeted_pic.png';
						$jp ['award_id'] = 0;
						$jplist [] = $jp;
					}
				}
				shuffle ( $jplist );
				$tmp = 'ninegrid';
				break;
		}
		$jplist = JSON ( $jplist );
		// 当前用户是否关注公众号
		$map ['token'] = get_token ();
		$map ['uid'] = $this->mid;
		$openid = get_openid ();
		// dump($map);exit;
		$has_subscribe = 0;
		if ($openid == '-1' || ! C ( 'USER_OAUTH' )) {
			$has_subscribe = 1;
		} elseif ($openid != '-1' && ! empty ( $openid )) {
			$has_subscribe = intval ( M ( 'public_follow' )->where ( $map )->getField ( 'has_subscribe' ) );
		}
		
		$this->assign ( 'has_subscribe', $has_subscribe );
		if ($has_subscribe == 0) { // 获取需要关注的公众号二维码
			$res = D ( 'Home/QrCode' )->add_qr_code ( 'QR_SCENE', 'Draw', $gameId );
			
			$this->assign ( 'qrcode', $res );
		}
		
		$joinUrl = addons_url ( 'Draw://Wap/draw_lottery', array (
				'games_id' => $gameId 
		) );
		$this->assign ( 'joinurl', $joinUrl );
		$this->assign ( 'has_prize', $hasPrize );
		$this->assign ( 'jplist', $jplist );
		$this->assign ( 'luck_lists', $luckLists );
		$this->assign ( 'award_lists', $awardLists );
		$info ['remark'] = str_replace ( "\n", "<br/>", $info ['remark'] );
		
		$this->assign ( 'info', $info );
		$this->assign ( "show_game", $show_game );
		$this->display ( $tmp );
	}
	function log($a, $b) {
		return false;
	}
	function ajaxCheckCount()
    {
        $gameId = I('games_id', 0, 'intval');
        $allow_draw = true; // 有机会抽奖
        $msg = '';
        if (empty($gameId)) {
            $status = 0;
            $msg = '活动已结束!！';
            $allow_draw = false;
        }
        $info = D('Addons://Draw/Games')->getInfo($gameId);
        if (empty($msg)) {
            if ($info['status'] == 0) {
                $status = 0;
                $msg = '活动已关闭';
                $allow_draw = false;
            } else {
                if (NOW_TIME >= $info['end_time']) {
                    $status = 0;
                    $msg = '活动已结束！';
                    $allow_draw = false;
                } else 
                    if (NOW_TIME < $info['start_time']) {
                        $status = 0;
                        $msg = '活动未开始！';
                        $allow_draw = false;
                    }
            }
        }
        $uid = $this->mid;
        if ($uid <= 0) {
            $status = 0;
            $msg = '抱歉，未能抽中奖品！';
            $allow_draw = false;
        }
        // 每人每天抽奖次数
        if (empty($msg) && $info['day_attend_limit']) {
            $day_attend_limit = D('Addons://Draw/DrawFollowLog')->get_user_attend_count($gameId, $uid, NOW_TIME);
            if ($day_attend_limit >= $info['day_attend_limit']) {
                $status = 0;
                $msg = '您今天的抽奖次数已经用完!';
                $allow_draw = false;
            }
        }
        // 每天最多中奖人数
        if (empty($msg) && $info['day_winners_count']) {
            $day_winners_count = D('Addons://Draw/LuckyFollow')->get_day_winners_count($gameId, NOW_TIME);
            if ($day_winners_count >= $info['day_winners_count']) {
                $status = 0;
                $msg = '今天奖品已抽完，明天再来吧!';
                $allow_draw = false;
            }
        }
        // 每人总共抽奖次数
        if ( empty($msg) && $info['attend_limit']) {
            $attend_limit = D('Addons://Draw/DrawFollowLog')->get_user_attend_count($gameId, $uid);
            if ($attend_limit >= $info['attend_limit']) {
                $status = 0;
                $msg = '您的所有抽奖次数已用完!';
                $allow_draw = false;
            }
        }
        // 每人每天中奖次数
        if (empty($msg) && $info['day_win_limit']) {
            $day_win_limit = D('Addons://Draw/LuckyFollow')->get_user_win_count($gameId, $uid, NOW_TIME);
            if ($day_win_limit >= $info['day_win_limit']) {
                // 抽奖者将无概率中奖
                $status = 0;
                $msg = '今天的运气用完了';
                $awardId = 0;
                $allow_draw = false;
            }
        }
        $returnData['status']=$allow_draw?1:0;
        $returnData['msg']=$msg;
        $this->ajaxReturn($returnData);
    }
    
    
	// 抽奖方法
	function draw_lottery() {
		ini_set ( 'display_errors', true );
		error_reporting ( E_ALL );
		
		$this->log ( 'coming in', 'draw_lottery_01' );
		$gameId = I ( 'get.games_id', 0, 'intval' );
		$msg = '';
		$status = 0;
		$uid = $this->mid;
		// $token = get_token();
		
		// $this->log($token,'dzp1');
		
		$awardId = 0;
		$angle = - 60;
		$allow_draw = true; // 有机会抽奖
		                    // $this->log($uid,'dzp3');
		if (empty ( $gameId )) {
			$status = 0;
			$msg = '活动已结束!！';
			$allow_draw = false;
		}
		$info = D ( 'Addons://Draw/Games' )->getInfo ( $gameId );
		$this->log ( $info, 'draw_lottery_02' );
		if (empty ( $msg )) {
			if ($info ['status'] == 0) {
				$status = 0;
				$msg = '活动已关闭';
				$allow_draw = false;
			} else {
				if (NOW_TIME >= $info ['end_time']) {
					$status = 0;
					$msg = '活动已结束！';
					$allow_draw = false;
				} else if (NOW_TIME < $info ['start_time']) {
					$status = 0;
					$msg = '活动未开始！';
					$allow_draw = false;
				}
			}
		}
		$this->log ( '333', 'draw_lottery_03' );
		$token = get_token ();
		$credit_title = "抽奖游戏";
		if ($uid <= 0) {
			$msg = '抱歉，未能抽中奖品！';
			$allow_draw = false;
		}
		$this->log ( '444', 'draw_lottery_04' );

		if (empty ( $msg )) {
			// 每人每天抽奖次数
			if ($info ['day_attend_limit']) {
				$day_attend_limit = D ( 'Addons://Draw/DrawFollowLog' )->get_user_attend_count ( $gameId, $uid, NOW_TIME );
				if ($day_attend_limit >= $info ['day_attend_limit']) {
					$status = 0;
					$msg = '您今天的抽奖次数已经用完!';
					$allow_draw = false;
				}
			}
			$this->log ( $day_attend_limit, 'draw_lottery_05' );
			// 每天最多中奖人数
			if ($info ['day_winners_count']) {
				$day_winners_count = D ( 'Addons://Draw/LuckyFollow' )->get_day_winners_count ( $gameId, NOW_TIME );
				if ($day_winners_count >= $info ['day_winners_count']) {
					$status = 0;
					$msg = '今天奖品已抽完，明天再来吧!';
					$allow_draw = false;
				}
			}
			$this->log ( $day_winners_count, 'draw_lottery_06' );
			// 每人总共抽奖次数
			if ($info ['attend_limit']) {
				$attend_limit = D ( 'Addons://Draw/DrawFollowLog' )->get_user_attend_count ( $gameId, $uid );
				if ($attend_limit >= $info ['attend_limit']) {
					$status = 0;
					$msg = '您的所有抽奖次数已用完!';
					$allow_draw = false;
				}
			}
			$this->log ( $attend_limit, 'draw_lottery_07' );
		}

		// $this->log('$info','dzp2');
		if (empty ( $msg )) {
			$this->log ( $uid, 'draw_lottery_071' );
			// 每人总共中奖次数
			if ($info ['win_limit']) {
				$this->log ( $gameId, 'draw_lottery_072' );
				$win_limit = D ( 'Addons://Draw/LuckyFollow' )->get_user_win_count ( $gameId, $uid );
				$this->log ( $attend_limit, 'draw_lottery_073' );
				if ($win_limit >= $info ['win_limit']) {
					// 超过此限制点击抽奖，抽奖者将无概率中奖
					$status = 0;
					$msg = '没有抽中,继续努力';
					$awardId = 0;
				}
			}
			$this->log ( $win_limit, 'draw_lottery_08' );
			// $this->log('$info','dzp3');
			// 每人每天中奖次数
			if ($info ['day_win_limit']) {
				$day_win_limit = D ( 'Addons://Draw/LuckyFollow' )->get_user_win_count ( $gameId, $uid, NOW_TIME );
				if ($day_win_limit >= $info ['day_win_limit']) {
					// 抽奖者将无概率中奖
					$status = 0;
					$msg = '今天的运气用完了';
					$awardId = 0;
				}
			}
			$this->log ( $day_win_limit, 'draw_lottery_09' );
			// 保存抽奖记录
			$drawLog ['follow_id'] = $uid;
			$drawLog ['sports_id'] = $gameId;
			$drawLog ['count'] = 1;
			$drawLog ['cTime'] = NOW_TIME;
			$drawLog ['token'] = $token;
			M ( 'draw_follow_log' )->add ( $drawLog );
			$this->log ( $drawLog, 'draw_lottery_10' );
		}
		
		// $this->log($msg,'dzp5');
		if (empty ( $msg )) {
			// 参与次数++
			$info ['attend_num'] ++;
			$key = 'Games_getInfo_' . $gameId;
			S ( $key, $info, 86400 );
			
			// 抽奖，获取奖品id
			$lotteryData = $this->_do_lottery ( $gameId );
			$this->log ( $lotteryData, 'draw_lottery_11' );
			$angle = $lotteryData ['angle'];
			$awardId = $lotteryData ['prize_id'];
			switch ($lotteryData ['game_type']) {
				case 1 :
					$credit_title .= "_刮刮乐";
					break;
				case 2 :
					$credit_title .= "_大转盘";
					break;
				case 3 :
					$credit_title .= "_砸金蛋";
					break;
				case 4 :
					$credit_title .= "_九宫格";
					break;
			}
			$this->log ( $credit_title, 'draw_lottery_12' );
			if ($awardId == 0) {
				$this->log ( $credit_title, 'draw_lottery_13' );
				$status = 0;
				$msg = '没有抽中,继续努力啊';
			} else {
				$awardInfo = D ( 'Addons://Draw/Award' )->getInfo ( $awardId );
				$this->log ( $awardInfo, 'draw_lottery_14' );
				// 保存中奖信息
				$res = $this->save_zjInfo ( $gameId, $awardId, $uid, $awardInfo );
				$this->log ( $res, 'draw_lottery_15' );
				// $this->log($res,'111');
				if ($res ['id']) {
					$status = 1;
					$msg = '恭喜，您中了 ' . $awardInfo ['name'];
					$img = get_cover_url ( $awardInfo ['img'] );
					if ($res ['other'] == 1) {
						// 领取奖品
						$jumpUrl = addons_url ( 'Draw://Wap/get_prize', array (
								'id' => $res ['id'] 
						) );
					} else if ($res ['other'] == 2) {
						// 优惠详情
						$jumpUrl = addons_url ( 'Coupon://Wap/show', array (
								'id' => $awardInfo ['coupon_id'],
								'sn_id' => $res ['sn_id'] 
						) );
					} else if ($res ['other'] == 3 && is_install("ShopCoupon")) {
						$jumpUrl = addons_url ( 'ShopCoupon://Wap/show', array (
								'id' => $awardInfo ['coupon_id'],
								'sn_id' => $res ['sn_id'] 
						) );
					} else if ($res ['other'] == 0) {
						$msg = '恭喜，您中了 ' . $awardInfo ['name'] . ',获得了' . $awardInfo ['score'] . '积分，已自动充到个人积分中。';
						$jumpUrl = addons_url ( 'Draw://Wap/index', array (
								'games_id' => $gameId 
						) );
					} else if ($res ['other'] == 5) {
						// 微信卡券
						$jumpUrl = addons_url ( 'CardVouchers://Wap/index', array (
								'id' => $res ['card_id'],
								'from_type' => 'drawgame',
								'aim_id' => $res ['id'] 
						) );
					}
					$this->log ( $jumpUrl, 'draw_lottery_16' );
				} else {
					$awardId = 0;
					$status = 0;
					$msg = '可惜，差一点了';
					$this->log ( $msg, 'draw_lottery_17' );
				}
			}
		}

		if (empty ( $jumpUrl )) {
			$jumpUrl = addons_url ( 'Draw://Wap/index', array (
					'games_id' => $gameId 
			) );
		}

		$this->log ( $jumpUrl, 'draw_lottery_18' );
		$returnData ['angle'] = $angle;
		$returnData ['status'] = $status;
		$returnData ['msg'] = $msg;
		$returnData ['img'] = $img;
		$returnData ['jump_url'] = $jumpUrl;
		$returnData ['award_id'] = $awardId;
		$this->log ( $returnData, 'draw_lottery_19' );

		$this->ajaxReturn ( $returnData );
	}
	// 保存中奖信息
	function save_zjInfo($gameId, $awardId, $uid, $awardInfo = array()) {
		$res ['other'] = 0;
		
		$data ['draw_id'] = $gameId;
		$data ['token'] = get_token ();
		$data ['aim_table'] = 'lottery_games';
		$data ['zjtime'] = NOW_TIME;
		$data ['num'] = 1;
		$data ['follow_id'] = $uid;
		$data ['award_id'] = $awardId;
		empty ( $awardInfo ) && $awardInfo = D ( 'Addons://Draw/Award' )->getInfo ( $awardId );
		// 修改库存
		// $res_award = M ( 'sport_award' )->where ( "id='$awardId' and count>0" )->setDec ( 'count' );
		// $this->log($res_award, 'save_zjInfo_02');
		// $this->log(M ( 'sport_award' )->getLastSql(), 'save_zjInfo_03');
		// if (! $res_award) {
		// return false;
		// }
		D ( 'Addons://Draw/Award' )->getInfo ( $awardId, true );
		$this->log ( $awardInfo ['award_type'], 'save_zjInfo_04' );
		switch ($awardInfo ['award_type']) {
			case 0 :
				$data ['state'] = 1;
				$data ['djtime'] = time ();
				// 虚拟物品，积分奖励
				$credit ['score'] = $awardInfo ['score'];
				$credit ['title'] = '抽奖游戏活动';
				$credit ['uid'] = $uid;
				add_credit ( 'lottery_games', 0, $credit );
				$res ['other'] = 0;
				break;
			case 1 :
				// 实物
				$data ['state'] = 0;
				$res ['other'] = 1;
				$str = time ();
				$rand = rand ( 1000, 9999 );
				$str .= $rand;
				$data ['scan_code'] = $str;
				break;
			case 2 :
				$data ['state'] = 1;
				$data ['djtime'] = time ();
				$res1 = D ( 'Addons://Coupon/Coupon' )->sendCoupon ( $awardInfo ['coupon_id'], $this->mid );
				$res ['sn_id'] = $res1;
				$res ['other'] = 2;
				// 优惠券
				break;
			case 3 :
				$data ['state'] = 1;
				$data ['djtime'] = time ();
				$res ['other'] = 3;
				if (is_install("ShopCoupon")) {
                    $res1 = D('Addons://ShopCoupon/Coupon')->sendCoupon($awardInfo['coupon_id'], $this->mid);
                }
				$res ['sn_id'] = $res1;
				// 代金券
				break;
			case 4 :
				$data ['state'] = 1;
				$map1 ['uid'] = $uid;
				$map1 ['token'] = get_token ();
				
				$cardMember = M ( 'card_member' )->where ( $map1 )->field ( 'id,recharge' )->find ();
				if ($cardMember) {
					// 直接加入会员卡
					$save ['recharge'] = $cardMember ['recharge'] + $awardInfo ['money'];
					M ( 'card_member' )->where ( $map1 )->save ( $save );
				} else {
					// 没有会员卡，
					$res ['other'] = 4;
				}
			case 5 :
				$data ['state'] = 0;
				// 微信卡券
				$res ['other'] = 5;
				$res ['card_id'] = $awardInfo ['card_id'];
				// 返现
				break;
		}
		$res ['id'] = M ( 'lucky_follow' )->add ( $data );
		$this->log ( $res, '2222' );
		return $res;
	}
	// 获取奖品id
	function _do_lottery($event_id) {
		// 奖品列表
		$awardLists = D ( 'Addons://Draw/LotteryGamesAwardLink' )->getGamesAwardlists ( $event_id );
		
		// 各奖品抽中发放数量
		$token = get_token ();
		$fafangjp = D ( 'Addons://Draw/LuckyFollow' )->getLzwgAwardNum ( $event_id, $token );
		// 各奖品剩余数量
		// 最大抽奖总次数
		$maxCount = 0;
		$awardNums = 0;
		foreach ( $awardLists as $v ) {
			// 大转盘获取angle
			$jp ['title'] = $v ['grade'];
			$jp ['pic'] = $v ['img'];
			$jp ['picUrl'] = $v ['img_url'];
			$jp ['id'] = $v ['award_id'];
			$jplist [] = $jp;
			$v ['total_num'] = $v ['num'];
			if ($fafangjp [$v ['award_id']]) {
				$v ['num'] = $v ['num'] - $fafangjp [$v ['award_id']];
			}
			if ($v ['num'] > 0) {
				$d [] = $v;
			}
			$maxCount += $v ['max_count'];
			$awardNums += $v ['num'];
		}
		if ($awardNums <= 0) {
			// 奖品数量为0，关闭活动
			$prizeid = 0;
		}
		foreach ( $d as $v ) {
			$prizeArr [] = array (
					'prize_id' => $v ['award_id'],
					'prize_num' => $v ['num'],
					'total_num' => $v ['total_num'],
					'max_count' => $v ['max_count'] 
			);
		}
		// dump($prizeArr);die;
		$info = D ( 'Addons://Draw/Games' )->getInfo ( $event_id );
		// dump($info);
		$attendCount = $info ['attend_num'];
		// 剩余抽奖次数
		$drawCount = $maxCount - $attendCount;
		
		if ($drawCount <= 0 && empty ( $d )) {
			$prizeid = 0;
		} else if ($drawCount <= 0 && $awardNums > $drawCount) {
			// 最大抽奖次数用完，直接返回奖品
			$prizeid = $d [0] ['award_id'];
		} else {
			$prizeid = $this->lottery ( $prizeArr, $drawCount );
		}
		
		$flat = true;
		// 根据优惠券的限制数发放
		if ($prizeid != 0) {
			$awardInfo = D ( 'Addons://Draw/Award' )->getInfo ( $prizeid );
			if ($awardInfo ['award_type'] == 2) {
				$info = D ( 'Addons://Coupon/Coupon' )->getInfo ( $awardInfo ['coupon_id'] );
				if ($info ['collect_count'] >= $info ['num']) {
					$flat = false;
				} else if (! empty ( $info ['start_time'] ) && $info ['start_time'] > NOW_TIME) {
					$flat = false;
				} else if (! empty ( $info ['end_time'] ) && $info ['end_time'] < NOW_TIME) {
					$flat = false;
				}else if($info['is_del'] == 1){
				    $flat = false;
				}
				
				$list = D ( 'Common/SnCode' )->getMyList ( $this->mid, $awardInfo ['coupon_id'], 'Coupon' );
				$my_count = count ( $list );
				
				if ($info ['max_num'] > 0 && $my_count >= $info ['max_num']) {
					$flat = false;
				}
			} else if ($awardInfo ['award_type'] == 3 && is_install("ShopCoupon")) {
				$info = D ( 'Addons://ShopCoupon/Coupon' )->getInfo ( $awardInfo ['coupon_id'] );
				if ($info ['collect_count'] >= $info ['num']) {
					$flat = false;
				} else if (! empty ( $info ['start_time'] ) && $info ['start_time'] > NOW_TIME) {
					$flat = false;
				} else if (! empty ( $info ['end_time'] ) && $info ['end_time'] < NOW_TIME) {
					$flat = false;
				}
				$list = D ( 'Common/SnCode' )->getMyList ( $this->mid, $awardInfo ['coupon_id'], 'ShopCoupon' );
				$my_count = count ( $list );
				
				if ($info ['limit_num'] > 0 && $my_count >= $info ['limit_num']) {
					$flat = false;
				}
			}
			if (! $flat) {
				$prizeid = 0;
			}
		}
		
		$rid = 0;
		$jp ['id'] = 0;
		$jplist [] = $jp;
		foreach ( $jplist as $k => $vo ) {
			if ($vo ['id'] == $prizeid) {
				$rid = $k;
			}
		}
		
		// 计算中奖角度的位置
		$result ['angle'] = 360 - (360 / sizeof ( $jplist ) / 2) - (360 / sizeof ( $jplist ) * $rid) - 90;
		$result ['angle'] == 0 && $result ['angle'] = 360;
		$result ['prize_id'] = $prizeid;
		
		return $result;
	}
	// 根据抽奖最大数抽奖算法
	// $prizeArr: 奖品数组（奖品id,剩余数量）
	// $drawCount： 剩余最大抽奖次数
	function lottery($prizeArr, $drawCount) {
		$prize = 0;
		$prizeArr = $this->array_sort ( $prizeArr, 'total_num' );
		$prizeArr = $this->prize_sort ( $prizeArr );
		
		if (! empty ( $prizeArr )) {
			foreach ( $prizeArr as $p ) {
				if ($p ['prize_num'] > 0 && $p ['total_num'] == $p ['max_count']) {
					$prize = $p ['prize_id'];
					break;
				} else {
					continue;
				}
			}
		}
		if (! empty ( $prizeArr ) && $prize == 0) {
			$prizeArr = $this->array_sort ( $prizeArr, 'total_num' );
			$prizeArr = $this->prize_sort ( $prizeArr );
			foreach ( $prizeArr as $pp ) {
				if ($pp ['prize_num'] > 0) {
					$rand = rand ( 1, $pp ['max_count'] );
					if ($rand <= $pp ['total_num']) {
						$prize = $pp ['prize_id'];
						break;
					}
				} else {
					continue;
				}
			}
		}
		
		return $prize;
	}
	// 奖品排序
	function prize_sort($prizeArr) {
		$count = 0;
		foreach ( $prizeArr as $v ) {
			$count += $v ['total_num'];
		}
		if ($count > 0) {
			foreach ( $prizeArr as $k => $d ) {
				$ra = rand ( 1, $count );
				if ($ra <= $d ['total_num']) {
					$fprizes [] = $d;
					unset ( $prizeArr [$k] );
				} else {
					$eprizes [] = $d;
				}
				$count = $count - $d ['total_num'];
			}
		}
		if (empty ( $eprizes )) {
			$prizeArr = $fprizes;
		} else if (empty ( $fprizes )) {
			$prizeArr = $eprizes;
		} else {
			$prizeArr = array_merge ( $fprizes, $eprizes );
		}
		return $prizeArr;
	}
	// 二维数组根据键排序
	function array_sort($arr, $keys, $type = 'desc') {
		$keysvalue = $new_array = array ();
		foreach ( $arr as $k => $v ) {
			$keysvalue [$k] = $v [$keys];
		}
		if ($type == 'asc') {
			asort ( $keysvalue );
		} else {
			arsort ( $keysvalue );
		}
		reset ( $keysvalue );
		foreach ( $keysvalue as $k => $v ) {
			$new_array [$k] = $arr [$k];
		}
		return $new_array;
	}
	
	// 我的奖品
	function my_prize() {
		$gameId = I ( 'games_id', 0, 'intval' );
		if ($this->mid > 0) {
			$userAward = D ( 'Addons://Draw/LuckyFollow' )->getGamesLuckyLists ( $gameId, $this->mid );
		} else {
			$userAward = array ();
		}
		$this->assign ( 'user_award', $userAward );
		$this->display ();
	}
	// 领取
	function get_prize() {
		$id = I ( 'id' );
		$userAward = D ( 'Addons://Draw/LuckyFollow' )->getUserAward ( $id );
		$this->assign ( 'user_award', $userAward );
		
		$addressList = D ( 'Common/Address' )->getUserList ( $this->mid );
		$this->assign ( 'address', $addressList );
		$this->display ();
	}
	function save_prize_address() {
		$map ['id'] = I ( 'id' );
		$save ['address'] = I ( 'address_id' );
		$res = M ( 'lucky_follow' )->where ( $map )->save ( $save );
		if ($res) {
			echo 1;
		} else {
			echo 0;
		}
	}
	// 活动中奖记录
	function prize_log() {
		$gameId = I ( 'games_id', 0, 'intval' );
		$luckLists = D ( 'Addons://Draw/LuckyFollow' )->getGamesLuckyLists ( $gameId );
		$this->assign ( 'luck_lists', $luckLists );
		$this->display ();
	}
	// 实物奖品扫码核销
	function scan_success() {
		$cTime = I ( 'cTime', 0, 'intval' );
		$tt = NOW_TIME * 1000 - $cTime;
		if ($cTime > 0) {
			if ($tt > 30000) {
				$this->error ( '二维码已经过期' );
			}
		}
		// 扫码员id
		$mid = $this->mid;
		// 授权表查询
		$map ['uid'] = $mid;
		$map ['token'] = get_token ();
		$map ['enable'] = 1;
		$role = M ( 'servicer' )->where ( $map )->getField ( 'role' );
		$roleArr = explode ( ',', $role );
		if (! in_array ( 2, $roleArr )) {
			$this->error ( '你还没有扫码验证的权限' );
			exit ();
		}
		
		$scanCode = I ( 'scan_code' );
		$map1 ['id'] = I ( 'id' );
		$lucky = M ( 'lucky_follow' )->find ( $map1 ['id'] );
		$is_check = 0;
		if ($lucky ['scan_code'] == $scanCode) {
			// 验证成功
			$save ['state'] = 2;
			$save ['djtime'] = time ();
			$res = M ( 'lucky_follow' )->where ( $map1 )->save ( $save );
			if ($res) {
				$is_check = 1;
			}
		}
		$userAward = D ( 'Addons://Draw/LuckyFollow' )->getUserAward ( $map1 ['id'], true );
		
		$this->assign ( 'user_award', $userAward );
		$this->assign ( 'is_check', $is_check );
		$this->display ( 'get_prize' );
	}
	function get_state() {
		$id = I ( 'id' );
		$state = M ( 'lucky_follow' )->where ( array (
				'id' => $id 
		) )->getField ( 'state' );
		echo $state;
	}
	// 大转盘
	function dzp() {
		$this->display ();
	}
	// 刮刮乐
	function guaguale() {
		$this->display ();
	}
	// 砸金蛋
	function zajindan() {
		$this->display ();
	}
	// 九宫格
	function ninegrid() {
		$this->display ();
	}
	// 测试
	function nine() {
		$this->display ();
	}
}
