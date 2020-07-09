<?php
	session_start();
	$page_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	if(strpos($page_link, "www.") !== false) {
	    header("Location:http://localhost/buddyBonds_backup");
	    exit();
	}
	if(isset($_SESSION['id']) && isset($_SESSION['username']) && isset($_SESSION['tz'])) {
		header("Location:http://localhost/buddyBonds_backup/home.php");
		exit();
	} else {
		if(isset($_COOKIE['id']) && isset($_COOKIE['username']) && isset($_COOKIE['tz'])) {
			$cookie_id = $_COOKIE['id'];
			$cookie_username = $_COOKIE['username'];
			$_SESSION['id'] = $cookie_id;
			$_SESSION['username'] = $cookie_username;
			header("Location:http://localhost/buddyBonds_backup/home.php");
			exit();
		}
	}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
	<meta name="referrer" content="origin-when-crossorigin" />
	<meta name="author" content="Vipul Sharma" />
	<meta name="author" content="Hrithik Chauhan" />
	<meta name="description"
		content="buddyBonds is a web application designed to help people connect,share photos and videos.Join this social media website to make buddies,chat,upload pictures,videos and for expanding your business." />
	<meta http-equiv="x-ua-compatible" content="IE=edge" />
	<title>buddyBonds &#8226; New Way For Connecting , Sharing And Uploading.</title>
	<script>
		if ("Intl" in window) {
			var tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
			var d = new Date();
			d.setTime(d.getTime() + (1000 * 60 * 60 * 24 * 30));
			var expires = "expires=" + d.toUTCString();
			document.cookie = "tz=" + tz + ";" + expires + ";path=/";
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function () {
				if (this.readyState == 4 && this.status == 200) console.log("Ready To Go!");
			};
			xhttp.open("GET", "http://localhost/buddyBonds_backup/scripts/settz.php?tz=" + tz, true);
			xhttp.send();
		} else {
			var tz = new Date().getTimezoneOffset();
			tz = tz == 0 ? 0 : -tz;
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function () {
				if (this.readyState == 4 && this.status == 200) console.log("Ready To Go!");
			};
			xhttp.open("GET", "http://localhost/buddyBonds_backup/scripts/settz_ob.php?tz=" + tz, true);
			xhttp.send();
		}
	</script>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
		integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<!--[if gte IE 9]><!-->
	<script src="http://code.jquery.com/jquery-2.2.4.min.js"
		integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
	<!--<![endif]-->
	<!--[if IE 9]>
      		<link href="css/bootstrap-ie9.min.css" rel="stylesheet">
   	 <![endif]-->
	<!--[if lte IE 8]>
      	<link href="css/bootstrap-ie8.min.css" rel="stylesheet">
      	<script src="js/html5shiv@3.7.3.js"></script>
      	<script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ="crossorigin="anonymous"></script>
    <![endif]-->
	<link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">
	<link rel="icon" href="images/color-star-3-72-217610.png" type="image/x-icon">
	<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="css/index.css">
	<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Lobster+Two|Open+Sans|Roboto|Fira+Code">
</head>

