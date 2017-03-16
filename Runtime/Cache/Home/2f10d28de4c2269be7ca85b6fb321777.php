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
<link href="<?php echo ADDON_PUBLIC_PATH;?>/bootstrap3.3.7/css/bootstrap.min.css?v=<?php echo SITE_VERSION;?>" rel="stylesheet" type="text/css">
<link href="<?php echo ADDON_PUBLIC_PATH;?>/weui.min.css?v=<?php echo SITE_VERSION;?>" rel="stylesheet" type="text/css">
<title></title>
<style>
    .informationUl{
        list-style: none;
        margin: 0px;
        padding: 0px;
    }
    .informationUl li{
        margin: 5px;
    }
    .panel-default>.panel-heading{
        background-color:#d9edf7;
        color: #31708f;
    }

    .attendance_head,.informationUl{
        position: relative;
    }
    .institution_name{
        text-align: center;
        margin: 40px auto 20px;
    }
    .attendance_left{
        display:inline-block;
        margin-left: 10px;
    }
    .attendance_right{
        display:inline-block;
        position: absolute;
        right: 10px;
    }
    .attendance-hd-lb{
        width: 15%;display: inline-block;
    }
    .attendance-lb{
        margin-left: 15px;margin-top: -30px;
    }
    .attendance-bd{
        display: inline-block;width: 80%;
    }
    .attendance_head_p{
        margin-top: 10px;
    }
    .omit_show{
        width: 100%; white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-left: 15px;
    }
    .collapse_left{
        display:inline-block;
        margin-left: 15px;
        margin-bottom: 0px;
    }
    .collapse_right{
        display:inline-block;
        position: absolute;
        right: 30px;
        margin-bottom: 0px;
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
    .weui-cell{
        padding: 5px 15px;
    }
    .weui-cell:before{
        left: 0px;
        border-top:0px;
    }
</style>
<body>
<div class="attendance_head">
    <div class="institution_name">
        <img src="<?php echo ADDON_PUBLIC_PATH;?>/A0.png" alt="" width="90px" height="50px">
        <p  class="attendance_head_p">成绩和表现</p>
    </div>
    <div>
        <p class="attendance_left">学号：<span class="studentNo"><?php echo ($studentno); ?></span></p>
        <p class="attendance_right">姓名：<?php echo ($student_name); ?></p>
    </div>
</div>
<!--<div class="weui-cell">-->
    <!--<div class="weui-cell__hd attendance-hd-lb" >-->
        <!--<label for="" class="weui-label attendance-lb" >日期</label>-->
    <!--</div>-->
    <!--<div class="weui-cell__bd attendance-bd" >-->
        <!--<input class="weui-input" type="date" value="" style="padding:8px;"/>-->
    <!--</div>-->
<!--</div>-->
<div class="weui-cell">
    <div class="weui-cell__hd " >
        <label for="" class="weui-label " >开始日期</label>
    </div>
    <div class="weui-cell__bd " >
        <input class="weui-input startdate" type="date" value=""  name="startdate" style="height: 40px"/>
    </div>
</div>
<div class="weui-cell weui">
    <div class="weui-cell__hd " >
        <label for="" class="weui-label " >结束日期</label>
    </div>
    <div class="weui-cell__bd " >
        <input class="weui-input" type="date" value=""  name="deadline" style="height: 40px"/>
    </div>
</div>
<div type="submit" onclick="query(1)" class="weui-btn weui-btn_primary weui-btn_mini "  style="margin-left: 20px">查询</div>
<div type="submit" onclick="query(2)" class="weui-btn weui-btn_primary weui-btn_mini">查询全部</div>
<div class="panel-group" id="accordion">
<!--    <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo ($i); ?>">
                    <div>
                        <p class="omit_show">课程：<?php echo ($vo["course_name"]); ?></p>
                        <p  class="collapse_left"><?php echo ($vo["classdate"]); ?></p>
                        <p  class="collapse_right"><?php echo ($vo["teacher"]); ?></p>
                    </div>
                </a>
            </h4>
        </div>
        <div id="collapse<?php echo ($i); ?>" class="panel-collapse collapse">
            <div class="panel-body">
                <ul class="informationUl">
                    <li>
                        <p class="attendance_left">课堂表现（70分）：</p>
                        <p class="attendance_right"><?php echo ($vo["score1"]); ?></p>
                    </li>
                    <li>
                        <p class="attendance_left">出勤情况（10分）：</p>
                        <p class="attendance_right"><?php echo ($vo["score2"]); ?></p>
                    </li>
                    <li>
                        <p class="attendance_left">作业完成（20分）：</p>
                        <p class="attendance_right"><?php echo ($vo["score3"]); ?></p>
                    </li>
                    <li>
                        <p class="attendance_left">总分：</p>
                        <p class="attendance_right"><?php echo ($vo["score"]); ?></p>
                    </li>
                    <li>
                        <p class="attendance_left">小测试：</p>
                        <p class="attendance_right"><?php echo ($vo["exmscore"]); ?></p>
                    </li>
                    <li>
                        <p class="attendance_left">教师评语：</p>
                        <span >测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试</span>
                    </li>
                </ul>
            </div>
        </div>
    </div><?php endforeach; endif; else: echo "" ;endif; ?>-->
</div>
<p class="p" style="text-align: center"></p>
<input type="hidden" value='<?php echo ($public_id); ?>' id = 'publicId'>
<input type="hidden" value="<?php echo U('addon/Student/Wap/score_ajax_show');?>" id = 'ajaxPostUrl'>

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
<!--查询-->
<script type="text/javascript">
    function query(Q) {
        var href = $('#ajaxPostUrl').val();
        var publicId=$("#publicId").val();
        var studentNo=$(".studentNo").html();
        var startdate="";
        var deadline="";
        if(Q==1){
            startdate=$(".startdate").val();
            deadline=$("input[name='deadline']").val();
        }else if(Q==2){
            deadline=null;
            startdate="1990-1-1";
        }
        $.post(href,{search_start_date:startdate, search_end_date:deadline,public_id:publicId,studentNo:studentNo},function(data){
            $(".panel-group").html("");
            $(".p").html("");
            var json_data =  JSON.parse(data);
            var str= " ";
            console.log (typeof (json_data));
            console.log (json_data);
            if(json_data==null){
                str = '没有数据';
                console.log("sss");
                $(".p").html(str);
                return false;
            }else if (json_data.length>0){
                console.log("aaa");
                for(var i=0; i < json_data.length;i++){
                    str= ' <div class="panel panel-default"> <div class="panel-heading"> <h4 class="panel-title"> <a data-toggle="collapse" data-parent="#accordion" href="#collapse'+i+'"> <div> <p class="omit_show">课程：'+json_data[i]["course_name"]+'</p> <p  class="collapse_left">'+json_data[i]["classdate"]+'</p> <p  class="collapse_right">'+json_data[i]["teacher"]+'</p> </div> </a> </h4> </div> <div id="collapse'+i+'" class="panel-collapse collapse"> <div class="panel-body"> <ul class="informationUl"> <li> <p class="attendance_left">课堂表现（70分）：</p> <p class="attendance_right">'+json_data[i]["score1"]+'</p> </li> <li> <p class="attendance_left">出勤情况（10分）：</p> <p class="attendance_right">'+json_data[i]["score2"]+'</p> </li> <li> <p class="attendance_left">作业完成（20分）：</p> <p class="attendance_right">'+json_data[i]["score3"]+'</p> </li> <li> <p class="attendance_left">总分：</p> <p class="attendance_right">'+json_data[i]["score"]+'</p> </li> <li> <p class="attendance_left">小测试：</p> <p class="attendance_right">'+json_data[i]["exmscore"]+'</p> </li> <li> <p class="attendance_left">教师评语：</p> <span >测试测试测试测试测试测试测试测试测试测试测试测试测试测试测试</span> </li> </ul> </div> </div> </div>';
                    $(".panel-group").append(str);
                }
            }

        }, 'text');
    }
</script>
</body>