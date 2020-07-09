<?php
include 'varcsc.php';
error_reporting(0);
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
$uiqr=$conn->prepare("SELECT * FROM users WHERE id=?");
$uiqr->bindParam(1,$uid,PDO::PARAM_INT);
$uiqr->execute();
$uiqrow=$uiqr->fetch(PDO::FETCH_ASSOC);
$unErr=$nameErr=$bdayErr=$emailErr="";
if($_SERVER['REQUEST_METHOD']=='POST') {
	if(empty($_POST['fullname'])) {
		$nameErr="Name cannot be empty!";
	} else {
		$nameErr="";
		$fullname=test_input($_POST['fullname']);
	}
	if(empty($_POST['description'])) {
		$description="";
	} else {
		$description=test_input($_POST['description']);
		$description = base64_encode($description);
	}
	if(empty($_POST['email'])) {
		$emailErr="Email cannot be empty!";
	} else {
		$emailErr="";
		$email=test_input($_POST['email']);
		if(!filter_var($email,FILTER_VALIDATE_EMAIL)) $emailErr="Invalid email format!!";
	}
	if(empty($_POST['birthday'])) {
		$bdayErr="Birthday cannot be empty!";
	} else {
		$bdayErr="";
		$birthday=$_POST['birthday'];
	}
	$gender=test_input($_POST['gender']);
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
	<meta name="description" content="buddyBonds web application.Edit your profile.Make changes.Keep your account safe,secure and updated!Connect and share.Social media website to make buddies,chat,upload pictures,videos and expanding your business."/>
	<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
	<title>Edit Your Profile</title>
	<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css"> 
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<link rel="icon" href="images/color-star-3-72-217610.png" type="image/x-icon">
	<link rel="icon" href="images/color-star-3-57-217610.png" type="image/x-icon">
	<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css" integrity="sha384-5sAR7xN1Nv6T6+dT2mhtzEpVJvfS3NScPQTrOxhwjIuvcA67KV2R5Jz6kr4abQsz" crossorigin="anonymous">
	<link rel="stylesheet" href="css/nav_footer.css">
	<link rel="stylesheet" href="css/edit_profile.css">
	<script src="http://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
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
				<input type="search" required maxlength="30" minlength="1" placeholder="Search..." title="Search buddies or business profiles" id="search" name="q">
				<div id="spnr"><img src="images/spnr.gif" id="spnr_img"></div>
				<button type="reset" class="btn btn-sm" id="srch_rb"><span id="srch_rbt">X</span></button>
			</div>
		</div>
	</form>
	<ul class="navbar-nav list-inline" id="opts">
        <li class="list-inline-item">
		    <a class="nav-link text-secondary" style="margin-right:0" href="profile.php?un=<?php echo $uun; ?>" data-toggle="tooltip" title="Your Profile" id="nav_profile_link">
		    <?php if(file_exists($uiqrow['buddypic'])) echo '<img src="'.$uiqrow['buddypic'].'" class="rounded-circle img-fluid" style="width:25px;height:25px;object-fit:cover">';
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
			<input type="search" required maxlength="30" minlength="1" placeholder="Search..." title="Search..." id="m_search" name="q" class="mx-auto">
			<div id="m_spnr"><img src="images/spnr.gif" id="m_spnr_img"></div>
			<button type="reset" class="btn btn-sm" id="m_srch_rb"><span id="m_srch_rbt">X</span></button>
		</div>
	<div id="m_suggestions" class="mx-auto"></div>
	</div>
</form>
</div>
<!--navbar for small devices end!-->
<div id="edit_pro_card">
	<div class="text-center">
		<?php 
			if($_SERVER['REQUEST_METHOD']=='POST') {
				$sql=$conn->prepare("UPDATE users SET fullname=?,description=?,email=?,gender=?,birthday=? WHERE username=? AND id=?");
				$sql->bindParam(1,$fullname,PDO::PARAM_STR);
				$sql->bindParam(2,$description,PDO::PARAM_STR);
				$sql->bindParam(3,$email,PDO::PARAM_STR);
				$sql->bindParam(4,$gender,PDO::PARAM_STR);
				$sql->bindParam(5,$birthday,PDO::PARAM_STR);
				$sql->bindParam(6,$uun,PDO::PARAM_STR);
				$sql->bindParam(7,$uid,PDO::PARAM_INT);
				if($sql->execute()) echo '<div class="alert alert-info alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="text-success"><b>Update successful!</b></span><br>&#10084; Your profile was updated successfully!</div>';
				else echo '<div class="alert alert-info alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></a><span class="text-danger"><b>Update failed!</b></span><br>&#10084; Your profile was not updated.It may be due to network errors Please try again!</div>';
				$sql=null;
			}
		?>
		<div id="edit_header" class="text-success">EDIT YOUR PROFILE</div>
		<div class="text-muted" id="user_name">@<?php if($uun) {
			echo '<a href="profile.php?un='.$uun.'" class="text-secondary ts_nu" style="text-decoration:none">'.$uun.'</a>';
		} else {
			header("Refresh:0");
			exit();
		} 
		?>
		</div> 
		<div id="edit_msg" class="text-primary">Make changes to your profile and click Done to save the changes! :-)</div>
	</div>
	<form method="post" action="" id="edit_profile_form">
		<?php 
			//dq is details query
			$dqr=$conn->prepare("SELECT * FROM users WHERE id=? AND username=?");
			$dqr->bindParam(1,$uid,PDO::PARAM_INT);
			$dqr->bindParam(2,$uun,PDO::PARAM_STR);
			$dqr->execute();
			$dqrow=$dqr->fetch(PDO::FETCH_ASSOC);
		?>
		<div class="form-group">
			<div class="input-group"><input type="text" name="username" class="form-control inputs" id="username" required maxlength="30" minlength="1" placeholder="Enter your username here" disabled title="You can not change your username" value="<?php if(isset($_SESSION['username'])) {
					echo base64_decode($_SESSION['username']);
				} else {
					header("Refresh:0");
				} ?>" onfocus="this.placeholder=''" onblur="this.placeholder='Enter your username here'">
			<div id="spnr1"><img src="images/spnr.gif" id="spnr_img1"></div><span id="unErr" class="error"><?php echo $unErr; ?></span><span id="usernamecheck"></span></div>
		</div>
		<div class="form-group">
			<label class="labels" id="fn_lbl"><b>Enter your fullname here</b></label>	
			<div class="input-group"><input type="text" name="fullname" class="form-control inputs" id="fullname" required maxlength="30" minlength="1" placeholder="Enter your fullname here" title="Enter your fullname here!" value="<?php echo $dqrow['fullname']; ?>" onfocus="this.placeholder=''" onblur="this.placeholder='Enter your full name here'"><span id="nameErr" class="error"><?php echo $nameErr; ?></span></div>
		</div>
		<div class="form-group">
			<label class="labels" id="descp_lbl"><b>Give your intro here</b></label>
			<div class="input-group"><textarea class="form-control inputs" name="description" maxlength="200" id="description" onkeyup="textAreaAdjust(this)" placeholder="Give your intro here..." title="Enter your introduction here" onfocus="this.placeholder=''" onblur="this.placeholder='Give your intro here...'"><?php echo base64_decode($dqrow['description']); ?></textarea></div>
		</div>
		<label class="input-group-addon"><b>General information</b></label>
		<div class="form-group">
			<label class="labels" id="eml_lbl"><b>Enter a valid email address</b></label>
			<div class="input-group"><input type="email" name="email" class="form-control inputs" id="email" required maxlength="40" minlength="8" placeholder="Enter a valid email address" title="Enter a valid email address" value="<?php echo $dqrow['email']; ?>" pattern="^\S+@\S+[\.][0-9a-z]+$" onfocus="this.placeholder=''" onblur="this.placeholder='Enter a valid email address'"><span id="emailErr" class="error"><?php echo $emailErr; ?></span></div><span id="eml_msg">Please make sure you enter a correct email address. This email helps you to connect back with your account in case you lose your password.</span>
		</div>
		<div class="form-group">
			<label class="input-group-addon labels" id="bd_lbl"><b>Tell us your birthday</b></label>
			<div class="input-group"><input type="date" name="birthday" class="form-control inputs" id="birthday" min="1900-01-01" max="2002-01-01" placeholder="Tell us your birthday" title="Tell us your birthdate" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" required value="<?php echo $dqrow['birthday']; ?>" onfocus="this.placeholder=''" onblur="this.placeholder='Tell us your birthday'"><span id="bdayErr" class="error"><?php echo $bdayErr; ?></span></div>
		</div>
		<div class="form-check">
			<input type="radio" name="gender" class="form-check-input" id="male" value="Male">
			<label class="form-check-label">Male</label>
		</div>
		<div class="form-check">
			<input type="radio" name="gender" class="form-check-input" id="female" value="Female">
			<label class="form-check-label">Female</label>
		</div>
		<div class="form-check">
			<input type="radio" name="gender" class="form-check-input" id="Transgender" value="Transgender">
			<label class="form-check-label">Transgender</label>
		</div>
		<?php 
		    if($dqrow['gender']=="Male") {
		        ?>
		        <script> $('#male').prop('checked', true); </script>
		        <?php
		    } else if($dqrow['gender']="Female") {
		        ?>
		        <script> $('#female').prop('checked', true); </script>
		        <?php
		    } else {
		        ?>
		        <script> $('#Transgender').prop('checked', true); </script>
		        <?php
		    }
		?>
		<div class="text-center"><button type="submit" class="btn btn-danger" id="done_btn">Done</button></div>
	</form>
</div>
<!-- inner navbar for small devices....at bottom!-->
<div class="d-lg-none">
<nav class="navbar fixed-bottom" id="infm"><!-- inner nav for mobile !-->
	<ul class="navbar-nav list-inline text-center" id="infm_ul">
		<li class="list-inline-item bottom_nav_division">
			<a class="nav-link" href="profile.php?un=<?php echo $uun; ?>">
				<?php 
					if(file_exists($uiqrow['buddypic'])) echo '<img src="'.$uiqrow['buddypic'].'" class="rounded-circle img-fluid m_navpic">';
					else echo '<img src="images/def_buddypic.png" class="rounded-circle img-fluid m_navpic">';
					$uiqr=null;
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
<script src="js/edit_profile.js"></script>
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
<?php $conn=null; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>