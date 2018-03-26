$(function(){
    var $wrapper = $("#wrapper");
    var $goRight = $("#goRight");
    var iNow = 0;
    var speed = 0;
    
    $("#menuButton").on('click',function(){
		if( $(this).hasClass('on')) return;
		$(".pg-floor").css("display","block");
		$("#gotoTop").hide();
		$wrapper.addClass("activeShadow").animate({'left':$(document).width()-50},"fast");
		$(".pg-ft").animate({'left':$(document).width()-50},"fast");
		$(this).addClass('on');
    });
    
    $wrapper.on('click',function(){
		if(parseInt($wrapper.css('left')) != 0){
			$wrapper.removeClass("activeShadow").animate({'left':0},"fast",function(){
				$(".pg-floor").css("display","none");
			})
			$(".pg-ft").animate({'left':0},"fast");
			$("#gotoTop").show();
			$("#menuButton").removeClass('on');
		}
    })
    
    $(".gotoTop").on('click',function(){
		$('html, body').animate({scrollTop: 0}, 500,function(){
			$("#gotoTop").hide()
		});
    });
    
    $(document).on("scroll",function(){
		if($(window).scrollTop() > $(window).height() ){
			$("#gotoTop").show();
		}
    })
    
    $("#getMore").on('click',function(){
		$(this).hide();
		$(".loading").show();
		setTimeout(getList,200);
    })
    
	loadsNum = 0;
	function getList(){
		loadsNum++;
		$.ajax({
			type: "POST",
			url: "#",
			data:"p="+loadsNum,
			dataType:'json',
			success: function(data){
				$(".loading").hide();
				if(data.status==1)$("#getMore").show();
				data = data.data;
				for(var i=0;i<data.length;i++){
					$('.jobList').append('<li><a href="'+data[i]['url']+'" target="_blank" rel="nofollow" class="allClick"><div class="jobRight"><span class="jiantou"></span></div><div class="jobLeft"><h3><span href="'+data[i]['url']+'">'+data[i]['title']+'</span></h3><p><span>'+data[i]['description']+'</span></p></div></a></li>');
				}
			}
		});
	}
})