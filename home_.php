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
$cltq = $conn->prepare("UPDATE users SET logout_time='0001-01-01 00:00:00' WHERE id=?");
$cltq->bindParam(1, $uid, PDO::PARAM_INT);
if(!$cltq->execute()) die('Execution failed:('.$cltq->errno.')'.$cltq->error);
$cltq = null;
//get user info
$uiqr = $conn->prepare("SELECT * FROM users WHERE id=?");
$uiqr->bindParam(1,$uid,PDO::PARAM_INT);
$uiqr->execute();
$uiqrow = $uiqr->fetch(PDO::FETCH_ASSOC);
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
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport"
		content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no" />
	<meta name="referrer" content="origin-when-crossorigin" />
	<meta name="author" content="Vipul Sharma" />
	<meta name="description"
		content="buddyBonds web application.Home page. Get your daily feed of posts from your buddies and the business profiles you like. Bond, comment and share. Save posts to view later. Connect. Social media website to make buddies, chat, upload pictures, videos and expanding your business." />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<title>Home &#8226; buddyBonds</title>
	<link rel="icon" href="images/color-star-3-72-217610.png" type="image/x-icon">
	<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
		integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.css"
		integrity="sha256-46qynGAkLSFpVbEBog43gvNhfrOj+BmwXdxFgVK/Kvc=" crossorigin="anonymous" />
	<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Lobster+Two|Roboto|Open+Sans|Fira+Code">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"
		integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
	<link rel="stylesheet" href="css/nav_footer_.css">
	<link rel="stylesheet" href="css/home_.css">
</head>

<!-- nn : navbar-nav !-->
<!-- nn_sso : navbar-nav sidebar showoff !-->
<!-- nnch : navbar-nav central_header !-->
<!-- ccatm : change color acc. to mode !-->
<!-- ktw : keep this white !-->
<!-- tdm  : toggle dark mode !-->

