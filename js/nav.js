$(function() {
    $(window).on("load", function() {
		if(!localStorage.getItem('ntfs_perm')) {
            Notification.requestPermission().then(result => localStorage.setItem('ntfs_perm', 'granted'))
            .catch(result => localStorage.setItem('ntfs_perm', 'default'));
        }
	});
    var color;
	if(localStorage.getItem('themecolor')) color = localStorage.getItem('themecolor');
	else color = "#9400D3";
    $('#nav').css('border-top', '3px solid ' + color);
    $('.big_nav_active_link').css({'color': color, 'border-bottom': '3px solid ' + color});
    $('a.big_nav_active_link').mouseover(function() {
        var coloredbtn;
        if(localStorage.getItem('themecolor')) coloredbtn = localStorage.getItem('themecolor');
        else coloredbtn = "#9400D3";
        $(this).css('color', coloredbtn);
    });
    $('a.text-secondary:not(.ts_nu)').mouseover(function() {
        var coloredbtn;
        if(localStorage.getItem('themecolor')) coloredbtn = localStorage.getItem('themecolor');
        else coloredbtn = "#9400D3";
        $(this).css('border-bottom', '3px solid ' + coloredbtn);
    });
    $('a.text-secondary:not(.ts_nu)').mouseout(function() {
        $(this).css('border-bottom', 'none');
    });
    if(window.matchMedia("(min-width:200px) and (max-width:1100px)").matches) {
	    var $ww = $(window).innerWidth();
	    $ww -= 84;
	    $('.bottom_nav_division').width($ww/5);
    }
	//set width of m_nav elements in too small
	if(window.matchMedia("(min-width:200px) and (max-width:317px)").matches) $('#infm_ul').css('font-size','17px');
	$('[data-toggle="tooltip"]').tooltip(); 
	//ajax buddy search suggestions
	$('#search').on('keyup',function() {
		var str = $('#search').val();
		if(str.length >= 1) {
			$('#spnr').show();
			$('#suggestions').hide();
			$('#srch_rb').hide();
			$.ajax({
				type:"POST",
				url:"http://localhost/buddyBonds_backup/scripts/search.php",
				data:{
					"q":str
				},
				success:function(result) {
					$('#suggestions').show();
					$('#suggestions').html(result);
					$('#spnr').hide();
					$('#srch_rb').show();
				}
			});
		} else {
			$('#suggestions').hide();
		}
	});
	//ajax buddy search for mobiles
	$('#m_search').on('keyup',function() {
		var str=$('#m_search').val();
		if(str.length>=1) {
			$('#m_spnr').show();
			$('#m_suggestions').hide();
			$('#m_srch_rb').hide();
			$.ajax({
				type:"POST",
				url:"http://localhost/buddyBonds_backup/scripts/search.php",
				data:{
					"q":str
				},
				success:function(result) {
					$('#m_suggestions').show();
					$('#m_suggestions').html(result);
					$('#m_spnr').hide();
					$('#m_srch_rb').show();
				}
			});
		} else {
			$('#m_suggestions').hide();
		}
	});
//show tooltips
$('[data-toggle="tooltip"]').tooltip(); 
//upload pic btn working
$('#upl_pic').on('click',function() {
	$('#pic').click();
});
//mob upload pic btn working
$('#m_upl_pic').on('click',function() {
	var x="http://localhost/buddyBonds_backup/home.php";
	if(window.location!=x) window.location="http://localhost/buddyBonds_backup/home.php";
	$('#pic').click();
});
//mobile search buddies....show the form
	$('#m_sbt').on('click',function() {
		var x=$('#m_search_form').is(':visible');
		if(x) {
			$('#m_search_form').hide();
			$('#m_search').val("");
			$('#m_suggestions').hide();
		} else {
			$('#m_search_form').show();
			$('#m_search').focus();
		}
	});
//options ul open
	$('#sdnav_obtn').on('click',function() {
		$('#optnav').fadeIn('fast');
		$('#sdnav_obtn').hide();
		$('#sdnav_cbtn').show();
		if(window.location == "http://localhost/buddyBonds_backup/home.php" && $(window).scrollTop() > 100) {
		    $('#share_your_moment').addClass('sticky-top');
            $('#share_your_moment').css({'top':'158px','border':'2px solid lavender'});
		}
	});
//options ul close
	$('#sdnav_cbtn').on('click',function() {
		$('#optnav').hide();
		$('#sdnav_cbtn').hide();
		$('#sdnav_obtn').show();
		if(window.location == "http://localhost/buddyBonds_backup/home.php" && $(window).scrollTop() > 100) {
		    $('#share_your_moment').addClass('sticky-top');
            $('#share_your_moment').css({'top':'51px','border':'2px solid lavender'});
		} else if(window.location == "http://localhost/buddyBonds_backup/home.php" && $(window).scrollTop() < 100) {
		    if($('#share_your_moment').hasClass('sticky-top')) {
		        $('#share_your_moment').removeClass('sticky-top');
		        $('#share_your_moment').css('top', '0');
		    }
		}
	}); 
//options in mobiles
	$('#m_sdnav_obtn').on('click',function() {
		$('#m_optnav').fadeIn('fast');
		$('#m_sdnav_obtn').hide();
		$('#m_sdnav_cbtn').show();
	});
//options in mob close
	$('#m_sdnav_cbtn').on('click',function() {
		$('#m_optnav').hide();
		$('#m_sdnav_cbtn').hide();
		$('#m_sdnav_obtn').show();
	});
//search reset btn
	$('#search').on('focus', function() {
		$('#optnav').hide();
		$('#sdnav_cbtn').hide()
		$('#sdnav_obtn').show();
		$('#srch_rb').fadeIn();
	});
//search reset btn close
	$('#search').on('blur', function() {
		$('#suggestions').css('display','none');
		$('#srch_rb').fadeOut();
	});
//search reset btn mobile
	$('#m_search').on('focus', function() {
		$('#m_srch_rb').fadeIn();
	});
//search reset btn mobile close
	$('#m_search').on('blur', function() {
		$('#suggestions').css('display','none');
		$('#m_srch_rb').fadeOut();
	});
//hide suggestions
$('#srch_rb').on('click',function() {
	$('#suggestions').css('display','none');
});
//hide suggestions mobile
$('#m_srch_rb').on('click',function() {
	$('#m_suggestions').css('display','none');
});
//show suggestions
	$('#search').on('focus', function() {
		var val=$('#search').val();
		if(val.length>=0) $('#suggestions').show();
	});
//hide suggestions
	$('#search').on('blur', function() {
		var val=$('#search').val();
		if(val.length<=0) $('#suggestions').hide();
	});
//set width of search bar in mob and tabs
	var $dw=$(window).width();
	var isSmall=window.matchMedia("only screen and (min-width:970px)");
	if(isSmall) {
		$dw = $dw-30;
		$('#m_search').width($dw);
	}
});