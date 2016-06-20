var attachHtml = $('<div class="attach_action_bg" style="display:none"><div class="close_layer"></div>'+
'<div class="attach_action">'+
	'<a id="preview" href="javascript:;">预览</a>'+
	'<a id="sendMail" href="javascript:;">发送到邮箱</a>'+
'</div>'+
'</div>');
$('body').append(attachHtml);
$('.close_layer',attachHtml).click(function(){
	attachHtml.hide();
})
$('.close_layer',attachHtml).click(function(){
	attachHtml.hide();
})
$('.close_layer',attachHtml).click(function(){
	attachHtml.hide();
})
$('#preview',attachHtml).click(function(){
	if(MID==0){
		window.location.href = U('w3g/Public/login');
		return;
	}
	$.post(U('w3g/Weiba/doDownloadLog'),{'attach_id':window.attachId,'post_id':window.postId,});
	window.location.href = window.attachUrl;
})
$('#sendMail',attachHtml).click(function(){
	if(MID==0){
		window.location.href = U('w3g/Public/login');
		return;
	}
	showSendMailBox();
})
function openAttachAction(url,name,attachId,postId){
	attachHtml.show();
	window.attachUrl = url;
	window.attachName = name;
	window.attachId = attachId;
	window.postId = postId;
}
function showSendMailBox(){
	attachHtml.hide();
	var html= $('<div class="send_mail_box"><div class="inner"><div id="mcc"><p class="title"></p><span class="close"></span><p class="con">邮箱：<input type="text" name="email"/></p><div class="send_bar"><a href="javascript:;" id="sendMail">发送</a></div></div><div id="mload"></div></div></div>');
	$('body').append(html);
	var name = window.attachName.length>10?window.attachName.substring(0,10)+'...':window.attachName
	$('.title',html).text('发送附件:'+name);
	$('.close',html).click(function(){
		html.remove();
	});
	$('#sendMail',html).click(function(){
		
		var postId = window.postId;
		var email = $('input',html).val();
		var attachId = window.attachId;
		var myreg = /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
		if(email=="" && !myreg.test(email)){
			$.ui.showMask("请填写正确的邮箱地址！", true);
			return;
		}
		$('#mcc',html).hide();
		$('#mload',html).show();
		$.post(U('w3g/Weiba/sendAttachMail'),{post_id:postId,email:email,attach_id:attachId},function(data){
				if(data==-1){
					$.ui.showMask("请填写正确的邮箱地址！", true);
					$('#mcc',html).show();
					$('#mload',html).hide();
				}else if(data==1){
					$.ui.showMask("发送成功", true);
					html.remove();
				}else{
					$.ui.showMask("发送失败:"+data, true);
					$('#mcc',html).show();
					$('#mload',html).hide();
					
				}
			})
		
	})
}
