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
    
<style text='text/css'>
.image_material{
    border: 1px dashed #ddd;
    width: 308px;
    height: 196px;
     background: #eee;
    text-align: center;
    color: #333;
    display: block;
    float: left;
	margin-left:10px;
   display:none;
   position:relative;
}
.image_material .select_image{line-height: 196px; display:block; height:200px;}
.image_material .delete{ position:absolute; bottom:3px; left:3px; display:none}
.appmsg_area{ position:relative;}
.appmsg_area .delete{ position:absolute; bottom:10px; left:10px; z-index:1000; margin:10px;}
.appmsg_area .select_video{  height: 240px;line-height: 240px;}
.voice_wrap{ width:308px;}
.video_wrap{ width:222px;}
#video_area{ height:250px}
</style>
  <div class="span9 page_message">
  
    <section id="contents"> <ul class="tab-nav nav">
  <?php if(is_array($nav)): $i = 0; $__LIST__ = $nav;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li class="<?php echo ($vo["class"]); ?>"><a href="<?php echo ($vo["url"]); ?>"><?php echo ($vo["title"]); ?><span class="arrow fa fa-sort-up"></span></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
  
  <?php if (defined ( '_ADDONS' )) { $page = _ADDONS . '_' . _CONTROLLER . '_' . _ACTION; } else { $page = MODULE_NAME . '_' . CONTROLLER_NAME . '_' . ACTION_NAME; } $url = 'http://help.weiphp.cn/index.php?s=/Home/Help/index/page/' . strtolower($page); ?>
  <span class="fr" style="margin:10px;"><a target="_blank" href="<?php echo ($url); ?>"><b style="font-size:16px;" class="fa fa-question-circle"></b>查看配置教程</a></span>
</ul>
<?php if(!empty($sub_nav)): ?><div class="sub-tab-nav">
       <ul class="sub_tab">
       <?php if(is_array($sub_nav)): $i = 0; $__LIST__ = $sub_nav;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li><a class="<?php echo ($vo["class"]); ?>" href="<?php echo ($vo["url"]); ?>"><?php echo ($vo["title"]); ?><span class="arrow fa fa-sort-up"></span></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
      </ul>
</div><?php endif; ?>
<?php if(!empty($normal_tips)): ?><p class="normal_tips"><b class="fa fa-info-circle"></b> <?php echo ($normal_tips); ?></p><?php endif; ?>
    
      <div class="tab-content"> 
      
      <div class="message_list" style="padding:10px;  text-align: left;color: #333;padding: 10px;
