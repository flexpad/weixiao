<?php
namespace Addons\Weiba\Controller;

use Home\Controller\AddonsController;
class SearchController extends AddonsController {

	private $curApp  = '';
	private $curType = '';
	private $keywd 	 = '';
	private $tabkey  = '';
	private $tabvalue = '';
	private $searchModel = '';
	private $scity = 0;
	private $sch = 'title';
	private $qdr = 'w';//周

	/**
	 * 模块初始化
	 * @return void
	 */
	public function _initialize() {
		$_GET 		= array_merge($_GET,$_POST);
		$this->curType 	= intval($_GET['t']); 
		$this->keywd  	= str_replace('%','',$_GET['k']);
		$this->tabkey	= t($_GET['tk']);
		$this->tabvalue = t($_GET['tv']);
		//$this->searchModel = ucfirst($this->curApp).'Search';
		$this->scity = $_GET['scity']?intval($_GET['scity']):session('city');
		$this->sch = $_GET['sch']?t($_GET['sch']):'all';
		$this->qdr = $_GET['qdr']?t($_GET['qdr']):'all';
		$this->assign('curType',$this->curType);
		$this->assign('tabkey',$this->tabkey); 
		$this->assign('tabvalue',$this->tabvalue);
		$this->assign('keyword',$this->keywd);
		$this->assign('jsonKey',json_encode($this->key));
		$this->assign('scity',$this->scity);
		$this->assign('sch',$this->sch);
		$this->assign('qdr',$this->qdr);
		
	}
	/**
	 * 根据关键字进行搜索
	 * @return void
	 */
	public function post() {
			if($this->keywd != ""){
				if(t($_GET['Stime']) && t($_GET['Etime'])){
					$Stime = strtotime(t($_GET['Stime']));
					$Etime = strtotime(t($_GET['Etime']));
					$this->assign('Stime',t($_GET['Stime']));
					$this->assign('Etime',t($_GET['Etime']));
				}	
				$order = 'post_time DESC';
				$map['title'] = array(
						'like',
						'%'.$this->keywd.'%'
				);
				if($this->sch=='all'){
					//全文搜索
					$map['content'] = array(
							'like',
							'%'.$this->keywd.'%'
					);
				}
				if($this->qdr=='w'){
					//周
					$start=time()-7*24*60*60;
					$end=time();
					$map['post_time'] = array('between',array($start,$end));
				}else if($this->qdr=='m'){
					//月
					$start=time()-30*24*60*60;
					$end=time();
					$map['post_time'] = array('between',array($start,$end));
				}else if($this->qdr=='y'){
					//年
					$start=time()-365*24*60*60;
					$end=time();
					$map['post_time'] = array('between',array($start,$end));
				}
				$map['is_del'] = 0; 
				$map['city'] = $this->scity; 
				$list = M('weiba_post')->where($map)->order($order)->selectPage(20);
				$weiba_ids = getSubByKey($list['data'], 'weiba_id');
				$nameArr = $this->_getWeibaName($weiba_ids);
				foreach($list['data'] as $k=>$v){
					$list['data'][$k]['weiba'] = $nameArr[$v['weiba_id']];
					$list['data'][$k]['user'] = model( 'User' )->getUserInfo( $v['post_uid'] );
					$list['data'][$k]['replyuser'] = model( 'User' )->getUserInfo( $v['last_reply_uid'] );
					$images = matchImages($v['content']);
					if($images){
						 foreach($images as $img){
							$imgInfo = getThumbImage($img,200,200,true,false,true);
							if(!strpos($imgInfo['dirname'],'um/dialogs/emotion') && $imgInfo['width']>50){
								$list['data'][$k]['image'][] = $imgInfo['is_http']?$imgInfo['src']:UPLOAD_URL.$imgInfo['src'];
							}
						 }
						$list['data'][$k]['is_img']=1;
					 }
					
					$is_digg = M('weiba_post_digg')->where('post_id='.$v['post_id'].' and uid='.$this->mid)->find();
					$list['data'][$k]['digg']= $is_digg ? 'digg':'undigg';
					$list['data'][$k]['content'] = t($list['data'][$k]['content']);

					//去掉版块已经删除的
					$is_del = D('weiba')->where('weiba_id='.$v['weiba_id'])->getField('is_del');
					if($is_del == 1 || $is_del == null) unset($list['data'][$k]);
					//标签处理
					if($list['data'][$k]['tag_id']){
						$tagInfo = D('weiba_tag')->find($list['data'][$k]['tag_id']);
						$list['data'][$k]['tag_name'] = $tagInfo['name'];
					}
				
				}
				//dump($list);exit;
				$this->assign('searchResult',$list);
			}
			//城市列表
			//$list = M('city')->select();
			//$this -> assign('cityList',$list);
			$this->display();

		
	}
	/**
	 * 根据关键字进行搜索用户
	 * @return void
	 */
	public function person() {
		if($this->keywd != ""){
			if(t($_GET['Stime']) && t($_GET['Etime'])){
				$Stime = strtotime(t($_GET['Stime']));
				$Etime = strtotime(t($_GET['Etime']));
				$this->assign('Stime',t($_GET['Stime']));
				$this->assign('Etime',t($_GET['Etime']));
			}
			//关键字匹配 采用搜索引擎兼容函数搜索 后期可能会扩展为搜索引擎
			$map['uname'] = array(
					'like',
					'%'.$this->keywd.'%'
			);
			$order = 'last_login_time DESC';
			$list = model('User')->where($map)->order($order)->selectPage(20);
			
			$fids = getSubByKey($list['data'], 'uid');
			// 获取用户信息
			$followUserInfo = model('User')->getUserInfoByUids($fids);
			// 获取用户的统计数目
			$userData = model('UserData')->getUserDataByUids($fids);
			// 获取用户用户组信息
			$userGroupData = model('UserGroupLink')->getUserGroupData($fids);
			$this->assign('userGroupData',$userGroupData);
			// 获取用户的最后分享数据
			//$lastFeedData = model('Feed')->getLastFeed($fids);
			// 获取用户的关注信息状态值
			$followState = model('Follow')->getFollowStateByFids($this->mid, $fids);
			// 获取用户的备注信息
			$remarkInfo = model('Follow')->getRemarkHash($this->mid);
			// 获取用户标签
			$this->_assignUserTag($fids);
			// 关注分组信息
			$followGroupStatus = model('FollowGroup')->getGroupStatusByFids($this->mid, $fids);
			$this->assign('followGroupStatus', $followGroupStatus);
			// 组装数据
			foreach($list['data'] as $key => $value) {
				$list['data'][$key] = $followUserInfo[$value['uid']];
				$list['data'][$key] = array_merge($list['data'][$key], $userData[$value['uid']]);
				$list['data'][$key] = array_merge($list['data'][$key], array('feedInfo'=>$lastFeedData[$value['uid']]));
				$list['data'][$key] = array_merge($list['data'][$key], array('followState'=>$followState[$value['uid']]));
				$list['data'][$key] = array_merge($list['data'][$key], array('remark'=>$remarkInfo[$value['uid']]));
			}
			$this->assign('searchResult',$list);
			//dump($list);
		}
		$this->display();
		
	}
	private function _getWeibaName($weiba_ids){
		$weiba_ids = array_unique($weiba_ids);
		if(empty($weiba_ids)){
			return false;
		}
		$map['weiba_id'] = array('in', $weiba_ids);
		$names = M('weiba')->where($map)->field('weiba_id,weiba_name')->findAll();
		foreach ( $names as $n){
			$nameArr[$n['weiba_id']] = $n['weiba_name'];
		}
		return $nameArr;
	}
	private function _assignUserTag($uids) {
		$user_tag = model('Tag')->setAppName('User')->setAppTable('user')->getAppTags($uids);
		$this->assign('user_tag', $user_tag);
	}
}