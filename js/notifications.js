$(function() {
    //update ntfs seen
    update_ntfs_seen();
	$('#br_hdr').on('click',function() {
		if($('#br_div').is(':visible')) {
			$('#br_div').fadeOut();
			$('#br_div_open').fadeIn();
			$('#br_div_close').fadeOut();
		} else if(!$('#br_div').is(':visible')) {
			$('#br_div').fadeIn();
			$('#br_div_close').fadeIn();
			$('#br_div_open').fadeOut();
		}
	});
	$('#pn_hdr').on('click',function() {
		if($('#pn_div').is(':visible')) {
			$('#pn_div').fadeOut();
			$('#pn_div_open').fadeIn();
			$('#pn_div_close').fadeOut();
		} else if(!$('#pn_div').is(':visible')) {
			$('#pn_div').fadeIn();
			$('#pn_div_close').fadeIn();
			$('#pn_div_open').fadeOut();
		}
	});
	$('#pg_hdr').on('click',function() {
		if($('#pg_div').is(':visible')) {
			$('#pg_div').fadeOut();
			$('#pg_div_open').fadeIn();
			$('#pg_div_close').fadeOut();
		} else if(!$('#pg_div').is(':visible')) {
			$('#pg_div').fadeIn();
			$('#pg_div_close').fadeIn();
			$('#pg_div_open').fadeOut();
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
});
function update_ntfs_seen() {
	$.ajax({
		type:"GET",
		url:"http://localhost/buddyBonds_backup/scripts/update_ntfs_seen.php",
		success:function(result) {}
	});
}