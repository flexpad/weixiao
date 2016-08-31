<?php

namespace Addons\Weiba\Controller;

use Home\Controller\AddonsController;

class WapController extends AddonsController {
	// 首页
	public function index() {
		$db_prefix = C ( 'DB_PREFIX' );
		$token = get_token ();
		/*
		 * $header = $this->fetch ( $this->getAddonTemplate ( 'header' ) );
		 * echo $header;
		 *
		 * if (S ( 'w3g_go_index_' . $token )) {
		 * echo S ( 'w3g_go_index_' . $token );
		 * echo $this->fetch ( $this->getAddonTemplate ( 'footer' ) );
		 * exit ();
		 * }
		 */
		// 剔除不符合版块ID
		$fwid = D ( 'weiba' )->where ( 'is_del=1 OR status=0 AND token="' . $token . '"' )->order ( $order )->select ();
		$fids = getSubByKey ( $fwid, 'id' );
		if ($fids) {
			$maps ['weiba_id'] = array (
					'not in',
					$fids 
			);
		}
		$maps ['is_del'] = 0;
		$maps ['is_index'] = 1;
		$maps ['token'] = $token;
		$indexList = D ( 'weiba_post' )->where ( $maps )->order ( 'index_order asc' )->limit ( 4 )->select ();
		$this->assign ( 'indexList', $indexList );
		$sql = "SELECT a.* FROM `{$db_prefix}weiba_post` a, `{$db_prefix}weiba` b WHERE a.weiba_id=b.id AND ( b.`is_del` = 0 ) AND ( b.`status` = 1 ) AND ( a.`is_del` = 0 ) AND ( a.`token` = '" . $token . "' ) ORDER BY a.read_count desc,a.reply_count desc LIMIT 20";
		$list = D ( 'weiba_post' )->query ( $sql );
		foreach ( $list as &$v ) {
			$images = matchImages ( $v ['content'] );
			if ($images) {
				foreach ( $images as $img ) {
					$imgInfo = getThumbImage ( $img, 200, 200, true, false, true );
					if (! strpos ( $imgInfo ['dirname'], 'um/dialogs/emotion' ) && $imgInfo ['width'] > 50) {
						$v ['image'] [] = $imgInfo ['is_http'] ? $imgInfo ['src'] : UPLOAD_URL . $imgInfo ['src'];
					}
				}
				$v ['is_img'] = 1;
			}
			if ($v ['tag_id']) {
				$tagInfo = D ( 'weiba_tag' )->find ( $v ['tag_id'] );
				$v ['tag_name'] = $tagInfo ['name'];
			}
			// 活动
			if ($v ['is_event']) {
				$event_detail = D ( 'WeibaEvent' )->where ( array (
						'post_id' => $v ['id'] 
				) )->find ();
				$v ['event'] = $event_detail;
			}
		}
		$post_uids = getSubByKey ( $list, 'post_uid' );
		$reply_uids = getSubByKey ( $list, 'last_reply_uid' );
		$uids = array_unique ( array_filter ( array_merge ( $post_uids, $reply_uids ) ) );
		$this->_assignUserInfo ( $uids );
		$this->assign ( 'list', $list );
		/*
		 * $body = $this->fetch ( $this->getAddonTemplate ( 'index_body' ) );
		 * S ( 'w3g_go_index_' . $token, $body, 1800 );
		 * echo $body;
		 * echo $this->fetch ( $this->getAddonTemplate ( 'footer' ) );
		 */
		$this->display ();
	}
	public function forum() {
		// if (!($weibacate = S ( 'weiba_cate_list' ))) {
		$token = $map ['token'] = get_token ();
		$weibacate = M ( 'weiba_category' )->where ( $map )->order ( 'id' )->select ();
		foreach ( $weibacate as &$val ) {
			$val ['weibalist'] = D ( 'weiba' )->where ( "cid={$val['id']} AND is_del=0 and status=1 and token='" . $token . "'" )->order ( 'recommend desc,follower_count desc,thread_count' )->select ();
		}
		$this->assign ( 'weibacate', $weibacate );
		
		// 获取无分类的内容
		$list = D ( 'weiba' )->where ( "cid=0 AND is_del=0 and status=1 and token='" . $token . "'" )->order ( 'recommend desc,follower_count desc,thread_count' )->select ();
		$this->assign ( 'weibalist', $list );
		
		$this->display ();
	}
	public function detail() {
		$weiba_id = intval ( $_GET ['weiba_id'] );
		// dump($weiba_id);exit;
		$weiba_detail = $this->_top_link ( $weiba_id );
		// 吧主
		// dump($weiba_id);exit;
		$map ['weiba_id'] = $weiba_id;
		// $map ['level'] = array (
		// 'in',
		// '2,3'
		// );
		// dump($map);exit;
		$weiba_master = D ( 'weiba_follow' )->where ( $map )->order ( 'level desc,id' )->field ( 'follower_uid,level' )->select ();
		// dump($weiba_master);
		// $where['uid']=$weiba_master[0]['follower_uid'];
		$uid = M ( 'weiba' )->where ( $map )->getField ( 'admin_uid' );
		// dump($where);
		$nickname = get_nickname ( $uid );
		// dump($nickname);
		
		$this->assign ( 'weiba_master', $weiba_master );
		$this->assign ( 'nickname', $nickname );
		// 帖子
		$maps ['is_del'] = 0;
		
		if ($_GET ['order'] == '1') {
			$order = 'last_reply_time desc';
			$order .= ',post_time desc';
			$this->assign ( 'order', 'post_time' );
		} else {
			$order = 'FIELD(top,0,1) desc,FIELD(recommend+digest,0,1,2) desc';
			$order .= ',last_reply_time desc';
			$this->assign ( 'order', 'reply_time' );
		}
		$maps ['weiba_id'] = $weiba_id;
		
		// 过滤活动
		if ($_GET ['filter'] == 'event') {
			$maps ['is_event'] = 1;
			$this->assign ( 'filter', 'event' );
		}
		// 是否有活动
		$eventMap ['is_event'] = 1;
		$eventMap ['weiba_id'] = $weiba_id;
		$eventMap ['is_del'] = 0;
		$hasEvent = D ( 'WeibaPost' )->where ( $eventMap )->count ();
		$this->assign ( 'hasEvent', $hasEvent );
		
		// 标签
		$tagList = D ( 'weiba_tag' )->where ( array (
				'weiba_id' => $weiba_id 
		) )->select ();
		$this->assign ( 'tagList', $tagList );
		
		if ($_GET ['type'] == 'digest') {
			$maps ['digest'] = 1;
		}
		if ($_GET ['tag_id']) {
			$maps ['tag_id'] = $_GET ['tag_id'];
			$tagInfo = D ( 'weiba_tag' )->find ( $maps ['tag_id'] );
			$this->assign ( 'tag_id', $maps ['tag_id'] );
			$this->assign ( 'tag_name', $tagInfo ['name'] );
		}
		$list = D ( 'weiba_post' )->where ( $maps )->order ( $order )->selectpage ( 20, false, array (), true );
		/*
		 * ! $topPostList && $topPostList = array ();
		 * ! $innerTop && $innerTop = array ();
		 * ! $list ['data'] && $list ['data'] = array ();
		 * $list ['data'] = array_merge ( $topPostList, $innerTop, $list ['data'] );
		 * foreach ( $list ['data'] as &$v ) {
		 * $images = matchImages ( $v ['content'] );
		 * if ($images) {
		 * foreach ( $images as $img ) {
		 * $imgInfo = getThumbImage ( $img, 200, 200, true, false, true );
		 * if (! strpos ( $imgInfo ['dirname'], 'um/dialogs/emotion' ) && $imgInfo ['width'] > 50) {
		 * $v ['image'] [] = $imgInfo ['is_http'] ? $imgInfo ['src'] : UPLOAD_URL . $imgInfo ['src'];
		 * }
		 * }
		 * $v ['is_img'] = 1;
		 * }
		 * if ($v ['tag_id']) {
		 * $tagInfo = D ( 'weiba_tag' )->find ( $v ['tag_id'] );
		 * $v ['tag_name'] = $tagInfo ['name'];
		 * }
		 * // 活动
		 * if ($v ['is_event']) {
		 * $event_detail = D ( 'WeibaEvent' )->where ( array (
		 * 'post_id' => $v ['post_id']
		 * ) )->find ();
		 * $v ['event'] = $event_detail;
		 * }
		 * }
		 */
		// dump($list['data']);
		
		$post_uids = getSubByKey ( $list ['data'], 'post_uid' );
		$reply_uids = getSubByKey ( $list ['data'], 'last_reply_uid' );
		$uids = array_unique ( array_filter ( array_merge ( $post_uids, $reply_uids ) ) );
		$this->_assignUserInfo ( $uids );
		
		$this->_assignFollowState ( $weiba_id );
		
		// dump($list);
		$this->assign ( 'list', $list );
		$this->assign ( 'weiba_detail', $weiba_detail );
		$this->assign ( 'weiba_name', $weiba_detail ['weiba_name'] );
		$this->assign ( 'weiba_id', $weiba_id );
		
		$this->assign ( 'type', $_GET ['type'] == 'digest' ? 'digest' : 'all' );
		
		$this->display ();
	}
	public function postDetail() {
		$this->assign ( 'is_post_detail', 1 );
		$post_id = intval ( $_GET ['post_id'] );
		$post_detail = D ( 'weiba_post' )->where ( 'is_del=0 and id=' . $post_id )->find ();
		// dump($post_detail);exit;
		$weiba_detail = $this->_top_link ( $post_detail ['weiba_id'], true );
		if (! $post_detail || $weiba_detail ['is_del'])
			$this->error ( '帖子不存在或已被删除' );
		if (D ( 'weiba_favorite' )->where ( 'uid=' . $this->mid . ' AND post_id=' . $post_id )->find ()) {
			$post_detail ['favorite'] = 1;
		}
		/*
		 * if ($post_detail ['attach']) {
		 * $attachids = unserialize ( $post_detail ['attach'] );
		 * $attachinfo = model ( 'Attach' )->getAttachByIds ( $attachids );
		 * foreach ( $attachinfo as $ak => $av ) {
		 * $_attach = array (
		 * 'attach_id' => $av ['attach_id'],
		 * 'attach_name' => $av ['name'],
		 * 'attach_url' => getImageUrl ( $av ['save_path'] . $av ['save_name'] ),
		 * 'extension' => $av ['extension'],
		 * 'size' => $av ['size']
		 * );
		 * $post_detail ['attachInfo'] [$ak] = $_attach;
		 * }
		 * }
		 */
		/*
		 * if($post_detail ['img_ids']){
		 * $imgIds = explode(',', $post_detail ['img_ids']);
		 * $post_detail ['img_ids'] = array_filter($imgIds);
		 * }
		 */
		$post_detail ['content'] = html_entity_decode ( $post_detail ['content'], ENT_QUOTES, 'UTF-8' );
		// 标签
		if ($post_detail ['tag_id']) {
			$tagInfo = D ( 'weiba_tag' )->find ( $post_detail ['tag_id'] );
			$post_detail ['tag_name'] = $tagInfo ['name'];
		}
		$this->_assignFollowState ( $post_detail ['weiba_id'] );
		// dump($post_detail);
		$this->assign ( 'post_detail', $post_detail );
		// 活动
		/*
		 * if ($post_detail ['is_event']) {
		 * $event_detail = D ( 'WeibaEvent' )->where ( array (
		 * 'post_id' => $post_detail ['post_id']
		 * ) )->find ();
		 * $event_detail ['attrs'] = M ( 'weiba_event_attr' )->where ( array (
		 * 'event_id' => $event_detail ['event_id']
		 * ) )->select ();
		 * foreach ( $event_detail ['attrs'] as &$v ) {
		 * $v ['extra'] = explode ( '\r\n', $v ['extra'] );
		 * }
		 * // dump($event_detail);
		 * $this->assign ( 'event_detail', $event_detail );
		 * $isJoin = M ( 'weiba_event_user' )->where ( array (
		 * 'event_id' => $event_detail ['event_id'],
		 * 'uid' => $this->mid,
		 * 'is_refuse' => 0
		 * ) )->find ();
		 * $this->assign ( 'isJoin', $isJoin );
		 * }
		 */
		// dump($post_detail);
		$this->_addPostReadCount ( $post_id );
		$weiba_name = $weiba_detail ['weiba_name'];
		$this->assign ( 'weiba_id', $post_detail ['weiba_id'] );
		$this->assign ( 'weiba_name', $weiba_name );
		$this->assign ( 'weiba_detail', $weiba_detail );
		// 获得圈主uid
		$map ['weiba_id'] = $post_detail ['weiba_id'];
		$map ['level'] = array (
				'in',
				'2,3' 
		);
		$weiba_admin = getSubByKey ( D ( 'weiba_follow' )->where ( $map )->order ( 'level desc' )->field ( 'follower_uid' )->select (), 'follower_uid' );
		$weiba_manage = false;
		/*
		 * if (CheckWeibaPermission ( $weiba_admin, 0, 'weiba_global_top' ) || CheckWeibaPermission ( $weiba_admin, 0, 'weiba_top' ) || CheckWeibaPermission ( $weiba_admin, 0, 'weiba_recommend' ) || CheckWeibaPermission ( $weiba_admin, 0, 'weiba_edit' ) || CheckWeibaPermission ( $weiba_admin, 0, 'weiba_del' )) {
		 * $weiba_manage = true;
		 * }
		 */
		$this->assign ( 'weiba_manage', $weiba_manage );
		$this->assign ( 'weiba_admin', $weiba_admin );
		
		$this->assign ( 'nav', 'weibadetail' );
		// $this->user_group ( $post_detail ['post_uid'] );
		
		// 帖子点评
		unset ( $map );
		$map ['post_id'] = $post_id;
		$postcomment = M ( 'weiba_reply' )->where ( $map )->order ( 'ctime desc' )->selectPage ( 20 );
		// dump($postcomment['list_data']);
		foreach ( $postcomment ['list_data'] as $k => $v ) {
			$postcomment ['list_data'] [$k] ['user_info'] = getUserInfo ( $v ['uid'] );
			// $postcomment ['list_data'] [$k] ['replyuser'] = getUserInfo ( $v ['to_uid'] );
		}
		// dump($postcomment['list_data']);
		$this->assign ( 'postcomment', $postcomment );
		// $this->_assignFollowUidState ( array (
		// $post_detail ['post_uid']
		// ) );
		$this->assign ( 'type', $_GET ['type'] == 'digg' ? 'digg' : 'time' );
		
		$list_count = D ( 'weiba_reply' )->where ( array (
				'post_id' => $post_id 
		) )->count ();
		$this->assign ( 'list_count', $list_count );
		
		$is_digg = M ( 'weiba_post_digg' )->where ( 'post_id=' . $post_id . ' and uid=' . $this->mid )->find ();
		$this->assign ( 'is_digg', $is_digg ? 1 : 0 );
		$userInfo = getUserInfo ( $post_detail ['post_uid'] );
		$this->assign ( 'userInfo', $userInfo );
		
		// isweixin
		$isWeixin = isMobile ();
		$this->assign ( 'isWeixin', $isWeixin );
		
		$this->assign ( 'curtoken', session ( 'token' ) );
		
		$this->display ();
	}
	function _addPostReadCount($post_id) {
		if ($post_id) {
			// $ip = get_client_ip();
			// $cname = md5($post_id.'_'.$ip);
			// if(!cookie($cname)){
			D ( 'weiba_post' )->where ( 'id=' . $post_id )->setInc ( 'read_count' );
			// cookie($cname, 1, array('expire'=>3600*24));
			// }
		}
	}
	// 面包屑
	function _top_link($weiba_id, $detail = false) {
		$weiba_detail = D ( 'weiba' )->where ( 'is_del=0 and status=1 and id=' . $weiba_id )->find ();
		if (! $weiba_detail) {
			$this->error ( '该版块不存在或已被删除' );
		}
		$detail && $this->assign ( 'weiba_detail', $weiba_detail );
		
		$cate = M ( 'weiba_category' )->where ( "id='$weiba_detail[cid]'" )->find ();
		$this->assign ( 'category', $cate );
		$this->assign ( 'cate', $cate ['name'] );
		
		return $weiba_detail;
	}
	public function _assignUserInfo($uids) {
		! is_array ( $uids ) && $uids = explode ( ',', $uids );
		// var_dump($uids);
		foreach ( $uids as $uid ) {
			$user_info [$uid] = getUserInfo ( $uid );
		}
		$this->assign ( 'user_info', $user_info );
		// dump($user_info);exit;
	}
	
