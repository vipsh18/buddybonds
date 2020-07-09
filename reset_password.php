<?php
session_start();
if(isset($_SESSION['id'])&&isset($_SESSION['username'])) {
	header("Location:http://localhost/buddyBonds_backup/home.php");
	exit();
}
if(!$_GET['fp']) {
	header("Location:http://localhost/buddyBonds_backup/frgt_password.php");
	exit();
}
function test_input($data) {
	$data=trim($data);
	$data=stripslashes($data);
	$data=htmlspecialchars($data);
	return $data;
}
$fp = test_input($_GET['fp']);
$config=parse_ini_file("../varcsc.ini"); 
$db_server=$config['db_server'];
$db_user=$config['db_user'];
$db_pass=$config['db_pass'];
$db_name=$config['db_name'];
try {
	$conn=new PDO("mysql:host=$db_server;dbname=$db_name",$db_user,$db_pass);
	//set PDO error mode to exception
	$conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
	echo 'Connection failed:'.$e->getMessage();
}
//check if fp exists and all that
$cffp = $conn->prepare("SELECT user_id,expired,time FROM fp_links WHERE fp_link = ?");
$cffp->bindParam(1,$fp,PDO::PARAM_STR);
$cffp->execute();
$expireErr = "";
if($cffp->rowCount()<=0) $expireErr="Sorry, this url does not exist. That's all I know!<br><a href='frgt_password.php'>Request another email here.</a>";
else {
    $cffpr = $cffp->fetch(PDO::FETCH_ASSOC);
    if($cffpr['expired']==1) $expireErr = "Sorry, this url has expired. That's all I know!<br><a href='frgt_password.php'>Request another email here.</a>";
    $user_id = $cffpr['user_id'];
    $mailto=test_input($_GET['mailto']);
    //verify receipient
    $vr = $conn->prepare("SELECT id,username FROM users WHERE email=?");
    $vr->bindParam(1,$mailto,PDO::PARAM_STR);
    $vr->execute();
    $vrr = $vr->fetch(PDO::FETCH_ASSOC);
    if($user_id != $vrr['id']) $expireErr = "This url is not meant for you. That's all I know!<br><a href='frgt_password.php'>Request another email here.</a>";
    if(time()-strtotime($cffpr['time'])>=86400) {
        //expire link
        $expired = 1;
        $el = $conn->prepare("UPDATE fp_links SET expired = ? WHERE fp_link = ?");
        $el->bindParam(1,$expired,PDO::PARAM_INT);
        $el->bindParam(2,$fp,PDO::PARAM_STR);
        $el->execute();
        $expireErr = "Sorry, this url has expired. That's all I know!<br><a href='frgt_password.php'>Request another email here.</a>";
    }
}
if(!$expireErr) {
    $pswErr = $psw_cnfErr = "";
    $un = $vrr['username'];
    if($_SERVER['REQUEST_METHOD']=='POST') {
        $new_psw = test_input($_POST['new_psw']);
        $new_psw_cnf = test_input($_POST['new_psw_cnf']);
        if(empty($new_psw)) {
            $pswErr = "New Password cannot be empty!";
        } else {
            $pswErr = "";
		    $new_psw = hash('sha256',$new_psw);
		    $new_psw = base64_encode($new_psw);
        }
        if(empty($new_psw_cnf)) {
            $psw_cnfErr = "Confirmed password cannot be empty!";
        } else {
            $psw_cnfErr = "";
            $new_psw_cnf = hash('sha256',$new_psw_cnf);
            $new_psw_cnf = base64_encode($new_psw_cnf);
        }
        if($new_psw != $new_psw_cnf) $psw_cnfErr = "Both the passwords should match!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
	<meta name="referrer" content="origin-when-crossorigin"/>
	<meta name="author" content="Vipul Sharma"/>
	<meta name="description" content="buddyBonds web application.Reset password page for buddyBonds.com.Change your forgot password here.Connect and share.Social media website to make buddies,chat,upload pictures,videos and expanding your business."/>
	<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
	<title>Reset Password &#8226; buddyBonds</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<link rel="icon" href="images/color-star-3-72-217610.png" type="image/x-icon">
	<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css" integrity="sha384-5sAR7xN1Nv6T6+dT2mhtzEpVJvfS3NScPQTrOxhwjIuvcA67KV2R5Jz6kr4abQsz" crossorigin="anonymous">
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
if(!$expireErr) {
	if($_SERVER['REQUEST_METHOD']=='POST') {
        if((!$pswErr)&&(!$psw_cnfErr)) {
            //reset password 
            $rp=$conn->prepare("UPDATE users SET password=? WHERE id=? AND email=?");
            $rp->bindParam(1,$new_psw,PDO::PARAM_STR);
            $rp->bindParam(2,$user_id,PDO::PARAM_INT);
            $rp->bindParam(3,$mailto,PDO::PARAM_STR);
            //set link expiry to 1
            $expired = 1;
            $sle = $conn->prepare("UPDATE fp_links SET expired = ? WHERE fp_link=?");
            $sle->bindParam(1,$expired,PDO::PARAM_INT);
            $sle->bindParam(2,$fp,PDO::PARAM_STR);
            if(($rp->execute()) && ($sle->execute())) {
                echo '<div class="msg text-success">Cool! Your Password Was Just Reset! <a href="http://localhost/buddyBonds_backup">Login</a> to access your account now.</div>';
            } else {
                echo '<div class="msg text-danger">There seems to be an error in the form. Please try again!</div>';
                ?>
                <div class="card">
                    <div class="card-header"><b>Reset Password</b></div>
                    <span id="hdr_support">Reset Password For <?php if($un) echo $un; ?></span>
                    <form method="post" action="" id="reset_form">
                        <input type="password" class="form-control" id="new_psw" name="new_psw" placeholder="Enter Your New Password" required maxlength="80" minlength="8" title="Password should contain 8-80 characters.">
                        <span id="pswErr" class="error"><?php echo $pswErr; ?></span><br>
                        <input type="password" class="form-control" id="new_psw_cnf" name="new_psw_cnf" placeholder="Confirm Your New Password" required maxlength="80" minlength="8" title="Password should match the new password entered above.">
                        <span id="psw_cnfErr" class="error"><?php echo $psw_cnfErr; ?></span><br>
                        <button type="submit" class="btn btn-success" id="reset_btn">Change Password</button>
                    </form>
                </div>	
                <?php
            }
        }
	} else {
	?>
    <div class="card">
        <div class="card-header"><b>Reset Password</b></div>
        <span id="hdr_support">Reset Password For <?php if($un) echo $un; ?></span>
        <form method="post" action="" id="reset_form">
            <input type="password" class="form-control" id="new_psw" name="new_psw" placeholder="Enter Your New Password" required maxlength="80" minlength="8" title="Password should contain 8-80 characters.">
            <span id="pswErr" class="error"><?php echo $pswErr; ?></span><br>
            <input type="password" class="form-control" id="new_psw_cnf" name="new_psw_cnf" placeholder="Confirm Your New Password" required maxlength="80" minlength="8" title="Password should match the new password entered above.">
            <span id="psw_cnfErr" class="error"><?php echo $psw_cnfErr; ?></span><br>
            <button type="submit" class="btn btn-success" id="reset_btn">Change Password</button>
        </form>
    </div>	
	<?php
	}
} else {
    echo '<div class="error" id="expireErr">'.$expireErr.'</div>';
}
?>
<script src="http://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
<script>
$(function() {
    $('#new_psw').on('blur', function() {
        $('#pswErr').html("<i class='fas fa-times-circle' title='Password should contain 1-80 characters.'></i>");    
    });
    $('#new_psw_cnf').on('blur', function() {
        if($('#new_psw').val() != $('#new_psw_cnf').val()) $('#psw_cnfErr').html("<i class='fas fa-times-circle' title='Confirmed password should match new password.'></i>");    
        else $('#psw_cnfErr').html("");
    });
	$('#reset_form').on('submit',function() {
		if(($('#new_psw').val().length<8)||($('#new_psw').val().length>80)||($('#new_psw_cnf').val().length<8)||($('#new_psw_cnf').val().length>80)||($('#new_psw').val() != $('#new_psw_cnf').val())) {
		    $('#new_psw').focus();
			return false;
		} else {
			var submit=$(this).find(':submit');
			submit.prop('disabled',true);
			submit.val('...sending information!');
			return true;
		}
	});
});
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>