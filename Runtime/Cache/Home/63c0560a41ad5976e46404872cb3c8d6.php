<?php if (!defined('THINK_PATH')) exit();?><!-- 底部导航最多能添加4个选项 -->
<link href="<?php echo CUSTOM_TEMPLATE_PATH;?>Footer/V2/footer.css?v=<?php echo SITE_VERSION;?>" rel="stylesheet" type="text/css">
<?php if(!empty($footer)): ?><nav class="bottom_nav" style="background-color: rgba(0, 45, 71, 0.56)">
<?php if(is_array($footer)): $i = 0; $__LIST__ = $footer;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if(empty($vo['child'])): ?><a class="item" href="<?php echo ($vo["url"]); ?>"><?php echo ($vo["icon"]); echo ($vo["title"]); ?></a>
<?php else: ?>
    <div class="item has_nav" href="javascript:;" onClick="$(this).find('#more_nav_<?php echo ($vo["id"]); ?>').toggle();">
    	<?php echo ($vo["icon"]); echo ($vo["title"]); ?>
    	<div class="more_nav" id="more_nav_<?php echo ($vo["id"]); ?>">
        	<em></em>
        	<?php if(is_array($vo["child"])): $i = 0; $__LIST__ = $vo["child"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vv): $mod = ($i % 2 );++$i;?><a href="<?php echo ($vv["url"]); ?>"><?php echo ($vv["title"]); ?></a><?php endforeach; endif; else: echo "" ;endif; ?>
        </div>
    </div><?php endif; endforeach; endif; else: echo "" ;endif; ?>
</nav><?php endif; ?>
<div class="bottom_nav_blank"/>