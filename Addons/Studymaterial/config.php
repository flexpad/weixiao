<?php
return array(
	'random'=>array(//配置在表单中的键名 ,这个会是config[random]
		'title'=>'是否开启随机:',//表单的文字
		'type'=>'radio',		 //表单的类型：text、textarea、checkbox、radio、select等
		'options'=>array(		 //select 和radion、checkbox的子选项
			'1'=>'开启',		 //值=>文字
			'0'=>'关闭',
		),
		'value'=>'1',			 //表单的默认值
	),
	'email_sendtype' => 'smtp',
	'email_host' => 'smtp.qq.com',
	'email_port' => 465,
	'email_ssl'  => true,
	'email_account' => '5811751@qq.com',
	'email_password' => 'cuctjvnkxrtkbjei',
	'email_sender_name' => '微学校（家长能随时掌握的学校）',
	'email_sender_email' => '5811751@qq.com',
);
					