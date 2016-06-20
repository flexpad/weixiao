<?php
        	
namespace Addons\QrAdmin\Model;
use Home\Model\WeixinModel;
        	
/**
 * QrAdmin的微信模型
 */
class WeixinAddonModel extends WeixinModel{
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'QrAdmin' ); // 获取后台插件的配置参数	
		//dump($config);
	}
}
        	