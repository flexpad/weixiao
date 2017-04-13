<?php
        	
namespace Addons\weixiao\Model;
use Home\Model\WeixinModel;
        	
/**
 * weixiao的微信模型
 */
class WeixinAddonModel extends WeixinModel{
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'Weixiao' ); // 获取后台插件的配置参数	
		//dump($config);
	}
}
        	