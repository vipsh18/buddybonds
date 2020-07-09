$(function() {
	//save_descp on click
	$('#save_descp_btn').on('click',function() {
		var description=$('#description').val();
		$('#spnr_img').show();
		if($('#description').val().length<=0) {
			$.dialog({
				title:"Description empty!",
				content:"Your description can not be empty!",
				animation:'RotateX',
        		animationBounce: 2.5, 
        		closeAnimation:'RotateY',
        		theme:'modern'
			});
		} else {
			$.ajax({
				type:"POST",
				url:"http://localhost/buddyBonds_backup/scripts/save_descp.php",
				data:{
					"description":description
				},
				success:function(result) {
					$('#spnr').hide();
					$('#description').text(result);
					$.dialog({
						title:"Description updated!",
						content:"Your description has been updated!",
						animation:'RotateX',
        				animationBounce: 2.5, 
        				closeAnimation:'RotateY',
        				theme:'modern'
					});
				}
			});
		}
	});
	if(window.matchMedia("(min-width:200px) and (max-width:1000px)").matches) {
		//navrow_sm width medium devices
		var $ww=$(window).width();
		$('#navrow_sm').width($ww);
		$ww=$ww-17;
		$('#posts_btn_sm').width($ww/2);
		$('#buddies_btn_sm').width($ww/2);
		//show navs small devices back
		$('.close').on('click', function() {
			$('#m_nav').fadeIn();
			$('#infm').fadeIn();
		});
	}
	if(window.matchMedia("(min-width:200px) and (max-width:700px)").matches) {
		//set width of posts area
		var $wpa=$(window).width();
		$wpa=$wpa-10;
		$('#posts_area').width($wpa);
		//set navrow_sm width
		var $ww1=$(window).width();
		$('#navrow_sm').width($ww1);
		$ww1=$ww1-17;
		$('#posts_btn_sm').width($ww1/2);
		$('#buddies_btn_sm').width($ww1/2);
	}
	if(window.matchMedia("(min-width:1000px) and (max-width:2000px)").matches) {
		//show navs large back
		$('.close').on('click',function() {
			$('#nav').fadeIn();
		});
		//set width of posts area larger devices
		var $wpa1=$(window).width();
		$wpa1=$wpa1-370;
		$('#posts_area').width($wpa1);
		//set width of navrow 
		var $paw=$('#profile_area').width();
		$('#navrow').width($paw);
	}
	//set width of posts_area in medium devices
	if(window.matchMedia("(min-width:700px) and (max-width:1000px)").matches) {
		var $wpa2=$(window).width();//width of posts area
		$wpa2=$wpa2-20;
		$('#posts_area').width($wpa2);
	}
	//set width of comment form acc. to smaller screen width
	if(window.matchMedia("(min-width:200px) and (max-width:700px)").matches) {
		var $wpa3=$('#posts_area').width();//width of posts area
		$wpa3=$wpa3-90;
		$('.comment').width($wpa3);
	}
	//set width of comment form acc. to medium screen width
	if(window.matchMedia("(min-width:701px) and (max-width:1100px)").matches) {
		var $wpa4=$('#posts_area').width();//width of posts area
		$wpa4=$wpa4-120;
		$('.comment').width($wpa4);
	}
	//set width of comment form acc. to larger screen width
	if(window.matchMedia("(min-width:1101px) and (max-width:2000px)").matches) {
		var $wpa5=$('#posts_area').width();//width of posts area
		$wpa5=$wpa5-110;
		$('.comment').width($wpa5);
	}
	if(window.matchMedia("(min-width:200px) and (max-width:900px)").matches) {
		var $saw=$('#posts_area').width();
		$saw-=10;
		$('.inner-image').width($saw/3);
	}
	//show image browse button when image has been selected
	$('#buddypic').on('change',function() {
		var image_file=$('#buddypic').val();
		if(image_file) $('#cbp_form').css('display','block');
	});
	//edit btn click
	$('#ed_btn').on('click',function() {
		$('#description').prop('disabled',false);
		$('#description').focus();
		$('#save_descp_btn').prop('disabled',false);
	});
	//resize descp textarea by default
	$('textarea').each(function() {
        $(this).height($(this).prop('scrollHeight'));
    });
	//close the modal
	$('.close').on('click',function() {
		$('#myModal').fadeOut();
		$('#smlr_modal').fadeOut();
	});
	//set logout time on window close
	$(window).on('beforeunload', function() {
	    $.ajax({
	        type:"POST",
	        url:"http://localhost/buddyBonds_backup/scripts/set_logout_time.php",
	        success:function(result) {}
        });
	});
});
//resize textarea descp on keyup
function textAreaAdjust(o) {
  o.style.height = "1px";
  o.style.height = (15+o.scrollHeight)+"px";
}