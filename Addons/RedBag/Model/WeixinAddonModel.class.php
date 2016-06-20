<?php

namespace Addons\RedBag\Model;

use Home\Model\WeixinModel;

/**
 * RedBag的微信模型
 */
class WeixinAddonModel extends WeixinModel {
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'RedBag' ); // 获取后台插件的配置参数
			                                       // dump($config);
	}
}
        	