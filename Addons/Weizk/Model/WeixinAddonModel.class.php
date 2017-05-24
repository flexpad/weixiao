<?php
        	
namespace Addons\Weizk\Model;
use Home\Model\WeixinModel;
        	
/**
 * Weizk的微信模型
 */
class WeixinAddonModel extends WeixinModel{
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'Weizk' ); // 获取后台插件的配置参数	
		//dump($config);
	}
}
        	