	/**
	 * 发布帖子
	 *
	 * @return void
	 */
	public function post() {
		if (! session ( 'post_before_url' )) {
			session ( 'post_before_url', t ( $_SERVER ["HTTP_REFERER"] ) );
		}
		
		$weiba_id = intval ( $_GET ['weiba_id'] );
		$weiba = D ( 'weiba' )->where ( 'id=' . $weiba_id )->find ();
		// if($weiba) {
		$this->assign ( 'weiba_id', $weiba_id );
		$this->assign ( 'weiba_name', $weiba ['weiba_name'] );
		$this->assign ( 'weiba', $weiba );
		// }else{
		$token = $map ['token'] = get_token ();
		$weibacate = M ( 'weiba_category' )->where ( $map )->order ( 'id' )->select ();
		
		foreach ( $weibacate as &$val ) {
			$val ['weibalist'] = D ( 'weiba' )->where ( "cid={$val['id']} AND is_del=0 and status=1 and token='{$token}'" )->order ( 'recommend desc,follower_count desc,thread_count' )->select ();
		}
		$this->assign ( 'weibacate', $weibacate );
		// }
		
		$this->display ();
	}
	/**
	 * 获取标签
	 *
	 * @return html
	 */
	public function getTagsByAjax() {
		$weiba_id = $_POST ['weiba_id'];
		$tag_id = $_POST ['tag_id'];
		$list = D ( 'weiba_tag' )->where ( array (
				'weiba_id' => $weiba_id 
		) )->select ();
		if (empty ( $list )) {
			echo '<option value="0">选择标签</option>';
			exit ();
		}
		
		$html = '<option value="0">选择标签</option>';
		foreach ( $list as $vo ) {
			if ($tag_id == $vo ['tag_id']) {
				$html .= '<option selected="selected" value="' . $vo ['tag_id'] . '">' . $vo ['name'];
			} else {
				$html .= '<option value="' . $vo ['tag_id'] . '">' . $vo ['name'];
			}
		}
		echo $html;
	}
	
