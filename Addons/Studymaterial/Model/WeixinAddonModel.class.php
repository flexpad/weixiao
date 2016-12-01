<?php
        	
namespace Addons\Studymaterial\Model;
use Home\Model\WeixinModel;
        	
/**
 * Studymaterial的微信模型
 */
class WeixinAddonModel extends WeixinModel{
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'Studymaterial' ); // 获取后台插件的配置参数	
		//dump($config);
	}
}
        	