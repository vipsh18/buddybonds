<?php
session_start();
if (isset($_SESSION['id']) && isset($_SESSION['username'])) {
	header("Location:http://localhost/buddyBonds_backup/home.php");
	exit();
}
$unErr = $arErr = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$un = test_input($_POST['un_input']);
	//check if already requested password in the last 24 hours
	$ciarp = $conn->prepare("SELECT time FROM fp_links WHERE username=? ORDER BY fp_id DESC LIMIT 1");
	$ciarp->bindParam(1, $un, PDO::PARAM_STR);
	$ciarp->execute();
	if ($ciarp->rowCount() > 0) {
		$ciarpw = $ciarp->fetch(PDO::FETCH_ASSOC);
		if (time() - strtotime($ciarpw['time']) <= 21600) $arErr = "You have already requested the email. If you have not received any email from <b>connect@buddybonds.com</b> yet, then check your spam folder too or try again after 6 hours. I appreciate your help.";
	}
	$ciarp = null;
	if (empty($un)) {
		$unErr = "Username cannot be empty!!";
	} else if (preg_match("/[^a-z0-9_]/", $un)) {
		$unErr = "Username cannot contain invalid characters!";
	} else {
		$unErr = "";
		$un = test_input($un);
	}
	if ($conn) {
		$result = $conn->prepare("SELECT id,username,email FROM users WHERE username=?");
		$result->bindParam(1, $un, PDO::PARAM_STR);
		$result->execute();
		if ($result->rowCount() == 1) {
			$row = $result->fetch(PDO::FETCH_ASSOC);
			$user_id = $row['id'];
			$fp_key = md5(microtime() . rand());
		} else $unErr = "This username isn't even signed up with us!";
	} else die("Unable to connect to the database!");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
	<meta name="referrer" content="origin-when-crossorigin" />
	<meta name="author" content="Vipul Sharma" />
	<meta name="description" content="buddyBonds web application.Explore page.View trending posts according to your choice.Connect and share.Social media website to make buddies,chat,upload pictures,videos and expanding your business." />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<title>Forgot Password &#8226; buddyBonds</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<link rel="icon" href="images/color-star-3-72-217610.png" type="image/x-icon">
	<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="css/loader.css">
	<link rel="stylesheet" href="css/frgt_password.css">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lobster+Two|Open+Sans">
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

<body class="text-center">
	<a href="buddyBonds.php" id="bb_link" class="text-dark">buddyBonds</a><br>
	<a href="buddyBonds.php" id="lr_link">Login / Register Instead</a>
	<?php
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		if ((!$unErr) && (!$arErr)) {
			//insert into fp table
			$iifp = $conn->prepare("INSERT INTO fp_links(fp_link,user_id,username,time) VALUES(?,?,?,NOW())");
			$iifp->bindParam(1, $fp_key, PDO::PARAM_STR);
			$iifp->bindParam(2, $user_id, PDO::PARAM_INT);
			$iifp->bindParam(3, $un, PDO::PARAM_STR);
			$iifp->execute();
			$from = "connect@buddybonds.com";
			$to = $row['email'];
			$subject = "buddyBonds Password Reset Link";
			$msg = 'Click the link given below to reset your buddyBonds account password
            http://localhost/buddyBonds_backup/reset_password.php?fp=' . $fp_key . '&mailto=' . $to . '
            Note : Please keep in mind that this link expires within 24 hours of requesting the email. After that time period it will no longer be useful to reset the password.
            If this link is already expired, click http://localhost/buddyBonds_backup/frgt_password.php to receive another email from buddyBonds.';
			$headers = "From: " . $from . "\n" . "Reply To: " . $from;
			if (mail($to, $subject, $msg, $headers)) echo '<div class="text-center text-success msg">An email was sent to the registered email address with this username. Please be patient while the mail is being delivered.</div>';
			else echo '<div class="text-center text-danger msg">Email could not be sent to the associated email address! Please try again!</div>';
		} else echo '<div class="msg text-danger">' . $arErr . '</div>';
	}
	?>
	<div class="card">
		<div class="card-header"><b>Forgot Your Password?</b></div>
		<span id="hdr_support">Just enter your username below and the email account associated with that username will receive a link to reset the password.</span>
		<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="frgt_form">
			<input type="text" class="form-control" id="un_input" name="un_input" placeholder="Enter Your Username" required maxlength="30" minlength="1" title="Enter your username here">
			<span id="unErr" class="error"><?php echo $unErr; ?></span><br>
			<button type="submit" class="btn btn-success" id="frgt_btn" disabled>Receive email</button>
		</form>
		<div style="font-size: 14px; margin-top: 5px;">Still Having Login Problems?<br> Mail them at <a href="mailto:connect@buddybonds.com">connect@buddybonds.com</a> and we'll get back to you asap.</div>
	</div>
	<!-- scripts start !-->
	<script src="http://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
	<script>
		$(function() {
			$('#frgt_form').on('submit', function() {
				if (($('#un_input').val().length < 1) || ($('#un_input').val().length > 30)) {
					$('#unErr').html('<i class="far fa-times-circle" title="Username should contain 1-30 characters!"></i>');
					return false;
				} else {
					$('#frgt_btn').prop('disabled', true);
					$('#frgt_btn').val("Sending mail...");
					return true;
				}
			});
		});
	</script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>

</html>