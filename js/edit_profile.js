$(function() {
	if(localStorage.getItem('hm_thm')) {
	    var hm_thm = "images/" + localStorage.getItem('hm_thm');
	    $('body').css('background-image','url(' + hm_thm + ')');
	}
	//show labels
	$('#username').on('focus',function() {
		$('#un_lbl').fadeIn();
	});
	$('#fullname').on('focus',function() {
		$('#fn_lbl').fadeIn();
	});
	$('#email').on('focus',function() {
		$('#eml_lbl').fadeIn();
	});
	$('#birthday').on('focus',function() {
		$('#bd_lbl').fadeIn();
	});
	$('#description').on('focus',function() {
		$('#descp_lbl').fadeIn();
	});
	//fade the labels into darkness
	$('#username').on('blur',function() {
		$('#un_lbl').fadeOut();
	});
	$('#fullname').on('blur',function() {
		$('#fn_lbl').fadeOut();
	});
	$('#email').on('blur',function() {
		$('#eml_lbl').fadeOut();
	});
	$('#birthday').on('blur',function() {
		$('#bd_lbl').fadeOut();
	});
	$('#description').on('blur',function() {
		$('#descp_lbl').fadeOut();
	});
	//resize descp textarea by default
	$('textarea').each(function() {
        $(this).height($(this).prop('scrollHeight'));
    });
	//fullname error img
	$('#fullname').on('keyup',function() {
		if($('#fullname').val().length<1) {
			$('#nameErr').html('<i class="far fa-times-circle err" title="Name can not have less than 1 character!"></i>');
		} else if($('#fullname').val().length>30) {
			$('#nameErr').html('<i class="far fa-times-circle err" title="Name can not have more than 25 characters!"></i>');
		} else {
			$('#nameErr').html("");
		}
	});
	//email error img
	$('#email').on('keyup',function() {
		if($('#email').val().length<8) {
			$('#emailErr').html('<i class="far fa-times-circle err" title="Email can not have less than 8 characters!"></i>');
		} else if($('#email').val().length>40) {
			$('#emailErr').html('<i class="far fa-times-circle err" title="Email can not have more than 40 characters!"></i>');
		} else {
			$('#emailErr').html("");
		}
	});
	//submit edit form
	$('#edit_profile_form').on('submit',function() {
		if(($('#username').val().length<1)||($('#username').val().length>30)||($('#fullname').val().length<1)||($('#fullname').val().length>30)||($('#birthday').val()<1900-01-01)||($('#birthday').val()>2002-01-01)||($('#email').val().length<8)||($('#email').val().length>40)) {
			$('#fullname').focus();
			return false;
		} else {
			var submit=$(this).find(':submit');
			submit.prop('disabled',true);
			submit.val('...sending information!');
			return true;
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
//resize textarea descp on keyup
function textAreaAdjust(o) {
  o.style.height = "1px";
  o.style.height = (3+o.scrollHeight)+"px";
}