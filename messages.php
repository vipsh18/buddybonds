<?php
include 'varcsc.php';
date_default_timezone_set($tz);
$uun=base64_decode($_SESSION['username']);
$uid=base64_decode($_SESSION['id']);
function decrypt($msg,$key) {
	$encryption_key=base64_decode($key);
	list($encrypted_data, $iv) = explode('::', base64_decode($msg), 2);
	return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
}
//key
$file=fopen("encoded_key_msg.txt","r") or die("ERROR!");
$key=fread($file,44);
fclose($file);
//change logout time
$cltq=$conn->prepare("UPDATE users SET logout_time='0001-01-01 00:00:00' WHERE id=?");
$cltq->bindParam(1,$uid,PDO::PARAM_INT);
if(!$cltq->execute()) die('Execution failed:('.$cltq->errno.')'.$cltq->error);
$uir=$conn->prepare("SELECT * FROM users WHERE id=?");
$uir->bindParam(1,$uid,PDO::PARAM_INT);
$uir->execute();
$uirow=$uir->fetch(PDO::FETCH_ASSOC);
$upk=$uirow['private_key'];
$upuk=$uirow['public_key'];
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
	<meta name="description" content="buddyBonds web application.Messages page for user.All your chats in one place.Send messages,receive messages.Chat with buddies.Connect and share.Social media website to make buddies,chat,upload pictures,videos and expanding your business."/>
	<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
	<title>Messages</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<link rel="icon" href="images/color-star-3-72-217610.png" type="image/x-icon">
	<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css" integrity="sha384-5sAR7xN1Nv6T6+dT2mhtzEpVJvfS3NScPQTrOxhwjIuvcA67KV2R5Jz6kr4abQsz" crossorigin="anonymous">
	<link rel="stylesheet" href="css/nav_footer.css">
	<link rel="stylesheet" href="css/messages.css">
	<script src="http://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
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
		<li class="nav-item">
			<a href="home.php" id="navhd" data-toggle="tooltip" title="Home" class="text-secondary"><i class="fas fa-home nav_large_icons"></i> buddyBonds</a>
			<a href="messages.php" data-toggle="tooltip" title="Messages" class="big_nav_active_link"><i class="fa fa-envelope faa-shake animated nav_large_icons"></i> <span class="nav_large_texts"><b>Messages</b></span> <?php echo '<span id="updated_msgs"></span>'; ?><span class="sr-only">(current)</span></a>
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
		    <?php if(file_exists($uirow['buddypic'])) echo '<img src="'.$uirow['buddypic'].'" class="rounded-circle img-fluid" style="width:25px;height:25px;object-fit:cover">';
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
			<input type="search" required maxlength="30" minlength="1" placeholder="Search..." id="m_search" name="q" class="mx-auto">
			<div id="m_spnr"><img src="images/spnr.gif" id="m_spnr_img"></div>
			<button type="reset" class="btn btn-sm" id="m_srch_rb"><span id="m_srch_rbt">X</span></button>
		</div>
	<div id="m_suggestions" class="mx-auto"></div>
	</div>
