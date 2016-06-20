<?php

namespace Addons\RedBag\Controller;

use Home\Controller\AddonsController;

if (! function_exists ( 'make_sign' )) {
	// 生成签名
	function make_sign($paraMap = array(), $partner_key = '') {
		$buff = "";
		ksort ( $paraMap );
		$paraMap ['key'] = $partner_key;
		foreach ( $paraMap as $k => $v ) {
			if (null != $v && "null" != $v && '' != $v && "sign" != $k) {
				$buff .= strtolower ( $k ) . "=" . $v . "&";
			}
		}
		$reqPar = '';
		if (strlen ( $buff ) > 0) {
			$reqPar = substr ( $buff, 0, strlen ( $buff ) - 1 );
		}
		return strtoupper ( md5 ( $reqPar ) );
	}
}
class WapController extends AddonsController {
	// 领取红包
	function collect() {
		$id = I ( 'id' );
		$msgData = D ( "Addons://RedBag/RedBag" )->getRedBag ( $id );
		
		$this->assign ( $msgData );
		$this->display ();
	}
	function index() {
		$id = I ( 'id' );
		
		$data = D ( 'Addons://RedBag/RedBag' )->getInfo ( $id );
		$this->assign ( 'data', $data );
		// dump ( $data );
		// exit ();
		$info = $public_info = get_token_appinfo ();
		
		$param ['publicid'] = $info ['id'];
		$param ['id'] = $id;
		$url = addons_url ( "RedBag://Wap/collect", $param );
		$this->assign ( 'jumpURL', $url );
		
		$this->assign ( 'public_info', $public_info );
		
		$this->display ();
	}
}
