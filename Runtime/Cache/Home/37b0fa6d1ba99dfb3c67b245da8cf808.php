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
      <div class="table-bar">
      	<div class="fl">
          <?php if(empty($model["extend"])): ?><div class="tools">
				<?php if($add_button): $add_url || $add_url = U('add?model='.$model['id'], $get_param); ?><a class="btn" href="<?php echo ($add_url); ?>">新 增</a><?php endif; ?>
				<?php if(C('MATERIAL')) { ?>
                <a class="btn" href="<?php echo U('syc_news_to_wechat',array('mdm'=>$_GET['mdm']));?>">一键上传素材到微信素材库</a>
                <a class="btn" href="<?php echo U('syc_news_from_wechat',array('mdm'=>$_GET['mdm']));?>">一键下载微信素材库到本地</a>
                 <?php } ?>

                <?php if(is_array($top_more_button)): $i = 0; $__LIST__ = $top_more_button;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if($vo[is_buttion]): ?><button class="btn <?php echo ($vo["class"]); ?>" target-form="ids" url="<?php echo ($vo["url"]); ?>"><?php echo ($vo["title"]); ?></button>
                <?php else: ?>
                <a class="btn" href="<?php echo ($vo["url"]); ?>"><?php echo ($vo["title"]); ?></a><?php endif; ?>
                &nbsp;<?php endforeach; endif; else: echo "" ;endif; ?>
			</div><?php endif; ?>
        </div>
        <div class="search-form fr cf">
          <div class="sleft">
            <?php $search_url = U('material_lists', $get_param); ?>
            <?php empty($search_key) && $search_key=$model['search_key'];empty($search_key) && $search_key='title'; ?>
            <input type="text" name="<?php echo ($search_key); ?>" class="search-input" value="<?php echo I($search_key);?>" placeholder="<?php echo ($placeholder); ?>">
            <a class="sch-btn" href="javascript:;" id="search" url="<?php echo ($search_url); ?>"><i class="btn-search"></i></a> </div>
        </div>
      </div>
      <!-- 数据列表 -->
      <div class="data-table">

        <div class="table-striped">
        	<ul class="material_list js-masonry"  data-masonry-options='{ "itemSelector": ".appmsg_li", "columWidth": 308 }'>
            <?php if(is_array($list_data)): $i = 0; $__LIST__ = $list_data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if($vo[count]==1): ?><!-- 单图文 -->
                <li class="appmsg_li">
                	<div class="appmsg_item">
                        <h6 class="ellipsis"><?php echo ($vo["title"]); ?></h6>
                        <p class="title"><?php echo (time_format($vo["cTime"])); ?></p>
                        <div class="main_img">
                            <img src="<?php echo (get_cover_url($vo["cover_id"])); ?>"/>
                        </div>
                        <p class="desc ellipsis"><?php echo ($vo["intro"]); ?></p>
                    </div>
                    <div class="appmsg_action">
                    	<a href="<?php echo U('add_material', array('group_id'=>$vo[group_id],'mdm'=>$_GET['mdm']));?>">编辑</a>
                        <a href="<?php echo U('del_material_by_groupid', array('group_id'=>$vo[group_id],'mdm'=>$_GET['mdm']));?>">删除</a>
                    </div>
                </li>
                <?php else: ?>
                <!-- 多图文 -->
                <li class="appmsg_li">
                	<div class="appmsg_item">
                        <p class="title"><?php echo (time_format($vo["cTime"])); ?></p>
                        <div class="main_img">
                            <img src="<?php echo (get_cover_url($vo["cover_id"])); ?>"/>
                            <h6><?php echo ($vo["title"]); ?></h6>
                        </div>
                        <p class="desc ellipsis"><?php echo ($vo["intro"]); ?></p>
                    </div>
                    <?php if(is_array($vo["child"])): $i = 0; $__LIST__ = $vo["child"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vv): $mod = ($i % 2 );++$i;?><div class="appmsg_sub_item">
                        <p class="title ellipsis"><?php echo ($vv["title"]); ?></p>
                        <div class="main_img">
                            <img src="<?php echo (get_cover_url($vv["cover_id"])); ?>"/>
                        </div>
                    </div><?php endforeach; endif; else: echo "" ;endif; ?>
                    <div class="appmsg_action">
                    	<a href="<?php echo U('add_material', array('group_id'=>$vo[group_id]));?>">编辑</a>
                        <a href="<?php echo U('del_material_by_groupid', array('group_id'=>$vo[group_id]));?>">删除</a>
                    </div>
                </li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
            </ul>

        </div>
      </div>
      <div class="page"> <?php echo ((isset($_page) && ($_page !== ""))?($_page):''); ?> </div>
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

  <script type="text/javascript">
$(function(){
	//搜索功能
    $("#search").click(function(){
        var url = $(this).attr('url');
        var str = $('.search-input').val()
        var query  = 'title='+str.replace(/(^\s*)|(\s*$)/g,"");
        if( url.indexOf('?')>0 ){
            url += '&' + query;
        }else{
            url += '?' + query;
        }
        window.location.href = url;
    });

    //回车自动提交
    $('.search-form').find('input').keyup(function(event){
        if(event.keyCode===13){
            $("#search").click();
        }
    });

})
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