</form>
</div>
<!--navbar for small devices end!-->
<div id="container" class="row">
	<div class="col-lg-3" id="sbtc_div">
		<div class="msg text-center msg_cntr text-success">Select a buddy to chat</div>
		<form id="scf">
			<div class="form-group"><div class="input-group"><i class="fas fa-search" id="search_chat_fas"></i><input type="search" id="search_chat" class="form-control" placeholder="SEARCH BUDDY TO CHAT" title="Search your chats" required><div id="srch_spnr"><img src="images/spnr.gif" id="srch_spnr_img"></div></div></div>
			<div id="srch_sugg"></div>
		</form>
		<div id="sbtc_list">
		<?php 
			$sbtcr=$conn->prepare("SELECT bud_id1,bud_id2 FROM buddies WHERE (bud_id1=:uid OR bud_id2=:uid) AND active='1' ORDER BY buddy_time DESC");
			$sbtcr->bindParam(":uid",$uid,PDO::PARAM_INT);
			$sbtcr->execute();
			if($sbtcr->rowCount()>0) {
				while($sbtcrw=$sbtcr->fetch(PDO::FETCH_ASSOC)) {
					if($sbtcrw['bud_id1']==$uid) $id=$sbtcrw['bud_id2'];
					else if($sbtcrw['bud_id2']==$uid) $id=$sbtcrw['bud_id1'];
					$gunr=$conn->prepare("SELECT username,buddypic FROM users WHERE id=?");
					$gunr->bindParam(1,$id,PDO::PARAM_INT);
					$gunr->execute();
					$gunrw=$gunr->fetch(PDO::FETCH_ASSOC);
					echo '<a href="chat.php?un='.$gunrw['username'].'" class="row bd_row">';
					if(file_exists($gunrw['buddypic'])) echo '<img src="'.$gunrw['buddypic'].'" class="rounded-circle bd_img">';
					else echo '<img src="images/def_buddypic.png" class="rounded-circle bd_img">';
					echo '<span class="bd_un">'.$gunrw['username'].'</span><i class="far fa-comment fas_msg ml-auto" data-toggle="tooltip" title="Message"></i>';
					echo '</a>';
					$gunr=null;
				}
			} else {
				echo '<i class="fas fa-handshake fas_nobuddy"></i>';
				echo '<div class="inner_msg text-center">You need to make a buddy first!</div>';
			}
		?>	
		</div>
	</div>
	<div class="col-lg-6" id="chat_div">
		<div id="chats_hdr">Your chats :</div>
		<div id="chats">
		<?php
		    //check for messages
	        $cfmr=$conn->prepare("SELECT * FROM messages WHERE (bud_id1=? OR bud_id2=?) ORDER BY message_time DESC");
        	$cfmr->bindParam(1,$upk,PDO::PARAM_STR);
        	$cfmr->bindParam(2,$upuk,PDO::PARAM_STR);
        	$cfmr->execute();
        	if($cfmr->rowCount()>0) {
	        	//buddy pk array...to avoid repetitions
		        $bprkarr=array(); 
		        $bpukarr=array();
		        while($cfmrw=$cfmr->fetch(PDO::FETCH_ASSOC)) {
			        //bpk is buddy public key
			        if($cfmrw['bud_id1']==$upk) {
				        //store buddy's keys
			        	$bpuk=$cfmrw['bud_id2'];
			        	foreach($bpukarr as $var1) {
					        if($var1==$bpuk) continue 2;
			        	}
				        array_push($bpukarr,$bpuk);
				        $gunr=$conn->prepare("SELECT username,buddypic,private_key FROM users WHERE public_key=?");
				        $gunr->bindParam(1,$bpuk,PDO::PARAM_STR);
				        $gunr->execute();
				        $gunrw=$gunr->fetch(PDO::FETCH_ASSOC);
				        $bprk=$gunrw['private_key'];
				        foreach ($bprkarr as $var2) {
					        if($var2==$bprk) continue 2;
				        }
			        } else if($cfmrw['bud_id2']==$upuk) {
				        //store buddy's keys
				        $bprk=$cfmrw['bud_id1'];
				        foreach($bprkarr as $var2) {
					        if($var2==$bprk) continue 2;
				        }
				        array_push($bprkarr,$bprk);
				        $gunr=$conn->prepare("SELECT username,buddypic,public_key FROM users WHERE private_key=?");
				        $gunr->bindParam(1,$bprk,PDO::PARAM_STR);
				        $gunr->execute();
				        $gunrw=$gunr->fetch(PDO::FETCH_ASSOC);
				        $bpuk=$gunrw['public_key'];
				        foreach ($bpukarr as $var1) {
					        if($var1==$bpuk) continue 2;
				        }
			        }		 
			        if($gunrw['username']=="") continue;
			        echo '<a href="chat.php?un='.$gunrw['username'].'" class="row bd_row">';
			        if(file_exists($gunrw['buddypic'])) echo '<img src="'.$gunrw['buddypic'].'" class="rounded-circle bd_img_cl">';
			        else echo '<img src="images/def_buddypic.png" class="rounded-circle bd_img_cl">';
		        	$msg=decrypt($cfmrw['message_content'],$key);
			        echo '<span class="bd_un_cl">'.$gunrw['username'].'</span><span class="text-dark bd_last_msg ml-auto">';
			        if($cfmrw['bud_id1']==$upk) {
			        	$seen=$cfmrw['seen'];
				        if($seen=='0') echo '<span class="text-muted"><b>&#10003;</b></span> ';
				        else if($seen=='1') echo '<span class="text-primary"><b>&#10003;</b></span> ';
			        } else if($cfmrw['bud_id2']==$upuk) {
				        echo '<span class="text-muted"> <i class="far fa-comment"></i></span> ';
			        }
			        if($cfmrw['is_file']==0) {
        				if(strlen($msg) > 16) {
					        $msg = substr($msg, 0, 16); 
					        $msg .= "...";
				        }
				        echo ' '.$msg;
			        } else {
			            $path = $msg;
			            if(file_exists($path)) {
			                $file_ext=strtolower(pathinfo($path,PATHINFO_EXTENSION));
			                if(($file_ext == "jpeg") || ($file_ext == "png") || ($file_ext == "jpg") || ($file_ext == "gif")) echo '<i class="fas fa-image"></i> Photo';
			                else if($file_ext=="pdf") echo '<i class="fas fa-file-pdf"></i> PDF';
			                else if($file_ext=="txt") echo '<i class="fas fa-file"></i> Text Document';
			                else if($file_ext=="html") echo '<i class="fas fa-file"></i> HTML Document';
			                else {
			                    $mime_type = mime_content_type($path);
			                    if(($mime_type=="video/webm") || ($mime_type=="video/ogg") || ($mime_type=="video/mp4") || ($file_ext=="3gp") || ($file_ext=="3gpp")) echo '<i class="fas fa-video"></i> Video';
			                    else if(($mime_type=="audio/mpeg") || ($mime_type=="audio/wav") || ($mime_type=="audio/mp3") || ($mime_type=="audio/x-wav")) echo '<i class="fas fa-microphone"></i> Audio';
			                } 
			            } else {
			                echo 'Attachment unavailable!';
			            }
			        }
			        echo '</span>';
			        if($cfmrw['bud_id2']==$upuk) {
				        //get number of new messages
				        $gnonmr=$conn->prepare("SELECT message_id FROM messages WHERE bud_id1=? AND bud_id2=? AND seen='0'");
				        $gnonmr->bindParam(1,$bprk,PDO::PARAM_STR);
				        $gnonmr->bindParam(2,$upuk,PDO::PARAM_STR);
				        $gnonmr->execute();
				        if($gnonmr->rowCount()>0) {
					        echo '<span class="badge badge-danger bd_new_msgs">';
					        echo $gnonmr->rowCount().'</span>';
			        	}
			        }
			        echo '</a>';
			        $gunr=null;
		        }
	        } else {
		        echo '<img src="images/rxn/rxns/Wow-500px.gif" id="wow" class="img-fluid">';
		        echo '<div class="text-primary msg text-center">Why is it so lonely here??<br>Your chats appear here! Start messaging to view your chats.</div>';
	        }
	        $cfmr=null;
		?>
		</div>
	</div>
	<div class="col-lg-3" id="cg_div">
		<div class="msg text-center msg_cntr text-success">Create a new group</div>
		<div id="selected_participants_div"><b>Selected Participants:</b> <span id="selected_participants">1</span><button class="btn btn-sm btn-success" disabled id="create_grp_btn">Create</button></div>
		<input type="text" name="grp_tobemade_name" id="grp_tobemade_name" placeholder="Group Name" maxlength="30" minlength="1" required title="Type Group Name Here">
		<div class="text-center"><input type="file" name="grp_tobemade_pic" id="grp_tobemade_pic"><button class="btn btn-primary btn-sm" id="grp_tobemade_picbtn">Upload Group Picture</button> <span id="grp_tobemade_picname"></span></div>
		<?php 
			//show make buddy msg when user has no buddies! how the fuck he's supposed to create a group and with whom
			$sbtcr=$conn->prepare("SELECT bud_id1,bud_id2 FROM buddies WHERE (bud_id1=:uid OR bud_id2=:uid) AND active='1' ORDER BY buddy_time DESC");
			$sbtcr->bindParam(":uid",$uid,PDO::PARAM_INT);
			$sbtcr->execute();
			if($sbtcr->rowCount()>0) {
				echo '<div class="row bd_row" style="background-color: lavender;padding: 5px;margin-bottom: 2px;" data-toggle="tooltip" title="You Will Be The Admin Of The Group">';
					if(file_exists($uirow['buddypic'])) echo '<img src="'.$uirow['buddypic'].'" class="rounded-circle bd_img">';
					else echo '<img src="images/def_buddypic.png" class="rounded-circle bd_img">';
					echo '<span class="bd_un text-primary">You</span>';
					echo '<i class="fas fa-check-circle ml-auto fas_msg text-success"></i>';
				echo '</div>';
				while($sbtcrw=$sbtcr->fetch(PDO::FETCH_ASSOC)) {
					if($sbtcrw['bud_id1']==$uid) $id=$sbtcrw['bud_id2'];
					else if($sbtcrw['bud_id2']==$uid) $id=$sbtcrw['bud_id1'];
					$gunr=$conn->prepare("SELECT username,buddypic FROM users WHERE id=?");
					$gunr->bindParam(1,$id,PDO::PARAM_INT);
					$gunr->execute();
					$gunrw=$gunr->fetch(PDO::FETCH_ASSOC);
					echo '<div class="row bd_row grp_checkboxes_div" data-toggle="tooltip" title="Add To Group" style="padding: 5px;margin-bottom: 2px;">';
						if(file_exists($gunrw['buddypic'])) echo '<img src="'.$gunrw['buddypic'].'" class="rounded-circle bd_img">';
						else echo '<img src="images/def_buddypic.png" class="rounded-circle bd_img">';
						echo '<span class="bd_un text-primary">'.$gunrw['username'].'</span>';
						echo '<input type="checkbox" name="sel_group_mems" class="grp_checkboxes">';
						echo '<span class="text-success ml-auto fas_msg grp_checkboxes_label"><i class="far fa-circle"></i></span>';
					echo '</div>';	
					$gunr=null;
				}
			} else {
				echo '<i class="fas fa-user-plus fas_nobuddy"></i>';
				echo '<div class="inner_msg text-center">You need to make a buddy first!</div>';
			}
			$sbtcr=null;
		?>
	</div>
</div>
<!-- inner navbar for small devices....at bottom!-->
<div class="d-lg-none">
<nav class="navbar fixed-bottom" id="infm"><!-- inner nav for mobile !-->
	<ul class="navbar-nav list-inline text-center" id="infm_ul">
		<li class="list-inline-item bottom_nav_division">
			<a class="nav-link" href="profile.php?un=<?php echo $uun; ?>">
				<?php 
					if(file_exists($uirow['buddypic'])) echo '<img src="'.$uirow['buddypic'].'" class="rounded-circle img-fluid m_navpic">';
					else echo '<img src="images/def_buddypic.png" class="rounded-circle img-fluid m_navpic">';
					$uir=null;
				?>
			</a>
		</li>
		<li class="list-inline-item active bottom_nav_division">
			<a class="nav-link" href="home.php"><i class="fa fa-home"></i></a>
		</li>
		<li class="list-inline-item bottom_nav_division">
			<a class="nav-link" href="messages.php" style="color:blue"><i class="fa fa-envelope faa-shake animated"></i>
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
</div>
<!-- scripts start !-->
<script src="js/nav.js"></script>
<script src="js/messages.js"></script>
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