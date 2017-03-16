<?php if (!defined('THINK_PATH')) exit();?>
<body style="background:#fff">
<div class="lists_data">
  <div class="span9 page_message">
    <section id="contents"> 
    <?php if(empty($tempList)): ?><div class="empty_container"><p>没有二级分类素材</p></div>
<?php else: ?>
        <!-- 多维过滤 --> 
      </div>
      <!-- 数据列表 -->
      <div class="data-table">
        <div class="table-striped">
        
        <div class="tab-content" id="hasSubNav"> 
        	<div class="sub_tab_content">
            <form>
            	<ul class="template_select">
                    <?php if(is_array($tempList)): $i = 0; $__LIST__ = $tempList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li class="template_item <?php echo ($vo["class"]); ?>" data-temple="<?php echo ($vo["dirName"]); ?>" data-tempname="<?php echo ($vo["title"]); ?>">
                    	<?php if(!empty($vo[desc])): ?><div class="use_tips"><?php echo ($vo["desc"]); ?></div><?php endif; ?>
                    	<div class="phone">
                        	<img src="<?php echo ($vo["icon"]); ?>" />
                        </div>
                        <p ><input type="hidden" <?php echo ($vo["checked"]); ?> name="ids[]" id="check_<?php echo ($vo["dirName"]); ?>" value="<?php echo ($vo["dirName"]); ?>" class="ids regular-radio"/>
                        <label for="check_<?php echo ($vo["dirName"]); ?>"><?php echo ($vo["title"]); ?></label></p>
                    </li><?php endforeach; endif; else: echo "" ;endif; ?>
                </ul>
              </form>
            </div>
      </div>
<!--           <table cellspacing="1"> -->
<!--             表头 -->
<!--             <thead> -->
<!--               <tr> -->
<!--                 <th class="row-selected row-selected">  -->
<!--                   <?php if(!isRadio): ?>-->
<!--                   <input type="checkbox" class="check-all regular-checkbox" id="checkAll"> -->
<!--                   <label for="checkAll"></label></th> -->
<!--<?php endif; ?> -->
<!--                 <th>编号</th> -->
<!--                 <th width='85%'>素材文本内容</th> -->
<!--               </tr> -->
<!--             </thead> -->
            
<!--             列表 -->
<!--             <tbody> -->
<!--               <?php if(is_array($list_data)): $i = 0; $__LIST__ = $list_data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>-->
<!--                 <tr> -->
<!--                   <td> -->
<!--                    <?php if(empty($isRadio)): ?>-->
<!--                   		<input type="checkbox" id="check_<?php echo ($vo["id"]); ?>" name="ids[]" value="<?php echo ($vo["id"]); ?>" class="ids regular-checkbox"> -->
<!--                     	<label for="check_<?php echo ($vo["id"]); ?>"></label> -->
<!--                     <?php else: ?> -->
<!--                     	<input type="radio" id="check_<?php echo ($vo["id"]); ?>" name="ids[]" value="<?php echo ($vo["id"]); ?>" class="ids regular-radio"> -->
<!--                     	<label for="check_<?php echo ($vo["id"]); ?>"></label> -->
<!--<?php endif; ?> -->
<!--                   </td> -->
<!--                   <td type="headimgurl"><?php echo ($vo["id"]); ?></td> -->
<!--                   <td type="nickname"><?php echo ($vo["content"]); ?></td> -->
<!--                 </tr> -->
<!--<?php endforeach; endif; else: echo "" ;endif; ?> -->
<!--             </tbody> -->
<!--           </table> -->
        </div>
      </div><?php endif; ?>
      <div class="page"> <?php echo ((isset($_page) && ($_page !== ""))?($_page):''); ?> </div>
    </section>
  </div>
  
<script type="text/javascript">
$(function(){
	//搜索功能
	$("#search").click(function(){
		var url = $(this).attr('url');
        var query  = $('.search-form').find('input').serialize();
        query = query.replace(/(&|^)(\w*?\d*?\-*?_*?)*?=?((?=&)|(?=$))/g,'');
        query = query.replace(/^&/g,'');
        if( url.indexOf('?')>0 ){
            url += '&' + query;
        }else{
            url += '?' + query;
        }
        if(query == '' ){
        	url="<?php echo U('Material/text_lists',array('isAjax'=>1,'isRadio'=>$isRadio));?>";
        }
		window.location.href = url;
	});

    //回车自动提交
    $('.search-form').find('input').keyup(function(event){
        if(event.keyCode===13){
            $("#search").click();
        }
    });
	$('select[name=group]').change(function(){
		location.href = this.value;
	});	
	$(function(){
		//选择模板并且ajax提交
		//选择Ajax提交		
		$('.template_item').click(function(){
			if(!$(this).hasClass('selected')){
				$(this).addClass('selected');
				$(this).siblings().removeClass("selected");
				$(this).find('input').click();

// 				//如果不是ajax提交 不要以下代码"<?php echo $_GET['_action'];?>"
// 				var value = $(this).find('input').val();				
// 				var type = "<?php echo $_GET['_action'];?>";
// 				$.ajax({
// 					url:"<?php echo U('save');?>",
// 					data:{template:value,type:type},
// 					type:"post",
// 					dataType:"json",
// 					success:function(data){ window.location.href = "<?php echo ($next_url); ?>&mdm=<?php echo I('mdm');?>"; }
// 					})
				}
			});
	})
	$('.template_item').hover(function(){
			$(this).find('.use_tips').stop().fadeIn();
			$(this).addClass('hover');
		},function(){
			$(this).find('.use_tips').stop().fadeOut();
			$(this).removeClass('hover');
			})
	
})
</script> 
</body>
</html>