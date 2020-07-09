<?php
include 'varcsc.php';
$tz = new DateTimeZone($tz);
$dt = new DateTime();
$dt->setTimezone($tz);
$uid = base64_decode($_SESSION['id']);
$uun = base64_decode($_SESSION['username']);
function test_input($data) {
	$data=trim($data);
	$data=stripslashes($data);
	$data=htmlspecialchars($data);
	$data=strip_tags($data);
	return $data;
}
$reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
/* variables for different ids are:
	$ipaddr=gcp();
	$ipaddr=$ipaddr.mt_rand();
	id for img in the post
//
	$ipmicro=gcp();
	$ipmicro=$ipmicro.microtime();
	id for nolnoc...shows just the number of likes 
//
	$cboxid=gcp();
	$cboxid=$cboxid.$pid;
	id for comment input text
//
	$ubid=uniqid();
	id for view all comments button
//
	$micran=microtime().mt_rand();
	id for comments showing area
//
	$btnid=mt_rand();
	id for post comment button
//
	$spnrimgid=md5($btnid);
	id for spnr img
*/
//change logout time
$cltq=$conn->prepare("UPDATE users SET logout_time='0001-01-01 00:00:00' WHERE id=?");
$cltq->bindParam(1,$uid,PDO::PARAM_INT);
if(!$cltq->execute()) die('Execution failed:('.$cltq->errno.')'.$cltq->error);
$cltq=null;
//get user info
$uiqr=$conn->prepare("SELECT * FROM users WHERE id=?");
$uiqr->bindParam(1,$uid,PDO::PARAM_INT);
$uiqr->execute();
$uiqrow=$uiqr->fetch(PDO::FETCH_ASSOC);
// Function to get the client IP address
function get_client_ip() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP'])) $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED'])) $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR'])) $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED'])) $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR'])) $ipaddress = $_SERVER['REMOTE_ADDR'];
    else $ipaddress = 'UNKNOWN';
    return $ipaddress;
}
$picErr = $picloadErr = "";
if($_SERVER["REQUEST_METHOD"] == "POST") {
	if(isset($_FILES['pic'])) {
	    if(!file_exists('uploads/'.$uun."/")) {
	    	mkdir('uploads/'.$uun.'/', 0777, true);
	    	$index_file_useless = fopen('uploads/'.$uun.'/index.html', 'w');
	    	fclose($index_file_useless);
	    }
	    $target_dir="uploads/".$uun."/".microtime().rand();
	    $org_file=basename($_FILES['pic']['name']);
	    $file_ext=strtolower(pathinfo($org_file,PATHINFO_EXTENSION));
	    $target_file=$target_dir.".".$file_ext;
	    $uploadOk=1;
   	    if(file_exists($target_file)) {
    	    $picErr="Sorry, file already exists!";
    	    $uploadOk = 0;
	    }
	    // Check file size
	    if ($_FILES["pic"]["size"] > 20000000) { // this size is 20 MB
    	    $picErr="Sorry, the file is too large!";
    	    $uploadOk = 0;
	    } 
	    // Allow certain file formats
	    $allowed=array('jpg','png','jpeg');
	    if(!in_array($file_ext,$allowed)) {
        	$picErr="Sorry, the file format is invalid! Only jpg/png/jpeg file formats are allowed!.";
   		    $uploadOk = 0;
	    } 
	    // Check if $uploadOk is set to 0 by an error
	    if($uploadOk == 0) {
    	    $picloadErr="Sorry, your file was not uploaded!";
		    // if everything is ok, try to upload file
	    } else {
    	    if(move_uploaded_file($_FILES["pic"]["tmp_name"], $target_file)) $pic=test_input($target_file);
            else $picloadErr="Sorry,there was an error uploading the file!";
	    }
		if(empty($_POST['pic_caption'])) { $post_mat=""; }
		else {
		    $post_mat=test_input($_POST['pic_caption']);
		    $real_post_mat = $post_mat;
		    $post_mat = base64_encode($post_mat);
		}
	} else {
	    $conn = null;
	    header("Location:http://localhost/buddyBonds_backup/home.php");
	    exit();
	}
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-105985024-2"></script>
	<script>
		window.dataLayer = window.dataLayer || [];

		function gtag() {
			dataLayer.push(arguments);
		}
		gtag('js', new Date());
		gtag('config', 'UA-105985024-2');
	</script>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
	<meta name="referrer" content="origin-when-crossorigin" />
	<meta name="author" content="Vipul Sharma" />
	<meta name="description"
		content="socialConnet web application.Home page.Get your daily feed of posts from your buddies and the business profiles you follow.Like,comment and share.Save posts to view later.Connect and share.Social media website to make buddies,chat,upload pictures,videos and expanding your business." />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<title>Home &#8226; buddyBonds</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
		integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<link rel="icon" href="images/color-star-3-72-217610.png" type="image/x-icon">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css"
		integrity="sha384-5sAR7xN1Nv6T6+dT2mhtzEpVJvfS3NScPQTrOxhwjIuvcA67KV2R5Jz6kr4abQsz" crossorigin="anonymous">
	<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="css/slick_carousel.css">
	<link rel="stylesheet" href="css/nav_footer.css">
	<link rel="stylesheet" href="css/home.css">
	<script src="http://code.jquery.com/jquery-2.2.4.min.js"
		integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
	<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Lobster+Two|Roboto|Open+Sans">
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
			<li class="nav-item active">
				<a href="" class="big_nav_active_link" id="navhd" data-toggle="tooltip" title="Refresh Home"><i
						class="fas fa-home nav_large_icons"></i> buddyBonds<span class="sr-only">(current)</span></a>
				<a href="messages.php" data-toggle="tooltip" title="Messages" class="text-secondary"><i
						class="fa fa-envelope faa-shake animated nav_large_icons"></i> <span
						class="nav_large_texts"><b>Messages</b></span>
					<?php echo '<span id="updated_msgs"></span>'; ?></a>
				<a href="notifications.php" data-toggle="tooltip" title="Notifications" class="text-secondary"><i
						class="fa fa-bell faa-ring animated nav_large_icons"></i> <span
						class="nav_large_texts"><b>Notifications</b>
					</span><?php echo '<span id="updated_ntfs"></span>'; ?></a>
				<a class="text-secondary" href="news.php" data-toggle="tooltip" title="News"><i
						class="fas fa-newspaper nav_large_icons"></i> <span
						class="nav_large_texts"><b>News</b></span></a>
			</li>
		</ul>
		<form action="search.php" method="get" class="form-inline" id="search_form">
			<div class="form-group">
				<div class="input-group" id="search_grp">
					<input type="search" required maxlength="30" minlength="1" placeholder="Search..." title="Search..."
						id="search" name="q">
					<div id="spnr"><img src="images/spnr.gif" id="spnr_img"></div>
					<button type="reset" class="btn btn-sm" id="srch_rb"><span id="srch_rbt">X</span></button>
				</div>
			</div>
		</form>
		<ul class="navbar-nav list-inline" id="opts">
			<li class="list-inline-item">
				<a class="nav-link text-secondary" style="margin-right:0" href="profile.php?un=<?php echo $uun; ?>"
					data-toggle="tooltip" title="Your Profile" id="nav_profile_link">
					<?php if(file_exists($uiqrow['buddypic'])) echo '<img src="'.$uiqrow['buddypic'].'" class="rounded-circle img-fluid" style="width:25px;height:25px;object-fit:cover">';
				else echo '<img src="images/def_buddypic.png" class="rounded-circle img-fluid m_navpic" style="width:25px;height:25px;object-fit:cover">' ?>
					<b><?php if(strlen($uun) > 10) echo substr($uun, 0, 10)."...";
						else echo $uun; ?></b></a>
			</li>
			<li class="list-inline-item" id="nav_lines">
				<a href="javascript:void(0)" id="sdnav_obtn" class="text-secondary"
					style="position: relative; top: 3px"><i class="fas fa-bars nav_large_icons" data-toggle="tooltip"
						title="Options"></i></a>
				<a href="javascript:void(0)" id="sdnav_cbtn" class="text-secondary"
					style="position: relative; top: 3px; color: #9400D3"><i class="fas fa-times nav_large_icons"
						data-toggle="tooltip" title="Close Options"></i></a>
			</li>
		</ul>
		<div id="optnav" class="text-center">
			<div><a href="settings.php" class="text-secondary ts_nu"><i
						class="fa fa-wrench faa-wrench animated nav_large_icons"></i> <span
						class="nav_large_texts"><b>Settings</b></span></a></div>
			<div><a href="hashtags.php" class="text-secondary ts_nu"><i class="fas fa-hashtag nav_large_icons"></i>
					<span class="nav_large_texts"><b>List Of Hashtags</b></span></a></div>
			<div><a href="edit_profile.php" class="text-secondary ts_nu"><i class="fas fa-edit nav_large_icons"></i>
					<span class="nav_large_texts"><b>Edit profile</b></span></a></div>
			<div><a href="logout.php" class="text-secondary ts_nu"><i class="fas fa-sign-out-alt nav_large_icons"></i>
					<span class="nav_large_texts"><b>Log Out</b></span></a></div>
		</div>
	</nav>
	<div id="suggestions"></div>
	<!-- !-->
	<!-- navbar for small and med devices !-->
	<!-- !-->
	<div class="d-lg-none">
		<nav class="navbar fixed-top" id="m_nav">
			<ul class="navbar-nav">
				<li class="nav-item">
					<a href="javascript:void(0)" id="m_upl_pic"><i class="fas fa-camera fa-2x"></i></a>
				</li>
			</ul>
			<ul class="navbar-nav">
				<li class="nav-item">
					<a href="#top" id="m_navhd">buddyBonds</a>
				</li>
			</ul>
			<ul class="navbar-nav">
				<li class="nav-item">
					<a href="javascript:void(0)" id="m_sdnav_obtn"><i class="fas fa-bars fa-2x"></i></a>
					<a href="javascript:void(0)" id="m_sdnav_cbtn" style="color:blue"><i
							class="fas fa-times fa-2x"></i></a>
				</li>
			</ul>
			<div id="m_optnav" class="text-center">
				<div><a href="settings.php"><i class="fa fa-wrench faa-wrench animated"></i> Settings</a></div>
				<div><a href="news.php"><i class="fas fa-newspaper"></i> News</a></div>
				<div><a href="hashtags.php"><i class="fas fa-hashtag"></i> List Of Hashtags</a></div>
				<div><a href="edit_profile.php"><i class="fas fa-edit"></i> Edit profile</a></div>
				<div><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Log Out</a></div>
			</div>
		</nav>
		<form action="search.php" method="get" id="m_search_form">
			<div class="form-group">
				<div class="input-group">
					<input type="search" required maxlength="30" minlength="1" placeholder="Search..." title="Search"
						id="m_search" name="q" class="mx-auto">
					<div id="m_spnr"><img src="images/spnr.gif" id="m_spnr_img"></div>
					<button type="reset" class="btn btn-sm" id="m_srch_rb"><span id="m_srch_rbt">X</span></button>
				</div>
				<div id="m_suggestions" class="mx-auto"></div>
			</div>
		</form>
	</div>
	<!--navbar for small devices end!-->
	<!-- edit profile label and 1000th visit label !-->
	<?php 
$nov = $uiqrow['nov'];
if($nov == 1) echo '<div class="alert alert-info alert-dismissable text-center"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="text-success"><b>New to buddyBonds??</b></span><br> It\'s time you tell me more about yourself in the <a href="edit_profile.php" style="text-decoration:none">EDIT PROFILE SECTION </a>:-)</div>';
if(($nov == 100) || ($nov == 500) || ($nov == 1000) || ($nov == 2000) || ($nov==5000)) 
	echo '<div class="alert alert-info alert-dismissable text-center"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="text-success"><b>YAYYE!</b></span> It\'s your <span class="text-success"><b>'.$nov.'th visit</b></span> to <b>buddyBonds.com</b><br>I <span class="text-success"><b>am extremely thankful</b></span> to you for being a part of this amazing journey!<br>And here\'s to many more futute visits 🥂</div>';
if($_SERVER["REQUEST_METHOD"] == "POST") {
	if((!$picErr) && (!$picloadErr)) {
		//hashtag position
		//looks for hashtag
		$lfh = base64_decode($post_mat);
		$htpos=strpos($lfh, "#");
		if($htpos!==false) {
			$hashtags=substr($lfh,$htpos);
			//check genuinity of hashtag
			$htarr=explode("#",$hashtags);
			$genuine_hashtags="";
			foreach($htarr as $hashtag) {
				if($hashtag=="") continue;
				$cgohr=$conn->prepare("SELECT id FROM hashtags_list WHERE hashtag=?");
				$cgohr->bindParam(1,$hashtag,PDO::PARAM_STR);
				$cgohr->execute();
				if($cgohr->rowCount()>0) $genuine_hashtags=$genuine_hashtags."#".$hashtag;
				$cgohr=null;
			}
		}
		$res=$conn->prepare("INSERT INTO posts(user_id,post_matter,hashtags,post_content,post_time) VALUES(?,?,?,?,NOW())");
		$res->bindParam(1,$uid,PDO::PARAM_INT);
		$res->bindParam(2,$post_mat,PDO::PARAM_STR);
		$res->bindParam(3,$genuine_hashtags,PDO::PARAM_STR);
		$res->bindParam(4,$pic,PDO::PARAM_STR);
		if($res->execute()) echo '<div class="alert alert-info alert-dismissable text-center"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="text-success"><b>Upload Successful!</b></span><br>❤️ Your picture was uploaded successfully!</div>';
		else echo '<div class="alert alert-info alert-dismissable text-center"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="text-danger"><b>Upload failed!</b></span><br>&#10084; Your picture could not be uploaded. Please try again!</div>';
		//analyze post matter
		$mentions = array();
		if(strpos($real_post_mat, "@") !== false) {
		    //get the post id 
    		$gtpid = $conn->prepare("SELECT post_id FROM posts WHERE user_id=? ORDER BY post_id DESC LIMIT 1"); 
    		$gtpid->bindParam(1,$uid,PDO::PARAM_INT);
    		$gtpid->execute();
    		$gtpidr = $gtpid->fetch(PDO::FETCH_ASSOC);
    		$postid = $gtpidr['post_id'];
		    $a = preg_replace_callback('/@.+?\b/', function($m)  {
                $str = substr($m[0], 1);
                array_push($GLOBALS['mentions'], $str);
                return sprintf("%s", $str);
            }, $real_post_mat);
            foreach($mentions as $mun) {
                //get id for each username
                $gidfun = $conn->prepare("SELECT id FROM users WHERE username=?");
                $gidfun->bindParam(1,$mun,PDO::PARAM_STR);
                $gidfun->execute();
                $gidfunr = $gidfun->fetch(PDO::FETCH_ASSOC);
                $munid = $gidfunr['id'];
                $smn = $conn->prepare("INSERT INTO notifications(bud_id1,bud_id2,notification_type,post_id,notification_time) VALUES(?,?,'tag',?,NOW())");
		        $smn->bindParam(1,$uid,PDO::PARAM_INT);
		        $smn->bindParam(2,$munid,PDO::PARAM_INT);
		        $smn->bindParam(3,$postid,PDO::PARAM_INT);
		        $smn->execute();
            }
		}
		$res=null;
	} else echo '<div class="alert alert-info alert-dismissable text-center"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="text-danger"><b>Upload failed!</b></span><br>Your picture could not be uploaded. Please try again!</div>';
}	
?>
	<!-- form for uploading pic all devices !-->
	<div class="alert alert-info alert-dismissable text-center" id="pic_form_alert">
		<a href="javascript:void(0)" aria-label="close" id="pic_alert_fadeout">&times;</a>
		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST" enctype="multipart/form-data"
			id="upl_form" class="text-center">
			<input type="file" name="pic" id="pic" required>
			<textarea class="form-control" id="pic_caption" name="pic_caption" maxlength="500"
				placeholder="Describe Your Picture" title="Describe your picture, you may use hashtags"
				spellcheck="false"></textarea>
			<button type="submit" class="btn btn-sm btn-success" id="pic_submit_btn">Upload</button>
		</form>
	</div>
	<div id="ui">
		<div id="user_info" class="text-center">
			<div id="just_some_padding">
				<div class="dropdown" data-toggle="tooltip" title="Change The Theme" data-placement="right">
					<i class="fas fa-ellipsis-v text-light dropdown-toggle" id="theme_colors_eicon"
						data-toggle="dropdown"></i>
					<div class="dropdown-menu" id="theme_box_palette">
						<span class="dropdown-header"><b>Choose Your Theme</b></span>
						<span class="theme_box_pc" id="theme_box_pc_darkviolet" data-themecolor="#9400D3"></span>
						<span class="theme_box_pc" id="theme_box_pc_red" data-themecolor="#FF0000"></span>
						<span class="theme_box_pc" id="theme_box_pc_teal" data-themecolor="#008080"></span>
						<span class="theme_box_pc" id="theme_box_pc_gold" data-themecolor="#FFD700"></span>
						<span class="theme_box_pc" id="theme_box_pc_springgreen" data-themecolor="#00FF7F"></span><br>
						<span class="theme_box_pc" id="theme_box_pc_rebeccapurple" data-themecolor="#663399"></span>
						<span class="theme_box_pc" id="theme_box_pc_tomatored" data-themecolor="#FF6347"></span>
						<span class="theme_box_pc" id="theme_box_pc_purple" data-themecolor="#800080"></span>
						<span class="theme_box_pc" id="theme_box_pc_fuchsia" data-themecolor="#FF00FF"></span>
						<span class="theme_box_pc" id="theme_box_pc_deepskyblue" data-themecolor="#00BFFF"></span><br>
						<span class="theme_box_pc" id="theme_box_pc_royalblue" data-themecolor="#4169E1"></span>
						<span class="theme_box_pc" id="theme_box_pc_green" data-themecolor="#008000"></span>
						<span class="theme_box_pc" id="theme_box_pc_deeppurple" data-themecolor="#FF1493"></span>
						<span class="theme_box_pc" id="theme_box_pc_darkslateblue" data-themecolor="#483D8B"></span>
						<span class="theme_box_pc" id="theme_box_pc_orange" data-themecolor="#FF4500"></span>
					</div>
				</div>
			</div>
			<?php 
		    if(file_exists($uiqrow['buddypic'])) echo '<img src="'.$uiqrow['buddypic'].'" class="rounded-circle uibp">';
		    else echo '<img src="images/def_buddypic.png" class="rounded-circle uibp">';
		    echo '<br><a href="profile.php?un='.$uun.'" id="uifn" class="text-dark" data-toggle="tooltip" title="Visit Your Profile"><b>'.$uiqrow['fullname'].'</b></a><br>';
		    echo '<a href="profile.php?un='.$uun.'" id="uiun" class="text-dark">@'.$uun.'</a><br>';
		    //get number of posts
		    $upqr = $conn->prepare("SELECT * FROM posts WHERE user_id=?");
		    $upqr->bindParam(1,$uid,PDO::PARAM_INT);
		    $upqr->execute();
	    ?>
			<a href="edit_profile.php" class="btn btn-sm" id="ui_edbtn"><i class="fas fa-edit"></i> Edit Your
				Profile</a>
			<div class="row" id="user_info_row">
				<div class="col-lg-6" style="border-right: 2px solid #dcdcdc"><span
						class="text-muted"><b>Posts</b></span><br><span
						id="user_info_posts"><b><?php echo $upqr->rowCount(); ?></b></span></div>
				<div class="col-lg-6"><span class="text-muted"><b>Buddies</b></span><br><span
						id="user_info_buddies"><b><?php echo $uiqrow['nobuddies']; ?></b></span></div>
			</div>
		</div>
		<div id="suggestions_for_you">
			<div class="card-header text-dark sticky-top"><b>Suggestions For You</b></div>
			<?php
            $sfyr = $conn->prepare("SELECT id, username, buddypic FROM users WHERE id!=? ORDER BY nobuddies LIMIT 100");
            $sfyr->bindParam(1, $uid, PDO::PARAM_INT);
            $sfyr->execute();
            $sfy = 0;
            while($sfyrw = $sfyr->fetch(PDO::FETCH_ASSOC)) {
                $sfyid = $sfyrw['id'];
                //check if already buddies
                $ciabr = $conn->prepare("SELECT buddies_id FROM buddies WHERE ((bud_id1 = :uid AND bud_id2 = :sfyid) OR (bud_id1 = :sfyid AND bud_id2 = :uid)) AND active = '1'");
                $ciabr->bindParam(":uid", $uid, PDO::PARAM_INT);
                $ciabr->bindParam(":sfyid", $sfyid, PDO::PARAM_INT);
                $ciabr->execute();
                if($ciabr->rowCount() > 0) continue;
                echo '<div class="row who_row">';
                    if(file_exists($sfyrw['buddypic'])) echo '<img src="'.$sfyrw['buddypic'].'" class="who_row_bp rounded-circle">';
	                else echo '<img src="images/def_buddypic.png" class="who_row_bp rounded-circle">';
	                echo '<a href="profile.php?un='.$sfyrw['username'].'" class="who_row_un"><b>'.$sfyrw['username'].'</b></a></div>';
                $sfy++;
            }
            if($sfy == 0) echo '<div class="who_row">You are buddies already with all the users</div>';
            $sfyr = $ciaar = null;
        ?>
		</div>
	</div>
	<!-- for medium large devices profile info!-->
	<div id="ui_ml">
		<?php 
	    //ml means medium large devices
		if(file_exists($uiqrow['buddypic'])) echo '<img src="'.$uiqrow['buddypic'].'" class="rounded-circle uibp_ml">';
		else echo '<img src="images/def_buddypic.png" class="rounded-circle uibp_ml">';
		echo '<a href="profile.php?un='.$uun.'" id="uifn_ml" class="text-danger"><b>'.$uiqrow['fullname'].'</b></a><br>';
		echo '<a href="profile.php?un='.$uun.'" id="uiun_ml" class="text-dark">'.$uun.'</a>';
	?>
		<a href="edit_profile.php" class="btn btn-outline-success btn-sm uibtns_ml" id="ui_edbtn_ml"><i
				class="fas fa-edit"></i> Edit Your Profile <i class="fas fa-laptop"></i></a>
	</div>
	<!-- showing home content and user profile link on its right!-->
	<div id="posts_area" class="inline">
		<div id="share_your_moment"><b>Hey, What's Happening? Share Your Moment</b> 🙂 <button
				class="btn btn-sm text-white" id="upl_pic" style="margin-left: 73px"><i class="fas fa-camera"></i>
				Create A Post</button></div>
		<?php
	$x=0;
	//check if cookie has buddies
	$cichb = $conn->prepare("SELECT nobuddies FROM users WHERE id = :uid");
	$cichb->bindParam(":uid",$uid, PDO::PARAM_INT);
	$cichb->execute();
	$cichbr = $cichb->fetch(PDO::FETCH_ASSOC);
    $pr=$conn->prepare("SELECT * FROM posts,buddies WHERE ((posts.user_id = buddies.bud_id1) OR (posts.user_id = buddies.bud_id2) OR (posts.user_id = :uid)) AND ((buddies.bud_id1 = :uid) OR (buddies.bud_id2 = :uid) OR (posts.user_id = :uid)) AND ((buddies.active = '1') OR (posts.user_id = :uid)) ORDER BY posts.post_time DESC");
	$pr->bindParam(":uid", $uid, PDO::PARAM_INT);
	$pr->execute();
	$cichb = null;
	$postarr = array();
	//arrange posts in proper form...which i don't know how to do yet!... 1:08 PM 10-MAY-2018
	//and it's 07/06/2018 1:28 PM and i feel like i figured it out....just feeling it...not done yet!
	while($prw=$pr->fetch(PDO::FETCH_ASSOC)) {
		$pid=$prw['post_id'];
		foreach($postarr as $var) {
			if($var == $pid) continue 2;
		}
		array_push($postarr, $pid);
		//uploader id
		$userid = $prw['user_id'];
		//get info about the post uploader
		$prwur=$conn->prepare("SELECT * FROM users WHERE id=?");
		$prwur->bindParam(1,$userid,PDO::PARAM_INT); 
		$prwur->execute();
		$prwurw=$prwur->fetch(PDO::FETCH_ASSOC);
		echo '<div class="enclose_post">';
			//show buddypic of post uploader
			if(file_exists($prwurw['buddypic'])) echo '<img src="'.$prwurw['buddypic'].'" class="img-fluid rounded-circle post_bud" alt="Buddy picture of the user">';
			else echo '<img src="images/def_buddypic.png" class="img-fluid rounded-circle post_bud" alt="Default buddy picture">';
			//show username of post uploader
			echo '<a href="profile.php?un='.$prwurw['username'].'" class="post_un"><b>'.$prwurw['username'].'</b></a>';
			$prwur=null;
			//show text with the post
			if($prw['post_matter']) {
			    $post_matter = base64_decode($prw['post_matter']);
			    if(strpos($post_matter, "@") !== false) {
					$post_matter = preg_replace_callback('/@.+?\b/', function($m) {
                        $str = substr($m[0], 1);
                        return sprintf("<a href='profile.php?un=%s' class='text-dark'><b>%s</b></a>", $str, $str);
                    }, $post_matter);
				}
				if(preg_match($reg_exUrl, $post_matter, $url)) {
                    $post_matter = preg_replace($reg_exUrl, '<a href="'.$url[0].'" rel="nofollow" target="_blank" class="chat_link_share" style="text-decoration:none"><b>'.$url[0].'</b></a><br>', $post_matter);
                }
				echo '<div class="post_mat">'.$post_matter.'</div>';
			}
			//show the post image
			if(file_exists($prw['post_content'])) {	
				$extension=strtolower(pathinfo($prw['post_content'],PATHINFO_EXTENSION));
				if(($extension=="jpg") || ($extension=="png") || ($extension=="jpeg") || ($extension=="gif")) {
					//setting $ipaddr for id of the image
					$ipaddr=get_client_ip();
					$ipaddr=$ipaddr.mt_rand();
					//just set $ipaddr for id of the image
					?>
		<img src="<?php echo $prw['post_content']; ?>" class="img-thumbnail img-fluid post_img"
			alt="Picture uploaded by the user" id="<?php echo $ipaddr; ?>">
		<?php
				} else if(($extension=="mp4") || ($extension=="webm") || ($extension=="ogg")) {
				    //for video support later
				} 
			}
			//lkcmb is like comment div
			//check if pic has been bonded before or not
			echo '<div class="lkcmd">'; 
				$cq=$conn->prepare("SELECT activity_id FROM activity WHERE bud_id1=? AND post_id=? AND activity_type='bond'");
				$cq->bindParam(1,$uid,PDO::PARAM_INT);
				$cq->bindParam(2,$pid,PDO::PARAM_INT);
				$cq->execute();
				//setting $ipmicro for id of div that shows number of likes
				$ipmicro=get_client_ip();
				$ipmicro=$ipmicro.microtime();
				echo '<div class="btn-group btn-group-toggle d-flex">';
				//set $ipmicro for id of div that shows number of likes
				if($cq->rowCount()>0) {
					?>
		<button class="btn btn-basics w-100 text-danger" onclick="like('<?php echo $pid; ?>','<?php echo $ipmicro; ?>')"
			id="<?php echo $pid; ?>"><i class="fas fa-heartbeat btn-basic-texts"></i> Bond</button>
		<?php //lb means lkcm buttons
				} else {
					?>
		<button class="btn btn-basics w-100 text-danger" onclick="like('<?php echo $pid; ?>','<?php echo $ipmicro; ?>')"
			id="<?php echo $pid; ?>"><i class="far fa-heart fa-spin btn-basic-texts"></i> Bond</button>
		<?php 
					$cq=null;
				}
				//setting $cboxid for id of comment box
				$cboxid=get_client_ip();
				$cboxid=$cboxid.$pid;
				//set $cboxid for id of comment box
				?>
		<button class="btn btn-basics w-100 text-danger" onclick="focuscb('<?php echo $cboxid; ?>')" title="Comment"><i
				class="far fa-comment"></i> Comment</button>
		<button class="btn btn-basics w-100 text-danger share_btn" data-toggle="tooltip" title="Click To Copy Link"
			data-pid="<?php echo $pid; ?>"><i class="fas fa-share-square"></i> Share</button>
	</div>
	<?php
			echo '</div>'; 
			//focus comment box
			//show number of likes and number of comments
			echo '<div class="nolnoc" id="'.$ipmicro.'">';
				if($prw['nol']>0&&$prw['nol']<6) {
					$lnr=$conn->prepare("SELECT activity.activity_id,users.id,users.username FROM activity,users WHERE activity.bud_id1=users.id AND activity.post_id=? AND activity.activity_type='bond'");
					$lnr->execute(array($pid));
					echo '<b>Bonded </b>by ';
					while($lnrw=$lnr->fetch(PDO::FETCH_ASSOC)) echo '<a href="profile.php?un='.$lnrw['username'].'" class="text-dark">'.$lnrw['username'].'</a> ';
					$lnr=null;
				} else if($prw['nol']>=6) {
					echo '<b>Bonded </b>by '.$prw['nol'];
				}
			echo '</div>';
			//setting $ubid for id of the view all comments button
			$ubid=uniqid();
			//set $ubid for id of the view all comments button
			//setting $micran for the id of dic that shows the comments
			$micran=microtime().mt_rand();
			//set $micran for the id of divv that shows the comments
			echo '<div id="'.$ubid.'">';
			//view all comments
			if($prw['noc']>=2) echo '<a href="post.php?p='.$pid.'" class="text-muted sacb" id="<?php echo $ubid; ?>" title="Show
	all the comments on this post">View all '.$prw['noc'].' comments</a>';
	echo '</div>';
	//row showing comment form
	echo '<div class="row cf">';
		if(file_exists($uiqrow['buddypic'])) echo '<img src="'.$uiqrow['buddypic'].'" class="rounded-circle comm_pic"
			alt="Your buddy picture.">';
		else echo '<img src="images/def_buddypic.png" class="rounded-circle comm_pic" alt="Default buddy picture.">';
		//setting $btnid for id of the post comment button
		$btnid=mt_rand();
		$spnrimgid=md5($btnid);
		//set $btnid for id of the post comment button
		?>
		<input type="text" class="comment" placeholder=" Add your comment..." title="Wanna add some comment?"
			name="comment" required maxlength="400" id="<?php echo $cboxid; ?>"
			onkeyup="spcb('<?php echo $cboxid; ?>','<?php echo $btnid; ?>')"><button class="btn btn-sm btn-success pacb"
			onclick="post_comment('<?php echo $pid ; ?>','<?php echo $cboxid; ?>','<?php echo $micran; ?>','<?php echo $ubid; ?>','<?php echo $spnrimgid; ?>')"
			id="<?php echo $btnid; ?>" title="Post your comment" disabled>Post</button>
		<?php 
				echo '<img src="images/spnr.gif" class="spnr mx-auto" id="'.$spnrimgid.'">';
				//row showing comment form ends
			echo '</div>';
			//we need to show comments less than or equal to 2
			//show all comments div
			?>
		<div id="<?php echo $micran; ?>">
			<?php  
				$cr=$conn->prepare("SELECT * FROM activity WHERE bud_id1=? AND activity_type='comment' AND post_id=? ORDER BY activity_time DESC LIMIT 2");
				$cr->bindParam(1,$uid,PDO::PARAM_INT);
				$cr->bindParam(2,$pid,PDO::PARAM_INT);
				$cr->execute();
				if($cr->rowCount()>0) {
					while($crw=$cr->fetch(PDO::FETCH_ASSOC)) {
						echo '<div class="row cmnt_row">';
						if(file_exists($uiqrow['buddypic'])) echo '<img src="'.$uiqrow['buddypic'].'" class="comm_pic rounded-circle" alt="Buddy picture of the user.">';
						else echo '<img src="images/def_buddypic.png" class="comm_pic rounded-circle" alt="Default buddy picture for the user.">';
						echo '<a href="profile.php?un='.$uiqrow['username'].'" class="cmnt_un text-dark"><b>'.$uiqrow['username'].'</b></a>';
						$comment = base64_decode($crw['activity_content']);
						echo '<span class="comm_show">';
						if(strpos($comment, "@") !== false) {
						    $comment = preg_replace_callback('/@.+?\b/', function($m) {
                                $str = substr($m[0], 1);
                                return sprintf("<a href='profile.php?un=%s' class='text-dark'><b>%s</b></a>", $str, $str);
                            }, $comment);
						}
						echo $comment.'</span>';
						echo '<span class="text-muted time_show">';
						$acti_time = strtotime($crw['activity_time']);
						$dt->setTimestamp($acti_time);
            			if(time()-$acti_time>0 && time()-$acti_time<86400) echo $dt->format("H:i A");
			            else if(time()-$acti_time>=86400 && time()-$acti_time<604800) echo $dt->format("D, H:i A");
			            else if(time()-$acti_time>=604800 && time()-$acti_time<31536000) echo $dt->format("M j, H:i A");
			            else echo $dt->format("M j, Y H:i A");
						echo '.</span>';
						if(($crw['bud_id1']==$uid)||($userid==$uid)) {
							?>
			<a href="javascript:void(0)" class="dlt_link" style="color:#708090"
				onclick="dlt_cmnt('<?php echo $pid; ?>','<?php echo $micran; ?>','<?php echo $ubid; ?>','<?php echo $crw['activity_id']; ?>')"
				title="Delete"><b><i class="far fa-trash-alt"></i></b></a>
			<?php
						}
						echo '</div>';
					}
					$cr=null;
				} else {
					$cr=$conn->prepare("SELECT activity.bud_id1,activity.activity_id,activity.activity_content,activity.activity_time,users.id,users.username,users.buddypic FROM activity,users WHERE activity.bud_id1=users.id AND activity.activity_type='comment' AND activity.post_id=? ORDER BY activity.activity_time LIMIT 2");
					$cr->execute(array($pid));
					if($cr->rowCount()>0) {
						while($crw=$cr->fetch(PDO::FETCH_ASSOC)) {
							echo '<div class="row cmnt_row">';
							if(file_exists($crw['buddypic'])) echo '<img src="'.$crw['buddypic'].'" class="comm_pic rounded-circle" alt="Buddy picture of the user.">';
							else echo '<img src="images/def_buddypic.png" class="comm_pic rounded-circle" alt="Default buddy picture for the user.">';
							echo '<a href="profile.php?un='.$crw['username'].'" class="cmnt_un text-dark"><b>'.$crw['username'].'</b></a>';
							$comment = base64_decode($crw['activity_content']);
							echo '<span class="comm_show">';
							if(strpos($comment, "@") !== false) {
						        $comment = preg_replace_callback('/@.+?\b/', function($m) {
                                    $str = substr($m[0], 1);
                                    return sprintf("<a href='profile.php?un=%s' class='text-dark'><b>%s</b></a>", $str, $str);
                                }, $comment);
						    }
						    echo $comment.'</span>';
							echo '<span class="text-muted time_show">';
							$acti_time = strtotime($crw['activity_time']);
							$dt->setTimestamp($tz);
						    if(time()-$acti_time>0 && time()-$acti_time<86400) echo date('H:i A',$acti_time+19800);
	                        else if(time()-$acti_time>=86400 && time()-$acti_time<604800) echo date('D,H:i A',$acti_time+19800);
	                        else if(time()-$acti_time>=604800 && time()-$acti_time<31536000) echo date('M d,H:i A',$acti_time+19800);
		                    else echo date('M d, Y H:i A',$acti_time+19800);
							echo '</span>';
							if($crw['bud_id1']==$cookie_id) {
								?>
			<a href="javascript:void(0)" class="dlt_link" style="color:#708090"
				onclick="dlt_cmnt('<?php echo $pid; ?>','<?php echo $micran; ?>','<?php echo $ubid; ?>','<?php echo $crw['activity_id']; ?>')"
				title="Delete"><b><i class="far fa-trash-alt"></i></b></a>
			<?php
							}	
							echo '</div>';
						}
					}
					$cr=null;
				}
				?>
		</div>
		<?php
			//show post tym
			echo '<div class="text-muted post_tym">UPLOADED on buddyBonds <div style="float:right"><b>';
			$post_uptime = strtotime($prw['post_time']);
			$dt->setTimestamp($post_uptime);
			if(time()-$post_uptime>0 && time()-$post_uptime<86400) echo $dt->format("H:i A");
			else if(time()-$post_uptime>=86400 && time()-$post_uptime<604800) echo $dt->format("D, H:i A");
			else if(time()-$post_uptime>=604800 && time()-$post_uptime<31536000) echo $dt->format("M j, H:i A");
			else echo $dt->format("M j, Y H:i A");
			echo '</b></div></div>';
		//end the enclosing post
		echo '</div>';
		$x++;
	}
	if($x==0) echo '<div class="msg text-center msg1">Nothing to show here.<br>Do some activity,make some buddies and upload some posts to have your home feed here.</div>';
	?>
	</div>
	<!--remove floating for proper display of div !-->
	<div class="clearBoth"></div>
	<!--homepage content ends!-->
	<!-- inner navbar for small and med devices....at bottom!-->
	<div class="d-lg-none">
		<nav class="navbar fixed-bottom" id="infm">
			<!-- inner nav for mobile !-->
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
				<li class="list-inline-item active bottom_nav_division">
					<a class="nav-link" href="" style="color:blue"><i class="fa fa-home"></i></a>
				</li>
				<li class="list-inline-item bottom_nav_division">
					<a class="nav-link" href="messages.php"><i class="fa fa-envelope faa-shake animated"></i>
						<?php echo '<span id="m_updated_msgs"></span>'; ?>
					</a>
				</li>
				<li class="list-inline-item bottom_nav_division">
					<a class="nav-link" href="notifications.php"><i class="fa fa-bell faa-ring animated"></i>
						<?php echo '<span id="m_updated_ntfs"></span>'; ?></a>
				</li>
				<li class="list-inline-item bottom_nav_division">
					<a class="nav-link" id="m_sbt" href="javascript:void(0)"><i class="fas fa-search"></i></a>
				</li>
			</ul>
		</nav>
	</div>
	<!-- scripts start !-->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"
		integrity="sha256-NXRS8qVcmZ3dOv3LziwznUHPegFhPZ1F/4inU7uC8h0=" crossorigin="anonymous"></script>
	<script src="js/home.js"></script>
	<script src="js/nav.js"></script>
	<script>
		$(function () {
			//12 functions 10:29 PM 07/06/2018
			//update_msg(),update_ntfs(),sbmtsl(),like(),updt_nlc(),post_comment(),updt_cmnts(),updt_cmnt_btn(),focuscb(),spcb(),dlt_cmnt(),theme()
			$('.modal-body').slick({
				slidesToShow: 3,
				slidesToScroll: 2,
				dots: true,
				infinite: false,
				arrows: false,
				variableWidth: true
			});
		});
		var updateWorker = new Worker("js/update_worker.js");
		check_updates();
		var ct = $(document).prop('title');

		function newNotification(Title, Body, Icon) {
			var notification = new Notification(Title, {
				body: Body,
				icon: Icon
			});
		}
		var ntfs_sent = false;
		var uid = "<?php echo $uid; ?>";
		$.ajax({
			type: "GET",
			url: "http://localhost/buddyBonds_backup/scripts/get_theme.php",
			data: {
				"theme_id": uid
			}
		}).done(function (color) {
			change_theme(color);
		});

		function change_theme(color) {
			localStorage.setItem('themecolor', color);
			$('#just_some_padding').css('background-color', color);
			$('#ui_edbtn').css({
				'color': color,
				'border': '1px solid ' + color
			});
			$('.who_row_un').css('color', color);
			$('#upl_pic').css('background-color', color);
			$('.big_nav_active_link').css({
				'color': color,
				'border-bottom': '3px solid ' + color
			});
			$('#user_info_posts').css('color', color);
			$('#user_info_buddies').css('color', color);
			$('#nav').css('border-top', '3px solid ' + color);
			$('.post_un').css('color', color);
		}
		//function check for new updates
		function check_updates() {
			updateWorker.postMessage("1");
			updateWorker.onmessage = function (e) {
				var result = e.data;
				if (result.new_msg >= 1 || result.new_ntf >= 1) {
					if (result.new_msg >= 1 && result.new_ntf >= 1) {
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
						if (!ntfs_sent) {
							newNotification("buddyBonds", "You Have A New Unseen Message And Notification On buddyBonds",
								"images/color-star-3-48-217610.png");
							ntfs_sent = true;
						}
						$(document).prop('title', ct + " • " + tot_ntfs + " New Notifications");
					} else if (result.new_msg >= 1 && result.new_ntf <= 0) {
						$('#updated_msgs').addClass('badge badge-danger');
						$('#m_updated_msgs').addClass('badge badge-danger');
						$('#updated_msgs').html(result.new_msg);
						$('#m_updated_msgs').html(result.new_msg);
						$('#search').css('margin-left', '13px');
						if (!ntfs_sent) {
							newNotification("buddyBonds", "You Have A New Unseen Message On buddyBonds",
								"images/color-star-3-48-217610.png");
							ntfs_sent = true;
						}
						$(document).prop('title', ct + " • " + result.new_msg + " New Notifications");
					} else if (result.new_msg <= 0 && result.new_ntf >= 1) {
						$('#updated_ntfs').addClass('badge badge-danger');
						$('#m_updated_ntfs').addClass('badge badge-danger');
						$('#updated_ntfs').html(result.new_ntf);
						$('#m_updated_ntfs').html(result.new_ntf);
						$('#search').css('margin-left', '13px');
						if (!ntfs_sent) {
							newNotification("buddyBonds", "You Have A New Unseen Notification On buddyBonds",
								"images/color-star-3-48-217610.png");
							ntfs_sent = true;
						}
						$(document).prop('title', ct + " • " + result.new_ntf + " New Notifications");
					}
				}
			}
			setTimeout(check_updates, 5000);
		}
		//ajax like process
		function like(id, ipm) {
			var pid = document.getElementById(id);
			$.ajax({
				type: "POST",
				url: "http://localhost/buddyBonds_backup/scripts/like.php",
				data: {
					"pid": id
				},
				success: function (result) {
					pid.innerHTML = result;
					updt_nlc(id, ipm);
				}
			});
		}
		//to update number of likes and number of comments
		function updt_nlc(pid, ipm) {
			var ipm = document.getElementById(ipm);
			$.ajax({
				type: "POST",
				url: "http://localhost/buddyBonds_backup/scripts/updatenlc.php",
				data: {
					"post_id": pid
				},
				success: function (result) {
					ipm.innerHTML = result;
				}
			});
		}
		//ajax comment process
		function post_comment(pid, cboxid, micran, ubid, spnrimgid) {
			var cboxid = document.getElementById(cboxid);
			var comment = cboxid.value;
			cboxid.value = "";
			$('.pacb').prop('disabled', true);
			var spnrimgid = document.getElementById(spnrimgid);
			spnrimgid.style.display = "inline";
			$.ajax({
				type: "POST",
				url: "http://localhost/buddyBonds_backup/scripts/comment.php",
				data: {
					"post_id": pid,
					"cmnt": comment
				},
				success: function (result) {
					updt_cmnts(pid, micran, ubid);
					updt_cmnt_btn(pid, ubid);
					spnrimgid.style.display = "none";
				}
			});
		}
		//to update comments aftr posting a comment
		function updt_cmnts(pid, micran, ubid) {
			var tobeupdatedmicran = document.getElementById(micran);
			$.ajax({
				type: "POST",
				url: "http://localhost/buddyBonds_backup/scripts/ucmnts.php",
				data: {
					"post_id": pid,
					"micran": micran,
					"ubid": ubid
				},
				success: function (result) {
					tobeupdatedmicran.innerHTML = result;
				}
			});
		}
		//to update vac btn aftr posting cmnt
		function updt_cmnt_btn(pid, ubid) {
			var tobeupdatedubid = document.getElementById(ubid);
			$.ajax({
				type: "POST",
				url: "http://localhost/buddyBonds_backup/scripts/ucmnt_btn.php",
				data: {
					"post_id": pid
				},
				success: function (result) {
					tobeupdatedubid.innerHTML = result;
				}
			});
		}
		//focus in the comment box on click of the comment button
		function focuscb(cboxid) {
			var cboxid = document.getElementById(cboxid);
			cboxid.focus();
		}
		//show post comment button only when input.length>=1
		function spcb(cboxid, btnid) {
			var cboxid = document.getElementById(cboxid);
			var btnid = document.getElementById(btnid);
			var length = cboxid.value.length;
			if (length >= 1) btnid.disabled = false;
			else btnid.disabled = true;
		}
		//to delete the comment 
		function dlt_cmnt(pid, micran, ubid, actid) {
			var choice = confirm("Confirm deletion of comment?");
			if (choice == true) {
				$.ajax({
					type: "POST",
					url: "http://localhost/buddyBonds_backup/scripts/dlt_cmnt.php",
					data: {
						"post_id": pid,
						"act_id": actid
					},
					success: function (result) {
						updt_cmnts(pid, micran, ubid);
						updt_cmnt_btn(pid, ubid);
					}
				});
			}
		}

		function theme(img) {
			$.ajax({
				type: "POST",
				url: "http://localhost/buddyBonds_backup/scripts/set_theme.php",
				data: {
					"imgname": img
				},
				success: function (result) {}
			});
		}
	</script>
	<!--close php conn !-->
	<?php $conn = null; ?>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
		integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
	</script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
		integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
	</script>
</body>

</html>