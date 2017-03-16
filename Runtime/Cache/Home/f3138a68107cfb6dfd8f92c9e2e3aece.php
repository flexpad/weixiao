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
    
<style type="text/css">
.money {
	width: 50px;
}
.specTable .param {
	display: none;
}
.specTable p {
	display: block;
	line-height: 50px;
}
.text-center {
	text-align: center;
}
.check-tips {
	color: #aaa;
	margin-left:2px;
}
.cf{
	margin-left:20px;
}
</style>
  <!-- 标签页导航 -->
  <div class="span9 page_message">
    <section id="contents">
      <ul class="tab-nav nav">
        <li class=""><a href="<?php echo U('lists');?>">学生中心</a></li>
        <li class="current"><a href="javascript:;">学生资料编辑<b class="arrow fa fa-sort"></b></a></li>
        <li class=""><a href="<?php echo U('import');?>">学生资料导入<b class="arrow fa fa-sort"></b></a></li>
      </ul>
      <div class="tab-content"> 
        <!-- 表单 -->
        <form id="form" action="<?php echo U('edit?model='.$model['id']);?>" method="post" class="form-horizontal">
            	 <div class="form-item cf">
                    <label class="item-label"><span class="need_flag">*</span>关键词<span class="check-tips"></span></label>
                    <div class="controls">
                      <input type="text" class="text input-large" name="keyword" value="<?php echo ($data["keyword"]); ?>">
                    </div>
                  </div>  
                   <div class="form-item cf">
                    <label class="item-label"><span class="need_flag">*</span>标题<span class="check-tips"></span></label>
                    <div class="controls">
                      <input type="text" class="text input-large" name="title" value="<?php echo ($data["title"]); ?>">
                    </div>
                  </div>  
                  <div class="form-item cf">
                  		<label class="item-label"><span class="need_flag">*</span>封面图片<span class="check-tips">图片高度控制在200px-400px之间</span></label>
                		<div class="controls uploadrow2" data-max="1" title="点击修改图片" rel="cover">
                            <input type="file" id="upload_picture_cover">
                            <input type="hidden" name="file" id="cover_id_cover" value="<?php echo ($data["file"]); ?>"/>
                            <div class="upload_file" rel="file">
                              <?php if(!empty($data[cover])): ?><div class="upload-pre-item2"><img width="100" height="100" src="<?php echo (get_cover_url($data["cover"])); ?>"/></div>
                                <em class="edit_img_icon">&nbsp;</em><?php endif; ?>
                            </div>
                      </div>
                  </div>
                  
                  <div class="form-item cf">
                    <label class="item-label"><span class="need_flag">*</span>描述<span class="check-tips"></span></label>
                    <div class="controls">
                      <label class="textarea input-large">
                      <textarea class="text input-large" name="intro" ><?php echo ($data["intro"]); ?></textarea>
                      </label>
                    </div>
                  </div> 
                  <div class="form-item cf toggle-prize_type" style="display:none">
              			<label class="item-label"><span class="need_flag">*</span>是否允许编辑<span class="check-tips"></span></label>
              			<div class="controls">
                        	<select name="can_edit">
                                <?php $_result=parse_field_attr($fields['type']['extra']);if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>" <?php if(($data[type]) == $key): ?>selected="selected"<?php endif; ?>><?php echo (clean_hide_attr($vo)); ?> </option><?php endforeach; endif; else: echo "" ;endif; ?> 
                            </select>       
                      </div>
            	  </div>
                  <div class="form-item cf">
                    <label class="item-label">用户提交后提示内容<span class="check-tips">为空默认为：提交成功，谢谢参与</span></label>
                    <div class="controls">
                       <input type="text" class="text input-large" name="finish_tip" value="<?php echo ($data["finish_tip"]); ?>">
                    </div>
                  </div>   