	/**
	 * 执行发布帖子
	 *
	 * @return void
	 */
	public function doPost() {
		/*
		 * $this->need_login ();
		 *
		 * if (! CheckPermission ( 'weiba_normal', 'weiba_post' )) {
		 * $this->error ( '对不起，您没有权限进行该操作！', true );
		 * }
		 */
		$weibaid = intval ( $_POST ['weiba_id'] );
		$tag_id = intval ( $_POST ['tag_id'] );
		if (! $weibaid) {
			$this->error ( '请选择版块！', true );
		}
		$weiba = D ( 'weiba', 'weiba' )->where ( 'id=' . $weibaid )->find ();
		/*
		 * if (! CheckPermission ( 'core_admin', 'admin_login' )) {
		 * switch ($weiba ['who_can_post']) {
		 * case 1 :
		 * $map ['weiba_id'] = $weibaid;
		 * $map ['follower_uid'] = $this->mid;
		 * $res = D ( 'weiba_follow' )->where ( $map )->find ();
		 * if (! $res && ! CheckPermission ( 'core_admin', 'admin_login' )) {
		 * $this->error ( '对不起，您没有发帖权限，请关注该版块！', true );
		 * }
		 * break;
		 * case 2 :
		 * $map ['weiba_id'] = $weibaid;
		 * $map ['level'] = array (
		 * 'in',
		 * '2,3'
		 * );
		 * $weiba_admin = D ( 'weiba_follow' )->where ( $map )->order ( 'level desc' )->field ( 'follower_uid' )->select ();
		 * if (! in_array ( $this->mid, getSubByKey ( $weiba_admin, 'follower_uid' ) ) && ! CheckPermission ( 'core_admin', 'admin_login' )) {
		 * $this->error ( '对不起，您没有发帖权限，仅限管理员发帖！', true );
		 * }
		 * break;
		 * case 3 :
		 * $map ['weiba_id'] = $weibaid;
		 * $map ['level'] = 3;
		 * $weiba_admin = D ( 'weiba_follow' )->where ( $map )->order ( 'level desc' )->field ( 'follower_uid' )->find ();
		 * if ($this->mid != $weiba_admin ['follower_uid'] && ! CheckPermission ( 'core_admin', 'admin_login' )) {
		 * $this->error ( '对不起，您没有发帖权限，仅限圈主发帖！', true );
		 * }
		 * break;
		 * }
		 * }
		 */
		$checkContent = str_replace ( '&nbsp;', '', $_POST ['content'] );
		$checkContent = str_replace ( '<br />', '', $checkContent );
		$checkContent = str_replace ( '<p>', '', $checkContent );
		$checkContent = str_replace ( '</p>', '', $checkContent );
		$checkContents = preg_replace ( '/<img(.*?)src=/i', 'img', $checkContent );
		$checkContents = preg_replace ( '/<embed(.*?)src=/i', 'img', $checkContents );
		if (get_str_length ( $_POST ['title'] ) < 4 || get_str_length ( $_POST ['title'] ) > 30) { // 汉字和字母都为一个字
			$this->error ( '帖子标题限制4~30个字!', true );
		}
		if (strlen ( $checkContents ) == 0)
			$this->error ( '帖子内容不能为空', true );
		if ($_POST ['attach_ids']) {
			$attach = explode ( '|', $_POST ['attach_ids'] );
			foreach ( $attach as $k => $a ) {
				if (! $a) {
					unset ( $attach [$k] );
				}
			}
			$attach = array_map ( 'intval', $attach );
			$data ['attach'] = serialize ( $attach );
		}
		$data ['token'] = get_token ();
		$data ['weiba_id'] = $weibaid;
		$data ['title'] = safe ( $_POST ['title'] );
		$data ['content'] = safe ( $_POST ['content'] );
		$data ['post_uid'] = $this->mid;
		$data ['post_time'] = time ();
		$data ['last_reply_uid'] = $this->mid;
		$data ['last_reply_time'] = $data ['post_time'];
		$data ['tag_id'] = $tag_id;
		// $data ['img_ids'] =implode(',',$_POST ['img_ids']) ;
		
		$imgIds = explode ( ',', $_POST ['imageIds'] );
		foreach ( $imgIds as $imgId ) {
			$imgId = intval ( $imgId );
			if ($imgId > 0) {
				$imgsrc = get_cover_url ( $imgId );
				if ($imgsrc) {
					$data ['content'] .= '<p><img src="' . $imgsrc . '" /></p>';
				}
			}
		}
		
		$res = D ( 'weiba_post' )->add ( $data );
		if ($res) {
			D ( 'weiba' )->where ( 'id=' . $data ['weiba_id'] )->setInc ( 'thread_count' );
			$this->success ( '发布成功', addons_url ( 'Weiba://Wap/postDetail', array (
					'post_id' => intval ( $res ) 
			) ) );
		} else {
			$this->error ( '发布失败', true );
		}
	}
	function getWeibaByAjax() {
		$map ['is_del'] = 0;
		$map ['cid'] = intval ( $_POST ['cid'] );
		$list = D ( 'Weiba', 'weiba' )->where ( $map )->field ( 'weiba_id,weiba_name,cid' )->select ();
		
		if (empty ( $list )) {
			echo '';
			exit ();
		}
		
		$html = '<option value="0">请选择子版块</option>';
		foreach ( $list as $vo ) {
			$html .= '<option value="' . $vo ['weiba_id'] . '">' . $vo ['weiba_name'];
		}
		
		echo $html;
	}
	/**
	 * 收藏帖子
	 *
	 * @return void
	 */
	public function favorite() {
		$data ['post_id'] = intval ( $_POST ['post_id'] );
		$data ['weiba_id'] = intval ( $_POST ['weiba_id'] );
		$data ['post_uid'] = intval ( $_POST ['post_uid'] );
		$data ['uid'] = $this->mid;
		$data ['favorite_time'] = time ();
		if (D ( 'weiba_favorite' )->add ( $data )) {
			
			// 添加积分
			// model ( 'Credit' )->setUserCredit ( $this->mid, 'collect_topic' );
			// model ( 'Credit' )->setUserCredit ( $data ['post_uid'], 'collected_topic' );
			
			// model ( 'UserData' )->setCountByStep ( $data ['uid'], 'favorite_count' );
			echo 1;
		} else {
			echo 0;
		}
	}
	
