<?php
        	
namespace Addons\Course\Model;
use Home\Model\WeixinModel;
        	
/**
 * Course的微信模型
 */
class WeixinAddonModel extends WeixinModel{
	function reply($dataArr, $keywordArr = array()) {
		$config = getAddonConfig ( 'Course' ); // 获取后台插件的配置参数	
		//dump($config);
	}
}
        	