<body>
	<nav class="navbar sticky-top" id="nav">
		<ul class="navbar-nav list-inline">
			<li class="list-inline-item">
				<a class="nav-link ccatm nav_li" href="javascript:void(0)" data-toggle="tooltip"
					title="Click To Make Nothing Happen. The sidebar is fixed intentionally :)" data-placement="right"
					id="nn_sso"><i class="fas fa-align-right"></i></a>
			</li>
			<li class="list-inline-item">
				<a class="nav-link ccatm
				" href="javascript:void(0)" data-toggle="tooltip" title="Go To buddyBonds Home" id="nn_ch"
					data-placement="right">buddyBonds</a>
			</li>
		</ul>
		<ul class="navbar-nav list-inline">
			<li class="list-inline-item">
				<form action="search.php" method="get" class="form-inline" id="search_form">
					<div class="form-group">
						<div class="input-group" id="search_grp">
							<input type="search" required maxlength="30" minlength="1" title="Search buddyBonds"
								id="search" name="q" class="form-control ccatm fcff">
							<div class="spinner-grow spinner-grow-sm" role="status" id="spnr">
								<span class="sr-only">Loading...</span>
							</div>
							<button type="reset" class="btn btn-sm" id="srch_rb"></button>
						</div>
					</div>
				</form>
			</li>
			<li class="list-inline-item">
				<a class="nav-link ccatm nav_li" href="messages.php" data-toggle="tooltip" title="Chats"
					data-placement="bottom"><i
						class="fas fa-comments faa-shake"></i><?php echo '<span id="updated_msgs" class="updated_badges"></span>'; ?></a>
			</li>
			<li class="list-inline-item">
				<a class="nav-link ccatm nav_li" href="notifications.php" data-toggle="tooltip" title="Notifications"
					data-placement="bottom"><i
						class="fa fa-bell faa-ring"></i><?php echo '<span id="updated_ntfs" class="updated_badges"></span>'; ?></a>
			</li>
			<li class="list-inline-item">
				<a class="nav-link ccatm nav_li" href="javascript:void(0)" data-toggle="tooltip"
					title="Toggle Dark Mode" data-placement="bottom" id="tdm"><i class="fas fa-moon"></i></a>
			</li>
			<li class="list-inline-item">
				<a class="nav-link ccatm nav_li" href="help_user.php" data-toggle="tooltip" title="Help"
					data-placement="bottom"><i class="fas fa-question-circle"></i></a>
			</li>
		</ul>
	</nav>

	<main>
		<div class="row">
			<div class="col-lg-3">
				<nav class="navbar" id="nav2">
					<img src="" id="nav2_hdr_wp">
					<div id="nav2_padded_content" class="text-center">
						<a href="profile.php?un=<?php echo $uun; ?>">
							<?php 
						if(file_exists($uiqrow['buddypic'])) echo '<img src="'.$uiqrow['buddypic'].'" class="rounded-circle mx-auto img-fluid nav2_bp nav2_hdr_rel">';
						else echo '<img src="images/def_buddypic.png" class="rounded-circle mx-auto img-fluid nav2_bp nav2_hdr_rel">';
					?>
							<?php 
					echo '<br><span class="ktw nav2_hdr_rel osff" data-toggle="tooltip" title="Visit Your Profile" id="uifn" data-placement="right"><b>'.$uiqrow['fullname'].'</b></span><br>';
					echo '<span class="ktw nav2_hdr_rel osff" id="uiun">@'.$uun.'</span><br>';
				?>
						</a>
						<a href="javascript: void(0)" class="btn btn-sm nav2_hdr_rel ktw fcff" id="ui_cpbtn"><i
								class="fas fa-camera"></i>
							Create A New Post</a>
						<a href="edit_profile.php" class="btn btn-sm nav2_hdr_rel ktw fcff" id="ui_edbtn"><i
								class="fas fa-edit"></i> Edit
							Your Profile</a>
						<hr id="nav2_hr">
						<ul class="navbar-nav nav2_hdr_rel fcff" id="nav2_ul">
							<li class="nav-item">
								<a class="nav-link ktw" href="news.php" data-toggle="tooltip"
									title="Get the latest global news from various news providers"
									data-placement="right"><i class="fas fa-newspaper"></i> NEWS</a>
							</li>
							<li class="nav-item">
								<a class="nav-link ktw" href="javascript:void(0)" data-toggle="tooltip"
									data-trigger="hover" id="tbp_tooltip"
									title="Customize the look of buddyBonds according to the themes that soothe you the most"
									data-placement="right">
									<span id="theme_box_popover" data-toggle="popover" tabindex="0" data-trigger="focus"
										title="Choose Your Theme" data-content='<span class="theme_box_pc" data-themecolor="#9400D3"></span>
								<span class="theme_box_pc" data-themecolor="#FF0000"></span>
								<span class="theme_box_pc" data-themecolor="#008080"></span>
								<span class="theme_box_pc" data-themecolor="#FFD700"></span>
								<span class="theme_box_pc" data-themecolor="#00FF7F"></span><br>
								<span class="theme_box_pc" data-themecolor="#663399"></span>
								<span class="theme_box_pc" data-themecolor="#FF6347"></span>
								<span class="theme_box_pc" data-themecolor="#800080"></span>
								<span class="theme_box_pc" data-themecolor="#FF00FF"></span>
								<span class="theme_box_pc" data-themecolor="#00BFFF"></span><br>
								<span class="theme_box_pc" data-themecolor="#4169E1"></span>
								<span class="theme_box_pc" data-themecolor="#008000"></span>
								<span class="theme_box_pc" data-themecolor="#FF1493"></span>
								<span class="theme_box_pc" data-themecolor="#483D8B"></span>
								<span class="theme_box_pc" data-themecolor="#FF4500"></span>' data-html="true">
										<i class="fas fa-paint-brush"></i> THEMES</span>
								</a>
							</li>
							<li class="nav-item">
								<a class="nav-link ktw" href="find_buddies.php" data-toggle="tooltip"
									title="Find suggestions to make new buddies" data-placement="right"><i
										class="fas fa-users"></i> FIND
									BUDDIES</a>
							</li>
							<li class="nav-item">
								<a class="nav-link ktw" href="advertise_main.php" data-toggle="tooltip"
									title="Have a business and wanna advertise it on buddyBonds? Click here then"
									data-placement="right"><i class="fas fa-ad"></i> ADVERTISE ON
									BUDDYBONDS</a>
							</li>
							<li class="nav-item">
								<a class="nav-link ktw" href="settings.php" data-toggle="tooltip"
									title="Change your profile and homepage settings" data-placement="right"><i
										class="fas fa-cog fa-spin"></i>
									SETTINGS</a>
							</li>
							<li class="nav-item">
								<a class="nav-link ktw" href="logout.php" data-toggle="tooltip"
									title="Log Out from buddyBonds to stop receiving notifications for new messages and posts."
									data-placement="right"><i class="fas fa-sign-out-alt"></i> LOG OUT</a>
							</li>
						</ul>
					</div>
				</nav>
			</div>
			<div class="col-lg-9">
				<?php
				//
				//check if cookie has buddies
				//
				$cichb = $conn->prepare("SELECT nobuddies FROM users WHERE id = :uid");
				$cichb->bindParam(":uid", $uid, PDO::PARAM_INT);
				$cichb->execute();
				$cichbr = $cichb->fetch(PDO::FETCH_ASSOC);
				if($cichbr['nobuddies'] >= 1) {
					//
					//user has buddies
					//get posts from buddies
					//
					$pr = $conn->prepare("SELECT * FROM posts,buddies WHERE ((posts.user_id = buddies.bud_id1) OR (posts.user_id = buddies.bud_id2) OR (posts.user_id = :uid)) AND ((buddies.bud_id1 = :uid) OR (buddies.bud_id2 = :uid) OR (posts.user_id = :uid)) AND ((buddies.active = '1') OR (posts.user_id = :uid)) ORDER BY posts.post_time DESC");
					$pr->bindParam(":uid", $uid, PDO::PARAM_INT);
					$pr->execute();
					$cichb = null;
					$postarr = array();
					echo '<div class="row">';
					while($prw = $pr->fetch(PDO::FETCH_ASSOC)) {
						//post_if saved in var pid
						$pid = $prw['post_id'];
						//if post already shown hide it
						//counter-measure because of our the current poor query to fetch posts
						foreach($postarr as $var) if($var == $pid) continue 2;
						array_push($postarr, $pid);
						//uploader id
						$userid = $prw['user_id'];
						//get info about the post uploader
						$prwur = $conn->prepare("SELECT * FROM users WHERE id=?");
						$prwur->bindParam(1, $userid, PDO::PARAM_INT); 
						$prwur->execute();
						$prwurw = $prwur->fetch(PDO::FETCH_ASSOC);
						echo '<div class="col-lg-6 col-md-6 col-xs-6">';
							echo '<div class="enclose_post cbatm">';
								//post info follows
								echo '<div class="post_info">';
									//show buddypic of post uploader
									if(file_exists($prwurw['buddypic'])) echo '<img src="'.$prwurw['buddypic'].'" class="img-fluid rounded-circle post_bud"
									alt="Buddy picture of user '.$prwurw['username'].'">';
									else echo '<img src="images/def_buddypic.png" class="img-fluid rounded-circle post_bud" alt="Default buddy picture">';
									//show username of post uploader
									echo '<a href="profile.php?un='.$prwurw['username'].'" class="post_un ccatm"><b>'.$prwurw['username'].'</b></a>';
									$prwur = null;
								echo '</div>';
								echo '<div class="carousel-inner">';
									echo '<div class="item">';
										//show the post image
										if(file_exists($prw['post_content'])) {	
											$extension = strtolower(pathinfo($prw['post_content'], PATHINFO_EXTENSION));
											if(($extension == "jpg") || ($extension == "png") || ($extension == "jpeg") || ($extension == "gif")) {
												echo '<a href="post.php?p='.$pid.'"><img src="'.$prw['post_content'].'" class="img-fluid post_img"
													alt="Picture uploaded by the user '.$prwurw['username'].'" data-id="'.$pid.'">';							
											} else if(($extension == "mp4") || ($extension == "webm") || ($extension == "ogg")) {
												echo '<video controls class="post_img" data-id="'.$pid.'">
												<source src="'.$prw['post_content'].'" type="video/'.$extension.'">
												<p class="ccatm">Unfortunately your browser is too old. It does not support HTML5 video.</p>';
											} 
											echo '<div class="carousel-caption"><small><b>UPLOADED to buddyBonds<br>';
											//post time display
											$post_uptime = strtotime($prw['post_time']);
											$dt->setTimestamp($post_uptime);
											if(time() - $post_uptime > 0 && time() - $post_uptime < 86400) echo $dt->format("H:i A");
											else if(time() - $post_uptime >= 86400 && time() - $post_uptime < 604800) echo $dt->format("D, H:i A");
											else if(time() - $post_uptime >= 604800 && time() - $post_uptime < 31536000) echo $dt->format("M j, H:i A");
											else echo $dt->format("M j, Y H:i A");
											echo '</b></small></div>';
											if(($extension == "jpg") || ($extension == "png") || ($extension == "jpeg") || ($extension == "gif"))
												echo '</a>';
											else echo '</video>';
										}
									echo '</div>';
								echo '</div>';
								//bond, comment div
								echo '<div class="btn-group btn-group-toggle d-flex">';
									$cq = $conn->prepare("SELECT activity_id FROM activity WHERE bud_id1=? AND post_id=? AND activity_type='bond'");
									$cq->bindParam(1, $uid, PDO::PARAM_INT);
									$cq->bindParam(2, $pid, PDO::PARAM_INT);
									$cq->execute();
									if($cq->rowCount() > 0)
										echo '<button class="btn btn-basics bb_bo" id="'.$pid.'" data-toggle="tooltip" title="Bonded"><b>
											<i class="fas fa-heartbeat btn-basic-texts"></i></b></button>';
									else 
										echo '<button class="btn btn-basics bb_bo" id="'.$pid.'" data-toggle="tooltip" title="Bond"><b>
											<i class="far fa-heart btn-basic-texts"></i></b></button>';
									$cq = null;
									echo '<button class="btn btn-basics bb_co" data-toggle="tooltip" title="Comment">
									<b><i class="far fa-comment"></i></b></button>';
									echo '<button class="btn btn-basics bb_sh" data-toggle="tooltip" title="Share">
									<b><i class="fas fa-share-alt"></i></b></button>';
									echo '<button class="btn btn-basics bb_bk" data-toggle="tooltip" title="Bookmark">
									<b><i class="far fa-bookmark"></i></b></button>';
								echo '</div>';
								
							echo '</div>';
						echo '</div>';
					}
					echo '</div>';
				} else {	
					//user has no buddies
					//show explore posts
					echo "Explore Posts to be show here!";
				}
				?>
			</div>
		</div>

	</main>

	<!-- toasts initialization !-->
	<!-- theme change toast !-->
	<div class="toast" style="position: absolute; bottom: 20px; right: 20px;" id="theme_toast">
		<div class="toast-header">
			<img src="images\color-star-3-512-217610.png" class="rounded mr-2" alt="...">
			<strong class="mr-auto ccatm">buddyBonds</strong>
			<small>1 sec ago</small>
			<button type="button" class="ml-2 mb-1 close ccatm" data-dismiss="toast" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="toast-body"></div>
	</div>

	<!-- mode change toast !-->
	<div class="toast" style="position: absolute; bottom: 20px; right: 20px;" id="mode_toast">
		<div class="toast-header">
			<img src="images\color-star-3-512-217610.png" class="rounded mr-2" alt="...">
			<strong class="mr-auto ccatm">buddyBonds</strong>
			<small>1 sec ago</small>
			<button type="button" class="ml-2 mb-1 close ccatm" data-dismiss="toast" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="toast-body"></div>
	</div>

	<!-- message and notification update toast !-->
	<div class="toast" style="position: absolute; bottom: 20px; right: 20px;" id="message_toast">
		<div class="toast-header">
			<img src="images\color-star-3-512-217610.png" class="rounded mr-2" alt="...">
			<strong class="mr-auto ccatm">buddyBonds</strong>
			<small>1 sec ago</small>
			<button type="button" class="ml-2 mb-1 close ccatm" data-dismiss="toast" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="toast-body"></div>
	</div>

	<!-- scripts start !-->
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"
		integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous">
	</script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"
		integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous">
	</script>
	<script src="js/nav_.js"></script>
	<script src="js/home_.js"></script>

	<script>
		$(function () {

		});
		var uid = "<?php echo $uid; ?>";
	</script>

	<!--close php conn !-->
	<?php $conn = null; ?>

</body>

</html>