background-color: #eaeaea;">
           <div class="msg_tab">
               <a class="txt current" href="javascript:;">文本消息</a>
                <a class="appmsg" href="javascript:;">图文消息</a>
                <a class="image" href="javascript:;">图片消息</a>
                <a class="voice" href="javascript:;">语音消息</a>
                <a class="video" href="javascript:;">视频消息</a>
            </div>
            <?php $post_url || $post_url = U('add?model='.$model['id'], $get_param); ?>
            <form id="form" action="<?php echo ($post_url); ?>" method="post" class="form-horizontal form-center">
                <input type="hidden" name="msg_type" value="text"/>
                <label class="textarea" style="position:relative; overflow:hidden; zoom:1;">
                	<a id="getText" class="txt_icon current" onClick="selectText();" style="position: absolute; bottom: 20px;left: 10px;cursor: pointer; color: #888;  border-radius: 5px; border: 1px solid #ccc;  padding: 5px 20px; background-color: #eee;">选择文本素材</a>
                    <textarea name="content" placeholder="请输入要发送的文本"  id='message_text'></textarea>
                    <div style="display:none" class="appmsg_area" id="appmsg_area">
                        <input type="hidden" name="appmsg_id" value="0"/>
                        <a class="select_appmsg" href="javascript:;" onClick="$.WeiPHP.openSelectAppMsg('<?php echo U('/Home/Material/material_data');?>',selectAppMsgCallback)">选择图文</a>
                        <div class="appmsg_wrap"></div>
                        <a class="delete" href="javascript:;" style="left: 310px;">删除</a>
                    </div>
                    
                    <div style="display:none;margin:0 10px 10px 0; background:#ddd; padding:6px;height:204px; width:930px;float:left" class="msg_image controls">
                    	<div class="uploadrow2" rel="image" title="点击修改图片" style="float:left; width:308px;">
                            <input type="file" id="upload_picture_image">
                            <input type="hidden" name="image" id="cover_id_image" value="0"/>
                            <div class="upload-img-box" style="display:none;">
                                <div class="upload-pre-item2"><img width="100" height="100" src=""/></div>
                            </div>
                        </div>
                        <div class='image_material' id='image_material'>
                            <input type="hidden" name="image_material" id="cover_id_image" value="0"/>
                            <a class="select_image" href="javascript:;"  onClick="$.WeiPHP.openSelectAppMsg('<?php echo U('/Home/Material/picture_data');?>',selectImageCallback,'选择图片素材')">从素材库选择图片</a>
                            <div class="image_wrap"></div>
                            <a class="delete" href="javascript:;" style="left: 15px;" >删除</a>
                         </div>
                    </div>
                     
                     <div style="display:none" class="appmsg_area" id="voice_area">
                        <input type="hidden" name="voice_id" value="0"/>
                        <a class="select_appmsg" href="javascript:;" onClick="$.WeiPHP.openSelectAppMsg('<?php echo U('/Home/Material/voice_data');?>',selectVoiceCallback,'选择语音素材')">选择语音素材</a>
                        <div class="voice_wrap"></div>
                        <a class="delete" href="javascript:;" style="left: 310px;display: inline;">删除</a>
                    </div>
                      <div style="display:none" class="appmsg_area" id="video_area">
                        <input type="hidden" name="video_id" value="0"/>
                        <a class="select_appmsg select_video" href="javascript:;" onClick="$.WeiPHP.openSelectAppMsg('<?php echo U('/Home/Material/video_data');?>',selectVideoCallback,'选择视频素材')">选择视频素材</a>
                        <div class="video_wrap"></div>
                        <a class="delete" href="javascript:;"  style="left: 310px;">删除</a>
                    </div>
                 </label>
                 
                 <!--
                 <div class="action_type">
                    <a class="action_item face" href="javascript:;" title="表情">&nbsp;</a>
                    <a class="action_item link" href="javascript:;" title="连接">&nbsp;</a>
                    <div class="action_item picture" href="javascript:;" title="图片">
                        <div class="controls uploadrow2" data-max="1" title="点击修改图片" rel="pic">
                          <input type="file" id="upload_picture_pic">
                          <input type="hidden" name="pic" id="cover_id_pic" value="0"/>
                          <div class="upload-img-box">
                           
                              <div class="upload-pre-item2"><img width="100" height="100" src=""/></div>
                            
                          </div>
                      </div>
                    </div>
                    <a class="action_item article" href="javascript:;" title="图文">&nbsp;</a>
                 </div>
                 -->
                 <div class="form-item cf toggle-send_type">
                    <label class="item-label"> 发送方式 <span class="check-tips"> </span></label>
                    <div class="controls">
                      <select name="send_type">
                        <option selected="" toggle-data="group_id@show,send_openids@hide" class="toggle-data" value="0">按用户组发送 </option>
                        <option toggle-data="group_id@hide,send_openids@show" class="toggle-data" value="1">指定OpenID发送 </option>
                      </select>
                    </div>
                  </div>
                  <div class="form-item cf toggle-group_id">
                        <label class="item-label"> 群发对象 <span class="check-tips"> （全部用户或者某分组用户） </span></label>
                        <div class="controls">
                          <div id="dynamic_select_group_id"></div>
                          <select name="group_id">
                            <option toggle-data="全部用户" class="toggle-data" value="0">全部用户</option>
                            <?php if(is_array($group_list)): $i = 0; $__LIST__ = $group_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option toggle-data="" class="toggle-data" value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["title"]); ?> </option><?php endforeach; endif; else: echo "" ;endif; ?>

                          </select>
                        </div>
                      </div>
                      <div class="form-item cf toggle-send_openids" style="display: none;">
                      	<a class="border-btn" href="javascript:;" onClick="selectUser('send_openids')">选择指定的用户</a>
                        <br/>
                        <div class="mt10">指定的用户：<span id="send_openids" class="colorless"></span></div>
                        <br/>
                        <div style="display:none">
                            <label class="item-label"> 要发送的OpenID <span class="check-tips"> （多个可用逗号或者换行分开，OpenID值可在微信用户的列表中找到） </span></label>
                            <div class="controls">
                              <label class="textarea input-large">
                                <textarea name="send_openids"></textarea>
                              </label>
                            </div>
                        </div>
                      </div>
                      <div class="form-item cf toggle-preview_openids">
                      	<a class="border-btn" href="javascript:;" onClick="selectUser('preview_openids')">选择预览人</a>
                        <br/>
                        <div class="mt10">预览人：<span id="preview_openids" class="colorless"></span></div>
                        <br/>
                        <div style="display:none">
                            <label class="item-label"> 预览人OPENID <span class="check-tips"> （选填，多个可用逗号或者换行分开，OpenID值可在微信用户的列表中找到） </span></label>
                            <div class="controls">
                              <label class="textarea input-large">
                                <textarea name="preview_openids" style="height:50px; min-height:50px;"></textarea>
                              </label>
                            </div>
                        </div>
                      </div>
                      
                 <button class="btn submit-btn ajax-post" id="submit1" type="button" target-form="form-horizontal">群 发</button>
                  &nbsp;&nbsp;&nbsp;&nbsp;
                  <button onclick="preview()" class="border-btn submit-btn ajax-post">预览</button>
           		       	
            </form>
        </div>
       
      </div>
    </section>
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

  <link href="/weiphp30/Public/static/datetimepicker/css/datetimepicker.css?v=<?php echo SITE_VERSION;?>" rel="stylesheet" type="text/css">
  <?php if(C('COLOR_STYLE')=='blue_color') echo '
    <link href="/weiphp30/Public/static/datetimepicker/css/datetimepicker_blue.css?v=<?php echo SITE_VERSION;?>" rel="stylesheet" type="text/css">
    '; ?>
  <link href="/weiphp30/Public/static/datetimepicker/css/dropdown.css?v=<?php echo SITE_VERSION;?>" rel="stylesheet" type="text/css">
  <script type="text/javascript" src="/weiphp30/Public/static/datetimepicker/js/bootstrap-datetimepicker.js"></script> 
  <script type="text/javascript" src="/weiphp30/Public/static/datetimepicker/js/locales/bootstrap-datetimepicker.zh-CN.js?v=<?php echo SITE_VERSION;?>" charset="UTF-8"></script> 
  <script type="text/javascript">
  $('#submit1').click(function(){
		if(confirm("确定要群发消息？")){
			$('#form').submit();
		}
	});

