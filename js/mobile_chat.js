$(function() {
    $('[data-toggle="popover"]').popover();
	var $dw=$(window).width();
	var $idw = $(window).innerWidth();
	var $dh=$(window).height();
	$('#new_msg_cont').width($idw-85);
	$('#chats_area').height($dh-148);
	$('#chats_area').css('max-height', $dh-148);
	$('#chats_area').css('max-width', $dw);
	// when popover's content is shown
	$('#emoji_btn').on('shown.bs.popover', function() {
    	$('.emojis').click(function() {
			var unicode = "0x1F" + $(this).data('emoji');
			var new_msg_cont = document.getElementById('new_msg_cont');
			$('#new_msg_cont').val($('#new_msg_cont').val() + String.fromCodePoint(unicode));
			textAreaAdjust(new_msg_cont);
			if($('#new_msg_cont').val().length >= 1) $('#send_msg_btn').prop('disabled', false);
	        else $('#send_msg_btn').prop('disabled', true);
			if($('#new_msg_cont').height() >= 100) $('#new_msg_cont').css('overflow-y','scroll');
		    else $('#new_msg_cont').css('overflow-y','hidden');
		});
	});
	// when popover's content is hidden
	$('#emoji_btn').on('hidden.bs.popover', function() {
    	$('.emojis').off('click');
	});
	$('#attach_btn').on('click', function() {
		$('#attachment').click();
	});
	$('#attachment').change(function() {
		$('#attach_file_form').css('display','inline');
		$('#close_atf').show();
		var file = this.files[0];
		var filetype = toLowerCase(file.type);
		var match= ["image/jpeg", "image/png", "image/jpg", "image/gif", "text/plain", "text/html", "audio/mpeg", "audio/wav", "audio/mp3", "video/mp4", "video/webm", "video/ogg", "video/mkv", "video/3gp","application/pdf"];
        if(!((filetype==match[0]) || (filetype==match[1]) || (filetype==match[2]) || (filetype==match[3]) || (filetype==match[4]) || (filetype==match[5]) || (filetype==match[6]) || (filetype==match[7]) || (filetype==match[8]) || (filetype==match[9]) || (filetype==match[10]) || (filetype==match[11]) || (filetype==match[11]) || (filetype==match[12]) || (filetype==match[13]) || (filetype==match[14]))) {
            alert('Please select a valid file type! The allowed file types are JPEG/PNG/JPG/GIF/TXT/HTML/MPEG/OGG/WAV/MKV/WEBM/MP3/MP4/3GP/PDF.');
            $("#attach_file_form").fadeOut();
            $('#close_atf').fadeOut();
            return false;
        }
	});
	//on send attachment click
	$('#attach_file_form').on('submit', function(e) {
		e.preventDefault();
		$('#attaching_file_spnr').fadeIn();
		$('#attach_send').prop('disabled', true);
		$.ajax({
			type: "POST",
			url: "http://localhost/buddyBonds_backup/scripts/msg_attach_file.php",
			data: new FormData(this),
			contentType: false,
			cache: false,
			processData: false
		}).done(function(result) {
		    $('#attach_file_form').fadeOut(); 
			$('#attaching_file_spnr').fadeOut();
			$('#close_atf').fadeOut();
			$('#chats_area').append('<div class="row row_spc"><div class="col-lg-12"><div class="user_msgs"><i class="fas fa-caret-right conv_arrow"></i>' + result + '</div></div></div>');
			$('#chats_area').animate({ scrollTop: $('#chats_area').prop("scrollHeight") });
			$('#message_sent_tone').trigger('play');
		}).fail(function(error) {
		    console.log(Error(error));
		});
	});
	$('#close_atf').on('click', function() {
		$(this).fadeOut();
		$('#attach_file_form').fadeOut();
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
//resize textarea on keyup
function textAreaAdjust(o) {
  o.style.height = "1px";
  o.style.height = (15+o.scrollHeight)+"px";
}