	/**
	 * 取消收藏帖子
	 *
	 * @return void
	 */
	public function unfavorite() {
		$map ['post_id'] = intval ( $_POST ['post_id'] );
		$map ['uid'] = $this->mid;
		if (D ( 'weiba_favorite' )->where ( $map )->delete ()) {
			// model ( 'UserData' )->setCountByStep ( $map ['uid'], 'favorite_count', - 1 );
			echo 1;
		} else {
			echo 0;
		}
	}
	
	/**
	 * 我的
	 */
	public function my() {
		// $this->need_login ();
		$profile = getUserInfo ( $this->mid );
		$this->assign ( 'profile', $profile );
		$weiba_arr = getSubByKey ( D ( 'weiba', 'weiba' )->where ( 'is_del=0 and status=1' )->field ( 'id' )->select (), 'id' ); // 未删除且通过审核的版块
		$map ['weiba_id'] = array (
				'in',
				$weiba_arr 
		);
		$map ['is_del'] = 0;
		$type = in_array ( $_GET ['type'], array (
				'myPost',
				'myReply',
				'myWeiba',
				'myFavorite',
				'myFollowing',
				'myFavoriteEvent',
				'myJoinEvent' 
		) ) ? ($_GET ['type']) : 'index';
		switch ($type) {
			case 'myPost' :
				$map ['post_uid'] = $this->mid;
				$map ['is_event'] = 0;
				$post_list = D ( 'weiba_post' )->where ( $map )->order ( 'last_reply_time desc' )->selectPage ( 20 );
				// model ( 'UserData' )->setKeyValue ( $this->mid, 'unread_comment', 0 );
				break;
			case 'myReply' :
				$myreply = D ( 'weiba_reply' )->where ( 'uid=' . $this->mid )->order ( 'ctime desc' )->field ( 'post_id' )->select ();
				$map ['id'] = array (
						'in',
						array_unique ( getSubByKey ( $myreply, 'post_id' ) ) 
				);
				$map ['is_event'] = 0;
				$post_list = D ( 'weiba_post' )->where ( $map )->order ( 'last_reply_time desc' )->selectPage ( 20 );
				// model ( 'UserData' )->setKeyValue ( $this->mid, 'unread_reply_comment', 0 );
				break;
			case 'myFavorite' :
				$myFavorite = D ( 'weiba_favorite', 'weiba' )->where ( 'uid=' . $this->mid )->order ( 'favorite_time desc' )->select ();
				$map ['id'] = array (
						'in',
						getSubByKey ( $myFavorite, 'post_id' ) 
				);
				$map ['is_event'] = 0;
				$post_list = D ( 'weiba_post', 'weiba' )->where ( $map )->order ( 'post_time desc' )->selectPage ( 20 );
				break;
			case 'myWeiba' :
				$sfollow = D ( 'weiba_follow' )->where ( 'follower_uid=' . $this->mid )->select ();
				$sfollow = getSubByKey ( $sfollow, 'weiba_id' );
				$map ['id'] = array (
						'in',
						$sfollow 
				);
				$map ['status'] = 1;
				// dump($map);
				$post_list = D ( 'weiba' )->where ( $map )->order ( 'new_day desc, new_count desc ,recommend desc,follower_count desc,thread_count desc' )->selectPage ( 100 );
				break;
			case 'myFollowing' :
				$myFollow_arr = getSubByKey ( D ( 'weiba_follow', 'weiba' )->where ( 'follower_uid=' . $this->mid )->select (), 'weiba_id' );
				foreach ( $myFollow_arr as $v ) {
					if (in_array ( $v, $weiba_arr )) {
						$weibas [] = $v;
					}
				}
				$map ['weiba_id'] = array (
						'in',
						$weibas 
				);
				$post_list = D ( 'weiba_post', 'weiba' )->where ( $map )->order ( 'last_reply_time desc' )->selectPage ( 20 );
				break;
			case 'myJoinEvent' :
				$myjoin = D ( 'weiba_event_user' )->where ( array (
						'uid' => $this->mid,
						'is_refuse' => 0 
				) )->order ( 'ctime desc' )->field ( 'event_id' )->select ();
				$eventMap ['event_id'] = array (
						'in',
						array_unique ( getSubByKey ( $myjoin, 'event_id' ) ) 
				);
				$eventMap ['is_del'] = 0;
				$myJoinEvents = D ( 'weiba_event' )->where ( $eventMap )->field ( 'post_id' )->selectPage ( 20 );
				$map ['post_id'] = array (
						'in',
						array_unique ( getSubByKey ( $myJoinEvents ['data'], 'post_id' ) ) 
				);
				$map ['is_event'] = 1;
				$post_list = D ( 'weiba_post' )->where ( $map )->order ( 'last_reply_time desc' )->select ();
				$myJoinEvents ['data'] = $post_list;
				$post_list = $myJoinEvents;
				model ( 'UserData' )->setKeyValue ( $this->mid, 'unread_event_comment', 0 );
				model ( 'UserData' )->setKeyValue ( $this->mid, 'unread_event_reply_comment', 0 );
				break;
			case 'myFavoriteEvent' :
				$myFavorite = D ( 'weiba_favorite', 'weiba' )->where ( 'uid=' . $this->mid )->order ( 'favorite_time desc' )->select ();
				$map ['id'] = array (
						'in',
						getSubByKey ( $myFavorite, 'post_id' ) 
				);
				$map ['is_event'] = 1;
				$post_list = D ( 'weiba_post', 'weiba' )->where ( $map )->order ( 'post_time desc' )->selectPage ( 20 );
				break;
		}
		// if($postList['nowPage']==1){ //列表第一页加上全局置顶的帖子
		// $topPostList = D('weiba_post')->where('top=2 and is_del=0')->order('post_time desc')->select();
		// !$topPostList && $topPostList = array();
		// !$postList['data'] && $postList['data'] = array();
		// $postList['data'] = array_merge($topPostList,$postList['data']);
		// }
		$weiba_ids = getSubByKey ( $post_list ['data'], 'weiba_id' );
		$nameArr = $this->_getWeibaName ( $weiba_ids );
		foreach ( $post_list ['list_data'] as $k => $v ) {
			$post_list ['list_data'] [$k] ['weiba'] = $nameArr [$v ['weiba_id']];
			$post_list ['list_data'] [$k] ['user'] = getUserInfo ( $v ['post_uid'] );
			$post_list ['list_data'] [$k] ['replyuser'] = getUserInfo ( $v ['last_reply_uid'] );
			/*
			 * $images = matchImages ( $v ['content'] );
			 * if ($images) {
			 * foreach ( $images as $img ) {
			 * $imgInfo = getThumbImage ( $img, 200, 200, true, false, true );
			 * if (! strpos ( $imgInfo ['dirname'], 'um/dialogs/emotion' ) && $imgInfo ['width'] > 50) {
			 * $post_list ['list_data'] [$k] ['image'] [] = $imgInfo ['is_http'] ? $imgInfo ['src'] : UPLOAD_URL . $imgInfo ['src'];
			 * }
			 * }
			 * }
			 */
			// $image = getEditorImages($v['content']);
			// !empty($image) && $post_list['data'][$k]['image'] = array($image);
			// dump($post_list['data'][$k]['image']);
			// 标签
			if ($v ['tag_id']) {
				$tagInfo = D ( 'weiba_tag' )->find ( $v ['tag_id'] );
				$post_list ['data'] [$k] ['tag_name'] = $tagInfo ['name'];
			}
			// 活动
			if ($v ['is_event']) {
				$event_detail = D ( 'WeibaEvent' )->where ( array (
						'post_id' => $v ['post_id'] 
				) )->find ();
				$post_list ['data'] [$k] ['event'] = $event_detail;
			}
		}
		// dump($post_list);
		
		$this->assign ( 'post_list', $post_list );
		$this->assign ( 'type', $type );
		$this->assign ( 'nav', 'myweiba' );
		$this->display ();
	}
	