function preview(){
	if(confirm("确定要预览消息？")){
			var query = $('#form').serialize();
	  		$.post("<?php echo U('preview');?>",query,function(data){
			if (data.status==1) {
				  if (data.url) {
						updateAlert(data.info + ' 页面即将自动跳转~','alert-success');
				  }else{
						updateAlert(data.info ,'alert-success');
				  }
				  setTimeout(function(){
						if (data.url) {
							  location.href=data.url;
						}else{
							  location.reload();
						}
				  },1500);
			}else{
				  updateAlert(data.info);
				  setTimeout(function(){
						if (data.url) {
							  location.href=data.url;
						}else{
							  $('#top-alert').find('button').click();
						}
				  },1500);
			}
	  	})
	}
	  
}

$(function(){
	
	showTab();
	
	$('.toggle-data').each(function(){
		var data = $(this).attr('toggle-data');
		if(data=='') return true;		
		
	     if($(this).is(":selected") || $(this).is(":checked")){
			 change_event(this)
		 }
	});
	
	$('select').change(function(){
		$('.toggle-data').each(function(){
			var data = $(this).attr('toggle-data');
			if(data=='') return true;		
			
			 if($(this).is(":selected") || $(this).is(":checked")){
				 change_event(this)
			 }
		});
	});
});
$(function(){
	//初始化上传图片插件
	initUploadImg({width:308,height:200,callback:function(){
		$('.image_wrap').html('').hide();
		$('.select_image').show();
		$('.image_material .delete').hide();
		$('input[name="image_material"]').val(0);
	}});
	$('.uploadify-button').css('background-color','#ccc')
})
$('.msg_tab .txt').click(function(){
	//纯文本
	$(this).addClass('current').siblings().removeClass('current');
	$('input[name="msg_type"]').val('text');
	$('textarea[name="content"]').show().siblings().hide();
	$('#getText').show();
})
$('.msg_tab .appmsg').click(function(){
	//图文消息
	$(this).addClass('current').siblings().removeClass('current');
	$('input[name="msg_type"]').val('appmsg');
	$('#appmsg_area').show().siblings().hide();
	$('#getText').hide();
})
$('.msg_tab .image').click(function(){
	//图片消息
	$(this).addClass('current').siblings().removeClass('current');
	$('input[name="msg_type"]').val('image');
	$('.msg_image').show().siblings().hide();
	$('#getText').hide();
	$('#image_material').show();
})
$('.msg_tab .voice').click(function(){
	//图片消息
	$(this).addClass('current').siblings().removeClass('current');
	$('input[name="msg_type"]').val('voice');
	$('#voice_area').show().siblings().hide();
	$('#getText').hide();
	$('#image_material').hide();
})
$('.msg_tab .video').click(function(){
	//图片消息
	$(this).addClass('current').siblings().removeClass('current');
	$('input[name="msg_type"]').val('video');
	$('#video_area').show().siblings().hide();
	$('#getText').hide();
	$('#image_material').hide();
})

