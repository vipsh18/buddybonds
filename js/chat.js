$(function() {
	if(localStorage.getItem('hm_thm')) {
	    var hm_thm = "images/" + localStorage.getItem('hm_thm');
	    $('#chats_area').css('background-image','url(' + hm_thm + ')');
	}
	$('[data-toggle="popover"]').popover();
	var $dw=$(window).width();
	var $dh=$(window).height();
	$('#container').css('max-width',$dw);
	$('#container').css('margin-left','0px');
	var $caw = $('#chats_area').innerWidth();
	$('#new_msg_cont').width($caw+5);
	$('#new_msg_cont').css('max-width',$caw+5);
	// when popover's content is shown
	$('#emoji_btn').on('shown.bs.popover', function() {
    	$('.emojis').click(function() {
			var unicode = "0x1F" + $(this).data('emoji');
			var new_msg_cont = document.getElementById('new_msg_cont');
			$('#new_msg_cont').val($('#new_msg_cont').val() + String.fromCodePoint(unicode));
			textAreaAdjust(new_msg_cont);
			if($('#new_msg_cont').height() >= 150) $('#new_msg_cont').css('overflow-y','scroll');
		    else $('#new_msg_cont').css('overflow','hidden');
		});
	});
	//hide emoji box on doc. click
	$(document).on('click', function (e) {
    	$('[data-toggle="popover"],[data-original-title]').each(function () {
        	//the 'is' for buttons that trigger popups
        	//the 'has' for icons within a button that triggers a popup
        	if(!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0)             
           		(($(this).popover('hide').data('bs.popover')||{}).inState||{}).click = false  // fix for BS 3.3.6
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
            alert('Please select a valid file type! The file types allowed are JPEG/PNG/JPG/GIF/TXT/HTML/MPEG/OGG/WAV/MKV/WEBM/MP3/MP4/3GP/PDF.');
            $("#attach_file_form").fadeOut();
            $('#close_atf').fadeOut();
            return false;
        }
	});
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
	//grp checkboxes div
	$('.grp_checkboxes_div').click(function() {
		if($(this).find(':checkbox').is(':checked')) {
			$(this).find(':checkbox').prop('checked', false);
			$(this).find('.grp_checkboxes_label').html('<i class="far fa-circle"></i>');
			var sp = parseInt($('#selected_participants').html(), 10) - 1;
			$('#selected_participants').html(sp);
			if(sp >= 3) {
				if($('#grp_tobemade_name').val().length >= 1) $('#create_grp_btn').prop('disabled', false);
				$('#grp_tobemade_picbtn').fadeIn();
				$('#grp_tobemade_name').fadeIn();
				$('#grp_tobemade_picname').fadeIn();
			}
			else {
				$('#create_grp_btn').prop('disabled', true);
				$('#grp_tobemade_picbtn').hide();
				$('#grp_tobemade_name').hide();
				$('#grp_tobemade_picname').hide();
			}
			$(this).css('background-color','white');
			$(this).tooltip('hide').attr('data-original-title', "Add To Group").tooltip('show');
		} else {
			$(this).find(':checkbox').prop('checked', true);
			$(this).find('.grp_checkboxes_label').html('<i class="fas fa-check-circle"></i>');
			var sp = parseInt($('#selected_participants').html(), 10) + 1;
			$('#selected_participants').html(sp);
			if(sp >= 3) {
				if($('#grp_tobemade_name').val().length >= 1) $('#create_grp_btn').prop('disabled', false);
				$('#grp_tobemade_picbtn').fadeIn();
				$('#grp_tobemade_name').fadeIn();
				$('#grp_tobemade_picname').fadeIn();
			}
			else {
				$('#create_grp_btn').prop('disabled', true);
				$('#grp_tobemade_picbtn').hide();
				$('#grp_tobemade_name').hide();
				$('#grp_tobemade_picname').hide();
			}
			$(this).css('background-color','lavender');
			$(this).tooltip('hide').attr('data-original-title', "Do Not Add To Group").tooltip('show');
			if(sp >= 26) {
				alert("Sorry, the current maximum for Group Members is 25");
				sp = 25;
				$('#selected_participants').html(sp);
				$(this).find(':checkbox').prop('checked', false);
				$(this).css('background-color','white');
				$(this).find('.grp_checkboxes_label').html('<i class="far fa-circle"></i>');
				$(this).tooltip('hide').attr('data-original-title', "Add To Group").tooltip('show');
			}
		}
	});
	$('#grp_tobemade_name').on('focus', function() {
		$(this).css('border-bottom','2px solid grey');
	});		
	$('#grp_tobemade_name').on('blur', function() {
		$(this).css('border-bottom','none');
	});	
	$('#grp_tobemade_name').keyup(function() {
		if($(this).val().length >= 1) $('#create_grp_btn').prop('disabled', false);
		else $('#create_grp_btn').prop('disabled', true);
	});
	$('#grp_tobemade_picbtn').click(function() {
		$('#grp_tobemade_pic').click();
	});
	$('#grp_tobemade_pic').change(function() {
		var file = this.files[0];
		var filetype = file.type;
		var match= ["image/jpeg", "image/png", "image/jpg"];
        if(!((filetype==match[0]) || (filetype==match[1]) || (filetype==match[2]))) {
            alert('Please select a valid image file (JPEG/JPG/PNG).');
            return false;
        } else $('#grp_tobemade_picname').html(file.name);
	});
	$('#create_grp_btn').click(function() {

	});
});
//check for chrome and edge and increase chats_area height
var isChromium = window.chrome;
var winNav = window.navigator;
var vendorName = winNav.vendor;
var isOpera = typeof window.opr !== "undefined";
var isIEedge = winNav.userAgent.indexOf("Edge") > -1;
var isIOSChrome = winNav.userAgent.match("CriOS");
if(isIOSChrome) { $('#chats_area').height(518);
$('#new_msg_cont').css('left','-1px'); }
else if(isChromium !== null 
		&& typeof isChromium !== "undefined" 
		&& vendorName === "Google Inc." 
		&& isOpera === false && 
		isIEedge === false) { $('#chats_area').height(518); $('#new_msg_cont').css('left','-1px'); }
else if(isIEedge) { $('#chats_area').height(512); $('#new_msg_cont').css('left','-1px'); }
//resize textarea on keyup
function textAreaAdjust(o) {
    o.style.height = "47px";
    o.style.height = (15+o.scrollHeight)+"px";
}