	/**
	 * 关注版块
	 */
	public function doFollowWeiba() {
		if (! ($this->mid > 0)) {
			$this->ajaxReturn ( - 1, '请先登录再关注', false );
			exit ();
		}
		$res = D ( 'weiba', 'weiba' )->doFollowWeiba ( $this->mid, intval ( $_REQUEST ['weiba_id'] ) );
		// 清理插件缓存
		$key = '_getRelatedGroup_' . $this->mid . '_' . date ( 'Ymd' ); // 达人
		S ( $key, null );
		$this->ajaxReturn ( $res, D ( 'weiba', 'weiba' )->getError (), false !== $res );
	}
	
	/**
	 * 取消关注版块
	 */
	public function unFollowWeiba() {
		if (! ($this->mid > 0)) {
			$this->ajaxReturn ( - 1, '请先登录再关注', false );
			exit ();
		}
		$res = D ( 'weiba', 'weiba' )->unFollowWeiba ( $this->mid, intval ( $_GET ['weiba_id'] ) );
		$this->ajaxReturn ( $res, D ( 'weiba', 'weiba' )->getError (), false !== $res );
	}
	private function _getWeibaInfo(&$post_list) {
		// 读取版块详细信息
		$weiba_ids = getSubByKey ( $post_list ['data'], 'weiba_id' );
		$nameArr = $this->_getWeibaName ( $weiba_ids );
		foreach ( $post_list ['data'] as $k => $v ) {
			$post_list ['data'] [$k] ['weiba'] = $nameArr [$v ['weiba_id']];
			$post_list ['data'] [$k] ['user'] = model ( 'User' )->getUserInfo ( $v ['post_uid'] );
			$post_list ['data'] [$k] ['replyuser'] = model ( 'User' )->getUserInfo ( $v ['last_reply_uid'] );
			// $images = matchImages($v['content']);
			// $images[0] && $post_list['data'][$k]['image'] = array_slice( $images , 0 , 5 );
			$image = getEditorImages ( $v ['content'] );
			! empty ( $image ) && $post_list ['data'] [$k] ['image'] = array (
					$image 
			);
		}
	}
	
