<?php

namespace Addons\CardVouchers\Controller;

use Home\Controller\AddonsController;

class WapController extends AddonsController {
	/*
	function index() {
		$sha1 ['timestamp'] = NOW_TIME;
		$sha1 ['openid'] = '';
		$sha1 ['code'] = '';
		$sha1 ['balance'] = '';
		
		$sha1 ['appsecre'] = '089211161352804511ab0b658be7790f';
		$sha1 ['card_id'] = $card_id = 'pY-EguGiW-3rSt5MHl6c9dygY--c';
		
		$sha1 ['signature'] = getSHA1 ( $sha1 );
		// dump($sha1);
		$this->assign ( 'info', $sha1 );
		$this->display ();
	}
	function index2() {
		$sha1 ['timestamp'] = NOW_TIME;
		$sha1 ['openid'] = get_openid ();
		$sha1 ['code'] = '';
		$sha1 ['balance'] = '';
		
		$sha1 ['appsecre'] = '3f8809558dbfe261c1556b5c7550f369';
		$sha1 ['card_id'] = $card_id = 'pDYw9uNN6GWEuFjq4lCC2AOYYruc';
		
		$sha1 ['signature'] = getSHA1 ( $sha1 );
		// dump($sha1);
		$this->assign ( 'info', $sha1 );
		$this->display ( 'index' );
	}
	function index3() {
		$sha1 ['timestamp'] = NOW_TIME;
		// $sha1 ['openid'] = '';
		// $sha1 ['code'] = '';
		// $sha1 ['balance'] = '';
		
		$sha1 ['appsecre'] = '089211161352804511ab0b658be7790f';
		$sha1 ['card_id'] = $card_id = 'pY-EguES0EhZZTIQnE9KoDZSiFZQ';
		
		$sha1 ['signature'] = getSHA1 ( $sha1 );
		
		$str = "{\"code\":\"{$sha1['code']}\",\"openid\":\"{$sha1['openid']}\",\"timestamp\":\"{$sha1['timestamp']}\",\"signature\":\"{$sha1['signature']}\"}";
		$this->assign ( 'card_ext', $str );
		$this->assign ( 'info', $sha1 );
		$this->display ( 'index' );
	}
	function index4() {
		$sha1 ['timestamp'] = NOW_TIME;
		
		$sha1 ['appsecre'] = '089211161352804511ab0b658be7790f';
		$sha1 ['card_id'] = 'pY-EguCvSac01XujgRHjWJsjaqHU';
		
		$sha1 ['signature'] = getSHA1 ( $sha1 );
		$sha1 ['openid'] = '';
		$sha1 ['code'] = '';
		// $sha1 ['balance'] = '';
		
		$str = json_encode ( $sha1 );
		
		// $str = "{\"code\":\"{$sha1['code']}\",\"openid\":\"{$sha1['openid']}\",\"timestamp\":\"{$sha1['timestamp']}\",\"signature\":\"{$sha1['signature']}\"}";
		$this->assign ( 'card_ext', $str );
		$this->assign ( 'info', $sha1 );
		$this->display ( 'index' );
	}
	
	
		function index() {		
		$id = I ( 'id' );
		$info = D ( 'CardVouchers' )->getInfo ( $id );
		$public_info = get_token_appinfo ();
// 		$sha1 ['api_ticket']=$this->getApiTicket($public_info['appid']);
		$sha1 ['api_ticket']=$info['appsecre'];
		$sha1 ['timestamp'] = NOW_TIME;
		$sha1 ['nonce_str']=uniqid();
		$sha1 ['appsecre'] = trim($info ['appsecre']);
		$sha1 ['card_id'] = $card_id = trim($info ['card_id']);
		$sha1 ['signature'] = getSHA1 ( $sha1 );
		$info ['card_ext'] = "{\"code\":\"{$sha1['code']}\",\"openid\":\"{$sha1['openid']}\",\"timestamp\":\"{$sha1['timestamp']}\",\"signature\":\"{$sha1['signature']}\",\"nonce_str\":\"{$sha1['nonce_str']}\"}";
		$this->assign ( 'info', $info );
		$this->assign ('public_info',$public_info);
		
		$this -> display();
	}
	
	*/
    
