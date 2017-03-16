<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML>
<html>
<head>
	<meta charset="UTF-8">
<meta content="<?php echo C('WEB_SITE_KEYWORD');?>" name="keywords"/>
<meta content="<?php echo C('WEB_SITE_DESCRIPTION');?>" name="description"/>
<link rel="shortcut icon" href="<?php echo SITE_URL;?>/favicon.ico">
<title><?php echo empty($page_title) ? C('WEB_SITE_TITLE') : $page_title; ?></title>
<link href="/weiphp30/Public/static/font-awesome/css/font-awesome.min.css?v=<?php echo SITE_VERSION;?>" rel="stylesheet">
<link href="/weiphp30/Public/Home/css/base.css?v=<?php echo SITE_VERSION;?>" rel="stylesheet">
<link href="/weiphp30/Public/Home/css/module.css?v=<?php echo SITE_VERSION;?>" rel="stylesheet">
<link href="/weiphp30/Public/Home/css/weiphp.css?v=<?php echo SITE_VERSION;?>" rel="stylesheet">
<link href="/weiphp30/Public/static/emoji.css?v=<?php echo SITE_VERSION;?>" rel="stylesheet">
<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
<!--[if lt IE 9]>
<script src="/weiphp30/Public/static/bootstrap/js/html5shiv.js?v=<?php echo SITE_VERSION;?>"></script>
<![endif]-->

<!--[if lt IE 9]>
<script type="text/javascript" src="/weiphp30/Public/static/jquery-1.10.2.min.js"></script>
<![endif]-->
<!--[if gte IE 9]><!-->
<script type="text/javascript" src="/weiphp30/Public/static/jquery-2.0.3.min.js"></script>
<!--<![endif]-->
<script type="text/javascript" src="/weiphp30/Public/static/bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/weiphp30/Public/static/uploadify/jquery.uploadify.min.js"></script>
<script type="text/javascript" src="/weiphp30/Public/static/zclip/ZeroClipboard.min.js?v=<?php echo SITE_VERSION;?>"></script>
<script type="text/javascript" src="/weiphp30/Public/Home/js/dialog.js?v=<?php echo SITE_VERSION;?>"></script>
<script type="text/javascript" src="/weiphp30/Public/Home/js/admin_common.js?v=<?php echo SITE_VERSION;?>"></script>
<script type="text/javascript" src="/weiphp30/Public/Home/js/admin_image.js?v=<?php echo SITE_VERSION;?>"></script>
<script type="text/javascript" src="/weiphp30/Public/static/masonry/masonry.pkgd.min.js"></script>
<script type="text/javascript" src="/weiphp30/Public/static/jquery.dragsort-0.5.2.min.js"></script> 
<script type="text/javascript">
var  IMG_PATH = "/weiphp30/Public/Home/images";
var  STATIC = "/weiphp30/Public/static";
var  ROOT = "/weiphp30";
var  UPLOAD_PICTURE = "<?php echo U('home/File/uploadPicture',array('session_id'=>session_id()));?>";
var  UPLOAD_FILE = "<?php echo U('File/upload',array('session_id'=>session_id()));?>";
var  UPLOAD_DIALOG_URL = "<?php echo U('home/File/uploadDialog',array('session_id'=>session_id()));?>";
</script>
<!-- 页面header钩子，一般用于加载插件CSS文件和代码 -->
<?php echo hook('pageHeader');?>

</head>
<body>
	<!-- 头部 -->
	<!-- 提示 -->
<div id="top-alert" class="top-alert-tips alert-error" style="display: none;">
  <a class="close" href="javascript:;"><b class="fa fa-times-circle"></b></a>
  <div class="alert-content"></div>
