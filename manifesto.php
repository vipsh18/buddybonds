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
	<meta name="description" content="buddyBonds web application.Manifesto page for buddyBonds.com.What is buddyBonds? History and developement.Connect and share.Social media website to make buddies,chat,upload pictures,videos and expanding your business."/>
	<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
	<title>Manifesto</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<link rel="icon" href="images/color-star-3-72-217610.png" type="image/x-icon">
	<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="css/manifesto.css">
	<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Macondo|Arvo|Berkshire+Swash|Open+Sans|Tangerine|Lobster+Two|Roboto">
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
				<a class="nav-link" href="" style="color:lavender"><b>MANIFESTO</b></a>
			</li>
			<li class="nav-item mx-auto">
				<a class="nav-link text-light" href="dev.php"><b>DEVELOPMENT</b></a>
			</li>
			<li class="nav-item mx-auto">
				<a class="nav-link text-light" href="cookie_policy.php"><b>COOKIE POLICY</b></a>
			</li>
			<li class="nav-item mx-auto">
				<a class="nav-link text-light" href="join_me.php"><b>JOIN ME</b></a>
			</li>
		</ul>
	</div>
</nav>
<div id="manifesto">
	<div id="header">
		<div id="mh" class="text-light">THE MANIFESTO</div><div id="mh1" class="text-light">Why to choose buddyBonds?</div>
	</div>
	<div class="container">
		<div class="text-center" id="pmsg"><b>PEOPLE SEEK CONNECTIONS</b></div>
			<div class="msg">Of course! In today's digital world where the number of real connections between people are reduced to minimum there needs to be some way to keep them connected. That is the quality of social networks.<br>There have been many social networks in the past and will be many in the future to come and as time flies these networks become more and more confusing and bigger. So the need of the hour was a <b>simple</b>,<b> secure</b> and a not-so confusing network where people could communicate and share their activities or photos just with a touch.<br>And that is when I thought of <b>buddyBonds</b>.<br>As the name suggests this network believes in building bonds, building connections and not only connections, but <b>real connections</b>.<br><div class="text-center" class="lmsg">You just have to pass this thought on to your buddies.<br>Help me in making buddyBonds a successful mission and become a part of this journey.</div>
			</div>
		<div class="text-center" id="amsg"><b>ABOUT buddyBonds</b></div>
		<div class="msg">
				<span class="text-muted"><b>1.Security</b><br></span>
				The first few questions that comes to the mind of the users whenever something new comes up are "Is it secure"? And "Is it okay if I tell them my email id?".<br><div class="lmsg">The answer is definitely a <b>YES</b>.</div>I do everything to make sure that the website and its data is <b>up to date</b> and <b>secure</b>.The account security of my users is my number one priority and I will make sure that you feel more secure than ever.The chats between you and your buddies are secured with <b> encryption</b>,so that no other person can read your private conversation.<br>I will be storing <b>Cookies</b> for buddyBonds on the user's computer. What are cookies??<a href="cookie_policy.php"><b> Click here to know more.</b></a>
				<div class="text-muted hd"><b>2.Features</b></div>
				Features are what draw users to a website,so I have tried to give users a lot of <b>exciting features</b> like <b>real-time chatting</b>,<b >creating</b> and <b>exploring</b> posts,making <b>buddies</b> and using <b>hashtags</b>.<br>On buddyBonds, it is all about the bonds you create with your buddies. You shower some love on their posts, your bond gets stronger. You chat with them, your bond gets stronger. And this is what made me change the like button that earlier used to show a thumbs-up 👍 to a <b>"Bond With"</b> button. Now whenever you actually like a picture, you click the "Bond With" button and that is how bonds get stronger. <br>On buddyBonds the users make buddies for which you need to send a <b>buddy request</b> to the other user and he needs to accept it. Your profile can not be viewed by any other user until and unless he/she is your buddy on buddyBonds.The same applies to messaging. Only your buddies can message you.<br>Now imagine if you want to expand your <b>business</b> or say if you want to create a <b>fan profile</b> for some celebrity you can do easily with the <b>Open Profile</b> option.You can easily promote any post from your open profile globally by using another buddyBonds feature, which is the hashtag, with which most of us might be really familiar with. Hashtags allow in grouping the content, with similar posts getting grouped together on the basis of hashtags.View this <a href="javascript:void(0)" id="take_to_hl"><b>list of hashtags</b></a> to select your favourite hashtags and use them in your posts. Adding to all these features there is an explore page which lets you view the latest trending photos and videos. The explore page shows you these posts according to your own preferences. So...enjoy scrolling down!!
				<div class="text-muted hd"><b>3.What do I need?</b></div>
				Nice question there! Lets clear it up! I just need you to have a <b>username</b> and a <b>password</b>. That's all! Other than that <b>enabling JavaScript</b> and <b>cookies</b> will be the best you can do for me! Cheers!! 
			<div class="lmsg">So at the end,I just advice you to <b>maintain ethics</b> and rest be assured.</div>
			<div class="lmsg" style="float:right">Yours<br>Vipul Sharma</div>
		</div>
	</div>
	<div id="made_msg" class="text-center text-primary">Made with <span class="text-danger">&#10084;</span> in INDIA</div>
</div>
<footer class="text-light text-center">
	<div id="wmsg"><b>Email me your suggestions:</b></div>
	<div class="text-muted" id="dmsg">connect@buddybonds.com</div>
	<div id="note"><b>Cookies</b> are used to give you the most amazing experience ever!<br>You may have a look at the <a href="cookie_policy.php" class="text-light"><b>Cookie Policy</b></a></div>
</footer>
<script src="http://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
<script>
$(function() {
	$('#take_to_hl').on('click',function() {
	    var choice = confirm("You need to login to view the list of hashtags!");
	    if(choice == true) location.href="http://localhost/buddyBonds_backup/";
	});
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
