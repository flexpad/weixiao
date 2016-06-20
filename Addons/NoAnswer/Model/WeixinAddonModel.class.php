<?php

namespace Addons\NoAnswer\Model;

use Home\Model\WeixinModel;

/**
 * NoAnswer的微信模型
 */
class WeixinAddonModel extends WeixinModel {
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'NoAnswer' ); // 获取后台插件的配置参数
		
		$this->material_reply ( $config ['stype'] );
	}
}
        	