<?php 
	session_start();
	if(isset($_COOKIE['id'])&&isset($_COOKIE['username'])&&isset($_COOKIE['tz'])) {
		$cookie_id=$_COOKIE['id'];
		$cookie_username=$_COOKIE['username'];
		$_SESSION['id']=$cookie_id;
		$_SESSION['username']=$cookie_username;
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-105985024-2"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'UA-105985024-2');
    </script>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
	<meta name="referrer" content="origin-when-crossorigin"/>
	<meta name="author" content="Vipul Sharma"/>
	<meta name="description" content="buddyBonds web application.Join us page for buddyBonds.com.Help us make buddyBonds a better place.Work with us.Connect and share.Social media website to make buddies,chat,upload pictures,videos and expanding your business."/>
	<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
	<title>Join Me</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<link rel="icon" href="images/color-star-3-72-217610.png" type="image/x-icon">
	<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="css/cookie_policy.css">
	<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Macondo|Arvo|Open+Sans|Lobster+Two">
	<!--[if IE]>
      <link href="/css/bootstrap-ie9.css" rel="stylesheet">
      <script src="https://cdn.jsdelivr.net/g/html5shiv@3.7.3"></script>
	<![endif]-->
	<!--[if lt IE 9]>
	  <link href="/css/bootstrap-ie8.css" rel="stylesheet">
	<![endif]-->
	<!--[if lte IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
</head>
<body>
<nav class="navbar navbar-expand-lg sticky-top">
	<a class="navbar-brand text-light" href="buddyBonds.php" id="navbar_brand"><img src="images/color-star-3-48-217610.png"><span class="text-light" id="sc_nav"> buddyBonds</span></a>
	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navnav"><span class="navbar-toggler-icon text-light">+</span></button>
	<div class="collapse navbar-collapse" id="navnav">
		<ul class="navbar-nav ml-auto">
			<li class="nav-item mx-auto">
				<a class="nav-link text-light" href="manifesto.php"><b>MANIFESTO</b></a>
			</li>
			<li class="nav-item mx-auto">
				<a class="nav-link text-light" href="dev.php"><b>DEVELOPMENT</b></a>
			</li>
			<li class="nav-item mx-auto">
				<a class="nav-link text-light" href="cookie_policy.php"><b>COOKIE POLICY</b></a>
			</li>
			<li class="nav-item mx-auto">
				<a class="nav-link" href="" style="color:lavender"><b>JOIN ME</b></a>
			</li>
		</ul>
	</div>
</nav>
<div id="main">
	<header>
		<div id="hdr" class="text-light">JOIN ME</div>
		<div id="mhdr" class="text-light text-center">How you can help me?</div>
	</header>
	<div class="container">
		<div class="msg">
			Have some programming experience??<br>Want to join me in an amazing coding environment??<br>Or just want to give a review about buddyBonds??<br> If yes , join hands with me and lets together make buddyBonds more better and more secure.<br>
			Connect with me using gmail : <b><a href="mailto:connect@buddybonds.com">connect@buddybonds.com</a></b>
		</div>
		<div id="mm" class="text-center text-primary">Made with <span class="text-danger">&#10084;</span> in INDIA</div>
	</div>
</div>
<footer class="text-light text-center">
	<div id="wmsg"><b>Email me your suggestions:</b></div>
	<div class="text-muted" id="dmsg">connect@buddybonds.com</div>
	<div id="note"><b>Cookies</b> are used to give you the most amazing experience ever!<br>You may have a look at the <a href="cookie_policy.php" class="text-light"><b>Cookie Policy</b></a></div>
</footer>
<script src="http://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
<script>
$(function() {
	//decrease padding of navbar on scroll
	$(window).scroll(function() {
		var x=window.pageYOffset;
		if(x>10) {
			$('.navbar').css('padding','3px');
			$('#sc_nav').css('font-size','26px');
		} else {
			$('.navbar').css('padding','10px');
			$('#sc_nav').css('font-size','28px');
		}
	});
});
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>