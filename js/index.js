$(function () {
	//
	//get input width
	//
	var input_org_width = $('#username').width();
	//
	//login to register form link
	$('#ltrfl').on('click', function () {
		$('#login_card').css('display', 'none');
		$('#reg_card').fadeIn();
		// $('#form_cards').css('margin-top','40px');
		// $('#form_cards').css('margin-bottom','40px');
		if (window.matchMedia('(min-width:200px) and (max-width:1100px)').matches) {
			// $('#form_cards').css('margin-top', '20px');
			// $('#form_cards').css('margin-bottom', '10px');
		}
	});
	//register to login form link
	$('#rtlfl').on('click', function () {
		$('#reg_card').css('display', 'none');
		$('#login_card').fadeIn();
		// $('#form_cards').css('margin-top','120px');
		// $('#form_cards').css('margin-top','100px');
		if (window.matchMedia('(min-width:200px) and (max-width:768px)').matches) {
			// $('#form_cards').css('margin-top', '100px');
			// $('#form_cards').css('margin-bottom', '10px');
		}
	});
	//decrease padding of navbar on scroll
	$(window).scroll(function () {
		var x = window.pageYOffset;
		if (x > 10) {
			$('.navbar').css('padding', '3px');
			$('#sc_nav').css('font-size', '26px');
		} else {
			$('.navbar').css('padding', '10px');
			$('#sc_nav').css('font-size', '28px');
		}
	});
	//
	//check input lengths
	//
	$('#username').on('keyup', function () {
		if ($('#username').val().length < 1) {
			$('#login_btn').prop('disabled', true);
			$('#unErr').html('<i class="far fa-times-circle err" title="Username can not have less than 1 character!"></i>');
			ispr($(this));
		} else if ($('#username').val().length > 30) {
			$('#login_btn').prop('disabled', true);
			$('#unErr').html('<i class="far fa-times-circle err" title="Username can not have more than 30 characters!"></i>');
			ispr($(this));
		} else {
			$('#unErr').html('');
			if ($('#password').val().length >= 8) $('#login_btn').prop('disabled', false);
			isb($(this));
		}
	});
	$('#password').on('keyup', function () {
		if ($('#password').val().length < 8) {
			$('#login_btn').prop('disabled', true);
			$('#pswErr').html('<i class="far fa-times-circle err" title="Password can not have less than 8 characters!"></i>');
			ispr($(this));
		} else if ($('#password').val().length > 80) {
			$('#login_btn').prop('disabled', true);
			$('#pswErr').html('<i class="far fa-times-circle err" title="Password can not have more than 80 characters!"></i>');
			ispr($(this));
		} else {
			$('#pswErr').html('');
			if ($('#username').val().length >= 1) $('#login_btn').prop('disabled', false);
			isb($(this));
		}
	});
	$('#fullname').on('keyup', function () {
		if ($('#fullname').val().length < 1) {
			$('#reg_btn').prop('disabled', true);
			$('#nameErr').html('<i class="far fa-times-circle err" title="Name can not have less than 1 character!"></i>');
			ispr($(this));
		} else if ($('#fullname').val().length > 30) {
			$('#reg_btn').prop('disabled', true);
			$('#nameErr').html('<i class="far fa-times-circle err" title="Name can not have more than 30 characters!"></i>');
			ispr($(this));
		} else {
			$('#nameErr').html('');
			if ($('#reg_username').val().length >= 1 && $('#reg_psw').val().length >= 8 &&
			$('#reg_cnfpsw').val().length >= 8 && $('#reg_email').val().length >= 8)
				$('#reg_btn').prop('disabled', false);
			isb($(this));
		}
	});
	//ajax check username and len check
	$('#reg_username').on('keyup', function () {
		if ($('#reg_username').val().length < 1) {
			$('#reg_btn').prop('disabled', true);
			$('#reg_unErr').html('<i class="far fa-times-circle err" title="Username can not have less than 1 character!"></i>');
			$('#usernamecheck').hide();
			ispr($(this));
		} else if ($('#reg_username').val().length > 30) {
			$('#reg_btn').prop('disabled', true);
			$('#reg_unErr').html('<i class="far fa-times-circle err" title="Username can not have more than 30 characters!"></i>');
			$('#usernamecheck').hide();
			ispr($(this));
		} else {
			$('#reg_unErr').html('');
			ispr($(this));
			if ($('#fullname').val().length >= 1 && $('#reg_psw').val().length >= 8 && 
			$('#reg_cnfpsw').val().length >= 8 && $('#reg_email').val().length >= 8)
				$('#reg_btn').prop('disabled', false);
			$('#spnr').show();
			$('#usernamecheck').hide();
			$.ajax({
				type: 'POST',
				url: 'http://localhost/buddyBonds_backup/scripts/checkUsername.php',
				data: {
					reg_username: $('#reg_username').val()
				},
				success: function (result) {
					$('#usernamecheck').show();
					$('#usernamecheck').html(result);
					$('#spnr').hide();
				}
			});
		}
	});
	$('#reg_psw').on('keyup', function () {
		if ($('#reg_psw').val().length < 8) {
			$('#reg_btn').prop('disabled', true);
			$('#reg_pswErr').html('<i class="far fa-times-circle err" title="Password can not have less than 8 characters!"></i>');
			ispr($(this));
		} else if ($('#reg_psw').val().length > 80) {
			$('#reg_btn').prop('disabled', true);
			$('#reg_pswErr').html('<i class="far fa-times-circle err" title="Password can not have more than 80 characters!"></i>');
			ispr($(this));
		} else {
			$('#reg_pswErr').html('');
			if ($('#reg_username').val().length >= 1 && $('#fullname').val().length >= 1 && 
			$('#reg_cnfpsw').val().length >= 8 && $('#reg_email').val().length >= 8)
				$('#reg_btn').prop('disabled', false);
			isb($(this));
		}
	});
	$('#reg_cnfpsw').on('keyup', function () {
		if ($('#reg_psw').val() != $('#reg_cnfpsw').val()) {
			$('#reg_btn').prop('disabled', true);
			$('#reg_cnfpswErr').html('<i class="far fa-times-circle err" title="Confirmed password does not match the original password!"></i>');
			ispr($(this));
		} else {
			$('#reg_cnfpswErr').html('');
			if ($('#reg_username').val().length >= 1 && $('#reg_psw').val().length >= 8 && 
			$('#fullname').val().length >= 1 && $('#reg_email').val().length >= 8)
				$('#reg_btn').prop('disabled', false);
			isb($(this));
		}
	});
	$('#reg_email').on('keyup', function () {
		if ($('#reg_email').val().length < 8) {
			$('#reg_btn').prop('disabled', true);
			$('#emailErr').html('<i class="far fa-times-circle err" title="Email can not have less than 8 characters!"></i>');
			ispr($(this));
		} else if ($('#reg_email').val().length > 40) {
			$('#reg_btn').prop('disabled', true);
			$('#emailErr').html('<i class="far fa-times-circle err" title="Email can not have more than 40 characters!"></i>');
			ispr($(this));
		} else {
			$('#emailErr').html('');
			$('#reg_email').width(input_org_width);
			if ($('#reg_username').val().length >= 1 && $('#reg_psw').val().length >= 8 && 
			$('#reg_cnfpsw').val().length >= 8 && $('#fullname').val().length >= 1)
				$('#reg_btn').prop('disabled', false);
			isb($(this));
		}
	});
	//validate forms
	$('#login_form').on('submit', function (e) {
		if ($('#username').val().length < 1 || $('#username').val().length > 30 || $('#password').val().length < 8 || $('#password').val().length > 80) {
			$('#fullname').focus();
			return false;
		} else {
			e.preventDefault();
			$('#login_btn').prop('disabled', true);
			$('#login_btn').html('Authenticating <i class="fa fa-spinner fa-spin"></i>');
			$.ajax({
				type: "POST",
				url : "http://localhost/buddyBonds_backup/scripts/login.php",
				data: {
					username: $('#username').val(),
					password: $('#password').val()
				}
			}).done(function (result) {
				$('#logout_alert_info').hide();
				if(result != "0") {
					$('#login_result').html(result);
					$('#login_btn').html('Log In');
					$('#login_btn').prop('disabled', false);
				}
				else if(result == "0") window.location.href = "http://localhost/buddyBonds_backup/home.php";
			});
		}
	});
	$('#reg_form').on('submit', function (e) {
		if ($('#fullname').val().length < 1 || $('#fullname').val().length > 30 ||
			$('#reg_username').val().length < 1 || $('#reg_username').val().length > 30 ||
			$('#reg_psw').val().length < 8 || $('#reg_psw').val().length > 80 ||
			$('#reg_psw').val() != $('#reg_cnfpsw').val() || $('#reg_email').val().length < 8 || $('#reg_email').val().length > 40) 
		{
			$('#fullname').focus();
			return false;
		} else {
			e.preventDefault();
			$('#reg_btn').prop('disabled', true);
			$('#reg_btn').html('Signing You Up <i class="fa fa-spinner fa-spin"></i>');
			$.ajax({
				type: "POST",
				url: "http://localhost/buddyBonds_backup/scripts/register.php",
				data: {
					fullname: $("#fullname").val(),
					reg_username: $("#reg_username").val(),
					reg_psw: $("#reg_psw").val(),
					reg_cnfpsw: $("#reg_cnfpsw").val(),
					reg_email: $("#reg_email").val()
				}
			}).done(function (result) {
				$('#logout_alert_info').hide();
				$("#reg_result").html(result);
				$('#reg_btn').html('Sign Up');
				$('#reg_btn').prop('disabled', false);
			});
		}
	});

	//log change datetime: 17/12/2019 12:38 PM

	//placeholder styling
	$('input').focus(function () {
		$(this).addClass("placeholder_pseudo_class");
	}).blur(function () {
		$(this).removeClass("placeholder_pseudo_class");
	});

	//input shift problem resolution
	function ispr(its) {
		if (input_org_width - $(its).width() < 19)
			$(its).width($(its).width() - 19 + "px");
	}
	//input shift back
	function isb(its) {
		$(its).width(input_org_width);
	}
});