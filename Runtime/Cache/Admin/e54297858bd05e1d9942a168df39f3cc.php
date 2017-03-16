<?php if (!defined('THINK_PATH')) exit(); switch($addons_config["editor_type"]): case "1": ?>
		<input type="hidden" name="parse" value="0">
		<script type="text/javascript">
			$('textarea[name="<?php echo ($addons_data["name"]); ?>"]').height('<?php echo ($addons_config["editor_height"]); ?>');
		</script><?php break;?>
	<?php case "2": ?>
		<input type="hidden" name="parse" value="0">
		<?php if(($addons_config["editor_wysiwyg"]) == "1"): ?><link rel="stylesheet" href="/weiphp30/Public/static/kindeditor/default/default.css?v=<?php echo SITE_VERSION;?>" />
			<script charset="utf-8" src="/weiphp30/Public/static/kindeditor/kindeditor-min.js"></script>
			<script charset="utf-8" src="/weiphp30/Public/static/kindeditor/zh_CN.js?v=<?php echo SITE_VERSION;?>"></script>
			<script type="text/javascript">
				var editor_<?php echo ($addons_data["name"]); ?>;
				KindEditor.ready(function(K) {
					editor_<?php echo ($addons_data["name"]); ?> = K.create('textarea[name="<?php echo ($addons_data["name"]); ?>"]', {
						allowFileManager : false,
						themesPath: K.basePath,
						width: '100%',
						height: '<?php echo ($addons_config["editor_height"]); ?>',
						resizeType: <?php if(($addons_config["editor_resize_type"]) == "1"): ?>1<?php else: ?>0<?php endif; ?>,
						pasteType : 2,
						urlType : 'absolute',
						fileManagerJson : '<?php echo U('fileManagerJson');?>',
						//uploadJson : '<?php echo U('uploadJson');?>' }
						uploadJson : '<?php echo addons_url("EditorForAdmin://Upload/ke_upimg");?>',
						extraFileUploadParams: {
							session_id : '<?php echo session_id();?>'
	                    }
					});
				});

				$(function(){
					//传统表单提交同步
					$('textarea[name="<?php echo ($addons_data["name"]); ?>"]').closest('form').submit(function(){
						editor_<?php echo ($addons_data["name"]); ?>.sync();
					});
					//ajax提交之前同步
					$('button[type="submit"],#submit,.ajax-post,#autoSave').click(function(){
						editor_<?php echo ($addons_data["name"]); ?>.sync();
					});
				})
			</script>

		<?php else: ?>
        
			<script type="text/javascript" charset="utf-8" src="/weiphp30/Public/static/<?php echo ($driver_file); ?>/ueditor.config.js?v=<?php echo SITE_VERSION;?>"></script>
			<script type="text/javascript" charset="utf-8" src="/weiphp30/Public/static/<?php echo ($driver_file); ?>/ueditor.all.js?v=<?php echo SITE_VERSION;?>"></script>
			<script type="text/javascript" charset="utf-8" src="/weiphp30/Public/static/<?php echo ($driver_file); ?>/lang/zh-cn/zh-cn.js?v=<?php echo SITE_VERSION;?>"></script>
         
			<script type="text/javascript">
				$('textarea[name="<?php echo ($addons_data["name"]); ?>"]').attr('id', 'editor_id_<?php echo ($addons_data["name"]); ?>');
				window.UEDITOR_HOME_URL = "/weiphp30/Public/static/<?php echo ($driver_file); ?>/";
				window.UEDITOR_CONFIG.initialFrameHeight = parseInt('<?php echo ($addons_config["editor_height"]); ?>');
				window.UEDITOR_CONFIG.scaleEnabled = <?php if(($addons_config["editor_resize_type"]) == "1"): ?>true<?php else: ?>false<?php endif; ?>;
				window.UEDITOR_CONFIG.imageUrl = '<?php echo addons_url("EditorForAdmin://Upload/ue_upimg");?>';
				window.UEDITOR_CONFIG.imagePath = '';
				window.UEDITOR_CONFIG.imageFieldName = 'imgFile';
				//在这里扫描图片
				window.UEDITOR_CONFIG.imageManagerUrl='<?php echo addons_url("EditorForAdmin://Upload/ue_mgimg");?>';//图片在线管理的处理地址
        		window.UEDITOR_CONFIG.imageManagerPath='';        
				var imageEditor = UE.getEditor('editor_id_<?php echo ($addons_data["name"]); ?>',{
						toolbars: [
							['fullscreen','source', 'undo', 'redo',  
                'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'removeformat', 'formatmatch', 'autotypeset', 'blockquote', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', 'selectall',  
                 'lineheight',  
                'customstyle', 'paragraph', 'fontfamily', 'fontsize', 'indent',
                'justifyleft', 'justifycenter', 'justifyright',
                'link', 'unlink',  'insertimage', 'wordimage', 'emotion', 'attachment', 'map']
						],
						autoHeightEnabled: false,
						autoFloatEnabled: true,
						initialFrameHeight:300,
						catchRemoteImageEnable: false
					});
				imageEditor.styleUrl = "<?php echo ($styleUrl); ?>";
				//添加一下判断是否是单个按钮管理图片 需要执行一下代码
				<?php if(isset($addons_data['btnClassName'])): ?>imageEditor.ready(function () {
					  //设置编辑器不可用
					  imageEditor.setDisabled();
					  //隐藏编辑器，因为不会用到这个编辑器实例，所以要隐藏
					  imageEditor.hide();
					  //侦听图片上传
					  imageEditor.addListener('beforeInsertImage', function (t, arg) {
						 //将地址赋值给相应的input,只去第一张图片的路径
						 //console.log(t);
						 //console.log(arg);
						 <?php if(empty($addons_data['is_mult'])): ?>//单张情况
						 	 $(".<?php echo ($addons_data["name"]); ?>_preview").html("");
						 	 var imghtml = $("<img src="+arg[0].src+" width='100' height='100'/>");
							
							 $(".<?php echo ($addons_data["name"]); ?>_preview").append(imghtml);
							 //储存路劲
							 //单张图片存储图片id
							  $("#editor_id_<?php echo ($addons_data["name"]); ?>").text(arg[0].id);
							 //图片预览
							 $(".<?php echo ($addons_data["name"]); ?>_preview").show();
							 //微信预览
							 $('.weixin-cover-pic').attr('src',arg[0].src);
							 //console.log(arg);
							 
						 <?php else: ?>
						 	//多张情况
						 	var srcs = "";
							var srcIds = "";
							 //$(".<?php echo ($addons_data["name"]); ?>_preview").html("");
							 for(var i=0;i<arg.length;i++){
								 if(i==arg.length-1){
									srcs = srcs + arg[i].src;
									srcIds = arg[i].id;
								 }else{
									 srcs = srcs + arg[i].src+"|";
									 srcIds = srcIds + arg[i].id+",";
									 }
								var imghtml = $("<img src="+arg[i].src+" width='100' height='100'/>");
								$(".<?php echo ($addons_data["name"]); ?>_preview").append(imghtml);
								//console.log(arg[i].src);	 
							 }
							 $(".<?php echo ($addons_data["name"]); ?>_preview").append(imghtml);
							  $(".<?php echo ($addons_data["name"]); ?>_preview").show();
							  var oldIds = $("#editor_id_<?php echo ($addons_data["name"]); ?>").text();
							  //多张图片存储一逗号分隔的id串
							  $("#editor_id_<?php echo ($addons_data["name"]); ?>").text(oldIds+","+srcIds);<?php endif; ?>
					  })
					  //增加按钮className
					  $('.<?php echo ($addons_data["btnClassName"]); ?>').bind('click',function(){
							var uploadImage = imageEditor.getDialog("insertimage");
								uploadImage.open();
						  })
					 
				 });<?php endif; ?>
			</script>
            <script type="text/javascript" charset="utf-8" src="/weiphp30/Public/static/<?php echo ($driver_file); ?>/h5/main.js?v=<?php echo SITE_VERSION;?>"></script><?php endif; break;?>
	<?php case "3": ?>
		<script type="text/javascript" src="/weiphp30/Public/static/jquery-migrate-1.2.1.min.js"></script>
		<script charset="utf-8" src="/weiphp30/Public/static/xheditor/xheditor-1.2.1.min.js"></script>
		<script charset="utf-8" src="/weiphp30/Public/static/xheditor/xheditor_lang/zh-cn.js?v=<?php echo SITE_VERSION;?>"></script>
		<script type="text/javascript" src="/weiphp30/Public/static/xheditor/xheditor_plugins/ubb.js?v=<?php echo SITE_VERSION;?>"></script>
		<script type="text/javascript">
		var submitForm = function (){
			$('textarea[name="<?php echo ($addons_data["name"]); ?>"]').closest('form').submit();
		}
		$('textarea[name="<?php echo ($addons_data["name"]); ?>"]').attr('id', 'editor_id_<?php echo ($addons_data["name"]); ?>')
		$('#editor_id_<?php echo ($addons_data["name"]); ?>').xheditor({
			tools:'full',
			showBlocktag:false,
			forcePtag:false,
			beforeSetSource:ubb2html,
			beforeGetSource:html2ubb,
			shortcuts:{'ctrl+enter':submitForm},
			'height':'<?php echo ($addons_config["editor_height"]); ?>',
			'width' :'100%'
		});
		</script>
		<input type="hidden" name="parse" value="1"><?php break;?>
	<?php case "4": ?>
		<link rel="stylesheet" href="/weiphp30/Public/static/thinkeditor/skin/default/style.css?v=<?php echo SITE_VERSION;?>" />
		<script type="text/javascript" src="/weiphp30/Public/static/jquery-migrate-1.2.1.min.js"></script>
		<script type="text/javascript" src="/weiphp30/Public/static/thinkeditor/jquery.thinkeditor.js?v=<?php echo SITE_VERSION;?>"></script>
		<script type="text/javascript">
			$(function(){
				$('textarea[name="<?php echo ($addons_data["name"]); ?>"]').attr('id', 'editor_id_<?php echo ($addons_data["name"]); ?>');
				var options = {
					"items"  : "h1,h2,h3,h4,h5,h6,-,link,image,-,bold,italic,code,-,ul,ol,blockquote,hr,-,fullscreen",
			        "width"  : "100%", //宽度
			        "height" : "<?php echo ($addons_config["editor_height"]); ?>", //高度
			        "lang"   : "zh-cn", //语言
			        "tab"    : "    ", //Tab键插入的字符， 默认为四个空格
					"uploader": "<?php echo addons_url('Editor://Upload/upload');?>"
			    };
			    $('#editor_id_<?php echo ($addons_data["name"]); ?>').thinkeditor(options);
			})
		</script>
		<input type="hidden" name="parse" value="2"><?php break; endswitch;?>