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
<style type="text/css">
.banner{ width:100%; overflow:hidden; position:relative;}
.banner ul{ position:absolute; left:0; top:0; z-index:10; -webkit-animation:}
.banner ul li{ float:left; display:table-cell; position:relative}
.banner li a{ width:100%; height:100%; display:block;}
.banner li .title{background-color:RGBA(0,0,0,.5); height:30px; color:#fff; line-height:30px; padding-left:10px; position:absolute; left:0; bottom:0; width:100%; z-index:1000;}
.identify{text-align:right; position:absolute; bottom:0; right:0; z-index:100; height:30px;}
.identify em{ display:inline-block; margin:10px 5px;-webkit-border-radius: 6px;-moz-border-radius: 6px;
border-radius: 6px;margin-left: 5px;width: 12px;height: 12px;background: #fff;}
.identify em.cur{ background-color:#090}
.identify .fl{ float:left}
.identify .fr{ float:right}
.small_pic_list li{ border-top:1px solid #fff; border-bottom:1px solid #ccc; height:80px; overflow:hidden}
.small_pic_list li a{ display:block; padding:10px 10px 10px 80px; position:relative; min-height:60px; color:#333}
.small_pic_list li a:active{ background-color:#CCC}
.small_pic_list li a img{ position:absolute; left:10px; top:10px; width:60px; height:60px;}
.small_pic_list li a h6{ font-size:18px; font-weight:bold; overflow:hidden; text-overflow:ellipsis; white-space:nowrap}
.small_pic_list li a p{ color:#999; font-size:13px; line-height:22px;}
.p{text-align: center}
</style>
<body id="weisite">
<div class="container">
 <if condition="!empty($slide_data)">
    <?php if(!empty($slide_data)): ?><section class="banner">
	<ul>
        <?php if(is_array($slide_data)): $i = 0; $__LIST__ = $slide_data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
            	<a href="<?php echo ($vo["url"]); ?>"><img src="<?php echo (get_cover_url($vo["cover"],900,450)); ?>"/></a>
            	<span class="title"><?php echo ($vo["title"]); ?></span>
            </li><?php endforeach; endif; else: echo "" ;endif; ?>
        </ul>
        <span class="identify">
            <span class="pointer">
            <?php if(is_array($slide_data)): $i = 0; $__LIST__ = $slide_data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><em></em><?php endforeach; endif; else: echo "" ;endif; ?> 
             </span>       
        </span>

    </section><?php endif; ?>
    <ul class="small_pic_list">
        <?php if(is_array($lists)): $i = 0; $__LIST__ = $lists;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
                <a href="<?php echo ($vo["url"]); ?>">
                    <?php if(!empty($vo["cover"])): ?><img src="<?php echo (get_square_url($vo["cover"],200)); ?>"/><?php endif; ?>
                    <h6><?php echo ($vo["title"]); ?></h6>
                    <p><?php echo ($vo["intro"]); ?></p>
                    <span class="colorless"><?php echo (time_format($vo["cTime"])); ?></span>
                </a>
            </li><?php endforeach; endif; else: echo "" ;endif; ?>
    </ul>
     <p class="p"></p>
    <!-- 分页 -->
    <div class="page"> <?php echo ((isset($_page) && ($_page !== ""))?($_page):''); ?> </div>
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
</body>
<script type="text/javascript">
$(function(){
	$.WeiPHP.initBanner(true,5000,9/5);
})
</script>
<!--新增====================================================================-->
<script type="text/javascript">
    $(function(){
         var winH = $(window).height(); //页面可视区域高度
        var i = 1; //设置当前页数
        var href = window.location.href;
        $(window).scroll(function() {
            var pageH = $(document.body).height();
            var scrollT = $(window).scrollTop(); //滚动条top
            var aa = (pageH-winH-scrollT)/winH;
            var str = "";
            var sstr = "";
            var json;
            if (aa<0.02){
                console.log("1");
                i++;
                $.post(href,{ajax:1, page:i},function(data){
                    console.log("2");
                    json = JSON.parse(data);
                    if (json){
                        $.each(json, function(index, array){
                            if (array.cover)
                                sstr = '<img src="' + array.coverurl + '"/>';
                            else
                                sstr = "";
                            str =  '<li><a href=\"'+array.url+'\">'+sstr+'<h6>'+array.title+'</h6>'+'<p>'+array.intro+'</p>'+'<span class="colorless">'+array.fcTime+'</span></a></li>';
                            $(".small_pic_list").append(str);
                        });


                    } else{
                        str = '已经到底';
                        $(".p").html(str);
                        return false;
                    }
                }, 'text');
            }
        });
    });
    /*
     $(function(){
     //$.WeiPHP.initBanner(true,5000);
     })
     */

</script>
</html>