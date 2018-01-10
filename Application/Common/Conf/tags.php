<?php
return array(
	'app_init'=>array('Common\Behavior\InitHookBehavior'),
    'view_filter' => array(
        'Behavior\TokenBuildBehavior', // 表单令牌
    ),

);