</div>
<!-- 导航条
================================================== -->
<div class="navbar navbar-inverse navbar-fixed-top">
    <div class="wrap">
    
       <a class="brand" title="<?php echo C('WEB_SITE_TITLE');?>" href="<?php echo U('index/index');?>">
       <?php if(!empty($userInfo[website_logo])): ?><img height="52" src="<?php echo (get_cover_url($userInfo["website_logo"])); ?>"/>
       	<?php else: ?>
       		<img height="52" src="/weiphp30/Public/Home/images/logo.png"/><?php endif; ?>
       </a>
        <?php if(is_login()): ?><div class="switch_mp">
            	<?php if(!empty($public_info["public_name"])): ?><a href="#"><?php echo ($public_info["public_name"]); ?><b class="pl_5 fa fa-sort-down"></b></a><?php endif; ?>
                <ul>
                <?php if(is_array($myPublics)): $i = 0; $__LIST__ = $myPublics;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li><a href="<?php echo U('home/index/main', array('publicid'=>$vo[mp_id]));?>"><?php echo ($vo["public_name"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
                </ul>
            </div><?php endif; ?>
      <?php $index_2 = strtolower ( MODULE_NAME . '/' . CONTROLLER_NAME . '/*' ); $index_3 = strtolower ( MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME ); ?>
       
            <div class="top_nav">
                <?php if(is_login()): ?><ul class="nav" style="margin-right:0">
                    	<?php if($myinfo["is_init"] == 0 ): ?><li><p>该账号配置信息尚未完善，功能还不能使用</p></li>
                    		<?php elseif($myinfo["is_audit"] == 0 and !$reg_audit_switch): ?>
                    		<li><p>该账号配置信息已提交，请等待审核</p></li>
                            <?php elseif($index_2 == 'home/public/*' or $index_3 == 'home/user/profile' or $index_2 == 'home/publiclink/*' or $index_3 == 'home/user/bind_login'): ?>
                    		
                    		<?php else: ?> 
                    		<?php if(is_array($core_top_menu)): $i = 0; $__LIST__ = $core_top_menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$ca): $mod = ($i % 2 );++$i;?><li data-id="<?php echo ($ca["id"]); ?>" class="<?php echo ($ca["class"]); ?>"><a href="<?php echo ($ca["url"]); ?>"><?php echo ($ca["title"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; endif; ?>
                    	
                    	
                        
                        <li class="dropdown admin_nav">
                            <a href="#" class="dropdown-toggle login-nav" data-toggle="dropdown" style="">
                                <?php if(!empty($myinfo[headimgurl])): ?><img class="admin_head url" src="<?php echo ($myinfo["headimgurl"]); ?>"/>
                                <?php else: ?>
                                    <img class="admin_head default" src="/weiphp30/Public/Home/images/default.png"/><?php endif; ?>
                                <?php echo (getShort($myinfo["nickname"],4)); ?><b class="pl_5 fa fa-sort-down"></b>
                            </a>
                            <ul class="dropdown-menu" style="display:none">
                               <?php if($mid==C('USER_ADMINISTRATOR')): ?><li><a href="<?php echo U ('Admin/Index/Index');?>" target="_blank">后台管理</a></li><?php endif; ?>
                            	<li><a href="<?php echo U ('Home/Public/lists');?>">公众号列表</a></li>
                                <li><a href="<?php echo U ('Home/Public/add');?>">账号配置</a></li>
                                <li><a href="<?php echo U('User/profile');?>">修改密码</a></li>
                                <li><a href="<?php echo U('User/logout');?>">退出</a></li>
                            </ul>
                        </li>
                    </ul>
                <?php else: ?>
                    <ul class="nav" style="margin-right:0">
                    	<li style="padding-right:20px">你好!欢迎来到<?php echo C('WEB_SITE_TITLE');?></li>
                        <li>
                            <a href="<?php echo U('User/login');?>">登录</a>
                        </li>
                        <li>
                            <a href="<?php echo U('User/register');?>">注册</a>
                        </li>
                        <li>
                            <a href="<?php echo U('admin/index/index');?>" style="padding-right:0">后台入口</a>
                        </li>
                    </ul><?php endif; ?>
            </div>
        </div>
</div>
	<!-- /头部 -->
	
	<!-- 主体 -->
	
<?php  if(!is_login()){ Cookie ( '__forward__', $_SERVER ['REQUEST_URI'] ); redirect(U('home/user/login',array('from'=>4))); } ?>
<div id="main-container" class="admin_container">
  <?php if(!empty($core_side_menu)): ?><div class="sidebar">
      <ul class="sidenav">
        <li>
          <?php if(!empty($now_top_menu_name)): ?><a class="sidenav_parent" href="javascript:;"> 
            <!--<img src="/weiphp30/Public/Home/images/left_icon_<?php echo ($core_side_category["left_icon"]); ?>.png"/>--> 
            <?php echo ($now_top_menu_name); ?></a><?php endif; ?>
          <ul class="sidenav_sub">
            <?php if(is_array($core_side_menu)): $i = 0; $__LIST__ = $core_side_menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li class="<?php echo ($vo["class"]); ?>" data-id="<?php echo ($vo["id"]); ?>"> <a href="<?php echo ($vo["url"]); ?>"> <?php echo ($vo["title"]); ?> </a><b class="active_arrow"></b></li><?php endforeach; endif; else: echo "" ;endif; ?>
          </ul>
        </li>
        <?php if(!empty($addonList)): ?><li> <a class="sidenav_parent" href="javascript:;"> <img src="/weiphp30/Public/Home/images/ico1.png"/> 其它功能</a>
            <ul class="sidenav_sub" style="display:none">
              <?php if(is_array($addonList)): $i = 0; $__LIST__ = $addonList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li class="<?php echo ($navClass[$vo[name]]); ?>"> <a href="<?php echo ($vo[addons_url]); ?>" title="<?php echo ($vo["description"]); ?>"> <i class="icon-chevron-right">
                  <?php if(!empty($vo['icon'])) { ?>
                  <img src="<?php echo ($vo["icon"]); ?>" />
                  <?php } ?>
                  </i> <?php echo ($vo["title"]); ?> </a> </li><?php endforeach; endif; else: echo "" ;endif; ?>
            </ul>
          </li><?php endif; ?>
      </ul>
    </div><?php endif; ?>
  <div class="main_body">
    
  <script type="text/javascript" src="/weiphp30/Public/static/qrcode/qrcode.js"></script>
  <script type="text/javascript" src="/weiphp30/Public/static/qrcode/jquery.qrcode.js"></script>
  <style type="text/css">
#phone {
	margin: 0 auto;
	width: 510px;
	height: 814px;
	background: url(/weiphp30/Public/Home/images/preview_phone.jpg) no-repeat;
}
#frame {
	margin: 100px 0 0 97px;
	width: 320px;
	border: 2px solid #333;
}
#qrcode {
	position: absolute;
	top: 200px;
	left: 50%;
	margin-left: 230px;
	width: 200px;
	height: 300px;
	text-align: center;
	line-height: 30px;
}
</style>
  <div class="setting_step app_setting content_step" style="margin-top:20px;">
         <a class="step " href="<?php echo addons_url('WeiSite://Template/index',array('mdm'=>I('mdm')));?>">1.选择模板</a>
       	 <a class="step " href="<?php echo addons_url('WeiSite://Slideshow/lists',array('mdm'=>I('mdm')));?>">2.幻灯片配置</a>
         <a class="step step_cur" href="<?php echo addons_url('WeiSite://Category/lists',array('mdm'=>I('mdm')));?>">3.栏目配置</a> 
         <a class="step step_cur_1" href="<?php echo addons_url('WeiSite://WeiSite/preview',array('mdm'=>I('mdm')));?>">4.效果预览</a>
  </div>
  <div class="tab-content">
   
    <form method="post" class="form-horizontal" action="<?php echo U('config',array('from'=>'preview'));?>">
      <div class="form-item cf">
        <label class="item-label"> 背景图: </label>
        <?php $data['show_background'] = intval($data['show_background']); ?>
        <div class="controls">
          <div class="check-item">
            <input type="radio" name="config[show_background]" value="0" class="select_bg" <?php if(($data[show_background]) == "0"): ?>checked="checked"<?php endif; ?> class="regular-radio" id="config[show_background]_0">
            <label for="config[show_background]_0"></label>
            使用默认背景图 </div>
          <div class="check-item">
            <input type="radio" name="config[show_background]" value="1" class="select_bg" <?php if(($data[show_background]) == "1"): ?>checked="checked"<?php endif; ?> class="regular-radio" id="config[show_background]_1">
            <label for="config[show_background]_1"></label>
            使用自定义背景图 </div>
        </div>
      </div>
      <div class="form-item cf" id="upload_bg" style="display:none">
        <label class="item-label"> 自定义背景图: <span class="check-tips">为空时不显示背景图片，最佳尺寸：640X1008,或上传比例为640X1008的更大尺寸图片</span> </label>
