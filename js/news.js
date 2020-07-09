$(function() {
    var color;
    if(localStorage.getItem('themecolor')) color = localStorage.getItem('themecolor');
    else color = "#9400D3";
    $('#tech').css('color', color);
    $('#busi').css('color', color);
    $('#sprt').css('color', color);
    $('.small_style').css('color', color);
	$('#tech').on('click',function() {
		$('#show').html("");
		$('#news_spnr').fadeIn();
		$('#tech').css('border-bottom','2px solid ' + color);
		$('#busi').css('border-bottom','0px');
		$('#sprt').css('border-bottom','0px');
		$.ajax({
			type:"GET",
			url:"http://localhost/buddyBonds_backup/scripts/news_tech.php",
			success:function(result) {
				$('#news_spnr').fadeOut();
				$('#show').html(result);
			}
		});
	});
	$('#tech_sm').on('click',function() {
		$('#show').html("");
		$('#news_spnr').fadeIn();
		$('#tech_sm').css('border-bottom','2px solid ' + color);
		$('#busi_sm').css('border-bottom','0px');
		$('#sprt_sm').css('border-bottom','0px');
		$.ajax({
			type:"GET",
			url:"http://localhost/buddyBonds_backup/scripts/news_tech.php",
			success:function(result) {
				$('#news_spnr').fadeOut();
				$('#show').html(result);
			}
		});
	});
	$('#busi').on('click',function() {
		$('#show').html("");
		$('#news_spnr').fadeIn();
		$('#busi').css('border-bottom','2px solid ' + color);
		$('#tech').css('border-bottom','0px');
		$('#sprt').css('border-bottom','0px');
		$.ajax({
			type:"GET",
			url:"http://localhost/buddyBonds_backup/scripts/news_busi.php",
			success:function(result) {
				$('#news_spnr').fadeOut();
				$('#show').html(result);
			}
		});
	});
	$('#busi_sm').on('click',function() {
		$('#show').html("");
		$('#news_spnr').fadeIn();
		$('#busi_sm').css('border-bottom','2px solid ' + color);
		$('#tech_sm').css('border-bottom','0px');
		$('#sprt_sm').css('border-bottom','0px');
		$.ajax({
			type:"GET",
			url:"http://localhost/buddyBonds_backup/scripts/news_busi.php",
			success:function(result) {
				$('#news_spnr').fadeOut();
				$('#show').html(result);
			}
		});
	});
	$('#sprt').on('click',function() {
		$('#show').html("");
		$('#news_spnr').fadeIn();
		$('#sprt').css('border-bottom','2px solid ' + color);
		$('#busi').css('border-bottom','0px');
		$('#tech').css('border-bottom','0px');
		$.ajax({
			type:"GET",
			url:"http://localhost/buddyBonds_backup/scripts/news_sprt.php",
			success:function(result) {
				$('#news_spnr').fadeOut();
				$('#show').html(result);
			}
		});
	});
	$('#sprt_sm').on('click',function() {
		$('#show').html("");
		$('#news_spnr').fadeIn();
		$('#sprt_sm').css('border-bottom','2px solid ' + color);
		$('#busi_sm').css('border-bottom','0px');
		$('#tech_sm').css('border-bottom','0px');
		$.ajax({
			type:"GET",
			url:"http://localhost/buddyBonds_backup/scripts/news_sprt.php",
			success:function(result) {
				$('#news_spnr').fadeOut();
				$('#show').html(result);
			}
		});
	});
	var $ww=$(window).width();
	$('#nav_row_sm').width($ww);
	if(window.matchMedia("(min-width:200px) and (max-width:900px)").matches) {
		$ww-=33;
		$('#tech_sm').width($ww/3);
		$('#busi_sm').width($ww/3);
		$('#sprt_sm').width($ww/3);
	}
	if(window.matchMedia("(min-width:900px) and (max-width:1020px)").matches) {
		$ww-=88;
		$('#tech_sm').width($ww/3);
		$('#busi_sm').width($ww/3);
		$('#sprt_sm').width($ww/3);	
	}
	//set logout time on window close
	$(window).on('beforeunload', function() {
	    $.ajax({
	        type:"POST",
	        url:"http://localhost/buddyBonds_backup/scripts/set_logout_time.php",
	        success:function(result) {}
        });
	}); 
});