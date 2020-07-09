<?php 
include 'varcsc.php';
$tz = new DateTimeZone($tz);
$dt = new DateTime();
$dt->setTimezone($tz);
$uun=base64_decode($_SESSION['username']);
$uid=base64_decode($_SESSION['id']);
//change logout time
$cltq=$conn->prepare("UPDATE users SET logout_time='0001-01-01 00:00:00' WHERE id=?");
$cltq->bindParam(1,$uid,PDO::PARAM_INT);
if(!$cltq->execute()) die('Execution failed:('.$cltq->errno.')'.$cltq->error);
//get user info 
$uiqr=$conn->prepare("SELECT username,buddypic FROM users WHERE id=?");
$uiqr->bindParam(1,$uid,PDO::PARAM_INT);
$uiqr->execute();
$uiqrow=$uiqr->fetch(PDO::FETCH_ASSOC);
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
	<meta name="description" content="buddyBonds web application.Hashtags page.View all hashtags on buddyBonds.Select proper hashtags for your posts for a larger audience.Connect and share.Social media website to make buddies,chat,upload pictures,videos and expanding your business."/>
	<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
	<title>Hashtags</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<link rel="icon" href="images/color-star-3-72-217610.png" type="image/x-icon">
	<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css" integrity="sha384-5sAR7xN1Nv6T6+dT2mhtzEpVJvfS3NScPQTrOxhwjIuvcA67KV2R5Jz6kr4abQsz" crossorigin="anonymous">
	<link rel="stylesheet" href="css/nav_footer.css">
	<link rel="stylesheet" href="css/hashtags.css">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lobster+Two|Roboto|Open+Sans">
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
				<input type="search" required maxlength="30" minlength="1" placeholder="Search..." title="Search" id="search" name="q">
				<div id="spnr"><img src="http://localhost/buddyBonds_backup/images/spnr.gif" id="spnr_img"></div>
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
		<div class="text-center"><a href="hashtags.php"><i class="fas fa-hashtag"></i>List Of Hashtags</a></div>
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
<!-- navbar for small devices end!-->
<div id="hdiv">
	<div id="hdiv_hdr"><i class="fas fa-hashtag"></i> <b>buddyBonds hashtags</b><br><span style="font-family:'Open Sans';font-size:16px">Suggest me more hashtags at <a href="mailto:connect@buddybonds.com" target="_blank">connect@buddybonds.com</a></span></div>
	<div id="hdiv_sec">
		<?php 
			//get hashtags list
			$ghlr=$conn->prepare("SELECT hashtag,added_time FROM hashtags_list ORDER BY id DESC");	
			$ghlr->execute();
			while($ghlrw=$ghlr->fetch(PDO::FETCH_ASSOC)) {
				echo '<div class="tag_row"><span class="tag">#'.$ghlrw['hashtag'].'</span><span class="addedon text-muted"><b>Added : </b>';
				$at = strtotime($ghlrw['added_time']);
				$dt->setTimestamp($at);
				if(time()-$at>0 && time()-$at<86400) echo $dt->format("H:i A");
                else if(time()-$at>=86400 && time()-$at<604800) echo $dt->format("D, H:i A");
                else if(time()-$at>=604800 && time()-$at<31536000) echo $dt->format("M j, H:i A");
                else echo $dt->format("M j, Y H:i A");
				echo '</span></div>';
			}
			$ghlr=null;
		?>
	</div>
</div>
<!-- inner navbar for small devices....at bottom!-->
<div class="d-lg-none">
<nav class="navbar fixed-bottom" id="infm"><!-- inner nav for mobile !-->
	<ul class="navbar-nav list-inline text-center" id="infm_ul">
		<li class="list-inline-item bottom_nav_division">
			<a class="nav-link" href="profile.php?un=<?php echo base64_decode($_SESSION['username']); ?>">
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
<div id="suggestions1"></div>
</div>
<!-- scripts start !-->
<script src="http://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
<script src="js/nav.js"></script>
<script src="js/gen.js"></script>
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