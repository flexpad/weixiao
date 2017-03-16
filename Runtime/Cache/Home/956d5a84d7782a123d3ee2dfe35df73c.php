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
    
  
  <!-- 标签页导航 -->
  <div class="span9 page_message">
    <section id="contents">
      <ul class="tab-nav nav">
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
        <!-- 表单 -->
        <?php $post_url || $post_url = U('edit?model='.$model['id'], $get_param); ?>
        <form id="form" action="<?php echo ($post_url); ?>" method="post" class="form-horizontal form-center">
          
              <?php if(is_array($fields)): $i = 0; $__LIST__ = $fields;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$field): $mod = ($i % 2 );++$i; if($field['is_show'] == 4): ?><input type="hidden" class="text" name="<?php echo ($field["name"]); ?>" value="<?php echo ($data[$field['name']]); ?>"><?php endif; ?>
                <?php if($field['is_show'] == 1 || $field['is_show'] == 3 || ($field['is_show'] == 5 && I($field['name']) )): ?><div class="form-item cf toggle-<?php echo ($field["name"]); ?>">
                    <label class="item-label">
                    <?php if(!empty($field["is_must"])): ?><span class="need_flag">*</span><?php endif; ?>
                    <?php echo ($field['title']); ?>
                    <span class="check-tips">
                      <?php if(!empty($field['remark'])): ?>（<?php echo ($field['remark']); ?>）<?php endif; ?>
                      </span></label>
                    <div class="controls">
                      <?php switch($field["type"]): case "num": ?><input type="number" class="text" name="<?php echo ($field["name"]); ?>" value="<?php echo ($data[$field['name']]); ?>"><?php break;?>
                        <?php case "string": ?><input type="text" class="text input-large" name="<?php echo ($field["name"]); ?>" value="<?php echo ($data[$field['name']]); ?>"><?php break;?>
                        <?php case "textarea": ?><label class="textarea input-large">
                            <textarea name="<?php echo ($field["name"]); ?>"><?php echo ($data[$field['name']]); ?></textarea>
                          </label><?php break;?>
                        <?php case "datetime": ?><input type="datetime" name="<?php echo ($field["name"]); ?>" class="text time" value="<?php echo (time_format($data[$field['name']])); ?>" placeholder="请选择时间" /><?php break;?>
                        <?php case "date": ?><input type="datetime" name="<?php echo ($field["name"]); ?>" class="text date" value="<?php echo (time_format($data[$field['name']],'Y-m-d')); ?>" placeholder="请选择时间" /><?php break;?>                        
                        <?php case "bool": $_result=parse_field_attr($field['extra']);if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="check-item">
							<!--[if !IE]><!-->  
								  <input type="radio" class="regular-radio toggle-data" value="<?php echo ($key); ?>" id="<?php echo ($field["name"]); ?>_<?php echo ($key); ?>" name="<?php echo ($field["name"]); ?>" toggle-data="<?php echo (get_hide_attr($vo)); ?>"
								  <?php if(($data[$field['name']]) == $key): ?>checked="checked"<?php endif; ?> />
								  <label for="<?php echo ($field["name"]); ?>_<?php echo ($key); ?>"></label><?php echo (clean_hide_attr($vo)); ?> 
							  <!--<![endif]-->
							   <!--[if IE]>
							       <input type="radio" value="<?php echo ($key); ?>" 
								   id="<?php echo ($field["name"]); ?>_<?php echo ($key); ?>" name="<?php echo ($field["name"]); ?>" class="toggle-data" toggle-data="<?php echo (get_hide_attr($vo)); ?>"
								  <?php if(($data[$field['name']]) == $key): ?>checked="checked"<?php endif; ?>/> 
								  <label for="<?php echo ($field["name"]); ?>_<?php echo ($key); ?>"></label><?php echo (clean_hide_attr($vo)); ?>
							   <![endif]-->
                             </div><?php endforeach; endif; else: echo "" ;endif; break;?>
                        <?php case "select": ?><select name="<?php echo ($field["name"]); ?>">
                            <?php $_result=parse_field_attr($field['extra']);if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>" class="toggle-data" toggle-data="<?php echo (get_hide_attr($vo)); ?>"
                              <?php if(($data[$field['name']]) == $key): ?>selected<?php endif; ?>
                              ><?php echo (clean_hide_attr($vo)); ?>
                              </option><?php endforeach; endif; else: echo "" ;endif; ?>
                          </select><?php break;?>
                        <?php case "cascade": ?><div id="cascade_<?php echo ($field["name"]); ?>"></div>
                        <?php echo hook('cascade', array('name'=>$field['name'],'value'=>$data[$field['name']],'extra'=>$field['extra'])); break;?>                      
                        <?php case "dynamic_select": ?><div id="dynamic_select_<?php echo ($field["name"]); ?>"></div>
                        <?php echo hook('dynamic_select', array('name'=>$field['name'],'value'=>$data[$field['name']],'extra'=>$field['extra'])); break;?>   
                        <?php case "news": ?><div id="news_<?php echo ($field["name"]); ?>"></div>
                        <?php echo hook('news', array('name'=>$field['name'],'value'=>$data[$field['name']],'extra'=>$field['extra'])); break;?> 
                        <?php case "image": ?><div id="image_<?php echo ($field["name"]); ?>"></div>
                        <?php echo hook('image', array('name'=>$field['name'],'value'=>$data[$field['name']],'extra'=>$field['extra'])); break;?>                                                    
                        <?php case "radio": $_result=parse_field_attr($field['extra']);if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="check-item">
							<!--[if !IE]><!-->  
								  <input type="radio" class="regular-radio toggle-data" value="<?php echo ($key); ?>" id="<?php echo ($field["name"]); ?>_<?php echo ($key); ?>" name="<?php echo ($field["name"]); ?>" toggle-data="<?php echo (get_hide_attr($vo)); ?>"
								  <?php if(($data[$field['name']]) == $key): ?>checked="checked"<?php endif; ?> />
								  <label for="<?php echo ($field["name"]); ?>_<?php echo ($key); ?>"></label><?php echo (clean_hide_attr($vo)); ?> 
							  <!--<![endif]-->
							   <!--[if IE]>
							       <input type="radio" value="<?php echo ($key); ?>" 
								   id="<?php echo ($field["name"]); ?>_<?php echo ($key); ?>" name="<?php echo ($field["name"]); ?>" class="toggle-data" toggle-data="<?php echo (get_hide_attr($vo)); ?>"
								  <?php if(($data[$field['name']]) == $key): ?>checked="checked"<?php endif; ?>/> 
								  <label for="<?php echo ($field["name"]); ?>_<?php echo ($key); ?>"></label><?php echo (clean_hide_attr($vo)); ?>
							   <![endif]-->
                             </div><?php endforeach; endif; else: echo "" ;endif; break;?>
                        <?php case "checkbox": $_result=parse_field_attr($field['extra']);if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="check-item">
                              <input type="checkbox" class="regular-checkbox toggle-data" value="<?php echo ($key); ?>" id="<?php echo ($field["name"]); ?>_<?php echo ($key); ?>" name="<?php echo ($field["name"]); ?>[]" toggle-data="<?php echo (get_hide_attr($vo)); ?>"
                              <?php if(in_array(($key), is_array($data[$field['name']])?$data[$field['name']]:explode(',',$data[$field['name']]))): ?>checked="checked"<?php endif; ?> >
                              <label for="<?php echo ($field["name"]); ?>_<?php echo ($key); ?>"></label><?php echo (clean_hide_attr($vo)); ?> 
                             </div><?php endforeach; endif; else: echo "" ;endif; break;?>
                        <?php case "editor": ?><label class="textarea">
                            <textarea name="<?php echo ($field["name"]); ?>" style="width:405px; height:100px;"><?php echo ($data[$field['name']]); ?></textarea>
                            <?php echo hook('adminArticleEdit', array('name'=>$field['name'],'value'=>$data[$field['name']]));?> </label><?php break;?>
                        <?php case "picture": ?><div class="controls uploadrow2" title="点击修改图片" rel="<?php echo ($field["name"]); ?>">
                            <input type="file" id="upload_picture_<?php echo ($field["name"]); ?>">
                            <input type="hidden" name="<?php echo ($field["name"]); ?>" id="cover_id_<?php echo ($field["name"]); ?>" value="<?php echo ($data[$field['name']]); ?>"/>
                            <div class="upload-img-box">
                              <?php if(!empty($data[$field['name']])): ?><div class="upload-pre-item2"><img width="100" height="100" src="<?php echo (get_cover_url($data[$field['name']])); ?>"/></div>
                                <em class="edit_img_icon">&nbsp;</em><?php endif; ?>
                            </div>
                          </div><?php break;?>
                        <?php case "mult_picture": ?><div class="mult_imgs">
                                <div class="upload-img-view" id='mutl_picture_<?php echo ($field["name"]); ?>'>
                                  <?php if(!empty($data[$field['name']])): if(is_array($data[$field['name']])): $i = 0; $__LIST__ = $data[$field['name']];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="upload-pre-item22">
                                    <img width="100" height="100" src="<?php echo (get_cover_url($vo)); ?>"/>
                                    <input type="hidden" name="<?php echo ($field["name"]); ?>[]" value="<?php echo ($vo); ?>"/>
                                    <em>&nbsp;</em>
                                    </div><?php endforeach; endif; else: echo "" ;endif; endif; ?>
                                </div>
                                <div class="controls uploadrow2 mult" title="点击上传图片" rel="<?php echo ($field["name"]); ?>">
                                  <input type="file" id="upload_picture_<?php echo ($field["name"]); ?>">
                                </div>
                            </div><?php break;?>
                        <?php case "file": ?><div class="controls upload_file" rel="<?php echo ($field["name"]); ?>">
                            <input type="file" id="upload_file_<?php echo ($field["name"]); ?>">
                            <input type="hidden" name="<?php echo ($field["name"]); ?>" value="<?php echo ($data[$field['name']]); ?>"/>
                            <div class="upload-img-box">
                              <?php if(isset($data[$field['name']])): ?><div class="upload-pre-file"><span class="upload_icon_all"></span><?php echo (get_table_field($data[$field['name']],'id','name','File')); ?></div><?php endif; ?>
                            </div>
                          </div><?php break;?>
                        <?php case "user": ?><div class="controls">
                            <div id="userList" class="common_add_list fl">
                                <?php if(!empty($data[$field['name']])): ?><div class="item" onClick="$.WeiPHP.selectSingleUser('<?php echo addons_url('UserCenter://UserCenter/lists');?>','<?php echo ($field["name"]); ?>');">
                                        <?php $userInfo = getUserInfo($data[$field['name']]); ?>
                                        <img src="<?php echo ($userInfo['headimgurl']); ?>"/><br/><span><?php echo ($userInfo['nickname']); ?></span>
                                        <input type="hidden" name="<?php echo ($field["name"]); ?>'" value="<?php echo ($data[$field['name']]); ?>"/>
                                        <span class="name"><?php echo ($userInfo['nickname']); ?></span>
                                    </div>
                                <?php else: ?>
                                <a href="javascript:;" class="common_add_btn fl" onClick="$.WeiPHP.selectSingleUser('<?php echo addons_url('UserCenter://UserCenter/lists');?>','<?php echo ($field["name"]); ?>');"></a><?php endif; ?>
                           </div>
                        </div><?php break;?>
                      <?php case "users": ?><div class="controls">
                            <div id="userList" class="common_add_list fl">
                                <?php if(!empty($data[$field['name']])): $userIds = explode(',',$data[$field['name']]); ?>
                                	<?php if(is_array($userIds)): $i = 0; $__LIST__ = $userIds;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$uid): $mod = ($i % 2 );++$i; $userInfo = getUserInfo($uid); ?>
                                    <div class="item" onClick="$.WeiPHP.selectSingleUser('<?php echo addons_url('UserCenter://UserCenter/lists');?>','<?php echo ($field["name"]); ?>');">
                                        <?php $userInfo = getUserInfo($data[$field['name']]); ?>
                                        <img src="<?php echo ($userInfo['headimgurl']); ?>"/><br/><span><?php echo ($userInfo['nickname']); ?></span>
                                        <input type="hidden" name="<?php echo ($field["name"]); ?>'[]" value="<?php echo ($data[$field['name']]); ?>"/>
                                        <span class="name"><?php echo ($userInfo['nickname']); ?></span>
                                    </div><?php endforeach; endif; else: echo "" ;endif; endif; ?>
                               <a href="javascript:;" class="common_add_btn fl" onClick="$.WeiPHP.selectMutiUser('<?php echo addons_url('UserCenter://UserCenter/lists');?>',9,'<?php echo ($field["name"]); ?>');"></a>
                           </div>
                        </div><?php break;?>
                        <?php default: ?>
                        <input type="text" class="text input-large" name="<?php echo ($field["name"]); ?>" value="<?php echo ($data[$field['name']]); ?>"><?php endswitch;?>
                    </div>
                  </div><?php endif; endforeach; endif; else: echo "" ;endif; ?>
            <?php if(($data["pid"]) == "0"): ?><a id="getText" class="txt_icon current" onClick="$.WeiPHP.openSelectAppMsg('<?php echo addons_url('WeiSite://Template/list_subcate');?>',selectAppMsgCallback,'选择二级模板')" style=" /* bottom: 20px; *//* left: 10px; */cursor: pointer; color: #4A3939;  border-radius: 5px; border: 1px solid #A59D9D;  padding: 5px 20px; background-color: #B3B4B9;hover: color:red;box-shadow: 3px 3px #BDB8B8;">选择二级模板</a>
           
           <div style="position: absolute; right: 10%; top: 10%;"> 
           <a href='javascript:;' id='temple_del'>删除</a>
          <ul class="template_select">
                    <li class="template_item">
                    	<div class="phone">
                        	<img src="<?php echo ($tmp_img); ?>" id='tmpimg'/>
                        </div>
                    </li>
                </ul>
                
            </div><?php endif; ?>
        	<input type='hidden' id='temp' name='template' value='<?php echo ($data["template"]); ?>'/>
        </div>
        <div class="form-item form_bh" style="text-align:center">
            <?php if(!empty($data["id"])): ?><input type="hidden" name="id" value="<?php echo ($data["id"]); ?>"><?php endif; ?>
            <button class="btn submit-btn ajax-post" id="submit" type="submit" target-form="form-horizontal"><?php echo ((isset($submit_name) && ($submit_name !== ""))?($submit_name):'确 定'); ?></button>
          </div>
      </form>
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
  <script type="text/javascript" src="/weiphp30/Public/static/datetimepicker/js/bootstrap-datetimepicker.min.js"></script> 
  <script type="text/javascript" src="/weiphp30/Public/static/datetimepicker/js/locales/bootstrap-datetimepicker.zh-CN.js?v=<?php echo SITE_VERSION;?>" charset="UTF-8"></script> 
  <script type="text/javascript">
  $(function(){
	initUploadImg();
	initUploadFile();
   
   $('.time').datetimepicker({
        format: 'yyyy-mm-dd hh:ii',
        language:"zh-CN",
        minView:0,
        autoclose:true
    });
    $('.date').datetimepicker({
        format: 'yyyy-mm-dd',
        language:"zh-CN",
        minView:2,
        autoclose:true
    });	
    showTab();
	
	$('.toggle-data').each(function(){
		var data = $(this).attr('toggle-data');
		if(data=='') return true;		
		
	     if($(this).is(":selected") || $(this).is(":checked")){
			 change_event(this)
		 }
	});
	
	$('.toggle-data').bind("click",function(){ change_event(this) });
	
	$('.template_item').find('.use_tips').stop().fadeIn();
	$('.template_item').addClass('hover');
	$('#temple_del').click(function(){
		$('#temp').val('');
		$('#tmpimg').attr('src','')
	});
	
});
  function selectAppMsgCallback(_this){
		var as=$(_this).data('temple');
		var name=$(_this).data('tempname');
		var tmpimg="<?php echo ONETHINK_ADDON_PATH;?>WeiSite/View/default/TemplateSubcate/"+as+"/icon.png";
		$('#temp').val(as);
		$('#tmpimg').attr('src',tmpimg);
		$.Dialog.close();
		
		
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