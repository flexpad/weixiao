<?php

namespace Addons\RedBag\Model;

use Think\Model;

/**
 * RedBag模型
 */
class RedBagModel extends Model {
	protected $tableName = 'redbag';
	function getInfo($id, $update = false, $data = array()) {
		$key = 'RedBag_getInfo_' . $id;
		$info = S ( $key );
		if ($info === false || $update) {
			$info = ( array ) (empty ( $data ) ? $this->find ( $id ) : $data);
			S ( $key, $info, 86400 );
		}
		
		return $info;
	}
	// 素材相关
	function getSucaiList($search = '') {
		$map ['token'] = get_token ();
		$map ['uid'] = session ( 'mid' );
		empty ( $search ) || $map ['act_name'] = array (
				'like',
				"%$search%" 
		);
		
		$data_list = $this->where ( $map )->field ( 'id' )->order ( 'id desc' )->selectPage ();
		foreach ( $data_list ['list_data'] as &$v ) {
			$data = $this->getInfo ( $v ['id'] );
			$v ['title'] = $data ['act_name'];
		}
		
		return $data_list;
	}
	function getPackageData($id) {
	   
		$info = get_token_appinfo ();
		$param ['publicid'] = $info ['id'];
		$param ['publicUid'] = session ( 'mid' );
		
		$param ['id'] = $id;
		$data ['jumpURL'] = addons_url ( "RedBag://Wap/collect", $param );
		$data ['data'] = $this->getInfo ( $id, true );
		return $data;
	}
	
