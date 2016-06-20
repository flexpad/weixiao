<?php
$bg = array();
for ($i=1; $i<11;$i++){
	$bg[$i] = '背景'.$i;
}
$bg[$i] = '自定义';
return array (
		'title' => array (
				'title' => '卡名',
				'type' => 'text',
				'value' => '时尚美容美发店VIP会员卡',
				'tip'=>''
		) ,
		'title_color' => array ( // 配置在表单中的键名 ,这个会是config[random]
				'title' => '会员卡名称颜色', // 表单的文字
				'type' => 'color', // 表单的类型：text、textarea、checkbox、radio、select等
				'value' => '#000000',
				'tip'=>''
				),
		'number_color' => array ( // 配置在表单中的键名 ,这个会是config[random]
				'title' => '卡号文字颜色', // 表单的文字
				'type' => 'color', // 表单的类型：text、textarea、checkbox、radio、select等
				'value' => '#000000',
				'tip'=>''
				),
		'background' => array ( // 配置在表单中的键名 ,这个会是config[random]
				'title' => '背景图', // 表单的文字
				'type' => 'select', // 表单的类型：text、textarea、checkbox、radio、select等
				'options'=>$bg,	
				'value' => '1',
				'tip'=>''
				),
		'logo' => array ( // 配置在表单中的键名 ,这个会是config[random]
				'title' => '会员卡LOGO', // 表单的文字
				'type' => 'picture', // 表单的类型：text、textarea、checkbox、radio、select等
				'value' => '',
				'tip'=>'高为100像素 宽度不限'
				),
		'show_logo' => array ( // 配置在表单中的键名 ,这个会是config[random]
				'title' => '是否显示图标', // 表单的文字
				'type' => 'radio', // 表单的类型：text、textarea、checkbox、radio、select等
				'value' => '',
				'options'=>array(
						'0'=>'不显示',
						'1'=>'显示',
				),	
				'tip'=>''
				),
		'length' => array ( 
				'title' => '卡号位数', 
				'type' => 'select', 
				'options'=>array(
						'80001'=>'5',
						'800001'=>'6',
						'8000001'=>'7',
						'80000001'=>'8',
						'800000001'=>'9',
						'8000000001'=>'10'
				),	
				'value' => '80001',
				'tip'=>'为了保证卡号规范，一旦配置好请谨慎修改'
		),
		'startNumber' => array ( 
				'title' => '卡号首位数字', 
				'type' => 'select', 
				'options'=>array(
						'1'=>'1',
						'2'=>'2',
						'3'=>'3',
						'4'=>'4',
						'5'=>'5',
						'6'=>'6',
						'7'=>'7',
						'8'=>'8',
						'9'=>'9'
				),	
				'value' => '4',
				'tip'=>'为了保证卡号规范，一旦配置好请谨慎修改'
		),
		
// 		'number_type' => array ( // 配置在表单中的键名 ,这个会是config[random]
// 				'title' => '会员卡号设置', // 表单的文字
// 				'type' => 'radio', // 表单的类型：text、textarea、checkbox、radio、select等
// 				'value' => '0',
// 				'options'=>array(
// 						'0'=>'默认',
// 						'1'=>'手机号作为卡号',
// 				),	
// 				'tip'=>''
// 				),
		'need_verify' => array ( // 配置在表单中的键名 ,这个会是config[random]
				'title' => '是否需要认证', // 表单的文字
				'type' => 'radio', // 表单的类型：text、textarea、checkbox、radio、select等
				'value' => '0',
				'options'=>array(
						'0'=>'不需要',
						'1'=>'需要',
				),	
				'tip'=>'勾选后，用户领取会员卡时则必须验证（需开通短信包）'
				),
		'managerPassword' => array ( // 配置在表单中的键名 ,这个会是config[random]
				'title' => '商家确认消费密码', // 表单的文字
				'type' => 'text', // 表单的类型：text、textarea、checkbox、radio、select等
				'value' => '',
				'tip'=>'手机端确认使用优惠券等输入此密码，不超过20个字。'
				),
		
		
		
		'background_custom' => array ( // 配置在表单中的键名 ,这个会是config[random]
				'type' => 'hidden', // 表单的类型：text、textarea、checkbox、radio、select等
		),	
		'bg_id' => array ( // 配置在表单中的键名 ,这个会是config[random]
				'type' => 'hidden', // 表单的类型：text、textarea、checkbox、radio、select等
		),
		'back_background' => array ( // 配置在表单中的键名 ,这个会是config[random]
				'title' => '背面背景图', // 表单的文字
				'type' => 'select', // 表单的类型：text、textarea、checkbox、radio、select等
				'options'=>$bg,	
				'value' => '1',
				'tip'=>''
				),
		'back_color' => array ( // 配置在表单中的键名 ,这个会是config[random]
				'title' => '背面文字颜色', // 表单的文字
				'type' => 'color', // 表单的类型：text、textarea、checkbox、radio、select等
				'value' => '#000000',
				'tip'=>''
				),
		'back_background_custom' => array ( // 配置在表单中的键名 ,这个会是config[random]
				'type' => 'hidden', // 表单的类型：text、textarea、checkbox、radio、select等
		),	
		'backbg_id' => array ( // 配置在表单中的键名 ,这个会是config[random]
				'type' => 'hidden', // 表单的类型：text、textarea、checkbox、radio、select等
		),
		'instruction' => array (
				'title' => '使用说明',
				'type' => 'textarea',
				'value' => '1、恭喜您成为时尚美容美发店VIP会员;
2、结帐时请出示此卡，凭此卡可享受会员优惠;
3、此卡最终解释权归时尚美容美发店所有',
				'tip'=>''
		),	
		/*
		'address' => array (
				'title' => '地址',
				'type' => 'hidden',
				'value' => '',
				'tip'=>''
		) ,	
		'phone' => array (
				'title' => '电话',
				'type' => 'hidden',
				'value' => '',
				'tip'=>''
		) ,	
		'url' => array (
				'title' => '网址',
				'type' => 'hidden',
				'value' => '',
				'tip'=>''
		) ,
		*/
);
					