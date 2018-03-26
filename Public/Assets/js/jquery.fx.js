
function searchButtun(dom,value){
	$('#searchform').find(dom).val(value);
	$('#searchform').submit();
}

//删除操作
function Delete(id,url,msg,dom){
	if(!msg) msg = '是否要删除？';
	var obj = dom?$(dom):$('#row-'+id);
	$.confirm(msg,function(){
		$.ajax({
			type: "GET",
		   	url: url,
		   	dataType: "json",
			timeout: 5000,
			error:function(){ alert('网络连接不正常或服务器错误，请重新登陆后再试！');},
		  	success:function(data){
				$.alert(data.info,data.status);
				if(data.status==1){
					obj.remove();	
				}
			}
		 });
	},true)
}

//选择列表
$.extend({Select:{
	All:function(obj,name,selected){ //全选及反选
		var checkbox = $("input[name='"+name+"']");
		if(!selected) selected = 'selected';
		if(obj.checked==true){
			checkbox.each(function(){
				this.checked = true;
				$(this).parent().parent().addClass(selected);
			});
		}else{
			checkbox.each(function(){
				this.checked = false;
				$(this).parent().parent().removeClass(selected);
			});
		}
	},
	This:function(obj,selected,callback){ //单选
		if(!selected) selected = 'selected';
		if(obj.checked==true){
			obj.parentNode.parentNode.className = selected;
		}else{
			obj.parentNode.parentNode.className = '';
		}
		if(callback){
			callback(obj);
		}
	}
}});

//排序
function orderBy(e,field){
	var obj = {asc:'asc',desc:'desc',ascClass:'ui-table-hcell-asc',descClass:'ui-table-hcell-desc'};
	var sot = $('#sort');
	if(sot.val() != obj.asc){
		sot.val(obj.asc); e.addClass(obj.ascClass).removeClass(obj.descClass);
	}else{
		sot.val(obj.desc);e.addClass(obj.descClass).removeClass(obj.ascClass);
	}
	$('#orderby').val(field);
	$('#searchform').submit();
}

//列表显示和隐藏
function rowsToggle(id){
	var _this   = $('#id-'+id);
	var _detail = $('#detail-'+id);
	var _class  = 'ui-table-subentry-opened';
	if(_this.hasClass(_class)){
		_this.removeClass(_class);
		_detail.hide();
	}else{
		_this.addClass(_class);
		_detail.show();
	}
}

//用户信息编辑
function infoEdit(e,id){
	var text  = new Array("修改","取消");
	var tbody = $(id).find('tbody');
	var btn   = $(id).find('.modify');

	if(e.innerHTML==text[0]){
		tbody.eq(0).hide().siblings('tbody').show();
		btn.text(text[1]);
	}else{
		tbody.eq(1).hide().siblings('tbody').show();
		btn.text(text[0]);
	}
}

//选项卡效果
(function($){
	$.fn.tabs = function(options) {  
		var defaults = {
			selected: 0,
			tabsTitle:'.ui-tab-group li',
			tabsContents:'.info-block',
			action:'click',
			current:'active'
		};
		var opt = $.extend(defaults, options);  
		var _this = this;

		//默认选择
		_this.find(opt.tabsTitle).eq(opt.selected).addClass(opt.current).siblings(opt.tabsTitle).removeClass(opt.current);
		_this.find(opt.tabsContents).eq(opt.selected).show().siblings(opt.tabsContents).hide();

		_this.find(opt.tabsTitle).bind(opt.action,function(){
			var index  = $(this).index();
			$(this).addClass(opt.current).siblings(opt.tabsTitle).removeClass(opt.current);
			_this.find(opt.tabsContents).eq(index).show().siblings(opt.tabsContents).hide();
		})
	};  
})(jQuery);


//Scroll Top
;(function(e){e.scrollUp=function(t){var n={scrollName:"scrollUp",topDistance:50,topSpeed:300,animation:"fade",animationInSpeed:200,animationOutSpeed:200,scrollText:"",scrollImg:false,activeOverlay:false};var r=e.extend({},n,t),i="#"+r.scrollName;e("<a/>",{id:r.scrollName,href:"#top",title:r.scrollText}).appendTo("body");if(!r.scrollImg){e(i).text(r.scrollText)}e(i).css({display:"none",position:"fixed","z-index":"2147483647"});if(r.activeOverlay){e("body").append("<div id='"+r.scrollName+"-active'></div>");e(i+"-active").css({position:"absolute",top:r.topDistance+"px",width:"100%","border-top":"1px dotted "+r.activeOverlay,"z-index":"2147483647"})}e(window).scroll(function(){switch(r.animation){case"fade":e(e(window).scrollTop()>r.topDistance?e(i).fadeIn(r.animationInSpeed):e(i).fadeOut(r.animationOutSpeed));break;case"slide":e(e(window).scrollTop()>r.topDistance?e(i).slideDown(r.animationInSpeed):e(i).slideUp(r.animationOutSpeed));break;default:e(e(window).scrollTop()>r.topDistance?e(i).show(0):e(i).hide(0))}});e(i).click(function(t){e("html, body").animate({scrollTop:0},r.topSpeed);t.preventDefault()})}})(jQuery);

//jQuery Pin
(function(e){"use strict";e.fn.pin=function(t){var n=0,r=[],i=!1;t=t||{};var s=function(){for(var n=0,s=r.length;n<s;n++){var o=r[n];if(t.minWidth&&e(window).width()<=t.minWidth){o.parent().is(".pin-wrapper")&&o.unwrap(),o.css({width:"",left:"",top:"",position:""}),i=!0;continue}i=!1;var u=t.containerSelector?o.closest(t.containerSelector):e(document.body),a=o.offset(),f=u.offset(),l=o.offsetParent().offset();o.parent().is(".pin-wrapper")||o.wrap("<div class='pin-wrapper'>"),o.data("pin",{from:t.containerSelector?f.top:a.top,to:f.top+u.height()-o.outerHeight(),end:f.top+u.height(),parentTop:l.top}),o.css({width:o.outerWidth()}),o.parent().css("height",o.outerHeight())}},o=function(){if(i)return;n=window.scrollY;for(var t=0,s=r.length;t<s;t++){var o=e(r[t]),u=o.data("pin"),a=u.from,f=u.to;if(a+o.outerHeight()>u.end){o.css("position","");continue}a<n&&f>n?o.css("position")!="fixed"&&o.css({left:o.offset().left,top:0}).css("position","fixed"):n>=f?o.css({left:"auto",top:f-u.parentTop}).css("position","absolute"):o.css({position:"",top:"",left:""})}},u=function(){s(),o()};return this.each(function(){var t=e(this),n=e(this).data("pin")||{};if(n&&n.update)return;r.push(t),e("img",this).one("load",s),n.update=u,e(this).data("pin",n)}),e(window).scroll(o),e(window).resize(function(){s()}),s(),e(window).load(u),this}})(jQuery);