<body>
	<section id="homepage">
		<div class="row" id="main_row">
			<div class="col-lg-6" id="ss_col">
				<div id="ss_carousel" class="carousel slide" data-ride="carousel">
					<div class="carousel-inner text-center">
						<div class="carousel-item active"><img src="images/ss_profile_aku27.png" alt="Profile page" class=" img-fluid rounded"></div>
						<div class="carousel-item"><img src="images/ss_home_bB.png" alt="Home page" class="img-fluid"></div>
						<div class="carousel-item"><img src="images/ss_messages.png" alt="Messages page" class="img-fluid"></div>
						<div class="carousel-item"><img src="images/ss_ntfs_bB.png" alt="Notifications page" class="img-fluid"></div>
					</div>
					<!-- <a class="carousel-control-prev" href="#ss_carousel" data-slide="prev"><span
						class="carousel-control-prev-icon"></span></a>
					<a class="carousel-control-next" href="#ss_carousel" data-slide="next"><span
						class="carousel-control-next-icon"></span></a> -->
				</div>
			</div>
			<div class="col-lg-6 text-center" id="form_cards">
				<div class="bbModal">
					<?php
				 		if(isset($_REQUEST['logout'])) {
							if(time() - $_REQUEST['logout'] <= 1800)
								echo '<div class="alert alert-dismissable text-center alert-info" id="logout_alert_info"><span class="text-success">You have been logged out successfully. See you soon !</span></div>';
							else
								echo '<div class="alert alert-dismissable text-center alert-info" id="logout_alert_info"><span class="text-danger">Come on ! You have got to login first to logout.</span></div>';
						}
					?>
					<h1 id="sc_hdr" class="text-center">buddyBonds</h1>
					<div class="text-center" style="margin-bottom:25px;color: grey">See how interesting this world can be.</div>
					<div id="login_card">
						<form method="POST" action="login.php" id="login_form">
							<div id="login_result"></div>
							<div class="form-group">
								<input type="text" name="username" id="username" required maxlength="30" minlength="1"
									title="Username" placeholder="Username" alt="Username"><span id="unErr"></span>
							</div>
							<div class="form-group">
								<input type="password" name="password" id="password" required maxlength="80"
									minlength="8" title="Enter your password here!" placeholder="Password"
									alt="Password"><span id="pswErr"></span>
							</div>
							<div><button type="submit" class="btn btn-sm btn-bb" id="login_btn" disabled>Log In</button>
							</div>
						</form>
						<div id="fp_div"><a href="frgt_password.php">Forgot Password?</a></div>
						<br>
						<a href="#" class="text-center bg_grey_btn" id="ltrfl">Create a New Account</a>
						<div id="getTheApp">
							<p class="fr-primary" style="font-family:'Open Sans', serif;">Get the buddyBonds app.<br></p>
							<a href="javascript:void(0)"><img src="images/appStore.png"
									alt="Download App from App Store" width="150px" /></a><br>
							<a href="javascript:void(0)"><img src="images/playStore.png"
									alt="Get the App from Play Store" width="150px" /></a>
						</div>
					</div>

					<div id="reg_card">
						<form method="post" action="register.php" id="reg_form">
							<div id="reg_result"></div>
							<div class="form-group">
								<input type="text" name="fullname" id="fullname" required maxlength="30" minlength="1"
									placeholder="Full Name" title="Enter your full name here" alt="Fullname"><span
									id="nameErr"></span>
							</div>
							<div class="form-group">
								<input type="text" name="reg_username" id="reg_username" required maxlength="30"
									minlength="1" placeholder="Username" title="Enter a new username for yourself!"
									alt="New Username"><img src="http://localhost/buddyBonds_backup/images/spnr.gif"
									id="spnr"><span id="usernamecheck"></span><span id="reg_unErr"></span>
							</div>
							<div class="form-group">
								<input type="password" name="reg_psw" id="reg_psw" required maxlength="80" minlength="8"
									placeholder="Password" title="Enter a new password for yourself"
									alt="New Password"><span id="reg_pswErr"></span>
							</div>
							<div class="form-group">
								<input type="password" name="reg_cnfpsw" id="reg_cnfpsw" required maxlength="80"
									minlength="8" placeholder="Password Confirmation" title="Confirm your password"
									alt="Repeat Password"><span id="reg_cnfpswErr"></span>
							</div>
							<div class="form-group">
								<input type="email" name="reg_email" id="reg_email" required maxlength="40"
									minlength="8" placeholder="Email Address" title="Enter your email address"
									alt="Enter your email address" pattern="^\S+@\S+[\.][0-9a-z]+$"><span
									id="reg_emailErr"></span><span id="emailErr"></span>
							</div>
							<div class="text-center"><button type="submit" class="btn btn-bb" id="reg_btn" disabled>Sign Up</button></div>
							<div class="form-group text-center">
								<p id="suf_message">By signing up, you agree to our <a href=""><b>Terms</b></a> , <a href=""><b>Data Policy</b></a> and <a href="cookie_policy.php"><b>Cookies Policy</b></a>.</p>
							</div>
						</form>

						<div class="text-center bg_grey_btn">Already have an account?<a href="#" id="rtlfl"> Login Here</a></div>
					</div>
				</div>
				<footer>
					<ul class="list-inline footer_ul">
						<li class="list-inline-item"><a href="manifesto.php"><b>MANIFESTO </b></a></li>
						<li class="list-inline-item"><a href="dev.php"><b>DEVELOPMENT </b></a></li>
						<li class="list-inline-item"><a href="cookie_policy.php"><b>COOKIE POLICY </b></a></li>
						<li class="list-inline-item"><a href="join_me.php"><b>JOIN OUR TEAM </b></a></li>
						<li class="list-inline-item"><a href="mailto:connect@buddybonds.com"><b>CONTACT US</b></a></li>
						<li class="list-inline-item copyright"><b>&#169; 2020 buddyBonds</b></li>
					</ul>
				</footer>
			</div>
		</div>
	</section>
	<!-- scripts start !-->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"
		integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous">
	</script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"
		integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous">
	</script>
	<!--[if IE 9]>
    	<script src="js/bootstrap-ie9.min.js"></script>
	<![endif]-->
	<!--[if lte IE 8]>
    	<script src="js/bootstrap-ie8.min.js"></script>
    	<script src="js/bootstrap-4.1.3.js"></script>
	<![endif]-->
	<script src="js/index.js"></script>
	<script>
		$(function () {});
		var sc_vsts = localStorage.getItem('sc_vsts');
		if (sc_vsts > 1) {
			$('#sc_msg').fadeOut();
			$('#next1').fadeOut();
			$('#msg1').css('display', 'block');
			$('#login_card').css('display', 'block');
			$('header').css('padding-top', '50px');
			$('#sc_hdr').css('padding-bottom', '10px');
		}
	</script>
</body>

</html>