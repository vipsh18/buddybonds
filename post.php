<?php
include 'varcsc.php';
$tz = new DateTimeZone($tz);
$dt = new DateTime();
$dt->setTimezone($tz);
function test_input($data) {
	$data=trim($data);
	$data=stripslashes($data);
	$data=htmlspecialchars($data);
	return $data;
}
$postid=test_input($_GET['p']);
$uun=base64_decode($_SESSION['username']);
if(!$postid) {
    $conn = null;
	header("Location:http://localhost/buddyBonds_backup/profile.php?un=".$uun);
	exit();
}
$uid=base64_decode($_SESSION['id']);
//get user info
$uiqr=$conn->prepare("SELECT * FROM users WHERE id=?");
$uiqr->bindParam(1,$uid,PDO::PARAM_INT);
$uiqr->execute();
$uiqrow=$uiqr->fetch(PDO::FETCH_ASSOC);
//get post info
$gpir=$conn->prepare("SELECT * FROM posts WHERE post_id=?");
$gpir->bindParam(1,$postid,PDO::PARAM_INT);
$gpir->execute();
if($gpir->rowCount()<=0) {
    $conn = null;
	header("Location:http://localhost/buddyBonds_backup/profile.php?un=".$uun);
	exit();
}
//change logout time
$cltq=$conn->prepare("UPDATE users SET logout_time='0001-01-01 00:00:00' WHERE id=?");
$cltq->bindParam(1,$uid,PDO::PARAM_INT);
if(!$cltq->execute()) die('Execution failed:('.$cltq->errno.')'.$cltq->error);
$gpirw=$gpir->fetch(PDO::FETCH_ASSOC);
$uplid=$gpirw['user_id'];
//get uploader info
$guir=$conn->prepare("SELECT * FROM users WHERE id=?");
$guir->bindParam(1,$uplid,PDO::PARAM_INT);
$guir->execute();
$guirw=$guir->fetch(PDO::FETCH_ASSOC);
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
	<meta name="description" content="Social connection web application.Post by <?php echo $guirw['username']; ?> on buddyBonds.com.<?php echo $gpirw['post_matter']; ?>. View complete post.Delete post.Connect and share.Social media website to make buddies,chat,upload pictures,videos and expanding your business."/>
	<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
	<title><?php echo $guirw['fullname'].' : '.base64_decode($gpirw['post_matter']).' &#8226; buddyBonds'; ?></title> 
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<link rel="icon" href="images/color-star-3-72-217610.png" type="image/x-icon">
	<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css" integrity="sha384-5sAR7xN1Nv6T6+dT2mhtzEpVJvfS3NScPQTrOxhwjIuvcA67KV2R5Jz6kr4abQsz" crossorigin="anonymous">
	<link rel="stylesheet" href="css/nav_footer.css">
	<link rel="stylesheet" href="css/post.css">
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
			<a href="messages.php" data-toggle="tooltip" title="Messages" class="text-secondary"><i class="fa fa-envelope faa-shake animated nav_large_icons"></i> <span class="nav_large_texts"><b>Messages</b></span> <?php echo '<span id="updated_msgs"></span>'; ?></a>
			<a href="notifications.php" data-toggle="tooltip" title="Notifications" class="text-secondary"><i class="fa fa-bell faa-ring animated nav_large_icons"></i> <span class="nav_large_texts"><b>Notifications</b> </span><?php echo '<span id="updated_ntfs"></span>'; ?></a>
			<a class="text-secondary" href="news.php" data-toggle="tooltip" title="News"><i class="fas fa-newspaper nav_large_icons"></i> <span class="nav_large_texts"><b>News</b></span></a>
		</li>
	</ul>
	<form action="search.php" method="get" class="form-inline" id="search_form">
		<div class="form-group">
			<div class="input-group" id="search_grp">
				<input type="search" required maxlength="30" minlength="1" placeholder="Search..." title="Search buddies or business profiles" id="search" name="q" data-toggle="tooltip">
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
<?php 
//check if buddies...only then show the post
$cibr=$conn->prepare("SELECT buddies_id FROM buddies WHERE (bud_id1=:uid OR bud_id1=:uplid) AND (bud_id2=:uplid OR bud_id2=:uid) AND active='1'");
$cibr->bindParam(":uid",$uid,PDO::PARAM_INT);
$cibr->bindParam(":uplid",$uplid,PDO::PARAM_INT);
$cibr->execute();
if(($cibr->rowCount()>0)||($uid==$uplid)) {
?>
<!-- navbar for small devices end!-->
<div id="myModal" class="modal">
	<div class="row" id="modal_row">
		<div class="col-lg-6 mx-auto" id="modal_content_col">
			<img class="modal-content img-fluid rounded" id="postpic" src="<?php echo $gpirw['post_content']; ?>">
		</div>
		<div class="col-lg-6" id="modal_details_col">
			<div id="modal_details">
				<?php
					if(file_exists($guirw['buddypic'])) echo '<img src="'.$guirw['buddypic'].'" class="rounded-circle img-fluid" id="uplbp">';
					else echo '<img src="images/def_buddypic.png" class="img-fluid rounded-circle" id="uplbp">';
					echo '<a href="profile.php?un='.$guirw['username'].'" id="uplun"><b>'.$guirw['username'].'</b>';
					echo '<span id="user_tooltip" data-toggle="tooltip" title="Click to visit user profile" class="text-dark"><i class="fas fa-user"></i></span></a>';
					//show text with post
					if($gpirw['post_matter']) {
			            $post_matter = base64_decode($gpirw['post_matter']);
			            if(strpos($post_matter, "@") !== false) {
					        $post_matter = preg_replace_callback('/@.+?\b/', function($m) {
                                $str = substr($m[0], 1);
                                return sprintf("<a href='profile.php?un=%s' class='text-dark'><b>%s</b></a>", $str, $str);
                            }, $post_matter);
				        }
				        echo '<div class="post_mat">'.$post_matter.'</div>';
					}
					//get hashtags to put em in user's interests
					$htarr=explode("#",$gpirw['hashtags']);
					foreach($htarr as $hashtag) {
						if($hashtag=="") continue;
						//check if hashtag exists related to id
						$chr=$conn->prepare("SELECT * FROM hashtags WHERE userid=? AND hashtag=?");
						$chr->bindParam(1,$uid,PDO::PARAM_INT);
						$chr->bindParam(2,$hashtag,PDO::PARAM_STR);
						$chr->execute();
						if($chr->rowCount()>0) {
							$chrw=$chr->fetch(PDO::FETCH_ASSOC);
							$ntimes=$chrw['ntimes']+1;
							//increase number of times
							$inotr=$conn->prepare("UPDATE hashtags SET ntimes=? WHERE userid=? AND hashtag=?");
							$inotr->bindParam(1,$ntimes,PDO::PARAM_INT);
							$inotr->bindParam(2,$uid,PDO::PARAM_INT);
							$inotr->bindParam(3,$hashtag,PDO::PARAM_INT);
							$inotr->execute();
						} else {
							//insert into hashtag
							$iihr=$conn->prepare("INSERT INTO hashtags(userid,hashtag,ntimes) VALUES(?,?,'1')");
							$iihr->bindParam(1,$uid,PDO::PARAM_INT);
							$iihr->bindParam(2,$hashtag,PDO::PARAM_INT);
							$iihr->execute();
						}
						$chr=$inotr=$iihr=null;
					}
					echo '<div class="btn-group btn-group-toggle d-flex">';
						$clr=$conn->prepare("SELECT * FROM activity WHERE bud_id1=? AND bud_id2=? AND activity_type='bond' AND post_id=?");
						$clr->bindParam(1,$uid,PDO::PARAM_INT);
						$clr->bindParam(2,$uplid,PDO::PARAM_INT);
						$clr->bindParam(3,$postid,PDO::PARAM_INT);
						$clr->execute();
						if($clr->rowCount()>0) echo '<button class="btn btn-outline-danger w-100" id="like_btn"><i class="fas fa-heartbeat btn-texts"></i> Bond</button>';
						else echo '<button class="btn btn-outline-danger w-100" id="like_btn"><i class="far fa-heart fa-spin btn-texts"></i> Bond</button>';
						$clr=null;
						echo '<button class="btn btn-outline-danger w-100" id="cmnt_btn" title="Comment"><i class="far fa-comment btn-texts"></i> Comment</button>';
						?> <button class="btn btn-outline-danger w-100 share_btn" data-toggle="tooltip" title="Click To Copy Link" data-pid="<?php echo $postid; ?>"><i class="fas fa-share-square btn-texts"></i> Share</button>
						<?php
						if($uid==$uplid) echo '<button class="btn btn-outline-danger" id="dlt_post_btn" title="Delete Post" onclick="dlt_post('.$postid.')"><i class="fas fa-trash-alt"></i></button>';
					echo '</div>';
					//show number of likes
					echo '<div id="nol">';
						//liked by names query
						if($gpirw['nol'] > 0 && $gpirw['nol'] <= 6) {
							$lnr=$conn->prepare("SELECT activity.activity_id,users.id,users.username FROM activity,users WHERE activity.bud_id1=users.id AND activity.post_id=? AND activity.activity_type='bond'"); //liked by names query result
							$lnr->bindParam(1,$postid,PDO::PARAM_INT);
							$lnr->execute();
							echo '<b>Bonded </b>by ';
							while($lnrw=$lnr->fetch(PDO::FETCH_ASSOC)) echo '<a href="profile.php?un='.$lnrw['username'].'" class="text-dark">'.$lnrw['username'].'</a> ';
							$lnr=null;
						} else if($gpirw['nol'] > 6) echo '<b>Bonded </b>by '.$gpirw['nol'];
					echo '</div>';
					//view all comments btn
					echo '<div id="sacb_div">';
						if($gpirw['noc'] > 7) echo '<a href="javascript:void(0)" class="text-muted" title="Show all the comments on this post" id="sacb" onclick="sacb('.$postid.')">View all '.$gpirw['noc'].' comments</a>';
					echo '</div>';
					//show comment row
					echo '<div class="row" id="cmnt_row">';
						if(file_exists($uiqrow['buddypic'])) echo '<img src="'.$uiqrow['buddypic'].'" class="img-fluid rounded-circle cmnt_disp" alt="User buddy picture">';
						else echo '<img src="images/def_buddypic.png" class="img-fluid rounded-circle cmnt_disp" alt="Default user buddy picture">';
						echo '<input type="text" id="cmnt_box" placeholder=" Add your comment..." title="Wanna add some comment?" name="cmnt_box" required maxlength="400"><button class="btn btn-sm btn-success" title="Post your comment" id="pycb" disabled onclick="comment('.$postid.')">Post</button>';
						echo '<div id="cmnt_spnr" class="mx-auto"><img src="images/spnr.gif" id="cmnt_spnr_img"></div>';
					echo '</div>';
					$gudr=$uiqr=null;
					echo '<div id="cmnts_sec">';
						$cqr=$conn->prepare("SELECT * FROM activity,users WHERE activity.bud_id1=users.id AND activity.post_id=? AND activity.activity_type='comment' ORDER BY activity.activity_time DESC LIMIT 7");
						$cqr->bindParam(1,$postid,PDO::PARAM_INT);
						$cqr->execute();
						while($cqrw=$cqr->fetch(PDO::FETCH_ASSOC)) {
							echo '<div class="row cmnt_row" style="padding-top:3px;padding-bottom:3px;">';
								if(file_exists($cqrw['buddypic'])) echo '<img src="'.$cqrw['buddypic'].'" class="rounded-circle cmnt_disp" alt="Buddy Picture">';
								else echo '<img src="images/def_buddypic.png" class="rounded-circle cmnt_disp" alt="Default Buddy Picture">';
								echo '<a href="profile.php?un='.$cqrw['username'].'" class="cmnt_un text-dark"><b>'.$cqrw['username'].'</b></a>';
								$comment = base64_decode($cqrw['activity_content']);
								echo '<span class="cmnt_cont">';
								if(strpos($comment, "@") !== false) {
						            $comment = preg_replace_callback('/@.+?\b/', function($m) {
                                        $str = substr($m[0], 1);
                                        return sprintf("<a href='profile.php?un=%s' class='text-dark'><b>%s</b></a>", $str, $str);
                                    }, $comment);
						        }
								echo $comment.'</span>';
								echo '<span class="cmnt_time text-muted">';
								$acti_time = strtotime($cqrw['activity_time']);
								$dt->setTimestamp($acti_time);
                                if(time()-$acti_time>0 && time()-$acti_time<86400) echo $dt->format("H:i A");
                                else if(time()-$acti_time>=86400 && time()-$acti_time<604800) echo $dt->format("D, H:i A");
                                else if(time()-$acti_time>=604800 && time()-$acti_time<31536000) echo $dt->format("M j, H:i A");
                                else echo $dt->format("M j, Y H:i A");
								echo '.</span>';
								if(($cqrw['bud_id1']==$uid)||($uid==$uplid)) {
									?>
									<a href="javascript:void(0)" class="dlt_link" style="color:#708090" title="Delete" onclick="dlt_cmnt('<?php echo $postid; ?>','<?php echo $cqrw['activity_id']; ?>')"><b><i class="far fa-trash-alt"></i></b></a>
									<?php
								}
 							echo '</div>';
						}
						$cqr=null;
					echo '</div>';
					//show post time
					echo '<div id="post_dtls" class="text-muted"><span>UPLOADED ON buddyBonds</span><span id="post_time"><b>';
					    $post_uptime = strtotime($gpirw['post_time']);
						$dt->setTimestamp($post_uptime);
                        if(time()-$post_uptime>0 && time()-$post_uptime<86400) echo $dt->format("H:i A");
                        else if(time()-$post_uptime>=86400 && time()-$post_uptime<604800) echo $dt->format("D, H:i A");
                        else if(time()-$post_uptime>=604800 && time()-$post_uptime<31536000) echo $dt->format("M j, H:i A");
                        else echo $dt->format("M j, Y H:i A");
					echo '</b></span></div>';	
					$gpir=null;
				?>
			</div>
		</div>
	</div>
</div>
<?php 
} else {
	echo '<div class="msg text-center">You need to be buddies with <a href="profile.php?un='.$guirw['username'].'" class="text-dark"><b>'.$guirw['username'].'</b></a> to view this post.</div>';
	$guir=null;
}
?>
<!-- scripts start !-->
<script src="http://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
<script src="js/post.js"></script>
<script src="js/nav.js"></script>
<script>
$(function() {
    $('#like_btn').click(function() {
        $.ajax({
            type:"POST",
		    url:"http://localhost/buddyBonds_backup/scripts/modal_like.php",
		    data: {
			    "post_id":"<?php echo $postid; ?>"
		    },
		    success:function(result) {
			    $('#like_btn').html(result);
			    updt_nlc("<?php echo $postid; ?>");
		    }
        });
    }); 
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
				$('#updated_msgs').html(result.new_msg);
				$('#updated_ntfs').html(result.new_ntf);
				$('#search').css('margin-left', '25px');
				if(!ntfs_sent) {
				    newNotification("buddyBonds","You Have A New Unseen Message And Notification On buddyBonds","../images/color-star-3-48-217610.png");
				    ntfs_sent = true;
				}
				$(document).prop('title',ct + " • " + tot_ntfs + " New Notifications");
			} else if(result.new_msg >= 1 && result.new_ntf <= 0) {
				$('#updated_msgs').addClass('badge badge-danger');
				$('#updated_msgs').html(result.new_msg);
				$('#search').css('margin-left', '13px');
				if(!ntfs_sent) {
				    newNotification("buddyBonds","You Have A New Unseen Message On buddyBonds","../images/color-star-3-48-217610.png");
				    ntfs_sent = true;
				}
				$(document).prop('title',ct + " • " + result.new_msg + " New Notifications");
			} else if(result.new_msg <= 0 && result.new_ntf >= 1) {
				$('#updated_ntfs').addClass('badge badge-danger');
				$('#updated_ntfs').html(result.new_ntf);
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
//to delete post 
function dlt_post(postid) {
    var choice = confirm("Confirm deletion for this post?");
    if(choice == true) {
        $.ajax({
       	    type:"POST",
       		url:"http://localhost/buddyBonds_backup/scripts/delete_post.php",
       		data:{
       			"post_id":postid
       		},
       		success:function(result) {
       			location.href="http://localhost/buddyBonds_backup/profile.php?un=<?php echo $uun; ?>";
       		}
       	});
    }
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
		    $('#cmnt_box').val("");
			$('#pycb').prop('disabled',true);
			$('#cmnt_spnr').hide();
			sacb(postid);
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
    var choice = confirm("Confirm deletion for this comment?");
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
</script>
<!--close php conn !-->
<?php $conn=null; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>