<!--         <div class="controls uploadrow2" title="点击修改图片" rel="background"> -->
<!--           <input type="file" id="upload_picture_background"> -->
<!--           <input type="hidden" name="config[background]" id="cover_id_background" value="<?php echo ($data['background']); ?>"/> -->
<!--           <div class="upload-img-box"> -->
<!--             <?php if(!empty($data['background'])): ?>-->
<!--               <div class="upload-pre-item2"><img width="100" height="100" src="<?php echo (get_cover_url($data['background'])); ?>"/></div> -->
<!--               <em class="edit_img_icon">&nbsp;</em><?php endif; ?> -->
<!--           </div> -->
<!--         </div> -->
			 <div class="mult_imgs">
                                <div class="upload-img-view" id='mutl_picture_background'>
                                  <?php if(!empty($data[background_arr])): if(is_array($data[background_arr])): $i = 0; $__LIST__ = $data[background_arr];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="upload-pre-item22">
                                    <img width="100" height="100" src="<?php echo (get_cover_url($vo)); ?>"/>
                                    <input type="hidden" name="background[]" value="<?php echo ($vo); ?>"/>
                                    <em>&nbsp;</em>
                                    </div><?php endforeach; endif; else: echo "" ;endif; endif; ?>
                                </div>
                                <div class="controls uploadrow2" data-max="9" title="点击上传图片" rel="background">
                                  <input type="file" id="upload_picture_background">
                                </div>
                            </div>