	function index() {		
		$id = I ( 'id' );
		$info = D ( 'CardVouchers' )->getInfo ( $id );
		//³é½±ÓÎÏ·
		$fromType=I('from_type');
		$aimId=I('aim_id');
		$this->assign('from_type',$fromType);
		$this->assign('aim_id',$aimId);
		
		$public_info = get_token_appinfo ();
		$sha1 ['timestamp'] = NOW_TIME;
// 		$sha1 ['appSecret'] = trim($info ['appsecre']);
		$sha1 ['api_ticket']=$this->getApiTicket($public_info['appid']);
// 		$sha1 ['openid']= get_openid();
		$sha1 ['card_id'] = $card_id = trim($info ['card_id']);
		$sha1 ['nonce_str']=uniqid();
		$sha1 ['signature'] = getSHA1 ( $sha1 );
		$info ['card_ext'] = "{\"code\":\"{$sha1['code']}\",\"openid\":\"{$sha1['openid']}\",\"timestamp\":\"{$sha1['timestamp']}\",\"nonce_str\":\"{$sha1['nonce_str']}\",\"signature\":\"{$sha1['signature']}\"}";
		$this->assign ( 'info', $info );
		$this->assign ('public_info',$public_info);
		
		$this -> display();
	}
	function doSaveAward(){
	    $aimId=I('aim_id');
	    $fromType=I('from_type');
	    if ($fromType == 'drawgame' && $aimId){
	        $save['state']=1;
	        $save['djtime']=time();
	        $res=M('lucky_follow')->where(array('id'=>$aimId))->save($save);
	        $userAward=D('Addons://Draw/LuckyFollow')->getUserAward($aimId,true);
	    }
	    echo $res;
	}
	function ajaxdosign(){
	    $cid=I('card_id');
	    $info = D ( 'Addons://CardVouchers/CardVouchers' )->getInfo ( $cid );
	    $public_info = get_token_appinfo ();
	    $sha1 ['timestamp'] = NOW_TIME;
	    $sha1 ['api_ticket']=$this->getApiTicket($public_info['appid']);
	    $sha1 ['card_id']  = trim($info ['card_id']);
	    $sha1 ['nonce_str']=uniqid();
	    $sha1 ['signature'] = getSHA1 ( $sha1 );
	    $rData ['card_ext'] = "{\"code\":\"{$sha1['code']}\",\"openid\":\"{$sha1['openid']}\",\"timestamp\":\"{$sha1['timestamp']}\",\"nonce_str\":\"{$sha1['nonce_str']}\",\"signature\":\"{$sha1['signature']}\"}";
		$rData ['strcard']=$info['card_id'];
		$this->ajaxReturn($rData);
	}
	//»ñÈ¡api_ticket
	private function getApiTicket($appid) {
	    $key='card_getApiTicket_'.$appid ;
	    $data = S ( $key );
	    if ($data !== false)
	        return $data;
	    $accessToken=get_access_token();
// 	    if (! $data) {
	        // 	        $accessToken = $this->getAccessToken ();
	        $url="https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=wx_card&access_token=".$accessToken;
	        $res = json_decode ( $this->httpGet ( $url ),true );
	        $data = $res['ticket'];
	        if ($data) {
	            S ( 'card_getApiTicket_'.$appid, $data, 6000 );
	        }
// 	    }
	    return $data;
	}
	private function httpGet($url) {
	    $curl = curl_init ();
	    curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, true );
	    curl_setopt ( $curl, CURLOPT_TIMEOUT, 500 );
	    curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, false );
	    curl_setopt ( $curl, CURLOPT_SSL_VERIFYHOST, false );
	    curl_setopt ( $curl, CURLOPT_URL, $url );
	
	    $res = curl_exec ( $curl );
	    curl_close ( $curl );
	
	    return $res;
	}
}
