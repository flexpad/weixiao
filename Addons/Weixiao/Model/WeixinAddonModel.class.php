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
	public function send_score_to_user($openId, $url, $info){
		if ($info == NULL) return false;
		$template_id = "4yl4CcKuTIVJrSObYB1SsP9uakWRnzzfpVXq7TANV3o";
		$data = array(
			"frist"=>"亲爱的" . $info["stuname"] . "家长," . $info["stuname"] . "同学的在" . $info["exam"] . "考试取得以下成绩",
			"childName"=>$info["stuname"],
			"courseName"=>$info["course"],
			"score"=>$info["socreStr"],
			"remark"=>"点击查看详情"
		);
		$this->send_msg_form($openId, $template_id, $url, $data);
	}
	
	public function test () {
		
	}
}
        	