$(function() {
	$('#curr_psw').on('keyup',function() {
		if($('#curr_psw').val().length<8) $('#pswErr').html('<i class="far fa-times-circle err" title="Password can not have less than 8 characters!"></i>');
		else if($('#curr_psw').val().length>80) $('#pswErr').html('<i class="far fa-times-circle err" title="Password can not have more than 80 characters!"></i>');
		else $('#pswErr').html("");
	});
	$('#new_psw').on('keyup',function() {
		if($('#new_psw').val().length<8) $('#newpswErr').html('<i class="far fa-times-circle err" title="Password can not have less than 8 characters!"></i>');
		else if($('#new_psw').val().length>80) $('#newpswErr').html('<i class="far fa-times-circle err" title="Password can not have more than 80 characters!"></i>');
		else $('#newpswErr').html("");
	});
	$('#newpsw_cnf').on('keyup',function() {
		if($('#new_psw').val()!=$('#newpsw_cnf').val()) $('#newpswcnfErr').html('<i class="far fa-times-circle err" title="Both the passwords should match!"></i>');
		else $('#newpswcnfErr').html("");
	});
	$('#chng_password_form').on('submit',function() {
		var new_psw=$('#new_psw').val();
		var newpsw_cnf=$('#newpsw_cnf').val();
		if(($('#curr_psw').val().length<8)||($('#curr_psw').val().length>80)||($('#new_psw').val().length<8)||($('#new_psw').val().length>80)||(new_psw!=newpsw_cnf)) {
			$('#curr_psw').focus();
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