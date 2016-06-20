<?php

namespace Addons\Weiba\Model;
use Think\Model;

/**
 * Weiba模型
 */
class WeibaModel extends Model{
	/**
	 * 批量获取版块关注状态
	 * 
	 * @param
	 *        	integer uid 用户UID
	 * @param
	 *        	array weiba_ids 版块ID
	 * @return [type] [description]
	 */
	public function getFollowStateByWeibaids($uid, $weiba_ids) {
		$_weibaids = is_array ( $weiba_ids ) ? implode ( ',', $weiba_ids ) : $weiba_ids;
		if (empty ( $_weibaids )) {
			return array ();
		}
		$follow_data = D ( 'weiba_follow' )->where ( " ( follower_uid = '{$uid}' AND weiba_id IN({$_weibaids}) ) " )->select ();
		
		$follow_states = $this->_formatFollowState ( $uid, $weiba_ids, $follow_data );
		return $follow_states [$uid];
	}
	/**
	 * 格式化，用户的关注数据
	 * 
	 * @param integer $uid
	 *        	用户ID
	 * @param array $fids
	 *        	用户ID数组
	 * @param array $follow_data
	 *        	关注状态数据
	 * @return array 格式化后的用户关注状态数据
	 */
	private function _formatFollowState($uid, $weiba_ids, $follow_data) {
		! is_array ( $weiba_ids ) && $fids = explode ( ',', $weiba_ids );
		foreach ( $weiba_ids as $weiba_ids ) {
			$follow_states [$uid] [$weiba_ids] = array (
					'following' => 0 
			);
		}
		foreach ( $follow_data as $r_v ) {
			if ($r_v ['follower_uid'] == $uid) {
				$follow_states [$r_v ['follower_uid']] [$r_v ['weiba_id']] ['following'] = 1;
			}
		}
		
		return $follow_states;
	}
	/**
	 * 关注版块
	 * 
	 * @param
	 *        	integer uid 用户UID
	 * @param
	 *        	integer weiba_id 版块ID
	 * @return integer 新添加的数据ID
	 */
	public function doFollowWeiba($uid, $weiba_id) {
		$data ['weiba_id'] = $weiba_id;
		$data ['follower_uid'] = $uid;
		if (D ( 'weiba_follow' )->where ( $data )->find ()) {
			$this->error = '您已关注该版块';
			return false;
		} else {
			$res = D ( 'weiba_follow' )->add ( $data );
			if ($res) {
				D ( 'weiba' )->where ( 'id=' . $weiba_id )->setInc ( 'follower_count' );
				
				// 添加积分
				//model ( 'Credit' )->setUserCredit ( $uid, 'follow_weiba' );
				
				return true;
			} else {
				$this->error = '关注失败';
				return false;
			}
		}
	}
	
	/**
	 * 取消关注版块
	 * 
	 * @param
	 *        	integer uid 用户UID
	 * @param
	 *        	integer weiba_id 版块ID
	 * @return integer 新添加的数据ID
	 */
	public function unFollowWeiba($uid, $weiba_id) {
		$data ['weiba_id'] = $weiba_id;
		$data ['follower_uid'] = $uid;
		if (D ( 'weiba_follow' )->where ( $data )->find ()) {
			$res = D ( 'weiba_follow' )->where ( $data )->delete ();
			if ($res) {
				D ( 'weiba' )->where ( 'id=' . $weiba_id )->setDec ( 'follower_count' );
				D ( 'weiba_apply' )->where ( $data )->delete ();
				
				// 添加积分
				//model ( 'Credit' )->setUserCredit ( $uid, 'unfollow_weiba' );
				
				return true;
			} else {
				$this->error = '关注失败';
				return false;
			}
		} else {
			$this->error = '您尚未关注该版块';
			return false;
		}
	}
	public function setNewcount($weiba_id,$num=1) {
		$map['weiba_id'] = $weiba_id;
		$time = time();
		$weiba = D ( 'weiba' )->where ( $map )->find ();
		if($weiba['new_day']!= date("Y-m-d",$time)){
			D ( 'weiba' )->where ( $map )->setField ('new_day',date("Y-m-d",$time));
			D ( 'weiba' )->where ( $map )->setField ('new_count',0);
		}
		if($num == 0){
			D ( 'weiba' )->where ( $map )->setField ('new_count',0);
		}
		if($num > 0 ){
			D ( 'weiba' )->where ( $map )->setField ('new_count',(int)$num+(int)$weiba['new_count']);
		}
		return true;
	}
}
