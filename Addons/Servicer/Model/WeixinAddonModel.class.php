<?php
        	
namespace Addons\Servicer\Model;
use Home\Model\WeixinModel;
        	
/**
 * Servicer的微信模型
 */
class WeixinAddonModel extends WeixinModel{
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'Servicer' ); // 获取后台插件的配置参数	
		//dump($config);
	}
}
        	