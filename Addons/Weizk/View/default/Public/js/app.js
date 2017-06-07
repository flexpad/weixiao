
$(function(){
	$("#menu-btn").click(function(){
		$("#menu-wrapper").toggle();
		$("#menu-backdrop").toggle();
	})
});


$(function(){
	$(".eva-adv .close").click(function(){
		$(".eva-adv").hide();
	})
})


//链接跳转问题
mui(".mui-scroll,.menu,.evaluating").on('tap','a',function(){document.location.href=this.href;});


//获得slider插件对象
var gallery = mui('.mui-slider');
gallery.slider({
  interval:5000//自动轮播周期，若为0则不自动播放，默认为0；
});

