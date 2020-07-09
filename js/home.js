$(function() {
    var color;
    //disable right click
    $('.post_img').contextmenu(function(e) {
	    e.preventDefault();
	    return false;
    });
    $('#pic_alert_fadeout').click(function() {
        $('#pic_form_alert').fadeOut(); 
    });
    $('#pic').change(function() {
        var imagefile = $('#pic').val();
	    if(imagefile) {
	        $('#pic_form_alert').fadeIn();
	        $(window).scrollTop(0);
	    }
    });
    //tinker this and that
    if(window.matchMedia("(min-width:200px) and (max-width:700px)").matches) {
	    //set width of posts_area in smaller devices
	    var $dws=$(window).width();
	    var $idw = $(window).width();
	    $dws=$dws-2;
	    $('#posts_area').width($dws);
	    //set width of comment form acc. to smaller screen width
	    //width of posts area
	    var $wpa=$('#posts_area').width();
	    $wpa=$wpa-90;
    	$('.comment').width($wpa);
    }
    //smae shit everyday
    if(window.matchMedia("(min-width:700px) and (max-width:1100px)").matches) {
	    //set width of posts_area in medium and med-large devices
	    var $dws1=$(window).width();
	    var $idw1 = $(window).innerWidth();
	    $dws1=$dws1-20;
	    $('#posts_area').width($dws1);
	    //set width of comment form acc. to medium screen width and med-large
	    //width of posts area
	    var $wpa1=$('#posts_area').width();
	    $wpa1=$wpa1-120;
        $('.comment').width($wpa1);
    }
    //change comment area width
    if(window.matchMedia("(min-width:1101px) and (max-width:2000px)").matches) {
	    //set width of comment form acc. to screen width
	    //width of posts area
	    var $wpa2=$('#posts_area').width();
    	$wpa2=$wpa2-120;
	    $('.comment').width($wpa2);
    }
    //small devices changes
    if(window.matchMedia("(min-width:200px) and (max-width:1000px)").matches) {
	    //show navs small devices back
	    $('.close').on('click',function() {
		    $('#m_nav').fadeIn();
		    $('#infm').fadeIn();
	    });
	    var $dw=$(window).width();
	    $('body').width($dw);
    }
    //large devices changes
    if(window.matchMedia("(min-width:1000px) and (max-width:2000px)").matches) {
	    //show navs large back
	    $('.close').on('click',function() {
		    $('#nav').fadeIn();
    	});
    }
    //close the modal
    $('.close').on('click',function() {
	    $('#myModal').fadeOut();
    });
    //autoplay video when scrolled to
    // Get media - with autoplay disabled (audio or video)
    var media=$('video').not("[autoplay='autoplay']");
    var tolerancePixel=40;
    function checkMedia() {
        // Get current browser top and bottom
        var scrollTop=$(window).scrollTop()+tolerancePixel;
        var scrollBottom=$(window).scrollTop()+$(window).height()-tolerancePixel;
   	    media.each(function(index,el) {
            var yTopMedia=$(this).offset().top;
            var yBottomMedia=$(this).height()+yTopMedia;
            if(scrollTop<yBottomMedia&&scrollBottom>yTopMedia) $(this).get(0).play();
            else $(this).get(0).pause();
        });
    }
    $(window).on('scroll',function() {
        if($(window).scrollTop() >= 100) {
            if($('#sdnav_cbtn').is(':visible')) {
                $('#share_your_moment').addClass('sticky-top');
                $('#share_your_moment').css({'top':'158px','border':'2px solid lavender'});
            } else {
                $('#share_your_moment').addClass('sticky-top');
                $('#share_your_moment').css({'top':'51px','border':'2px solid lavender'});
            }
        }
    });
    //set logout time on window close
    $(window).on('beforeunload', function() {
        $.ajax({
	        type:"POST",
	        url:"http://localhost/buddyBonds_backup/scripts/set_logout_time.php",
	        success:function(result) {}
        });
    });
    $('.share_btn').click(function() {
        var link = "http://localhost/buddyBonds_backup/post.php?p=" + $(this).data('pid');
        var tempInput = document.createElement("input");
        tempInput.style = "position: absolute; left: -1000px; top: -1000px";
        tempInput.value = link;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand("copy");
        document.body.removeChild(tempInput);
        $(this).tooltip('hide').attr('data-original-title', 'Copied!').tooltip('show');
    });
    $('.share_btn').mouseout(function() {
        $(this).tooltip('hide').attr('data-original-title', 'Click To Copy Link');
    });
    $('.theme_box_pc').click(function() {
        color = $(this).data('themecolor');
        localStorage.setItem('themecolor', color);
        $.ajax({
            type:"GET",
            url:"http://localhost/buddyBonds_backup/scripts/set_theme.php",
            data:{
                "themecolor": color
            }
        }).done(function(result) {
            change_theme(color);
        });
    });
    $('#ui_edbtn').mouseover(function() {
        var coloredbtn;
        if(localStorage.getItem('themecolor')) coloredbtn = localStorage.getItem('themecolor');
        else coloredbtn = "#9400D3";
        $(this).css('box-shadow', '3px 3px ' + coloredbtn);
    });
    $('#ui_edbtn').mouseout(function() {
        $(this).css('box-shadow', '0px 0px white');
    });
    $("#search").on('focus', function() {
    	if (!$("#suggestions").is(":hidden")) $("#share_your_moment").hide();
    });
    $("#search").on('blur', function() {
    	if ($("#suggestions").is(":hidden")) $('#share_your_moment').show();
    });
});