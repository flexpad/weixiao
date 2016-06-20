<?php

namespace Addons\HelpOpen\Model;

use Think\Model;

/**
 * HelpOpen模型
 */
class HelpOpenUserModel extends Model {
	function checkJoin($help_id, $uid, $invite_uid) {
		$map ['help_id'] = $help_id;
		$map ['invite_uid'] = $invite_uid;
		$map ['friend_uid'] = array (
				'neq',
				0 
		);
		
		$list = $this->where ( $map )->select ();
		
		$res ['has_help'] = 0; // 是否已帮拆过
		$res ['invite_count'] = 0;
		$res ['sn_id'] = '';
		$name = get_nickname ( $invite_uid );
		foreach ( $list as &$vo ) {
			$res ['invite_count'] += 1;
			if ($vo ['friend_uid'] == $uid) {
				$res ['has_help'] = 1;
				$res ['sn_id'] = $vo ['sn_id'];
			}
			$vo ['invite_nickname'] = $name;
			$vo ['friend_nickname'] = get_nickname ( $vo ['friend_uid'] );
		}
		$res ['list_data'] = $list;
		
		return $res;
	}
	function join_info($help_id, $uid) {
		$map ['help_id'] = $help_id;
		$map ['invite_uid'] = $uid;
		$map ['friend_uid'] = 0;
		
		return $this->where ( $map )->find ();
	}
	function check_use($help_id, $invite_uid) {
		$map ['help_id'] = $help_id;
		$map ['invite_uid'] = $invite_uid;
		$map ['friend_uid'] = 0;
		
		$id = D ( 'HelpOpenUser' )->where ( $map )->getField ( 'sn_id' );
		$sn = D ( 'Common/SnCode' )->getInfoById ( $id );
		return $sn ['is_use'];
	}
	// 赠送优惠券
	function sendCoupon($id, $uid) {
		$param ['id'] = $id;
		
		$info = $this->getInfo ( $id );
		
		$flat = true;
		if ($info ['collect_count'] >= $info ['num']) {
			$flat = false;
		} else if (! empty ( $info ['start_time'] ) && $info ['start_time'] > NOW_TIME) {
			$flat = false;
		} else if (! empty ( $info ['end_time'] ) && $info ['end_time'] < NOW_TIME) {
			$flat = false;
		}
		
		$list = D ( 'Common/SnCode' )->getMyList ( $uid, $id, 'Coupon' );
		$my_count = count ( $list );
		
		if ($info ['max_num'] > 0 && $my_count >= $info ['max_num']) {
			$flat = false;
		}
		if (! $flat)
			return false;
		
		$data ['target_id'] = $id;
		$data ['uid'] = $uid;
		$data ['addon'] = 'Coupon';
		$data ['sn'] = uniqid ();
		$data ['cTime'] = NOW_TIME;
		$data ['token'] = $info ['token'];
		
		$sn_id = D ( 'Common/SnCode' )->add ( $data );
		return $sn_id;
	}
}
