<?php
include 'varcsc.php';
$tz = new DateTimeZone($tz);
$dt = new DateTime();
$dt->setTimezone($tz);
//get user's username
$getun=test_input($_GET['un']);
$uun=base64_decode($_SESSION['username']);
$uid=base64_decode($_SESSION['id']);
if(!$getun) {
	$conn=null;
	header("Location:http://localhost/buddyBonds_backup/profile.php?un=".$uun);
	exit();
}
//change logout time
$cltq=$conn->prepare("UPDATE users SET logout_time='0001-01-01 00:00:00' WHERE id=?");
$cltq->bindParam(1,$uid,PDO::PARAM_INT);
if(!$cltq->execute()) die('Execution failed:('.$cltq->errno.')'.$cltq->error);
//get info about the user....check if it exists
$uqr=$conn->prepare("SELECT * FROM users WHERE username=?");
$uqr->bindParam(1,$getun,PDO::PARAM_STR);
$uqr->execute();
$uqrw=$uqr->fetch(PDO::FETCH_ASSOC);
$id=$uqrw['id'];
$uiqr=$conn->prepare("SELECT * FROM users WHERE id=?");
$uiqr->bindParam(1,$uid,PDO::PARAM_INT);
$uiqr->execute();
$uiqrow=$uiqr->fetch(PDO::FETCH_ASSOC);
function test_input($data) {
	$data=trim($data);
	$data=stripslashes($data);
	$data=htmlspecialchars($data);
	$data=strip_tags($data);
	return $data;
}	
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
$picErr=$picloadErr="";
if($_SERVER['REQUEST_METHOD']=='POST') {
	if(!file_exists('uploads/'.$uun."/buddypic_uploads/")) {
		mkdir('uploads/'.$uun.'/buddypic_uploads/', 0777, true);
		$index_file_useless = fopen('uploads/'.$uun.'/buddypic_uploads/index.html', 'w');
	    fclose($index_file_useless);
	}
	$ipaddr=get_client_ip();
	$ipaddr=md5($ipaddr);
	$target_dir="uploads/".$uun."/buddypic_uploads/".$ipaddr.microtime().rand();
	$org_file=basename($_FILES['buddypic']['name']);
	$file_ext=strtolower(pathinfo($org_file,PATHINFO_EXTENSION));
	$target_file=$target_dir.".".$file_ext;
	$uploadOk=1;
	$FileType=strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
   	if(file_exists($target_file)) {
    	$picErr="Sorry, file already exists!";
    	$uploadOk = 0;
	}
	// Check file size
	if($_FILES["buddypic"]["size"] > 20000000) { // this size is 20 MB
    	$picErr="Sorry, the file is too large!";
    	$uploadOk = 0;
	}	 
	// Allow certain file formats
	$allowed = array('jpg', 'png', 'jpeg');
	if(!in_array($file_ext,$allowed)) {
    	$picErr="Sorry, the file format is invalid! Only jpg/png/jpeg are allowed for images.";
    	$uploadOk = 0;
	} 
	// Check if $uploadOk is set to 0 by an error
	if($uploadOk == 0) {
    	$picloadErr="Sorry, your file was not uploaded!";
		// if everything is ok, try to upload file
	}
	else {
    	if(move_uploaded_file($_FILES["buddypic"]["tmp_name"], $target_file)) $pic = test_input($target_file);
   		else $picloadErr="Sorry,there was an error uploading the file!";
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
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'UA-105985024-2');
    </script>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
	<meta name="referrer" content="origin-when-crossorigin"/>
	<meta name="author" content="Vipul Sharma"/>
	<meta name="description" content="buddyBonds web application.Profile page for <?php echo $uqrw['username']; ?> on buddyBonds.com.<?php echo $uqrw['nobuddies']; ?>Customize your profile.View other profiles.Delete and upload posts.Connect and share.Social media website to make buddies,chat,upload pictures,videos and expanding your business."/>
	<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
	<title><?php if($uqr->rowCount()<=0) {
		echo 'User not found!';
	} else {
		echo $uqrw['fullname'].' ('.$getun.') &#8226; buddyBonds';
	} ?></title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<link rel="icon" href="images/color-star-3-72-217610.png" type="image/x-icon">
	<link rel="icon" href="images/color-star-3-57-217610.png" type="image/x-icon">
	<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css" integrity="sha384-5sAR7xN1Nv6T6+dT2mhtzEpVJvfS3NScPQTrOxhwjIuvcA67KV2R5Jz6kr4abQsz" crossorigin="anonymous">
	<link rel="stylesheet" href="css/nav_footer.css">
	<link rel="stylesheet" href="css/profile.css">
	<script src="http://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
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
				<input type="search" required maxlength="30" minlength="1" placeholder="Search..." title="Search buddies or business profiles" id="search" name="q">
				<div id="spnr"><img src="http://localhost/buddyBonds_backup/images/spnr.gif" id="spnr_img"></div>
				<button type="reset" class="btn btn-sm" id="srch_rb"><span id="srch_rbt">X</span></button>
			</div>
		</div>
	</form>
	<ul class="navbar-nav list-inline" id="opts">
        <li class="list-inline-item">
		    <div id="nav_profile_link_div"><a class="nav-link text-secondary" style="margin-right:0" href="profile.php?un=<?php echo $uun; ?>" data-toggle="tooltip" title="Your Profile" id="nav_profile_link">
		    <script>
		        var un ="<?php echo $getun; ?>";
		        var uun = "<?php echo $uun; ?>";
		        if(un === uun) {
		            $('#nav_profile_link').removeClass('text-secondary');
		            $('#nav_profile_link').addClass('big_nav_active_link');
		        }
		    </script>
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
			<a href="#top" id="m_navhd">buddyBonds</a>
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
<!-- navbar for small devices end!-->
<?php 
	if($_SERVER['REQUEST_METHOD']=='POST') {
	    if((!$picErr) && (!$picloadErr)) {
	        $budr=$conn->prepare("UPDATE users SET buddypic='$pic' WHERE id=?");
		    $budr->bindParam(1,$uid,PDO::PARAM_INT);
		    if($budr->execute()) echo '<div class="alert alert-info alert-dismissable text-center"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="text-success"><b>Upload Successful!</b></span><br>&#10084; Your buddy picture was uploaded successfully!</div>';
	        else echo '<div class="alert alert-info alert-dismissable text-center"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="text-danger"><b>Upload failed!</b></span><br>&#10084; Your buddy picture could not be updated. Please try again!</div>';
	    	$budr=null;
	    } else echo '<div class="alert alert-info alert-dismissable text-center"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="text-danger"><b>Upload failed!</b></span><br>&#10084; Your buddy picture could not be updated. Please try again!</div>';
	}
?>
<!-- show user info small and med devices!-->
<?php 
if($uqr->rowCount()<=0) {
	//unfd means user not found details
	echo '<div class="text-center">';
		echo '<div class="msg text-danger">We did not find any user named <b>'.$getun.'</b> or like <b>'.$getun.'</b> on buddyBonds !</div>';
		//get related searches
		$unfun="%".$getun."%";
		$grsr=$conn->prepare("SELECT * FROM users WHERE username LIKE ?");
		$grsr->bindParam(1,$unfun,PDO::PARAM_STR);
		$grsr->execute();
		if($grsr->rowCount()>0) {
			echo '<div class="unfd">Here\'s what we found in our searches:<br>';
			while($grsrw=$grsr->fetch(PDO::FETCH_ASSOC)) echo '<a href="profile.php?un='.$grsrw['username'].'"><b>'.$grsrw['username'].'</b></a><br>';
			echo '</div>';
			$grsr=null;
		} else {
			$grsr=$conn->prepare("SELECT * FROM users WHERE (fullname LIKE ? OR username=?)");
			$grsr->bindParam(1,$unfun,PDO::PARAM_STR);
			$grsr->bindParam(2,$getun,PDO::PARAM_STR);
			$grsr->execute();
			if($grsr->rowCount()>0) {
				echo '<div class="unfd">Here\'s what we found in our searches:<br>';
				while($grsrw=$grsr->fetch(PDO::FETCH_ASSOC)) echo '<a href="profile.php?un='.$grsrw['username'].'"><b>'.$grsrw['username'].'</b></a><br>';
				echo '</div>';
				$grsr=null;
			} else {
				//lets explode using _
				$sp=explode("_",$getun);
				$x=count($sp);
				if($x>0) {
					echo '<div class="unfd">Here\'s what we found in our searches:<br>';
					for($i=0;$i<$x;$i++) {
						$unfun="%".$sp[$i]."%";
						$grsr=$conn->prepare("SELECT * FROM users WHERE (username LIKE ? OR fullname LIKE ?)");
						$grsr->bindParam(1,$unfun,PDO::PARAM_STR);
						$grsr->bindParam(2,$unfun,PDO::PARAM_STR);
						$grsr->execute();
						if($grsr->rowCount()>0) {
							while($grsrw=$grsr->fetch(PDO::FETCH_ASSOC)) echo '<a href="profile.php?un='.$grsrw['username'].'"><b>'.$grsrw['username'].'</b></a><br>';
							break;
						}
					}
					echo '</div>';
					$grsr=null;
				} else {
					$sp=explode(" ",$getun);
					$x=count($sp);
					if($x>0) {
						echo '<div class="unfd">Here\'s what we found in our searches:<br>';
						for($i=0;$i<$x;$i++) {
							$unfun="%".$sp[$i]."%";
							$grsr=$conn->prepare("SELECT * FROM users WHERE (username LIKE ? OR fullname LIKE ?)");
							$grsr->bindParam(1,$unfun,PDO::PARAM_STR);
							$grsr->bindParam(2,$unfun,PDO::PARAM_STR);
							$grsr->execute();
							if($grsr->rowCount()>0) {
								while($grsrw=$grsr->fetch(PDO::FETCH_ASSOC)) echo '<a href="profile.php?un='.$grsrw['username'].'"><b>'.$grsrw['username'].'</b></a><br>';
								break;
							} 
						}
						echo '</div>';
						$grsr=null;
					} else {
						$sp=str_split($getun);
						$x=count($sp);
						if($x>0) {
							echo '<div class="unfd">Here\'s what we found in our searches:<br>';
							for($i=0;$i<$x;$i++) {
								$unfun="%".$sp[$i]."%";
								$grsr=$conn->prepare("SELECT * FROM users WHERE username LIKE ?");
								$grsr->bindParam(1,$unfun,PDO::PARAM_STR);
								if($grsr->rowCount()>0) {
									while($grsrw=$grsr->fetch(PDO::FETCH_ASSOC)) echo '<a href="profile.php?un='.$grsrw['username'].'"><b>'.$grsrw['username'].'</b></a><br>';
									break;
								}
							}
							echo '</div>';
						}
						$grsr=null;
					}
				}
			}
		}
		echo '<div class="unfd">Check your spelling and then try again if you are searching for someone.</div>';
	echo '</div>';
} else {
?>
<!-- show user info large devices !-->
<div id="profile_area">
<?php 
	$uqr=null;
	$uqr=$conn->prepare("SELECT * FROM users WHERE username=?");
	$uqr->bindParam(1,$getun,PDO::PARAM_STR);
	$uqr->execute();
	$uqrw=$uqr->fetch(PDO::FETCH_ASSOC);
	if(file_exists($uqrw['buddypic'])) {
		echo '<span id="u_bp"><img src="'.$uqrw['buddypic'].'" class="ubs rounded-circle" alt="';
		if($getun==$uun) echo 'Your buddy picture';
		else echo $getun.' \'s buddy picture';
		echo '"></span>';
	} else echo '<span id="u_bp"><img src="images/def_buddypic.png" class="ubs rounded-circle" alt="Default buddy picture"></span>';
	if($getun==$uun) {
		echo '<span class="text-primary" id="load_buddypic_btn" data-toggle="tooltip" title="Click to update your buddy picture" data-placement="bottom"><i class="fas fa-camera"></i></span>';
		echo '<a href="" id="ufn" class="ttl" data-toggle="tooltip" title="Refresh Page"><b>'.$uqrw['fullname'].'</b></a>';
	} else echo '<a href="" id="ufnsevp" class="ttl" data-toggle="tooltip" title="Refresh Page"><b>'.$uqrw['fullname'].'</b></a>';
	$uqr=null;
	echo '<br><span id="uun" class="text-muted"><b>@ '.$getun.'</b></span>';
	//btns rw for own profile and when visiting someone;s else's profile
	echo '<div id="profile_btns">';
	if($getun==$uun) {
		echo '<div id="btns_rw">';
			echo '<a href="edit_profile.php" class="btn btn-outline-success btns_rw_btns" role="button" id="edit_link"><i class="fas fa-edit"></i> Edit Profile <i class="fas fa-pencil-alt"></i></a>';
			echo '<button class="btn btn-outline-primary btns_rw_btns" id="smlr_btn"><i class="fas fa-users"></i> Similar accounts <i class="fas fa-laptop"></i></button>';
		echo '</div>';
	} else {
		echo '<div id="btns_rw_vsep">';
			//check if buddies already
			$cibr=$conn->prepare("SELECT * FROM buddies WHERE (bud_id1=:uid OR bud_id1=:id) AND (bud_id2=:uid OR bud_id2=:id) AND active='1'");
			$cibr->bindParam(":uid",$uid,PDO::PARAM_INT);
			$cibr->bindParam(":id",$id,PDO::PARAM_INT);
			$cibr->execute();
			if($cibr->rowCount()>0) {
				echo '<span id="buddy_btn"><button class="btns_rw_btns btn btn-outline-success btn" onclick="unbuddy('.$id.')"><i class="fas fa-handshake" id="fa_bdy_hand"></i> Buddies <i class="fas fa-check" id="fa_bdy_check"></i></button></span>';
				echo '<button class="btn btn-outline-danger btns_rw_btns" id="see_mtl" onclick="see_mutual()"><i class="fas fa-heart" id="fa_bdy_heart"></i> Mutual</button>';
			} else {
				$cibr1=$conn->prepare("SELECT * FROM buddies WHERE bud_id1=? AND bud_id2=? AND active='0'");
				$cibr1->bindParam(1,$uid,PDO::PARAM_INT);
				$cibr1->bindParam(2,$id,PDO::PARAM_INT);
				$cibr1->execute();
				if($cibr1->rowCount()>0) {
					echo '<span id="buddy_btn"><button class="btns_rw_btns btn-outline-success btn" onclick="buddy('.$id.')"><i class="fas fa-heart" id="fa_bdy_heart"></i> Buddy requested <i class="fas fa-check"></i></button></span>';
				} else {
					$cibr2=$conn->prepare("SELECT * FROM buddies WHERE bud_id1=? AND bud_id2=? AND active='0'");
					$cibr2->bindParam(1,$id,PDO::PARAM_INT);
					$cibr2->bindParam(2,$uid,PDO::PARAM_INT);
					$cibr2->execute();
					if($cibr2->rowCount()>0) {
						echo '<span id="buddy_btn"><button class="btns_rw_btns btn-outline-success btn" onclick="buddy('.$id.')"><i class="fas fa-plus"></i> Confirm Request <i class="fas fa-user" id="fa_bdy_user1"></i></button></span>';
						echo '<span id="delete_bdreq_btn"><button class="btns_rw_btns btn-outline-danger btn" onclick="delete_bdreq('.$id.')"><i class="fas fa-trash-alt"></i> Delete request <i class="fas fa-user" id="fa_bdy_user2"></i></button></span>';
					} else {
						echo '<span id="buddy_btn"><button class="btns_rw_btns btn-outline-success btn" onclick="buddy('.$id.')"><i class="fas fa-user" id="fa_bdy_user1"></i> Add Buddy <i class="fas fa-plus"></i></button></span>';
					}
				}
			}
			$cibr1=$cibr2=null;
			echo '<button class="btn btn-outline-primary btns_rw_btns" id="smlr_btn"><i class="fas fa-users" id="fa_bdy_users"></i> Similar accounts <i class="fas fa-laptop" id="fa_bdy_laptop"></i></button>';
		echo '</div>';
	}
	echo '</div>';
	//show description row in own profile and when visiting someone else's profile
	if($getun==$uun) {
		echo '<div id="description_rw">';
			//upload buddy picture form
			echo '<form id="cbp_form" action="profile.php?un='.$uun.'" method="POST" enctype="multipart/form-data">Upload buddy picture <input type="file" name="buddypic" id="buddypic" required><button class="btn btn-warning" type="submit">Upload</button></form><span id="picErr" class="error">'.$picErr.'</span><span id="picloadErr" class="error">'.$picloadErr.'</span>';
			echo '<textarea id="description" onkeyup="textAreaAdjust(this)" style="overflow:hidden" placeholder="Give your intro here..." disabled spellcheck="false">'.base64_decode($uqrw['description']).'</textarea>';
			echo '<span class="text-muted" data-toggle="tooltip" title="Edit description" id="ed_btn"><i class="fas fa-edit"></i></span>';
			echo '<img src="images/spnr.gif" id="spnr_img">';
			echo '<button class="btn btn-sm text-white" id="save_descp_btn" disabled>Save</button>';
		echo '</div>';
	} else {
		echo '<textarea id="dwvsep" style="overflow:hidden" disabled>'.base64_decode($uqrw['description']).'</textarea>';
	}
	//modal for modals like similar accounts etc
	echo '<div id="smlr_modal" class="modal">';
		echo '<span class="close">&times;</span>';
		echo '<div id="smlr_pages" class="text-center"><div id="smlr_spnr"><img src="images/spnr.gif" id="smlr_spnr_img" class="img-fluid"></div></div>';
	echo '</div>';
	if($getun==$uun) {
		$cibr=$conn->prepare("SELECT * FROM users");
		$cibr->execute();
	}
?>
</div>
<?php
	echo '<div id="update_access">';
	if($cibr->rowCount()<=0) {
		echo '<div class="msg text-center">You need to be buddies with '.$getun.' to view more details about them.</div>';
	} else {
	echo '<div id="navrow" class="row">';
		//get number of posts
		$upqr=$conn->prepare("SELECT * FROM posts WHERE user_id=?");
		$upqr->bindParam(1,$id,PDO::PARAM_INT);
		$upqr->execute();
		echo '<div class="col-lg-6 text-center chnl_links" id="posts"><a href="javascript:void(0)" id="posts_btn" data-toggle="tooltip" title="Shows ';
		if($getun==$uun) echo 'your';
		else echo $getun.'\'s';
		echo ' posts"><b><i class="fas fa-list-ul"></i> POSTS <span class="badge text-light" id="postsno">'.$upqr->rowCount().'</span></b></a></div>';
		echo '<div class="col-lg-6 text-center chnl_links" id="buddies"><a href="javascript:void(0)" id="buddies_btn" data-toggle="tooltip" title="Shows ';
		if($getun==$uun) echo 'your';
		else echo $getun.'\'s';
		echo ' buddies"><b><i class="fas fa-handshake"></i> BUDDIES <span class="badge text-light" id="buddiesno">'.$uqrw['nobuddies'].'</span></b></a></div>';
	echo '</div>';
	echo '<div id="navrow_sm">';
		echo '<button class="btn btn-light chnl_links text-center" id="posts_btn_sm"><a href="javascript:void(0)" id="posts_btn_sm_link"><i class="fas fa-list-ul"></i> <span class="badge text-light" id="postsno_sm">'.$upqr->rowCount().'</span></a></button>';
		$upqr=null;
		echo '<button class="btn btn-light chnl_links text-center" id="buddies_btn_sm"><a href="javascript:void(0)" id="buddies_btn_sm_link"><i class="fas fa-handshake"></i> <span class="badge text-light" id="buddiesno_sm">'.$uqrw['nobuddies'].'</span></a></button>';
		$gnubr=null;
	echo '</div>';
?>
<div id="pro_spnr"><img src="images/spnr.gif" id="pro_spnr_img"></div>
<!-- posts area!-->
<!-- show user's posts!-->
<div id="posts_area">
	<?php 
		//get the user's posts
		$pr=$conn->prepare("SELECT * FROM posts WHERE user_id=? ORDER BY post_time DESC");
		$pr->bindParam(1,$id,PDO::PARAM_INT);
		$pr->execute();
		if($pr->rowCount()>0) {
			echo '<div class="flex-container">';
				while($prw=$pr->fetch(PDO::FETCH_ASSOC)) {
					//random id for image 
					$rifi=$prw['post_id'];
					$extension=strtolower(pathinfo($prw['post_content'],PATHINFO_EXTENSION));
					if(($extension=="jpg") || ($extension=="jpeg") || ($extension=="png")) {
						?>
						<img src="<?php echo $prw['post_content']; ?>" class="inner-image rounded" onclick="modal('<?php echo $rifi; ?>')" id="<?php echo $rifi; ?>" alt="Uploaded picture">
						<?php
					}
				}
			$pr=null;
			echo '</div>';
			echo '<div id="myModal" class="modal">';
				echo '<span class="close">&times;</span>';
				echo '<div class="row" id="modal_row">';
					echo '<div class="col-lg-6 mx-auto" id="modal_content_col"><img class="modal-content img-fluid rounded" id="postpic"></div>';
					echo '<div class="col-lg-6" id="modal_details_col"><div id="modal_details"><div id="lp_spnr" class="mx-auto"><img id="lp_spnr_img" src="images/spnr.gif"></div></div></div>';
				echo '</div>';
			echo '</div>';
		} else {
			echo '<div class="text-center msg">';
				if($uid==$id) echo 'You have';
				else echo $getun.' has';
				echo ' not uploaded any posts yet!';
			echo '</div>';
		}
	?>
</div>
<?php
	//end the else part that updates update_access
	}
	//end access update div
	echo '</div>';
//end the else part of loop that checks if user exists or not
}
?>
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
		<li class="list-inline-item active bottom_nav_division">
			<a class="nav-link" href="home.php"><i class="fa fa-home"></i></a>
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
			<a class="nav-link" id="m_sbt" href="javascript:void(0)"><i class="fas fa-search"></i></a>
		</li>	
	</ul>
</nav>
<div id="suggestions1"></div>
</div>
<!-- scripts start !-->
<script src="js/profile.js"></script>
<script src="js/nav.js"></script>
<script>
$(function() {
    check_updates();
    profile_updates();
    var color;
    var uid = "<?php echo $uid; ?>";
    var id = "<?php echo $id; ?>";
    if(uid == id) {
        $.ajax({
            type:"GET",
            url:"http://localhost/buddyBonds_backup/scripts/get_theme.php",
            data:{
                "theme_id":uid
            }
        }).done(function(colorui) {
            $('#ufn').css('color', colorui);
            color = colorui;
            change_theme(colorui); 
        });
    } else {
        $.ajax({
            type:"GET",
            url:"http://localhost/buddyBonds_backup/scripts/get_theme.php",
            data:{
                "theme_id":id
            }
        }).done(function(colorui) {
            $('#ufnsevp').css('color', colorui);
            color = colorui;
            change_theme(colorui); 
        });
    }
    function change_theme(color) {
        $('#postsno').css('background-color', color);
        $('#postsno_sm').css('background-color', color);
        $('#buddiesno').css('background-color', color);
        $('#buddiesno_sm').css('background-color', color);
        $('#save_descp_btn').css('background-color', color);
        $('#posts_btn').css('color', color);
        $('#buddies_btn').css('color', color);
        $('#posts_btn_sm_link').css('color', color);
        $('#buddies_btn_sm_link').css('color', color);
        $('#posts').css('border-bottom','2px solid ' + color);
        $('#posts_btn_sm').css('border-bottom','2px solid ' + color);
    }
    $('#smlr_btn').click(function() {
        $('#smlr_spnr').show();
	    var userid='<?php echo $id; ?>';
	    $('#smlr_modal').css('display','block');
	    $('#nav').fadeOut();
	    $('#m_nav').fadeOut();
	    $('#infm').fadeOut();
	    $.ajax({
		    type:"POST",
		    url:"http://localhost/buddyBonds_backup/scripts/similar_accounts.php",
		    data:{
			    "user_id":userid	
		    },
		    success:function(result) {
			    $('#smlr_pages').html(result);
			    $('#smlr_spnr').hide();
		    }	
	    });
    }); 
	//show user's posts
	$('#posts_btn').on('click',function() {
		var id='<?php echo $id; ?>';
		$('#pro_spnr').show();
		$('#posts').css('border-bottom','2px solid ' + color);
		$('#buddies').css('border-bottom','0px');
		$.ajax({
			type:"POST",
			url:"http://localhost/buddyBonds_backup/scripts/user_posts.php",
			data:{
				"id":id
			},
			success:function(result) {
				$('#pro_spnr').hide();
				$('#posts_area').html(result);	
			}
		});
	});
	//show user's posts small
	$('#posts_btn_sm').on('click',function() {
		var id='<?php echo $id; ?>';
		$('#pro_spnr').show();
		$('#posts_btn_sm').css('border-bottom','2px solid ' + color);
		$('#buddies_btn_sm').css('border-bottom','0px');
		$.ajax({
			type:"POST",
			url:"http://localhost/buddyBonds_backup/scripts/user_posts.php",
			data:{
				"id":id
			},
			success:function(result) {
				$('#pro_spnr').hide();
				$('#posts_area').html(result);	
			}
		});
	});
	//show user's buddies
	$('#buddies_btn').on('click',function() {
		var id='<?php echo $id; ?>';
		$('#pro_spnr').show();
		$('#buddies').css('border-bottom','2px solid ' + color);
		$('#posts').css('border-bottom','0px');
		$.ajax({
			type:"POST",
			url:"http://localhost/buddyBonds_backup/scripts/user_buddy_list.php",
			data:{
				"id":id
			},
			success:function(result) {
				$('#pro_spnr').hide();
				$('#posts_area').html(result);
			}
		});
	});
	//show user's buddies small
	$('#buddies_btn_sm').on('click',function() {
		var id='<?php echo $id; ?>';
		$('#pro_spnr').show();
		$('#buddies_btn_sm').css('border-bottom','2px solid ' + color);
		$('#posts_btn_sm').css('border-bottom','0px');
		$.ajax({
			type:"POST",
			url:"http://localhost/buddyBonds_backup/scripts/user_buddy_list.php",
			data:{
				"id":id
			},
			success:function(result) {
				$('#pro_spnr').hide();
				$('#posts_area').html(result);
			}
		});
	});
	//click original file button on fake click
	$('#load_buddypic_btn').on('click',function() {
		var bp=$('.ubs').attr('src');
		if(bp=="images/def_buddypic.png") {
			$('#buddypic').click();
		} else {
			$.alert({
				title:"Select your choice",
				content:"Choose one of the options given below",
				type:'purple',
       	 		animation:'RotateX',
        		animationBounce:2.5,
        		backgroundDismiss:true, 
        		closeAnimation:'RotateY',
        		theme:'modern',
        		buttons:{
        			rmvbdp:{
        				text:"Remove picture",
        				btnClass:"btn-primary",
        				action:function() {
        					$.ajax({
        						type:"POST",
        						url:"http://localhost/buddyBonds_backup/scripts/remove_bdp.php",
        						success:function(result) {
        							$('#u_bp').html(result);
        						}
        					});
        				}
        			},
        			chngbdp:{
        				text:"Change picture",
        				btnClass:"btn-success",
        				action:function() {
        					$('#buddypic').click();
        				}
        			},	
        		}
			});
		}
	});
});
var updateWorker = new Worker("js/update_worker.js");
var pid = "<?php echo $id; ?>";
var uid = "<?php echo $uid; ?>";
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
//profile updates
function profile_updates() {
    $.ajax({
       type:"GET",
       url:"http://localhost/buddyBonds_backup/scripts/profile_updates.php",
       dataType:"json",
       data: {
           "pid": pid 
       }
    }).done(function(result) {
        $('#postsno').html(result.posts);
		$('#postsno_sm').html(result.posts);
		$('#buddiesno').html(result.buddies);
		$('#buddiesno_sm').html(result.buddies);
		if(pid != uid) $('#profile_btns').html(result.btns);
    }).fail(function(error) {
        console.log(Error(error));
    }).always(function(result) {
        setTimeout(profile_updates, 9000);
    });
}
//like pic shit
function like(id) {
    $.ajax({
	    type:"POST",
		url:"http://localhost/buddyBonds_backup/scripts/modal_like.php",
		data: {
		    "post_id":id
	    }
	}).done(function(result) {
	    $('#like_btn').html(result);
	}).fail(function(error) {
	    console.log(Error(error));
	}).always(function(result) {
	    updt_nlc(id);
	});
}
//modal open/close
function modal(pid) {
	$('#suggestions').hide();
	$('#m_suggestions').hide();
	$('#lp_spnr').show();
	var postid=$('#'+pid).attr('src');
	$('#myModal').css('display','block');
	$('#postpic').attr('src',postid);
	$('#nav').fadeOut();
	$('#m_nav').fadeOut();
	$('#infm').fadeOut();
	$.ajax({
		type:"POST",
		url:"http://localhost/buddyBonds_backup/scripts/lpd.php",
		data:{
			"post_id":pid
		},
		success:function(result) {
			$('#modal_details').html(result);
			$('#lp_spnr').hide();
		}
	});
}
//ajax buddy request send process
function buddy(kid) {
	var dbrb=document.getElementById('delete_bdreq_btn');
	$.ajax({
		type:"POST",
		url:"http://localhost/buddyBonds_backup/scripts/buddy.php",
		data: {
			"bid":kid
		},
		success:function(result) {
			if(dbrb) $('#delete_bdreq_btn').hide();
			$('#buddy_btn').html(result);
			update_access(id,"bud");
		}
	});
}
//unbuddy some1
function unbuddy(id) {
    var choice = confirm("Confirm unbuddy? However the user may not get any notification of this action but you may have to request back that person again in the future.");
    if(choice == true) {
        $.ajax({
			type:"POST",
			url:"http://localhost/buddyBonds_backup/scripts/buddy.php",
			data: {
				"bid":id
			},	
			success:function(result) {
				$('#buddy_btn').html(result);
				$('#see_mtl').hide();
				update_access(id,"del");
			}
		});
    }
}
//delete buddy from ur buddylist
function dlt_bdy(id) {
    var choice = confirm("Confirm unbuddy? However the user may not get any notification of this action but you may have to request back that person again in the future.");
    if(choice == true) {
        $.ajax({
			type:"POST",
			url:"http://localhost/buddyBonds_backup/scripts/delete_buddy.php",
			data: {
				"id":id
			},	
			success:function(result) {
				$('#posts_area').html(result);
			}
		});
    }
}
//to update number of likes
function updt_nlc(id) {
	$.ajax({
		type:"POST",
		url:"http://localhost/buddyBonds_backup/scripts/updatenlc.php",
		data:{
			"post_id":id
		},
		success:function(result) {
			$('#nol').html(result);
		}
	});
}
//post comment btn on click
function comment(postid) {
	$('#cmnt_spnr').show();
	var cmnt=$('#cmnt_box').val();
	$.ajax({
		type:"POST",
		url:"http://localhost/buddyBonds_backup/scripts/comment.php",
		data:{
			"post_id":postid,
			"cmnt":cmnt
		},
		success:function(result) {
			sacb(postid);
			$('#cmnt_box').val("");
			$('#pycb').prop('disabled',true);
			$('#cmnt_spnr').hide();
			update_sacb(postid);
		}
	});
}
//show all comments 
function sacb(postid) {
	$.ajax({
		type:"POST",
		url:"http://localhost/buddyBonds_backup/scripts/sac.php",
		data:{
			"post_id":postid
		},
		success:function(result) {
			$('#cmnts_sec').html(result);
		}
	});
}
//update sacb 
function update_sacb(postid) {
	$.ajax({
		type:"POST",
		url:"http://localhost/buddyBonds_backup/scripts/updt_sacb.php",
		data:{
			"post_id":postid
		},
		success:function(result) {
			$('#sacb_div').html(result);
		}
	});
}
//delete comment
function dlt_cmnt(postid,actid) {
    var choice = confirm("Confirm deletion of comment? People may have seen it already.");
    if(choice == true) {
        $.ajax({
			type:"POST",
			url:"http://localhost/buddyBonds_backup/scripts/dlt_cmnt.php",
			data:{
				"post_id":postid,
				"act_id":actid
			},
			success:function(result) {
				sacb(postid);
				update_sacb(postid);
			}
		});
    }
}
//to delete post 
function dlt_post(postid) {
    var choice = confirm("Confirm deletion of post? It may still be present in other places.");
    if(choice == true) {
        $.ajax({
       		type:"POST",
       		url:"http://localhost/buddyBonds_backup/scripts/delete_post.php",
       		data:{
       			"post_id":postid
       		},
       		success:function(result) {
       			$('.close').click();
       			$('#posts_btn').click();
       			$('#posts_btn_sm').click();
       		}
       	});
    }
}
function update_access(id,str) {
	$.ajax({
		type:"POST",
		url:"http://localhost/buddyBonds_backup/scripts/update_access.php",
		data:{
			"id":id,
			"status":str
		},
		success:function(result) {
			$('#update_access').html(result);
		}
	});
}
//see mutual
function see_mutual() {
	$('#smlr_spnr').show();
	var id='<?php echo $id; ?>';
	$('#smlr_modal').css('display','block');
	$('#nav').fadeOut();
	$('#m_nav').fadeOut();
	$('#infm').fadeOut();
	$.ajax({
		type:"POST",
		url:"http://localhost/buddyBonds_backup/scripts/see_mutual.php",
		data:{
			"id":id	
		},
		success:function(result) {
			$('#smlr_pages').html(result);
			$('#smlr_spnr').hide();
		}	
	});
}
//delete buddy req
function delete_bdreq() {
	//nadbid means not a damned buddy id cause u deleted that req
	var id='<?php echo $id; ?>';
	$.ajax({
		type:"POST",
		url:"http://localhost/buddyBonds_backup/scripts/delete_bdreq.php",
		data:{
			"nadbid":id
		},
		success:function(result) {
			$('#delete_bdreq_btn').hide();
			$('#buddy_btn').html(result);
		}
	});
}
//similar accounts
function similar_accounts() {
    $('#smlr_spnr').show();
	var userid='<?php echo $id; ?>';
	$('#smlr_modal').css('display','block');
	$('#nav').fadeOut();
	$('#m_nav').fadeOut();
	$('#infm').fadeOut();
	$.ajax({
		type:"POST",
		url:"http://localhost/buddyBonds_backup/scripts/similar_accounts.php",
		data:{
			"user_id":userid	
		},
		success:function(result) {
			$('#smlr_pages').html(result);
			$('#smlr_spnr').hide();
	    }	
    });
}
//
function share_btn(pid) {
    var link = "http://localhost/buddyBonds_backup/post.php?p=" + pid;
    var tempInput = document.createElement("input");
    tempInput.style = "position: absolute; left: -1000px; top: -1000px";
    tempInput.value = link;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand("copy");
    document.body.removeChild(tempInput);
}
</script>
<!--close php conn !-->
<?php $conn = null; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>