$('#appmsg_area .delete').click(function(){
	$('#appmsg_area .appmsg_wrap').html('').hide();
	$('#appmsg_area .select_appmsg').show();
	$('#appmsg_area .delete').hide();
	$('input[name="appmsg_id"]').val(0);
})
$('.image_material .delete').click(function(){
	$('.image_wrap').html('').hide();
	$('.select_image').show();
	$('.image_material .delete').hide();
	$('input[name="image_material"]').val(0);
})
$('#voice_area .delete').click(function(){
	$('#voice_area .voice_wrap').html('').hide();
	$('#voice_area .select_appmsg').show();
	$('#voice_area .delete').hide();
	$('input[name="voice"]').val(0);
})
$('#video_area .delete').click(function(){
	$('#video_area .video_wrap').html('').hide();
	$('#video_area .select_appmsg').show();
	$('#video_area .delete').hide();
	$('input[name="video"]').val(0);
})
function selectAppMsgCallback(_this){
	$('.appmsg_wrap').html($(_this).html()).show();
	$('#appmsg_area .select_appmsg').hide();
	$('#appmsg_area .delete').show();
	$('input[name="appmsg_id"]').val($(_this).data('group_id'));
	$.Dialog.close();
}
function selectImageCallback(_this){
	$('.image_wrap').html($(_this).html()).show();
	$('.select_image').hide();
	$('.image_material .delete').show();
	$('input[name="image_material"]').val($(_this).data('id'));
	$("input[name='image']").val(0);
	$('.upload-img-box').hide();
	$.Dialog.close();
}
function selectVoiceCallback(_this){
	$(_this).find('.icon_sound').attr('src',IMG_PATH+'/icon_sound.png');
	$('.voice_wrap').html($(_this).html()).show();
	$('#voice_area .select_appmsg').hide();
	$('#voice_area .delete').show();
	$('input[name="voice_id"]').val($(_this).data('id'));
	$.Dialog.close();
}
function selectVideoCallback(_this){
	$('.video_wrap').html($(_this).html()).show();
	$('#video_area .select_appmsg').hide();
	$('#video_area .delete').show();
	$('input[name="video_id"]').val($(_this).data('id'));
	$.Dialog.close();
}
function selectText(){
	$.WeiPHP.openSelectLists("<?php echo U('Material/text_lists');?>",1,'选择素材',function(data){
		if(data && data.length>0){
			for(var i=0;i<data.length;i++){
				var id=data[i]['id'];
				if(id){
					$.post("<?php echo U('Material/get_content_by_id');?>",{'id':id},function(d){
						$('#message_text').val(d);
					})
				}
			}
		}
	})
}
// function selectText(){
// 	$.WeiPHP.openSelectUsers("<?php echo U('Material/text_lists');?>",1,function(data){
// 		if(data && data.length>0){
// 			for(var i=0;i<data.length;i++){
// 				var id=data[i]['id'];
// 				if(id){
// 					$.post("<?php echo U('Material/get_content_by_id');?>",{'id':id},function(d){
// 						$('#message_text').text(d);
// 					})
// 				}
// 			}
// 		}
// 	})
// }
function selectUser(name){
	var seIds = $('textarea[name="'+name+'"]').val();
	$.WeiPHP.openSelectUsers("<?php echo addons_url('UserCenter://UserCenter/lists');?>&seIds="+seIds,0,function(data){
		if(data && data.length>0){
			var idsArr  = new Array();
			var nameArr = new Array();
			for(var i=0;i<data.length;i++){
				idsArr.push(data[i].openid);
				nameArr.push(data[i].nickname);
			}
			$('textarea[name="'+name+'"]').val(idsArr.toString());
			$('#'+name).text(nameArr.toString());
		}else{
			$('textarea[name="'+name+'"]').val('');
			$('#'+name).text('');
		}
	})
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