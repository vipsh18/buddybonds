<?php  
include 'varcsc.php';
$uun=base64_decode($_SESSION['username']);
$uid=base64_decode($_SESSION['id']);
//change logout time
$cltq=$conn->prepare("UPDATE users SET logout_time='0001-01-01 00:00:00' WHERE id=?");
$cltq->bindParam(1,$uid,PDO::PARAM_INT);
if(!$cltq->execute()) die('Execution failed:('.$cltq->errno.')'.$cltq->error);
$cltq=null;
function test_input($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}	
date_default_timezone_set($tz);
$result=$conn->prepare("SELECT password,buddypic FROM users WHERE id=?");
$result->bindParam(1,$uid,PDO::PARAM_INT);
$result->execute();
$row=$result->fetch(PDO::FETCH_ASSOC);
$pswErr=$newpswErr=$newpswcnfErr="";
	if($_SERVER['REQUEST_METHOD']=='POST') {
		if(isset($_POST['curr_psw'])&&isset($_POST['new_psw'])&&isset($_POST['newpsw_cnf'])) {
			$password = test_input($_POST['curr_psw']);
			$new_psw = test_input($_POST['new_psw']);
			$newpsw_cnf = test_input($_POST['newpsw_cnf']);
			$newpsw_cnf = hash('sha256',$newpsw_cnf);
			$newpsw_cnf = base64_encode($newpsw_cnf);
			if(empty($_POST['curr_psw'])) {
				$pswErr="Current password cannot be empty!";
			} else {
				$pswErr="";
				$password=hash('sha256',$password);
				$password = base64_encode($password);
			}
			if($password!=$row['password']) {
				$pswErr="Current password is wrong!"; 
			}
			if(empty($new_psw)) {
				$newpswErr="New password cannot be empty!";
			} else {
				$newpswErr="";
				$new_psw = hash('sha256',$new_psw);
				$new_psw = base64_encode($new_psw);
			}
			if(empty($newpsw_cnf)) $newpswcnfErr="Confirmed password cannot be empty!";
			else if($new_psw != $newpsw_cnf) $newpswcnfErr="Both new passwords should match!";
			else $newpswcnfErr="";
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
	<meta name="description" content="buddyBonds web application.Change password page for user.Keep your account safe and secure.Maintain privacy.Connect and share.Social media website to make buddies,chat,upload pictures,videos and expanding your business."/>
	<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
	<title>Change Your Password</title>
	<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css"> 
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<link rel="icon" href="images/color-star-3-72-217610.png" type="image/x-icon">
	<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css" integrity="sha384-5sAR7xN1Nv6T6+dT2mhtzEpVJvfS3NScPQTrOxhwjIuvcA67KV2R5Jz6kr4abQsz" crossorigin="anonymous">
	<link rel="stylesheet" href="css/nav_footer.css">
	<link rel="stylesheet" href="css/chng_password.css">
	<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Lobster+Two|Merienda|Roboto|Open+Sans">
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
<!--navbar for large devices !-->
<nav class="navbar sticky-top" id="nav">
	<ul class="navbar-nav">
		<li class="nav-item">
			<a href="home.php" id="navhd" data-toggle="tooltip" title="Home" class="text-secondary"><i class="fas fa-home nav_large_icons"></i> buddyBonds</a>
			<a href="messages.php" data-toggle="tooltip" title="Messages" class="text-secondary"><i class="fa fa-envelope faa-shake animated nav_large_icons"></i> <span class="nav_large_texts"><b>Messages</b></span> <?php echo '<span id="updated_msgs"></span>'; ?></a>
			<a href="notifications.php" data-toggle="tooltip" title="Notifications" class="text-secondary"><i class="fa fa-bell faa-ring animated nav_large_icons"></i> <span class="nav_large_texts"><b>Notifications</b> </span><?php echo '<span id="updated_ntfs"></span>'; ?></a>
			<a class="text-secondary" href="news.php" data-toggle="tooltip" title="News"><i class="fas fa-newspaper nav_large_icons"></i> <span class="nav_large_texts"><b>News</b></span></a>
		</li>
	</ul>
	<form action="search.php" method="get" class="form-inline" id="search_form">
		<div class="form-group">
			<div class="input-group" id="search_grp">
				<input type="search" required maxlength="30" minlength="1" placeholder="Search..." title="Search..." id="search" name="q" data-toggle="tooltip">
				<div id="spnr"><img src="images/spnr.gif" id="spnr_img"></div>
				<button type="reset" class="btn btn-sm" id="srch_rb"><span id="srch_rbt">X</span></button>
			</div>
		</div>
	</form>
	<ul class="navbar-nav list-inline" id="opts">
        <li class="list-inline-item">
		    <a class="nav-link text-secondary" style="margin-right:0" href="profile.php?un=<?php echo $uun; ?>" data-toggle="tooltip" title="Your Profile" id="nav_profile_link">
		    <?php if(file_exists($row['buddypic'])) echo '<img src="'.$row['buddypic'].'" class="rounded-circle img-fluid" style="width:25px;height:25px;object-fit:cover">';
				else echo '<img src="images/def_buddypic.png" class="rounded-circle img-fluid m_navpic" style="width:25px;height:25px;object-fit:cover">' ?> <b><?php if(strlen($uun) > 10) echo substr($uun, 0, 10)."...";
						else echo $uun; ?></b></a>
		</li>
		<li class="list-inline-item">
			<a href="javascript:void(0)" id="sdnav_obtn" class="text-secondary" style="position: relative; top: 3px"><i class="fas fa-bars nav_large_icons" data-toggle="tooltip" title="Options"></i></a>
			<a href="javascript:void(0)" id="sdnav_cbtn" class="text-secondary" style="position: relative; top: 3px"><i class="fas fa-times nav_large_icons" data-toggle="tooltip" title="Close Options"></i></a>
		</li>
	</ul>
	<div id="optnav" class="text-center">
		<div><a href="settings.php" class="text-secondary ts_nu"><i class="fa fa-wrench faa-wrench animated nav_large_icons"></i> <span class="nav_large_texts"><b>Settings</b></a></div>
		<div><a href="hashtags.php" class="text-secondary ts_nu"><i class="fas fa-hashtag nav_large_icons"></i> <span class="nav_large_texts"><b>List Of Hashtags</b></a></div>
		<div><a href="edit_profile.php" class="text-secondary ts_nu"><i class="fas fa-edit nav_large_icons"></i> <span class="nav_large_texts"><b>Edit profile</b></a></div>
		<div><a href="logout.php" class="text-secondary ts_nu"><i class="fas fa-sign-out-alt nav_large_icons"></i> <span class="nav_large_texts"><b>Log Out</b></a></div>
	</div>
</nav>
<div id="suggestions"></div>
<!-- navbar for small types devices !-->
<div class="d-lg-none">
<nav class="navbar fixed-top" id="m_nav">
	<ul class="navbar-nav">
		<li class="nav-item">
			<a href="javascript:void(0)" id="m_upl_pic"><i class="fas fa-camera fa-2x"></i></a>
		</li>
	</ul>
	<ul class="navbar-nav">
		<li class="nav-item">
			<a href="home.php" id="m_navhd">buddyBonds</a>
		</li>
	</ul>
	<ul class="navbar-nav">
		<li class="nav-item">
			<a href="javascript:void(0)" id="m_sdnav_obtn"><i class="fas fa-bars fa-2x"></i></a>
			<a href="javascript:void(0)" id="m_sdnav_cbtn" style="color:blue"><i class="fas fa-times fa-2x"></i></a>
		</li>
	</ul>
	<div id="m_optnav">
		<div class="text-center"><a href="settings.php"><i class="fa fa-wrench faa-wrench animated"></i> Settings</a></div>
		<div class="text-center"><a href="news.php"><i class="fas fa-newspaper"></i> News</a></div>
		<div class="text-center"><a href="hashtags.php"><i class="fas fa-hashtag"></i> List Of Hashtags</a></div>
		<div class="text-center"><a href="edit_profile.php"><i class="fas fa-edit"></i>Edit profile</a></div>
		<div class="text-center"><a href="logout.php"><i class="fas fa-sign-out-alt"></i>Log Out</a></div>
	</div>
</nav>
<form action="search.php" method="get" id="m_search_form">
	<div class="form-group">
		<div class="input-group">
			<input type="search" required maxlength="30" minlength="1" placeholder="Search..." title="Search" id="m_search" name="q" class="mx-auto">
			<div id="m_spnr"><img src="images/spnr.gif" id="m_spnr_img"></div>
			<button type="reset" class="btn btn-sm" id="m_srch_rb"><span id="m_srch_rbt">X</span></button>
		</div>
	<div id="m_suggestions" class="mx-auto"></div>
	</div>
</form>
</div>
<!--navbar for small devices end!-->
<?php 
	if($_SERVER['REQUEST_METHOD']=='POST') {
		if((!$pswErr)&&(!$newpswErr)&&(!$newpswcnfErr)) {
			$sql=$conn->prepare("UPDATE users SET password=? WHERE username=? AND password=?");
			$sql->bindParam(1,$new_psw,PDO::PARAM_STR);
			$sql->bindParam(2,$uun,PDO::PARAM_STR);
			$sql->bindParam(3,$password,PDO::PARAM_STR);
			if($sql->execute()) echo '<div class="text-success text-center" id="chng_success"><b>&#10084;COOL!!<br>Your password has been changed!!</b></div>';
			else echo '<div class="text-center text-danger" class="chng_failure">Sorry, your password could not be changed! Please try again !</div>';
		} else {
			echo '<div class="text-danger text-center" class="chng_failure"><b>Hey,it looks like you made some mistake! Please try again after correcting the error!</b></div>';
		}
		$sql = null;
	}
?>
<div id="chng_psw_card">
	<div class="text-center">
		<div id="chng_header" class="text-danger">CHANGE PASSWORD</div>
		<div id="user_name" class="text-muted">@<?php echo $uun; ?></div>
		<div id="chng_msg" class="text-primary">Make changes to your password and click Change Password to save the changes! :-)</div>
	</div>
	<form id="chng_password_form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
		<div class="form-group">
			<label class="labels"><b>Current password</b></label>
			<div class="input-group"><input type="password" name="curr_psw" class="form-control inputs" id="curr_psw" required maxlength="80" minlength="8" placeholder="Type your current password" title="Type in your current password"><span id="pswErr" class="error"><?php echo $pswErr; ?></span></div>
		</div>
		<div class="form-group">
			<label class="labels"><b>New password</b></label>
			<div class="input-group"><input type="password" name="new_psw" class="form-control inputs" id="new_psw" required maxlength="80" minlength="8" placeholder="Type a new password" title="Type in a new password"><span id="newpswErr" class="error"><?php echo $newpswErr; ?></span></div>
		</div>
		<div class="form-group">
			<label class="labels"><b>Re-type new password</b></label>
			<div class="input-group"><input type="password" name="newpsw_cnf" class="form-control inputs" id="newpsw_cnf" required maxlength="80" minlength="8" placeholder="Re-type your new password" title="Re-type your new password"><span id="newpswcnfErr" class="error"><?php echo $newpswcnfErr; ?></span></div>
		</div>
		<button type="submit" class="btn btn-danger" id="chng_psw_btn">Change password</button>
	</form>
</div>
<!-- inner navbar for small devices....at bottom!-->
<div class="d-lg-none">
<nav class="navbar fixed-bottom" id="infm"><!-- inner nav for mobile !-->
	<ul class="navbar-nav list-inline text-center" id="infm_ul">
		<li class="list-inline-item bottom_nav_division">
			<a class="nav-link" href="profile.php?un=<?php echo $uun; ?>">
				<?php 
					if(file_exists($row['buddypic'])) echo '<img src="'.$row['buddypic'].'" class="rounded-circle img-fluid m_navpic">';
					else echo '<img src="images/def_buddypic.png" class="rounded-circle img-fluid m_navpic">';
					$result=null;
				?>
			</a>
		</li>
		<li class="list-inline-item bottom_nav_division">
			<a class="nav-link" href="messages.php"><i class="fa fa-envelope faa-shake animated"></i>
			<?php echo '<span id="m_updated_msgs"></span>'; ?>
			</a>
		</li>
		<li class="list-inline-item bottom_nav_division">
			<a class="nav-link" href="notifications.php"><i class="fa fa-bell faa-ring animated"></i> 
			<?php echo '<span id="m_updated_ntfs"></span>'; ?>
			</a>
		</li>
		<li class="list-inline-item bottom_nav_division">
			<a class="nav-link" href="explore.php"><i class="fas fa-compass"></i></a>
		</li>
		<li class="list-inline-item bottom_nav_division">
			<a class="nav-link" id="m_sbt" href="javascript:void(0)"><i class="fas fa-search"></i></a>
		</li>	
	</ul>
</nav>
</div>
<!-- scripts start !-->
<script src="http://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
<script src="js/chng_password.js"></script>
<script src="js/nav.js"></script>
<script>
$(function() {
});
var updateWorker = new Worker("js/update_worker.js");
check_updates();
var ct = $(document).prop('title');
function newNotification(Title, Body, Icon) {
  	var notification = new Notification(Title,{
  	    body: Body,
  	    icon: Icon
  	});
}
var ntfs_sent = false;
//function check for new updates
function check_updates() {
    updateWorker.postMessage("1");
    updateWorker.onmessage = function(e) {
	    var result = e.data;
		if(result.new_msg >= 1 || result.new_ntf >= 1) {
			if(result.new_msg >= 1 && result.new_ntf >= 1) {
			    var tot_ntfs = result.new_msg + result.new_ntf;
				$('#updated_msgs').addClass('badge badge-danger');
				$('#updated_ntfs').addClass('badge badge-danger');
				$('#m_updated_ntfs').addClass('badge badge-danger');
				$('#m_updated_msgs').addClass('badge badge-danger');
				$('#updated_msgs').html(result.new_msg);
				$('#updated_ntfs').html(result.new_ntf);
				$('#m_updated_ntfs').html(result.new_ntf);
				$('#m_updated_msgs').html(result.new_msg);
				$('#search').css('margin-left', '25px');
				if(!ntfs_sent) {
				    newNotification("buddyBonds","You Have A New Unseen Message And Notification On buddyBonds","../images/color-star-3-48-217610.png");
				    ntfs_sent = true;
				}
				$(document).prop('title',ct + " • " + tot_ntfs + " New Notifications");
			} else if(result.new_msg >= 1 && result.new_ntf <= 0) {
				$('#updated_msgs').addClass('badge badge-danger');
				$('#m_updated_msgs').addClass('badge badge-danger');
				$('#updated_msgs').html(result.new_msg);
				$('#m_updated_msgs').html(result.new_msg);
				$('#search').css('margin-left', '13px');
				if(!ntfs_sent) {
				    newNotification("buddyBonds","You Have A New Unseen Message On buddyBonds","../images/color-star-3-48-217610.png");
				    ntfs_sent = true;
				}
				$(document).prop('title',ct + " • " + result.new_msg + " New Notifications");
			} else if(result.new_msg <= 0 && result.new_ntf >= 1) {
				$('#updated_ntfs').addClass('badge badge-danger');
				$('#m_updated_ntfs').addClass('badge badge-danger');
				$('#updated_ntfs').html(result.new_ntf);
				$('#m_updated_ntfs').html(result.new_ntf);
				$('#search').css('margin-left', '13px');
				if(!ntfs_sent) {
				    newNotification("buddyBonds","You Have A New Unseen Notification On buddyBonds","../images/color-star-3-48-217610.png");
				    ntfs_sent = true;
				}
				$(document).prop('title',ct + " • " + result.new_ntf + " New Notifications");
            }
		}
    }
	setTimeout(check_updates, 5000);
}
</script>
<!--close php conn !-->
<?php $conn=null ; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>