<?php

namespace Addons\PublicBind\Controller;

use Home\Controller\AddonsController;

class PublicBindController extends AddonsController {
	function setTicket() {
		include_once (ONETHINK_ADDON_PATH . 'PublicBind/wxBizMsgCrypt.php');
		// 第三方发送消息给公众平台
		$encodingAesKey = 'DfEqNBRvzbg8MJdRQCSGyaMp6iLcGOldKFT0r8I6Tnp';
		$token = 'weiphp';
		$appId = C ( 'COMPONENT_APPID' );
		
		$timeStamp = empty ( $_GET ['timestamp'] ) ? "" : trim ( $_GET ['timestamp'] );
		$nonce = empty ( $_GET ['nonce'] ) ? "" : trim ( $_GET ['nonce'] );
		$msg_sign = empty ( $_GET ['msg_signature'] ) ? "" : trim ( $_GET ['msg_signature'] );
		$encryptMsg = file_get_contents ( 'php://input' );
		$pc = new \WXBizMsgCrypt ( $token, $encodingAesKey, $appId );
		
		// 第三方收到公众号平台发送的消息
		$msg = '';
		$errCode = $pc->decryptMsg ( $msg_sign, $timeStamp, $nonce, $encryptMsg, $msg );
		if ($errCode == 0) {
			$data = $this->_xmlToArr ( $msg );
			if (isset ( $data ['ComponentVerifyTicket'] )) {
				$map ['name'] = 'PublicBind';
				$config = M ( 'addons' )->where ( $map )->getField ( 'config' );
				$config = ( array ) json_decode ( $config, true );
				
				$config ['ComponentVerifyTicket'] = $data ['ComponentVerifyTicket'];
				$save ['config'] = json_encode ( $config );
				M ( 'addons' )->where ( $map )->save ( $save );
			} elseif ($data ['InfoType'] == 'unauthorized') {
				// 在公众号后台取消授权后，同步把系统里的公众号删除掉，并更新相关用户缓存
				$map ['appid'] = $data ['AuthorizerAppid'];
				$map2 ['mp_id'] = M ( 'public' )->where ( $map )->getField ( 'id' );
				if ($map2 ['mp_id']) {
					$uids = M ( 'public_link' )->where ( $map2 )->getFields ( 'uid' );
					
					M ( 'public' )->where ( $map )->limit ( 1 )->delete ();
					M ( 'public_link' )->where ( $map2 )->delete ();
					
					foreach ( $uids as $uid ) {
						D ( 'Common/User' )->getUserInfo ( $uid, true );
					}
				}
			}
			
			echo 'success';
		} else {
			addWeixinLog ( '解密后失败：' . $errCode, 'setTicket_error' );
		}
	}
	function bind() {
		$res = D ( 'Addons://PublicBind/PublicBind' )->bind ();
		if (! $res ['status']) {
			$this->error ( $res ['msg'] );
			exit ();
		}
		$this->assign ( 'jumpURL', $res ['jumpURL'] );
		
		$this->display ();
	}
	// 用户授权后,获取公众号信息
	function after_auth() {
		// auth_code=xxx&expires_in=600
		$auth_code = I ( 'auth_code' );
		$auth_info = D ( 'Addons://PublicBind/PublicBind' )->getAuthInfo ( $auth_code );
		
		$public_info = D ( 'Addons://PublicBind/PublicBind' )->getPublicInfo ( $auth_info ['authorization_info'] ['authorizer_appid'] );
		
		$map ['public_id'] = $data ['public_id'] = $data ['token'] = $public_info ['authorizer_info'] ['user_name'];
		$data ['public_name'] = $public_info ['authorizer_info'] ['nick_name'];
		$data ['wechat'] = $public_info ['authorizer_info'] ['alias'];
		if ($public_info ['authorizer_info'] ['service_type_info'] ['id'] == 2) { // 服务号
			$data ['type'] = 2;
		} else { // 订阅号
			$data ['type'] = 0;
		}
		if ($public_info ['authorizer_info'] ['verify_type_info'] ['id'] != - 1) { // 已认证
			$data ['type'] += 1;
		}
		$data ['appid'] = $public_info ['authorization_info'] ['authorizer_appid'];
		if($this->mid>0) {
			$data ['uid'] = $this->mid;
		}
		$data ['is_bind'] = 1;
		$data ['encodingaeskey'] = 'DfEqNBRvzbg8MJdRQCSGyaMp6iLcGOldKFT0r8I6Tnp';
		$data ['cTime'] = NOW_TIME;
		$data ['authorizer_refresh_token'] = $auth_info ['authorization_info'] ['authorizer_refresh_token'];
		
		$info = M ( 'public' )->where ( $map )->find ();
		if ($info) {
			M ( 'public' )->where ( $map )->save ( $data );
			D('Common/Public')->clear($info['id']);
			$url = U ( 'Home/Public/lists' );
		} else {
			
			$param ['id'] = $link ['mp_id'] = M ( 'public' )->add ( $data );
			$link ['uid'] = $this->mid;
			$link ['is_creator'] = 1;
			M ( 'public_link' )->add ( $link );
			
			$map2 ['uid'] = $this->mid;
			M ( 'manager' )->where ( $map2 )->setField ( 'has_public', 1 );
			D ( 'Common/User' )->clear ( $this->mid );
			$url = U ( 'Home/Public/public_edit', $param );
		}
		$key1 = 'pre_auth_code';
		S ( $key1 ,null);
		// 授权完成，进入平台
		redirect ( $url );
	}
	function _xmlToArr($xml) {
		$res = @simplexml_load_string ( $xml, NULL, LIBXML_NOCDATA );
		$res = json_decode ( json_encode ( $res ), true );
		return $res;
	}
}
