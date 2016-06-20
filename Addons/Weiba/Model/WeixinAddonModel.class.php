<?php

namespace Addons\Weiba\Model;

use Home\Model\WeixinModel;

/**
 * Weiba的微信模型
 */
class WeixinAddonModel extends WeixinModel {
	function reply($dataArr, $keywordArr = array()) {
		$param ['token'] = $dataArr ['ToUserName'];
		$param ['openid'] = $dataArr ['FromUserName'];
		$url = addons_url ( 'Weiba://Wap/index', $param );
		$this->replyText ( "快来微社区参与我们的讨论吧，<a href='{$url}'>点击这里进入</a>" );
	}
}
        	