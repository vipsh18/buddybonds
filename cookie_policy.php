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
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
	<meta name="referrer" content="origin-when-crossorigin"/>
	<meta name="author" content="Vipul Sharma"/>
	<meta name="description" content="buddyBonds web application.Cookie policy page for buddyBonds.com.Standard for using cookies with user information in them.Connect and share.Social media website to make buddies,chat,upload pictures,videos and expanding your business."/>
	<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
	<title>Cookie Policy</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<link rel="icon" href="images/color-star-3-72-217610.png" type="image/x-icon">
	<link rel="icon" href="images/color-star-3-57-217610.png" type="image/x-icon">
	<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="css/cookie_policy.css">
	<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Macondo|Arvo|Berkshire+Swash|Open+Sans|Tangerine|Lobster+Two">
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
				<a class="nav-link" href="" style="color:lavender"><b>COOKIE POLICY</b></a>
			</li>
			<li class="nav-item mx-auto">
				<a class="nav-link text-light" href="join_me.php"><b>JOIN ME</b></a>
			</li>
		</ul>
	</div>
</nav>
<div id="main">
	<header>
		<div id="hdr" class="text-light">COOKIE POLICY</div>
		<div id="mhdr" class="text-light text-center">How and what is stored?</div>
	</header>
	<div class="container">
	    <div class="msg">Don't worry, I am not gonna show you the biggest document you have ever seen in the name of Cookie Policy like others. Nobody ever reads that completely. I wonder 🤔 if the ones who write them do.Here's a short one just for you!</div>
		<div class="chdr text-center">WHAT IS A COOKIE?</div>
			<div class="msg">A <b>cookie</b> is a <b>small piece of data</b> stored on the user's computer or the server computer by a particular website when it is loaded in the browser. Cookies are usually used as a mechanism to remember stateful information like the contents of a user's shopping cart, login credentials of a user or user preferences either for a single visit that is a session cookie or a persistent cookie that stays with the user even when the browser is closed.<br>buddyBonds uses cookies in ways like <b>authenticating your account</b> and it's <b>precious data</b>.They help count the number of visitors to a page, keeping track of how many pages were visited in a day and stuff like that and last but not the least, give you a satisfying experience. Sometimes <a href="https://top.quora.com/What-is-the-meaning-of-a-third-party-website">third parties</a> also store cookies to serve content and provide advertising and analytics services on the website.<br>buddyBonds uses Google for providing analytics on the website. Basically just a bunch of cookies is stored on the user end and it helps me know how many visitors I have on my website everyday, which pages are liked the most by the users, what areas should I improve, where are the users from and similar things. In the end all of this leads to a promising and secure environment that is buddyBonds.</div>
		<div class="chdr text-center">WHAT INFORMATION IS STORED?</div>
			<div class="msg"><span class="text-muted"><b>Security</b></span><br>These cookies are used to prevent security risks. Such cookies are used to store information about you so that no other person can access your account illegally.<br>The most basic and I can say important use of a cookie for buddyBonds is recognizing and authenticating that a user is who he/she claims to be!<br><span class="text-muted"><b>Functionality</b></span><br>Cookies are used for a lot of functionality in buddyBonds. However there are some other ways to do that too, but a cookie also helps in keeping a user logged in his/her account. Otherwise just imagine how hectic and boring would that be for you to login everytime you open your browser and just want to see your favourite posts right away or want to message your buddy instantly. If your browser has blocked cookies that will greatly affect the availaibility of buddyBonds services.</div>
			<div class="text-center" id="atc">By using buddyBonds I assume you agree to our use of cookies.</div>
	</div>
	<div id="made_msg" class="text-center text-primary">Made with <span class="text-danger">&#10084;</span> in INDIA</div>
</div>
<footer class="text-light text-center">
	<div id="wmsg"><b>Email me your suggestions:</b></div>
	<div class="text-muted" id="dmsg">connect@buddybonds.com</div>
	<div id="note"><b>Cookies</b> are used to give you the most amazing experience ever!</div>
</footer>
<script src="http://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
<script>
$(function() {
	//decrease padding of navbar on scroll
	$(window).scroll(function() {
		var x = window.pageYOffset;
		if(x > 10) {
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
