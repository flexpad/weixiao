<?php if (!defined('THINK_PATH')) exit();?><!-- 学生详情-->
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <title><?php echo empty($page_title) ? C('WEB_SITE_TITLE') : $page_title; ?></title>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
	<meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport">
    <meta content="application/xhtml+xml;charset=UTF-8" http-equiv="Content-Type">
    <meta content="no-cache,must-revalidate" http-equiv="Cache-Control">
    <meta content="no-cache" http-equiv="pragma">
    <meta content="0" http-equiv="expires">
    <meta content="telephone=no, address=no" name="format-detection">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="stylesheet" type="text/css" href="/weiphp30/Public/Home/css/mobile_module.css?v=<?php echo SITE_VERSION;?>" media="all">
    <script type="text/javascript">
		//静态变量
		var SITE_URL = "<?php echo SITE_URL;?>";
		var IMG_PATH = "/weiphp30/Public/Home/images";
		var STATIC_PATH = "/weiphp30/Public/static";
		var WX_APPID = "<?php echo ($jsapiParams["appId"]); ?>";
		var	WXJS_TIMESTAMP='<?php echo ($jsapiParams["timestamp"]); ?>'; 
		var NONCESTR= '<?php echo ($jsapiParams["nonceStr"]); ?>'; 
		var SIGNATURE= '<?php echo ($jsapiParams["signature"]); ?>';
	</script>
    <script type="text/javascript" src="/weiphp30/Public/static/jquery-2.0.3.min.js"></script>
	<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
	<script type="text/javascript" src="minify.php?f=/weiphp30/Public/Home/js/prefixfree.min.js,/weiphp30/Public/Home/js/m/dialog.js,/weiphp30/Public/Home/js/m/flipsnap.min.js,/weiphp30/Public/Home/js/m/mobile_module.js&v=<?php echo SITE_VERSION;?>"></script>
</head>
<link href="<?php echo ADDON_PUBLIC_PATH;?>/bootstrap3.3.7/css/bootstrap.min.css?v=<?php echo SITE_VERSION;?>" rel="stylesheet" type="text/css">
<link href="<?php echo ADDON_PUBLIC_PATH;?>/weui.min.css?v=<?php echo SITE_VERSION;?>" rel="stylesheet" type="text/css">
<style>
    .informationUl{
        list-style: none;
        margin: 0px;
        padding: 0px;
    }
    .informationUl li{
        margin: 5px;
    }
    .weui-btn_primary {
        background-color: #337ab7;
    }
    .panel-default>.panel-heading{
        background-color:#d9edf7;
        color: #31708f;
    }

    .attendance_head{
        position: relative;
    }
    .institution_name{
        text-align: center;
        margin: 40px auto 20px;
    }
    .attendance_head_p{
        margin-top: 10px;
    }
    a:link{
        text-decoration:none;
    }
    a:visited{
        text-decoration:none;
    }
    a:hover{
        text-decoration:none;
    }
    a:active{
        text-decoration:none;
    }
</style>
<body>
<div class="panel-group" id="accordion">

    <div class="attendance_head">
        <div class="institution_name">
            <img src="<?php echo ADDON_PUBLIC_PATH;?>/A37.png" alt="" >
            <p  class="attendance_head_p">学生详情</p>
        </div>
    </div>

    <IF>
        <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo ($vo["sid"]); ?>"><?php echo ($vo["name"]); ?>详细信息</a>
                    </h4>
                </div>
                <div id="collapse<?php echo ($vo["sid"]); ?>" class="panel-collapse collapse">
                    <div class="panel-body">
                        <ul class="informationUl">
                            <li>学号：<?php echo ($vo["studentno"]); ?></li>
                            <li>性别：<?php echo ($vo["gender"]); ?></li>
                            <li>来自学校：<?php echo ($vo["school"]); ?></li>
                            <li>年级：<?php echo ($vo["grade"]); ?></li>
                            <li>家长联系电话：<?php echo ($vo["phone"]); ?></li>
                            <li>
                                <a href="<?php echo U('addon/Student/Wap/score', array('publicid'=>$public_id, 'studentno' => $vo[studentno]));?>" class="weui-btn weui-btn_primary weui-btn_mini">成绩查询</a>
                                <a href="<?php echo U('addon/Student/Wap/show_attendance', array('publicid'=>$public_id, 'studentno' => $vo[studentno]));?>" class="weui-btn weui-btn_primary weui-btn_mini">考勤查询</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div><?php endforeach; endif; else: echo "" ;endif; ?>
    </IF>

</div>
<!-- 底部导航 -->
<?php echo ($footer_html); ?>
<!-- 统计代码 -->
<?php if(!empty($config["code"])): ?><p class="hide bdtongji">
        <?php echo ($config["code"]); ?>
    </p>
    <?php else: ?>
    <p class="hide bdtongji">
        <?php echo ($tongji_code); ?>
    </p><?php endif; ?>
<script src="<?php echo ADDON_PUBLIC_PATH;?>/jquery-3.1.1.min.js?v=<?php echo SITE_VERSION;?>"></script>
<script src="<?php echo ADDON_PUBLIC_PATH;?>/bootstrap3.3.7/js/bootstrap.min.js?v=<?php echo SITE_VERSION;?>"></script>
</body>