	// 获取版块名称
	private function _getWeibaName($weiba_ids) {
		$weiba_ids = array_unique ( $weiba_ids );
		if (empty ( $weiba_ids )) {
			return false;
		}
		$map ['weiba_id'] = array (
				'in',
				$weiba_ids 
		);
		$names = D ( 'weiba' )->where ( $map )->field ( 'id,weiba_name' )->select ();
		foreach ( $names as $n ) {
			$nameArr [$n ['id']] = $n ['weiba_name'];
		}
		return $nameArr;
	}
	
	/**
	 * 获取uid与版块的关注状态
	 *
	 * @return void
	 */
	private function _assignFollowState($weiba_ids) {
		// 批量获取uid与版块的关注状态
		$follow_state = D ( 'weiba' )->getFollowStateByWeibaids ( $this->mid, $weiba_ids );
		// dump($follow_state);exit;
		$this->assign ( 'follow_state', $follow_state );
	}
	public function reply() {
		if (! ($this->mid > 0)) {
			exit ( '请先登录' );
		}
		$post_id = intval ( $_GET ['post_id'] );
		$map ['id'] = $post_id;
		$map ['lock'] = 0;
		$map ['is_del'] = 0;
		$post = D ( 'weiba_post', 'weiba' )->where ( $map )->find ();
		if (! $post)
			exit ( '帖子不存在！' );
		if (! empty ( $_GET ['to_reply_id'] )) {
			$reply_id = intval ( $_GET ['to_reply_id'] );
			$map = array ();
			$map ['reply_id'] = $reply_id;
			$map ['is_del'] = 0;
			$reply = D ( 'weiba_reply', 'weiba' )->where ( $map )->find ();
			if ($reply) {
				$this->assign ( 'reply', $reply );
			}
		}
		$list_count = D ( 'weiba_reply', 'weiba' )->where ( array (
				'post_id' => $post_id 
		) )->count ();
		$this->assign ( 'list_count', $list_count );
		$this->assign ( 'post', $post );
		$this->display ();
	}
	public function addPostDigg() {
		$maps ['id'] = $map ['post_id'] = intval ( $_POST ['row_id'] );
		$map ['uid'] = $this->mid;
		$hasdigg = M ( 'weiba_post_digg' )->where ( $map )->find ();
		$weiba = M ( 'weiba_post' )->where ( 'id=' . $map ['post_id'] )->find ();
		// $is_follow = $this->is_follow($weiba['weiba_id']);
		// if(!$is_follow){
		// echo 0;exit;
		// }
		
		$map ['cTime'] = time ();
		$result = M ( 'weiba_post_digg' )->add ( $map );
		if ($result && ! $hasdigg) {
			$post = M ( 'weiba_post' )->where ( $maps )->find ();
			M ( 'weiba_post' )->where ( $maps )->setField ( 'praise', $post ['praise'] + 1 );
			echo 1;
		} else {
			echo 0;
		}
	}
	public function delPostDigg() {
		$maps ['id'] = $map ['post_id'] = intval ( $_POST ['row_id'] );
		$map ['uid'] = $this->mid;
		$hasdigg = M ( 'weiba_post' )->where ( 'id=' . $map ['post_id'] )->find ();
		// $is_follow = $this->is_follow($hasdigg['weiba_id']);
		// if(!$is_follow){
		// echo 0;exit;
		// }
		
		$result = M ( 'weiba_post_digg' )->where ( $map )->delete ();
		if ($result) {
			$post = M ( 'weiba_post' )->where ( $maps )->find ();
			M ( 'weiba_post' )->where ( $maps )->setField ( 'praise', $post ['praise'] - 1 );
			echo 1;
		} else {
			echo 0;
		}
	}
	public function sendAttachMail() {
		$post_id = $data ['post_id'] = $_POST ['post_id'];
		$attach_id = $data ['attach_id'] = $_POST ['attach_id'];
		$data ['email'] = $_POST ['email'];
		if (empty ( $data ['email'] ) && ! ereg ( "^[-a-zA-Z0-9_\.]+\@([0-9A-Za-z][0-9A-Za-z-]+\.)+[A-Za-z]{2,5}$", $data ['email'] )) {
			// TODO:邮箱格式验证
			echo - 1;
			exit ();
		}
		$post_detail = D ( 'weiba_post' )->where ( 'is_del=0 and id=' . $post_id )->find ();
		/*
		 * if ($post_detail ['attach']) {
		 * $attachids = unserialize ( $post_detail ['attach'] );
		 * $attachinfo = model ( 'Attach' )->getAttachByIds ( $attachids );
		 * foreach ( $attachinfo as $ak => $av ) {
		 * //$_attach = array (
		 * // 'attach_id' => $av ['attach_id'],
		 * // 'attach_name' => $av ['name'],
		 * // 'attach_url' => getImageUrl ( $av ['save_path'] . $av ['save_name'] ),
		 * // 'extension' => $av ['extension'],
		 * // 'size' => $av ['size']
		 * //);
		 *
		 * $downlink = U('widget/Upload/down',array('attach_id'=>$av ['attach_id']));
		 * $attachStr .= '《'.$av ['name'].'》:<a href="'.$downlink.'">'.$downlink.'</a><br/>';
		 * }
		 * }
		 */
		$downlink = U ( 'widget/Upload/down', array (
				'attach_id' => $attach_id 
		) );
		$attachStr .= '<a href="' . $downlink . '">' . $downlink . '</a><br/>';
		// 城市
		$tokenInfo = get_tokeninfo ();
		$data ['uid'] = $mid;
		if ($tokenInfo) {
			$data ['title'] = "来自【新东方论坛-" . $tokenInfo ['token'] . "站】的附件";
		} else {
			$data ['title'] = "来自【新东方论坛】的附件";
		}
		$data ['body'] = "新东方用户，您好：<br/><br/><span style='padding-left:3em'>您查看帖子【" . $post_detail ['title'] . "】的资料下载地址为：";
		$data ['body'] .= $attachStr;
		$data ['body'] .= '</span><br/><span style="color:#888888">（如果链接点击无效，请将它拷贝到浏览器的地址栏中。）</span><br/><br/><br/>新东方教育科技集团<br/><br/>';
		$res = model ( 'SendAttach' )->sendEmail ( $data );
		if ($res) {
			// 记录下载
			$logRes = model ( 'DownloadLog' )->doLog ( $attach_id, $post_id, 2 );
			echo 1;
		} else {
			echo 0;
		}
	}
	// 记录下载 预览时的ajax请求
	public function doDownloadLog() {
		$aid = intval ( $_POST ['attach_id'] );
		$post_id = intval ( $_POST ['post_id'] );
		$logRes = model ( 'DownloadLog' )->doLog ( $aid, $post_id, 1 );
		echo $logRes;
	}
	// 我的下载
	public function myDownload() {
		$type = $_GET ['type'];
		$list = model ( 'DownloadLog' )->getDownloadListByUid ( $this->mid );
		// dump($list);
		$this->assign ( 'type', $type );
		$this->assign ( 'list', $list );
		$this->display ();
	}
	// 活动报名
	public function doJoinEvent() {
		$map ['event_id'] = $event_id = intval ( $_POST ['event_id'] );
		$fields = M ( 'weiba_event_attr' )->where ( $map )->order ( 'sort asc, id asc' )->select ();
		$post = array ();
		foreach ( $fields as $vo ) {
			$value = $_POST [$vo ['name']];
			$post [$vo ['name']] = $_POST [$vo ['name']];
			unset ( $_POST [$vo ['name']] );
		}
		$data ['name'] = $_POST ['name'];
		$data ['phone'] = $_POST ['phone'];
		$data ['uid'] = $this->mid;
		$data ['ctime'] = time ();
		$data ['event_id'] = $event_id;
		$data ['value'] = serialize ( $post );
		
		$res = M ( 'weiba_event_user' )->where ( array (
				'event_id' => $event_id,
				'uid' => $this->mid,
				'is_refuse' => 0 
		) )->find ();
		if ($res) {
			$return ['status'] = 0;
			$return ['info'] = "你已经报名";
		} else {
			$res2 = M ( 'weiba_event_user' )->where ( array (
					'event_id' => $event_id 
			) )->add ( $data );
			if ($res2) {
				D ( 'WeibaEvent' )->where ( array (
						'event_id' => $event_id 
				) )->setInc ( 'join_count' );
				$return ['status'] = 1;
				$return ['info'] = "成功提交";
			} else {
				$return ['status'] = 0;
				$return ['info'] = "提交失败，请重试";
			}
		}
		exit ( json_encode ( $return ) );
	}
	public function getLocaltoken() {
		$locationtoken = getUsertoken ();
		if ($locationtoken) {
			echo $locationtoken ['token'];
		} else {
			echo 0;
		}
		session ( "init_token", - 1 );
	}
	public function tokenList() {
		$locationtoken = getUsertoken ();
		if ($locationtoken) {
			$this->assign ( 'curtoken', $locationtoken ['token'] );
		}
		
		$list = M ( 'token' )->select ();
		$this->assign ( 'list', $list );
		$token = get_token ();
		session ( "init_token", $token );
		$this->display ();
	}
	public function switchtoken() {
		$token = get_token ();
		session ( "init_token", $token );
		// dump(session('init_token'));
		// dump(session('token'));
		// exit;
		redirect ( U ( 'index' ) );
		exit ();
	}
	// 详情页预览
	public function postDetailPreview() {
		$userInfo = D ( 'User' )->getUserInfo ( $this->mid );
		$this->assign ( 'userInfo', $userInfo );
		$this->display ();
	}
	public function profile() {
		$uid = intval ( $_GET ['uid'] ) ? intval ( $_GET ['uid'] ) : $this->mid;
		// 判断隐私设置
		// $userPrivacy = $this->privacy ( $uid );
		// $isAllowed = 0;
		// $isMessage = 1;
		// ($userPrivacy ['space'] == 1) && $isMessage = 0;
		// $this->assign ( 'sendmsg', $isMessage );
		
		// if ($userPrivacy === true || $userPrivacy ['space'] == 0) {
		// $isAllowed = 1;
		// }
		// $this->assign ( 'isAllowed', $isAllowed );
		$this->assign ( 'uid', $uid );
		// 获取我的个人信息
		// $user = getUserInfo($uid);
		$data ['user_id'] = $uid;
		$data ['page'] = 1;
		$profile = getUserInfo ( $uid );
		// dump($profile);exit;
		// if(!$profile['uname']){
		// redirect(U('w3g/Public/home'), 3, '参数错误');
		// }
		$this->assign ( 'profile', $profile );
		if ($this->mid == $this->uid) {
			$this->assign ( 'datatitle', '用户资料' );
		} else {
			$this->assign ( 'datatitle', '用户资料' );
		}
		// 他的帖子
		$weiba_arr = getSubByKey ( D ( 'weiba' )->where ( 'is_del=0 and status=1' )->field ( 'id' )->select (), 'id' ); // 未删除且通过审核的版块
		$map ['weiba_id'] = array (
				'in',
				$weiba_arr 
		);
		$map ['is_del'] = 0;
		$map ['post_uid'] = $uid;
		$post_list = D ( 'weiba_post' )->where ( $map )->order ( 'post_time desc' )->selectPage ( 20 );
		$weiba_ids = getSubByKey ( $post_list ['data'], 'weiba_id' );
		$nameArr = $this->_getWeibaName ( $weiba_ids );
		foreach ( $post_list ['list_data'] as $k => $v ) {
			$post_list ['list_data'] [$k] ['weiba'] = $nameArr [$v ['weiba_id']];
			$post_list ['list_data'] [$k] ['user'] = getUserInfo ( $v ['post_uid'] );
			$post_list ['list_data'] [$k] ['replyuser'] = getUserInfo ( $v ['last_reply_uid'] );
			$images = matchImages ( $v ['content'] );
			if ($images) {
				foreach ( $images as $img ) {
					$imgInfo = getThumbImage ( $img, 200, 200, true, false, true );
					if (! strpos ( $imgInfo ['dirname'], 'um/dialogs/emotion' ) && $imgInfo ['width'] > 50) {
						$post_list ['list_data'] [$k] ['image'] [] = $imgInfo ['is_http'] ? $imgInfo ['src'] : UPLOAD_URL . $imgInfo ['src'];
					}
				}
			}
			if ($v ['tag_id']) {
				$tagInfo = D ( 'weiba_tag' )->find ( $v ['tag_id'] );
				$post_list ['list_data'] [$k] ['tag_name'] = $tagInfo ['name'];
			}
		}
		// 查找赞数
		$map2 ['uid'] = $uid;
		$map2 ['is_del'] = 0;
		$praiseCount = D ( 'weiba_post', 'weiba' )->where ( $map )->sum ( 'praise' );
		$praiseCommentCount = D ( 'weiba_reply', 'weiba' )->where ( $map2 )->sum ( 'digg_count' );
		$this->assign ( 'praiseCount', $praiseCount + $praiseCommentCount );
		
		$this->assign ( 'post_list', $post_list );
		
		$this->display ();
	}
	// 通知数目
	public function MCount() {
		/*
		 * if(!($this->mid>0)){
		 * echo json_encode ( array () );
		 * exit;
		 * }
		 * // $amap['uid'] = $this->mid;
		 * $mcount = D ( 'UserCount' )->getUnreadCount ( $this->mid );
		 * $this->assign ( 'mcount', $mcount );
		 */
		$mcount = array ();
		echo json_encode ( $mcount );
	}
	// 关注微吧
	public function followWeiba() {
		$weiba_id = intval ( $_GET ['weiba_id'] );
		$is_follow = $_POST ['is_follow'];
		if ($is_follow) {
			$res = D ( 'weiba' )->unFollowWeiba ( $this->mid, $weiba_id );
		} else {
			$res = D ( 'weiba' )->doFollowWeiba ( $this->mid, $weiba_id );
		}
		if ($res) {
			$this->success ( $is_follow ? '取消关注成功' : '关注成功' );
		} else {
			$this->error ( '操作失败' );
		}
	}
	/**
	 * 添加帖子回复的操作
	 *
	 * @return array 评论添加状态和提示信息
	 */
	public function addReply() {
		// echo $_POST['post_id'];exit;
		// if( !$this->mid || !CheckPermission('weiba_normal','weiba_reply')){
		// return;
		// }
		$is_lock = M ( 'weiba_blacklist' )->where ( 'weiba_id=' . intval ( $_POST ['weiba_id'] ) . ' and uid=' . intval ( $_POST ['post_uid'] ) )->find ();
		if ($is_lock) {
			$return ['status'] = 0;
			$return ['data'] = '您是黑名单用户没有发帖权限！';
			exit ( json_encode ( $return ) );
		}
		$return = array (
				'status' => 0,
				'data' => L ( 'PUBLIC_CONCENT_IS_ERROR' ) 
		);
		$data ['weiba_id'] = intval ( $_POST ['weiba_id'] );
		$data ['post_id'] = intval ( $_POST ['post_id'] );
		$data ['post_uid'] = intval ( $_POST ['post_uid'] );
		$data ['to_reply_id'] = intval ( $_POST ['to_reply_id'] );
		$data ['to_uid'] = intval ( $_POST ['to_uid'] );
		$data ['uid'] = $this->mid;
		$data ['ctime'] = time ();
		$data ['content'] = safe ( $_POST ['content'] );
		$data ['attach_id'] = intval ( $_POST ['attach_id'] );
		
		// $filterContentStatus = filter_words($data['content']);
		// if (!$filterContentStatus['status']) {
		// exit(json_encode(array('status'=>0, 'data'=>$filterContentStatus['data'])));
		// }
		// $data['content'] = $filterContentStatus['data'];
		
		if (isSubmitLocked ()) {
			$return ['status'] = 0;
			$return ['data'] = '发布内容过于频繁，请稍后再试！';
			exit ( json_encode ( $return ) );
		}
		$data ['comment_id'] = 0;
		if ($data ['reply_id'] = D ( 'weiba_reply' )->add ( $data )) {
			
			// 锁定发布
			lockSubmit ();
			
			// 更新版块今日新帖
			D ( 'Weiba' )->setNewcount ( $data ['weiba_id'] );
			
			// 添加积分
			// model('Credit')->setUserCredit(intval($_POST['post_uid']),'comment_topic');
			// model('Credit')->setUserCredit($data['to_uid'],'commented_topic');
			
			$map ['last_reply_uid'] = $this->mid;
			$map ['last_reply_time'] = $data ['ctime'];
			$map ['reply_count'] = array (
					'exp',
					"reply_count+1" 
			);
			$map ['reply_all_count'] = array (
					'exp',
					"reply_all_count+1" 
			);
			D ( 'weiba_post' )->where ( 'id=' . $data ['post_id'] )->save ( $map );
			// 同步到分享评论
			// $feed_id = intval($_POST['feed_id']);
			$datas ['app'] = 'weiba';
			$datas ['table'] = 'feed';
			$datas ['content'] = safe ( $data ['content'] );
			$datas ['app_uid'] = intval ( $_POST ['post_uid'] );
			$datas ['is_event'] = intval ( $_POST ['is_event'] );
			$datas ['row_id'] = intval ( $_POST ['feed_id'] );
			$datas ['to_comment_id'] = $data ['to_reply_id'] ? D ( 'weiba_reply' )->where ( 'reply_id=' . $data ['to_reply_id'] )->getField ( 'comment_id' ) : 0;
			$datas ['to_uid'] = intval ( $_POST ['to_uid'] );
			$datas ['uid'] = $this->mid;
			$datas ['ctime'] = time ();
			$datas ['client_type'] = 0;
			$data ['cancomment'] = 1;
			$data ['list_count'] = intval ( $_POST ['list_count'] );
			// 解锁
			unlockSubmit ();
			
			// if($comment_id = model('Comment')->addComment($datas)){
			// $data1['comment_id'] = $comment_id;
			// $data1['storey'] = model('Comment')->where('comment_id='.$comment_id)->getField('storey');
			// D('weiba_reply','weiba')->where('reply_id='.$data['reply_id'])->save($data1);
			// 给应用UID添加一个未读的评论数
			/*
			 * if($GLOBALS['ts']['mid'] != $datas['app_uid'] && $datas['app_uid'] != '') {
			 * !$notCount && model('UserData')->updateKey('unread_comment', 1, true, $datas['app_uid']);
			 * }
			 * if (! empty ( $datas ['to_uid'] )) {
			 * model ( 'UserData' )->updateKey ( 'unread_reply_comment', 1, true, $datas ['to_uid'] );
			 * }
			 */
			// model('Feed')->cleanCache($datas['row_id']);
			// }
			// 转发到我的分享
			/*
			 * if($_POST['ifShareFeed'] == 1) {
			 * $commentInfo = model('Source')->getSourceInfo($datas['table'], $datas['row_id'], false, $datas['app']);
			 * $oldInfo = isset($commentInfo['sourceInfo']) ? $commentInfo['sourceInfo'] : $commentInfo;
			 * // 根据评论的对象获取原来的内容
			 * $s['sid'] = $data['post_id'];
			 * $s['app_name'] = 'weiba';
			 * if (!empty ( $data ['to_comment_id'] )) {
			 * $replyInfo = model ( 'Comment' )->init ( $data ['app'], $data ['table'] )->getCommentInfo ( $data ['to_comment_id'], false );
			 * $data ['content'] .= $replyInfo ['content'];
			 * }
			 * $s ['body'] = $data ['content'];
			 * $s['type'] = 'weiba_post';
			 * $s['comment'] = $data['comment_old'];
			 * // 去掉回复用户@
			 * $lessUids = array();
			 * if(!empty($data['to_uid'])) {
			 * $lessUids[] = $data['to_uid'];
			 * }
			 * // 如果为原创分享，不给原创用户发送@信息
			 * if($oldInfo['feedtype'] == 'post' && empty($data['to_uid'])) {
			 * $lessUids[] = $oldInfo['uid'];
			 * }
			 * unlockSubmit();
			 * model('Share')->shareFeed($s,'comment', $lessUids);
			 * }
			 */
			$data ['feed_id'] = $datas ['row_id'];
			$data ['comment_id'] = $comment_id;
			$data ['storey'] = $data1 ['storey'];
			
			/*
			 * $data['attach_info'] = model('Attach')->getAttachById($data['attach_id']);
			 * if ($data['attach_info']['attach_type'] == 'weiba_comment_image' || $data['attach_info']['attach_type'] == 'feed_image') {
			 * $data['attach_info']['attach_url'] = getImageUrl($data['attach_info']['save_path'].$data['attach_info']['save_name'], 590);
			 * }
			 */
			$return ['status'] = 1;
			// $return['data'] = $this->parseReply($data);
		}
		
		echo json_encode ( $return );
		exit ();
	}
	
	/**
	 * 删除回复(在分享评论删除中同步删除版块回复)
	 *
	 * @return bool true or false
	 */
	public function delReply() {
		// if ( !CheckPermission('core_admin','comment_del') ){
		$map ['reply_id'] = intval ( $_POST ['reply_id'] );
		D ( 'weiba_reply' )->where ( $map )->delete ();
		echo 1;
	}
}