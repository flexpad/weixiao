<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
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
<link href="<?php echo ADDON_PUBLIC_PATH;?>/userCenter.css?v=<?php echo SITE_VERSION;?>" rel="stylesheet" type="text/css">

<body>
<div class="container">
  <div class="userHead">
    <div class="userInfo">
      <div class="head"><img src="<?php echo ($info["headimgurl"]); ?>"/></div>
      <div class="info">
        <p class="name"><strong><?php echo ($info["nickname"]); ?></strong></p>
        <p class="attr"><span>积分:</span> <?php echo (intval($info["score"])); ?></p>
        <p class="attr"><span>经历值:</span> <?php echo (intval($info["experience"])); ?></p>
      </div>
    </div>
  </div>
  
      <h3>个人管理</h3>
    <div class="user_box"> 
    <a class="item" href="<?php echo U('bind_prize_info');?>"><span class="td title">资料编辑</span><em class="arrow">&nbsp;</em> </a> 
<!--    <a class="item" href="<?php echo U('credit');?>"><span class="td title">积分记录</span><em class="arrow">&nbsp;</em> </a> 
    <a class="item" href="<?php echo U('weixin_message');?>"><span class="td title">咨询消息</span><em class="arrow">&nbsp;</em> </a> 
    <a class="item" href="<?php echo U('message');?>"><span class="td title">系统推送</span><em class="arrow">&nbsp;</em> </a> -->
    
        <?php if(is_array($default_link)): $i = 0; $__LIST__ = $default_link;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$link): $mod = ($i % 2 );++$i;?><a class="item" href="<?php echo ($link["url"]); ?>"> 
    <?php if(!empty($link[icon])): ?><img src="<?php echo ($link["icon"]); ?>"><?php endif; ?>
    <span class="td title"><?php echo ($link["title"]); ?></span>
    <?php if(($link["new_count"]) != "0"): ?><span class="new_count"><?php echo ($link["new_count"]); ?></span><?php endif; ?>
    
     <em class="arrow">&nbsp;</em> </a><?php endforeach; endif; else: echo "" ;endif; ?>

    </div>
    
  <?php if(is_array($list_data)): $i = 0; $__LIST__ = $list_data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$group): $mod = ($i % 2 );++$i;?><h3><?php echo ($key); ?></h3>
    <div class="user_box"> 
    <?php if(is_array($group)): $i = 0; $__LIST__ = $group;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$link): $mod = ($i % 2 );++$i;?><a class="item" href="<?php echo ($link["url"]); ?>"> 
    <?php if(!empty($link[icon])): ?><img src="<?php echo ($link["icon"]); ?>"><?php endif; ?>
    <span class="td title"><?php echo ($link["title"]); ?></span>
    <?php if(($link["new_count"]) != "0"): ?><span class="new_count"><?php echo ($link["new_count"]); ?></span><?php endif; ?>
    
     <em class="arrow">&nbsp;</em> </a><?php endforeach; endif; else: echo "" ;endif; ?>
    </div><?php endforeach; endif; else: echo "" ;endif; ?>
  <!--<div class="m_10 unbind">
    <p class="colorless pb_10">你还没绑定用户信息!</p>
    <p><a class="btn" href="#">马上去绑定</a></p>
  </div>-->
</div>
<!-- Wap页面脚部 -->
<div style="height:0; visibility:hidden; overflow:hidden;">
<?php echo ($tongji_code); ?>
</div>
</body>
</html>