<div class="form-item cf">
        <label class="item-label"><span class="need_flag">*</span>字段管理<span class="check-tips"> </span></label>
        <div style="margin:15px 0;" class="specTable data-table">
          <table cellspacing="1" cellpadding="0">
            <thead>
              <tr>
                <th align="center">字段名称</th>
                <th align="center">字段类型</th>
                <th align="center">选项数据</th>
                <th align="center">是否必填</th>
                <th align="center">操作</th>
              </tr>
            </thead>
            <tbody id="list_data_tbody">
              <?php if(is_array($attr_list)): $i = 0; $__LIST__ = $attr_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$cd): $mod = ($i % 2 );++$i;?><tr class="list_tr" rel="<?php echo ($cd["sort"]); ?>">
                <td align="center"><input type="text" value="<?php echo ($cd["title"]); ?>" class="form-control" name="attr_title[<?php echo ($cd["sort"]); ?>]" style="width:150px"></td>
                <td align="center"><select name="type[<?php echo ($cd["sort"]); ?>]" class="select_type" style="width:150px">
                    <option value="string" <?php if(($cd[type]) == "string"): ?>selected<?php endif; ?> >单行输入 </option>
                    <option value="textarea" <?php if(($cd[type]) == "textarea"): ?>selected<?php endif; ?> >多行输入 </option>
                    <option value="radio" <?php if(($cd[type]) == "radio"): ?>selected<?php endif; ?> >单选 </option>
                    <option value="checkbox" <?php if(($cd[type]) == "checkbox"): ?>selected<?php endif; ?> >多选 </option>
                    <option value="select" <?php if(($cd[type]) == "select"): ?>selected<?php endif; ?> >下拉选择 </option>
                    <option value="datetime" <?php if(($cd[type]) == "datetime"): ?>selected<?php endif; ?> >时间 </option>
                    <option value="picture" <?php if(($cd[type]) == "picture"): ?>selected<?php endif; ?> >上传图片 </option>
                  </select></td>
                <td align="center"><input type="text" value="<?php echo ($cd["extra"]); ?>" class="form-control" name="extra[<?php echo ($cd["sort"]); ?>]" placeholder=""></td>
                <td><input type="checkbox" name="is_must[<?php echo ($cd["sort"]); ?>]" value="1" 
                  <?php if($cd[is_must]==1): ?>checked="checked"<?php endif; ?>
                  > 必填</td>
                <td>
                <input type="hidden" value="<?php echo ($cd["id"]); ?>" name="attr_id[<?php echo ($cd["sort"]); ?>]">
                <input type="hidden" value="<?php echo ($cd["value"]); ?>" name="value[<?php echo ($cd["sort"]); ?>]" class="value">
                <input type="hidden" value="<?php echo ($cd["remark"]); ?>" name="remark[<?php echo ($cd["sort"]); ?>]" class="remark">
                <input type="hidden" value="<?php echo ($cd["validate_rule"]); ?>" name="validate_rule[<?php echo ($cd["sort"]); ?>]" class="validate_rule">
                <input type="hidden" value="<?php echo ($cd["error_info"]); ?>" name="error_info[<?php echo ($cd["sort"]); ?>]" class="error_info"> 
                <a href="javascript:void(0);" onclick="move_up(this)" class="move_up">↑</a>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="move_down(this)" class="move_down">↓</a>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="show_more(this)">高级设置</a>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="remove_tr(this)">删除</a></td>
              </tr><?php endforeach; endif; else: echo "" ;endif; ?>
              <tr class="more_tr">
                <td colspan="5"><a href="javascript:add_tr()">+增加新字段</a></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>      
      <div class="controls" style="display:none">
        <label class="item-label"> 详细介绍<span class="check-tips">为空默认只显示描述</span> </label>
        <textarea name="content" style="width:405px; height:100px;"><?php echo ($data["content"]); ?></textarea>
        <?php echo hook('adminArticleEdit', array('name'=>'content','value'=>$data[content]));?> </div>
    </div>
    <div class="form-item form_bh">
      <input type="hidden" name="id" value="<?php echo ($data["id"]); ?>">
      <button class="btn submit-btn ajax-post" id="submit" type="submit" target-form="form-horizontal">确 定</button>
    </div>
  </form>
  <table style="display:none">
      <tr id="default_tr1">
        <td align="center"><input type="text" value="" class="form-control" name="attr_title[sort_id]" style="width:150px"></td>
        <td align="center"><select name="type[sort_id]" class="select_type" style="width:150px">
            <option value="string" selected >单行输入 </option>
            <option value="textarea">多行输入 </option>
            <option value="radio">单选 </option>
            <option value="checkbox">多选 </option>
            <option value="select">下拉选择 </option>
            <option value="datetime">时间 </option>
            <option value="picture">上传图片 </option>
          </select></td>
        <td align="center"><input type="text" value="" class="form-control" name="extra[sort_id]"></td>
        <td><input type="checkbox" name="is_must[sort_id]" value="1"> 必填</td>
        <td>
        <input type="hidden" value="" name="value[sort_id]" class="value">
        <input type="hidden" value="" name="remark[sort_id]" class="remark">
        <input type="hidden" value="" name="validate_rule[sort_id]" class="validate_rule">
        <input type="hidden" value="" name="error_info[sort_id]" class="error_info">        
        <a href="javascript:void(0);" onclick="move_up(this)" class="move_up">↑</a>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="move_down(this)" class="move_down">向下</a>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="show_more(this)">高级设置</a>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="remove_tr(this)">删除</a></td>
      </tr>
      <tr id="default_tr2">
        <td align="center"><input type="text" value="" class="form-control" name="name[sort_id]" style="width:150px"></td>
        <td align="center"><input type="number" value="" class="form-control" name="money[sort_id]" style="width:120px" placeholder="为空时表示免费"></td>
        <td align="center"><input type="number" value="" class="form-control" name="max_limit[sort_id]" style="width:130px" placeholder="为空时表示不限制"></td>
        <td align="center"><input type="number" value="" class="form-control" name="init_count[sort_id]" style="width:100px"></td>
        <td align="center">0</td>
        <td><a href="javascript:void(0);" onclick="remove_tr(this)">删除</a></td>
      </tr>      
  </table>
  
  <div id="default_more_html" style="display:none">
      <div class="form-item cf">
        <label class="item-label">默认值<span class="check-tips"> （字段的默认值） </span></label>
        <div class="controls">
          <input type="text" value="[value]" name="value" id="open_value" class="text input-large">
        </div>
      </div>
      <div class="form-item cf">
        <label class="item-label">字段备注<span class="check-tips"> （用于微预约中的提示） </span></label>
        <div class="controls">
          <input type="text" value="[remark]" name="remark" id="open_remark" class="text input-large">
        </div>
      </div>
      <div class="form-item cf">
        <label class="item-label">正则验证<span class="check-tips"> （为空表示不作验证） </span></label>
        <div class="controls">
          <input type="text" value="[validate_rule]" name="validate_rule" id="open_validate_rule" class="text input-large">
        </div>
      </div>
      <div class="form-item cf">
        <label class="item-label">出错提示<span class="check-tips"> （验证不通过时的提示语） </span></label>
        <div class="controls">
          <input type="text" value="[error_info]" name="error_info" id="open_error_info" class="text input-large">
        </div>
      </div>
      <div class="form-item form_bh">
      <div class="btn_bar"><a href="javascript:;" class="btn confirm_btn">确定</a>&nbsp;&nbsp;<a href="javascript:;" class="border-btn cancel_btn">取消</a></div>
    </div>
  </div>