<!-- 			 <div class="mult_imgs"> -->
<!--                                 <div class="upload-img-view" id='mutl_picture_background'> -->
<!--                                   <?php if(!empty($data["background_arr"])): ?>-->
<!--                                   	<?php if(is_array($data["background_arr"])): $i = 0; $__LIST__ = $data["background_arr"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>-->
<!--                                     <div class="upload-pre-item22"> -->
<!--                                     <img width="100" height="100" src="<?php echo (get_cover_url($vo)); ?>"/> -->
<!--                                     <input type="hidden" name="background[]" value="<?php echo ($vo); ?>"/> -->
<!--                                     <em>&nbsp;</em> -->
<!--                                     </div> -->
<!--<?php endforeach; endif; else: echo "" ;endif; ?> -->
<!--<?php endif; ?> -->
<!--                                 </div> -->
<!--                                 <div class="controls uploadrow2 mult" title="点击上传图片" rel="background"> -->
<!--                                   <input type="file" id="upload_picture_background"> -->
<!--                                 </div> -->
<!--                             </div> -->

      </div>
      <button style="margin-top:20px;" type="submit" class="btn submit-btn ajax-post" target-form="form-horizontal">确 定</button>
    </form>
  </div>
  <div style="margin:20px; clear:both;position:relative; border-top:1px solid #ddd; padding:20px 0;">
  <div id="phone" style="margin:0 0 80px">
    <iframe id="frame" src="<?php echo ($url); ?>" height="565" width="320" frameborder="0"></iframe>
  </div>
  <div id="qrcode" style="top:50px; left:360px;">
    <div id="canvas" style="height:200px; width:200px;"></div>
    用微信扫一扫预览 </div>
    </div>

  </div>
</div>

	<!-- /主体 -->

	<!-- 底部 -->
	<div class="wrap bottom" style="background:#fff; border-top:#ddd;">
    <p class="copyright">本系统由<a href="http://weiphp.cn" target="_blank">WeiPHP</a>强力驱动</p>
</div>

<script type="text/javascript">
(function(){
	var ThinkPHP = window.Think = {
		"ROOT"   : "/weiphp30", //当前网站地址
		"APP"    : "/weiphp30/index.php?s=", //当前项目地址
		"PUBLIC" : "/weiphp30/Public", //项目公共目录地址
		"DEEP"   : "<?php echo C('URL_PATHINFO_DEPR');?>", //PATHINFO分割符
		"MODEL"  : ["<?php echo C('URL_MODEL');?>", "<?php echo C('URL_CASE_INSENSITIVE');?>", "<?php echo C('URL_HTML_SUFFIX');?>"],
		"VAR"    : ["<?php echo C('VAR_MODULE');?>", "<?php echo C('VAR_CONTROLLER');?>", "<?php echo C('VAR_ACTION');?>"]
	}
})();
</script>

  <script type="text/javascript">
    	var url = "<?php echo ($url); ?>&publicid=<?php echo ($public_info["id"]); ?>";
        $('#canvas').qrcode({width:200,height:200,text:url}); 

	$(function(){
		//初始化上传图片插件
		initUploadImg();
		
		show_bg();
		$('.select_bg').click(function(){ show_bg(); });
	})
	
	function show_bg(){
		var val = $('input[name="config[show_background]"]:checked').val();
// 		console.log(val);
		if(val==0){
			$('#upload_bg').hide();
		}else{
			$('#upload_bg').show();
		}
	}
</script>
 <!-- 用于加载js代码 -->
<!-- 页面footer钩子，一般用于加载插件JS文件和JS代码 -->
<?php echo hook('pageFooter', 'widget');?>
<div style='display:none'><?php echo ($tongji_code); ?></div>
<div class="hidden"><!-- 用于加载统计代码等隐藏元素 -->
	
</div>

	<!-- /底部 -->
</body>
</html>