<?php if (!defined('THINK_PATH')) exit();?><div class="pic_category">
	<?php if(is_array($cateList)): $i = 0; $__LIST__ = $cateList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$ca): $mod = ($i % 2 );++$i;?><a <?php if($ca['current']): ?>class="current"<?php else: ?>onclick="switchPicCate(this,2);"<?php endif; ?> data-href="<?php echo U('/Home/File/systemPics',array('dir'=>$ca['dir']));?>"><?php echo ($ca["cate"]); ?></a><?php endforeach; endif; else: echo "" ;endif; ?>
</div>
<ul class="upload_piclist">
	<?php if(is_array($picList)): $i = 0; $__LIST__ = $picList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li class="upload-pre-item22" data-id="<?php echo ($vo["id"]); ?>" onClick="toggleCheck(this)"><img src="<?php echo ($vo["url"]); ?>" width="100" height="100"/><span class="ck-ico"></span><input type="hidden" value="<?php echo ($vo["id"]); ?>"/></li><?php endforeach; endif; else: echo "" ;endif; ?>
</ul>