</div>
</section>
</div>
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
$('#submit').click(function(){
    $('#form').submit();
});

$(function(){
    initUploadFile();
	
    $('.time').datetimepicker({
        format: 'yyyy-mm-dd hh:ii',
        language:"zh-CN",
        minView:0,
        autoclose:true
    });
    showTab();
	hide_move();
	
	$('.select_type').each(function(){ select_type(this); });
	$('.select_type').change(function(){ select_type(this); });
});
//增加字段
var tr_sort_id = 0;
function add_tr(){
	var list_count = 0;
	$('.list_tr').each(function() {
		list_count += 1;
		var sort_id = $(this).attr('rel');
		if(sort_id>tr_sort_id) tr_sort_id = sort_id;
    });	
	
	tr_sort_id += 1;
	
	re = new RegExp("sort_id", "g");
	str  = $('#default_tr1').html().replace(re, tr_sort_id);
	//console.log(str);
	var html = '<tr class="list_tr" rel="'+tr_sort_id+'">'+ str +'</tr>';
	if(list_count==0)
	  $('#list_data_tbody tr').before(html);	
	else
	  $('.list_tr:last').after(html);
	  
	hide_move();
	$('.select_type').each(function(){ select_type(this); });
	$('.select_type').change(function(){ select_type(this); });
}
//删除字段
function remove_tr(_this){	
	$(_this).parent().parent().remove();
	hide_move();
}
//排序--向上
function move_up(obj) { 
  var objParentTR = $(obj).parent().parent(); 
  var prevTR = objParentTR.prev(); 
  if (prevTR.length > 0) { 
	prevTR.insertAfter(objParentTR); 
  }
  hide_move();
} 
//排序--向下
function move_down(obj) { 
  var objParentTR = $(obj).parent().parent(); 
  var nextTR = objParentTR.next(); 
  if (nextTR.length > 0) { 
	nextTR.insertBefore(objParentTR); 
  } 
  hide_move();
} 
//第一行只显示向下，最后一行只显示向上
function hide_move(){
	$('.move_up').each(function() {
		$(this).show();
    });
	$('.move_down').each(function() {
		$(this).show();
    });	
	$('.list_tr:first').find('.move_up').hide();
	$('.list_tr:last').find('.move_down').hide();
}
//选择字段类型
function select_type(_this){
	var type = $(_this).val();
	var obj = $(_this).parent().next().find('input');
	
	switch(type){
		case 'textarea': obj.attr('placeholder','').attr('readonly', true); break;
		case 'radio': obj.attr('placeholder','多个选项用空格分开，如：男 女').attr('readonly', false); break;
		case 'checkbox': obj.attr('placeholder','多个选项用空格分开，如：男 女').attr('readonly', false); break;
		case 'select': obj.attr('placeholder','多个选项用空格分开，如：男 女').attr('readonly', false); break;
		case 'datetime': obj.attr('placeholder','').attr('readonly', true); break;
		case 'picture': obj.attr('placeholder','').attr('readonly', true); break;
	    default: obj.attr('placeholder','').attr('readonly', true); break;
	}
}
//高级设置
function show_more(_this){	
	var obj = $(_this).parent();
	
	var value = obj.find('.value').val();
	var remark = obj.find('.remark').val();
	var validate_rule = obj.find('.validate_rule').val();
	var error_info = obj.find('.error_info').val();
	
	var html = $('#default_more_html').html().replace("[value]", value).replace("[remark]", remark).replace("[validate_rule]", validate_rule).replace("[error_info]", error_info);
	$contentHtml = $(html);
	  
	
	$.Dialog.open("高级设置",{width:500,height:500},$contentHtml);
	
	$('.cancel_btn',$contentHtml).click(function(){
		$.Dialog.close();
	})
	$('.confirm_btn',$contentHtml).click(function(){
		obj.find('.value').val( $('#open_value',$contentHtml).val() );
		obj.find('.remark').val( $('#open_remark',$contentHtml).val() );
		obj.find('.validate_rule').val( $('#open_validate_rule',$contentHtml).val() );
		obj.find('.error_info').val( $('#open_error_info',$contentHtml).val() );
		
		$.Dialog.close();
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