	// 获取红包共用方法
	function getRedBag($id) {
		$config = getAddonConfig ( 'RedBag' );
		
		$info = $this->getInfo ( $id, true );
		
		$left_num = $info ['total_num'] - $info ['collect_count'];
		$left_amount = $info ['total_amount'] - $info ['collect_amount'];
		
		if ($left_num <= 0 || $left_amount <= 0) {
			$returnData ['msg_code'] = 0;
			$returnData ['msg'] = '你来晚了，红包领光啦';
			return $returnData;
		}
		
		$recode ['redbag_id'] = $id;
		
		$recode ['openid'] = getPaymentOpenid ( $config ['wxappid'], $config ['wxappsecret'] );
		
		
		if ($info ['collect_limit'] > 0) {
			$my_count = M ( 'redbag_follow' )->where ( $recode )->count ();
			if ($my_count >= $info ['collect_limit']) {
				$returnData ['msg_code'] = 0;
				$returnData ['msg'] = '每人只能取领取' . $info ['collect_limit'] . '次，您的领取次数已经用完';
				return $returnData;
			}
		}
		
		if ($left_amount < $info ['max_value']) {
			$info ['max_value'] = $left_amount;
		}
		
		$money = rand ( $info ['min_value'], $info ['max_value'] );
		
		// 商户和公众号信息
		$data ['mch_id'] = $config ['mch_id']; // 微信支付分配的商户号
		$data ['mch_billno'] = $config ['mch_id'] . date ( Ymd ) . $this->getRandStr (); // 商户订单号（每个订单号必须唯一）组成： mch_id+yyyymmdd+10位一天内不能重复的数字。接口根据商户订单号支持重入， 如出现超时可再调用。
		$data ['wxappid'] = $config ['wxappid']; // 商户appid，如：wx9e088eb8b3152ae2
		$data ['re_openid'] = $recode ['openid']; // 接受收红包的用户
		$data ['nonce_str'] = uniqid (); // 随机字符串，不长于32位
		$data ['nick_name'] = $info ['nick_name']; // 提供方名称
		$data ['send_name'] = $info ['send_name']; // 红包发送者名称
		$data ['total_amount'] = $data ['min_value'] = $data ['max_value'] = $money;
		$data ['total_num'] = 1;
		$data ['wishing'] = $info ['wishing'];
		$data ['client_ip'] = $_SERVER ['SERVER_ADDR'];
		$data ['act_name'] = $info ['act_name'];
		$data ['remark'] = $info ['act_name'];
		$data ['sign'] = make_sign ( $data, $config ['partner_key'] );
		
		$vars = "<xml>
        <sign>{$data ['sign']}</sign>
        <mch_billno>{$data ['mch_billno']}</mch_billno>
        <mch_id>{$data ['mch_id']}</mch_id>
        <wxappid>{$data ['wxappid']}</wxappid>
        <nick_name>{$data ['nick_name']}</nick_name>
        <send_name>{$data ['send_name']}</send_name>
        <re_openid>{$data ['re_openid']}</re_openid>
        <total_amount>{$data ['total_amount']}</total_amount>
        <min_value>{$data ['min_value']}</min_value>
        <max_value>{$data ['max_value']}</max_value>
        <total_num>{$data ['total_num']}</total_num>
        <wishing>{$data ['wishing']}</wishing>
        <client_ip>{$data ['client_ip']}</client_ip>
        <act_name>{$data ['act_name']}</act_name>
        <remark>{$data ['remark']}</remark>
        <nonce_str>{$data ['nonce_str']}</nonce_str>
        </xml>";
		// dump ( $data );
		// dump ( $vars );
		$url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack';
		// 获取证书路径
		$ids [] = $config ['cert_dir'];
		$ids [] = $config ['key_dir'];
		$map ['id'] = array (
				'in',
				$ids 
		);
		$fileData = M ( 'file' )->where ( $map )->select ();
		
		$downloadConfig = C ( DOWNLOAD_UPLOAD );
		$certpath = $keypath = '';
		foreach ( $fileData as $f ) {
			if ($config ['cert_dir'] == $f ['id']) {
				$certpath = SITE_PATH .  substr ( $downloadConfig ['rootPath'], 1 ) . $f ['savepath'] . $f ['savename'] ;
			} else {
				$keypath = SITE_PATH . substr ( $downloadConfig ['rootPath'], 1 ) . $f ['savepath'] . $f ['savename'] ;
			}
		}
		
		$res = $this->curl_post_ssl ( $url, $vars, $certpath, $keypath );
		
		if ($res ['return_code'] == 'FAIL') {
			$returnData ['msg_code'] = 0;
			$returnData ['msg'] = $res ['return_msg'];
			return $returnData;
		}
		if ($res ['result_code'] == 'FAIL') {
			$returnData ['msg_code'] = 0;
			$returnData ['msg'] = $res ['err_code_des'] . ', 错误码： ' . $res ['err_code'];
			return $returnData;
		}
		
		// dump($res);
		// 记录个人日志
		$recode ['openid'] = $data ['re_openid'];
		$recode ['amount'] = $data ['total_amount'];
		$recode ['cTime'] = NOW_TIME;
		$recode ['follow_id'] = get_mid ();
		$recode ['order_data'] = json_encode ( $data );
		M ( 'redbag_follow' )->add ( $recode );
		$saveData ['collect_amount'] = $info ['collect_amount'] + $data ['total_amount'];
		$saveData ['collect_count'] = $info ['collect_count'] + 1;
		M ( 'redbag' )->where ( array (
				'id' => $id 
		) )->save ( $saveData );
		$this->getInfo ( $id, true );
		
		$returnData ['msg_code'] = 1;
		$returnData ['msg'] = '红包发放中，稍后您会收到领取通知 ';
		return $returnData;
	}
	// 获取红包发放情况
	function getStatus($mch_billno) {
		$config = getAddonConfig ( 'RedBag' );
		
		$data ['mch_billno'] = $mch_billno;
		$data ['mch_id'] = $config ['mch_id'];
		$data ['appid'] = $config ['wxappid'];
		$data ['bill_type'] = 'MCHT';
		$data ['nonce_str'] = uniqid ();
		$data ['sign'] = make_sign ( $data, $config ['partner_key'] );
		
		$url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/gethbinfo';
		
		// 获取证书路径
		$ids [] = $config ['cert_dir'];
		$ids [] = $config ['key_dir'];
		$map ['id'] = array (
				'in',
				$ids 
		);
		$fileData = M ( 'file' )->where ( $map )->select ();
		$downloadConfig = C ( DOWNLOAD_UPLOAD );
		$certpath = $keypath = '';
		foreach ( $fileData as $f ) {
			if ($config ['cert_dir'] == $f ['id']) {
				$certpath = SITE_PATH . str_replace ( '/', '\\', substr ( $downloadConfig ['rootPath'], 1 ) . $f ['savepath'] . $f ['savename'] );
			} else {
				$keypath = SITE_PATH . str_replace ( '/', '\\', substr ( $downloadConfig ['rootPath'], 1 ) . $f ['savepath'] . $f ['savename'] );
			}
		}
		$res = $this->curl_post_ssl ( $url, $vars, $certpath, $keypath );
		if ($res ['return_code'] == 'FAIL') {
			$returnData ['msg_code'] = 0;
			$returnData ['msg'] = $res ['return_msg'];
			return $returnData;
		}
		if ($res ['result_code'] == 'FAIL') {
			$returnData ['msg_code'] = 0;
			$returnData ['msg'] = $res ['err_code_des'] . ', 错误码： ' . $res ['err_code'];
			return $returnData;
		}
	}
	function curl_post_ssl($url, $vars, $cert_dir = '', $key_dir = '') {
		$ch = curl_init ();
		// 超时时间
		curl_setopt ( $ch, CURLOPT_TIMEOUT, 30 );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		// 这里设置代理，如果有的话
		// curl_setopt($ch,CURLOPT_PROXY, '10.206.30.98');
		// curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, false );
		
		// 以下两种方式需选择一种
		
		// 第一种方法，cert 与 key 分别属于两个.pem文件
		// 默认格式为PEM，可以注释
		curl_setopt ( $ch, CURLOPT_SSLCERTTYPE, 'PEM' );
		curl_setopt ( $ch, CURLOPT_SSLCERT, $cert_dir );
		// 默认格式为PEM，可以注释
		curl_setopt ( $ch, CURLOPT_SSLKEYTYPE, 'PEM' );
		curl_setopt ( $ch, CURLOPT_SSLKEY, $key_dir );
		
		// 第二种方式，两个文件合成一个.pem文件
		// curl_setopt ( $ch, CURLOPT_SSLCERT, getcwd () . '/all.pem' );
		
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $vars );
		$content = curl_exec ( $ch );
		
		if ($content) {
			$data = new \SimpleXMLElement ( $content );
			foreach ( $data as $key => $value ) {
				$msg [$key] = $value;
			}
		} else {
			$msg ['return_code'] = 'FAIL';
			$msg ['return_msg'] = "请求失败, 失败编号: " . curl_errno ( $ch );
		}
		curl_close ( $ch );
		return $msg;
	}
	function obj2array($xml) {
	}
	function getRandStr() {
		$arr = array (
				'A',
				'B',
				'C',
				'D',
				'E',
				'F',
				'G',
				'H',
				'I',
				'J',
				'K',
				'L',
				'M',
				'N',
				'O',
				'P',
				'Q',
				'R',
				'S',
				'T',
				'U',
				'V',
				'W',
				'X',
				'Y',
				'Z' 
		);
		$key = array_rand ( $arr );
		return substr ( time (), - 5 ) . substr ( microtime (), 2, 4 ) . $arr [$key];
	}
}
