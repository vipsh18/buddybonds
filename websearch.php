<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
	<meta name="referrer" content="origin-when-crossorigin" />
	<meta name="author" content="Vipul Sharma" />
	<meta name="description"
		content="buddyBonds is a web application designed to help people connect,share photos and videos.Join this social media website to make buddies,chat,upload pictures,videos and for expanding your business." />
	<meta http-equiv="x-ua-compatible" content="IE=edge" />
	<?php
		$config = parse_ini_file("varcsc.ini");
		$db_server = $config['db_server'];
		$db_user = $config['db_user'];
		$db_pass = $config['db_pass'];
		$db_name = $config['db_name'];
		try {
			$conn = new PDO("mysql:host=$db_server;dbname=$db_name", $db_user, $db_pass);
			//set PDO error mode to exception
			$conn -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch(PDOException $e) {
			echo 'Connection failed:'.$e -> getMessage();
		}
		function test_input($data) {
			$data = trim($data);
			$data = stripslashes($data);
			$data = htmlspecialchars($data);
			return $data;
		}
		if(isset($_GET['q'])) $q = test_input($_GET['q']);
		//
		//checking if user is logged in, if yes save searches
		//if not, get timezone atleast to help in search results showup
		//
		if(isset($_SESSION['id']) && isset($_SESSION['username']) && isset($_SESSION['tz'])) {
			if(isset($_COOKIE['id']) && isset($_COOKIE['username']) && isset($_COOKIE['tz'])) {
				$cookie_id = base64_decode($_COOKIE['id']);
				$cookie_username = base64_decode($_COOKIE['username']);
				$tz = $_COOKIE['tz'];
				$cid = $_COOKIE['id'];
				$cun = $_COOKIE['username'];
			} else {
				$sid = $_SESSION['id'];
				$sun = $_SESSION['username'];
				$tz = $_SESSION['tz'];
				setcookie('id', $sid, time()+(60*24*60*30), "/");
				setcookie('username', $sun, time()+(60*60*24*30), "/");
				setcookie('tz', $tz, time()+(60*60*24*30), "/");
				$cookie_id = base64_decode($sid);
				$cookie_username = base64_decode($sun);
				$tz = $_COOKIE['tz'];
			}
		} else if(!isset($_SESSION['id'])||!isset($_SESSION['username'])||!isset($_SESSION['tz'])) {
			if(isset($_COOKIE['id']) && isset($_COOKIE['username']) && isset($_COOKIE['tz'])) {
				$cookie_username = base64_decode($_COOKIE['username']);
				$cookie_id = base64_decode($_COOKIE['id']);
				$cid = $_COOKIE['id'];
				$cun = $_COOKIE['username'];
				$tz = $_COOKIE['tz'];
				$_SESSION['id'] = $cid;
				$_SESSION['username'] = $cun;
				$_SESSION['tz'] = $tz;
			} else {
				//
				//get timezone
				//
	?>
	<script>
		if ("Intl" in window) {
			var tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
			var d = new Date();
			d.setTime(d.getTime() + (1000 * 60 * 60 * 24 * 30));
			var expires = "expires=" + d.toUTCString();
			document.cookie = "tz=" + tz + ";" + expires + ";path=/";
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function () {
				if (this.readyState == 4 && this.status == 200) console.log("Ready To Go!");
			};
			xhttp.open("GET", "http://localhost/buddyBonds_backup/scripts/settz.php?tz=" + tz, true);
			xhttp.send();
		} else {
			var tz = new Date().getTimezoneOffset();
			tz = tz == 0 ? 0 : -tz;
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function () {
				if (this.readyState == 4 && this.status == 200) console.log("Ready To Go!");
			};
			xhttp.open("GET", "http://localhost/buddyBonds_backup/scripts/settz_ob.php?tz=" + tz, true);
			xhttp.send();
		}
	</script>
	<?php
			}
		} 
	?>
	<title> <?php if (isset($q)) echo $q. " &#8226"; ?> buddyBonds WebSearch</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
		integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<link rel="icon" href="images/color-star-3-72-217610.png" type="image/x-icon">
	<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css"
		integrity="sha384-5sAR7xN1Nv6T6+dT2mhtzEpVJvfS3NScPQTrOxhwjIuvcA67KV2R5Jz6kr4abQsz" crossorigin="anonymous">
	<link rel="stylesheet" href="css/nav_footer.css">
	<link rel="stylesheet" href="css/websearch.css">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lobster+Two|Open+Sans|Roboto|Fira+Code">
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
	<?php
		if(isset($_SESSION['id']) && isset($_SESSION['username']) && isset($_SESSION['tz'])) {
			//
			//user is logged in, set some vars, show usual navbar
			//
			//change logout time
			$uid = base64_decode($_SESSION['id']);
			$uun = base64_decode($_SESSION['username']);
			$cltq = $conn -> prepare("UPDATE users SET logout_time='0001-01-01 00:00:00' WHERE id=?");
			$cltq -> bindParam(1,$uid,PDO::PARAM_INT);
			if(!$cltq -> execute()) die('Execution failed:('.$cltq -> errno.')'.$cltq -> error);
			$uiqr = $conn->prepare("SELECT * FROM users WHERE id=?");
			$uiqr -> bindParam(1,$uid,PDO::PARAM_INT);
			$uiqr -> execute();
			$uiqrow = $uiqr->fetch(PDO::FETCH_ASSOC);
	?>
	<!--navbar for large devices !-->
	<nav class="navbar sticky-top" id="nav">
		<ul class="navbar-nav">
			<li class="nav-item">
				<a href="home.php" id="navhd" data-toggle="tooltip" title="Home" class="text-secondary"><i
						class="fas fa-home nav_large_icons"></i> buddyBonds</a>
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
					<input type="search" required maxlength="30" minlength="1" placeholder="Search..."
						title="Search buddies or business profiles" id="search" name="q">
					<div id="spnr"><img src="http://localhost/buddyBonds_backup/images/spnr.gif" id="spnr_img"></div>
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
			<li class="list-inline-item">
				<a href="javascript:void(0)" id="sdnav_obtn" class="text-secondary"
					style="position: relative; top: 3px"><i class="fas fa-bars nav_large_icons" data-toggle="tooltip"
						title="Options"></i></a>
				<a href="javascript:void(0)" id="sdnav_cbtn" class="text-secondary"
					style="position: relative; top: 3px"><i class="fas fa-times nav_large_icons" data-toggle="tooltip"
						title="Close Options"></i></a>
			</li>
		</ul>
		<div id="optnav" class="text-center">
			<div><a href="settings.php" class="text-secondary ts_nu"><i
						class="fa fa-wrench faa-wrench animated nav_large_icons"></i> <span
						class="nav_large_texts"><b>Settings</b></a></div>
			<div><a href="hashtags.php" class="text-secondary ts_nu"><i class="fas fa-hashtag nav_large_icons"></i>
					<span class="nav_large_texts"><b>List Of Hashtags</b></a></div>
			<div><a href="edit_profile.php" class="text-secondary ts_nu"><i class="fas fa-edit nav_large_icons"></i>
					<span class="nav_large_texts"><b>Edit profile</b></a></div>
			<div><a href="logout.php" class="text-secondary ts_nu"><i class="fas fa-sign-out-alt nav_large_icons"></i>
					<span class="nav_large_texts"><b>Log Out</b></a></div>
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
					<a href="javascript:void(0)" id="m_sdnav_cbtn" style="color:blue"><i
							class="fas fa-times fa-2x"></i></a>
				</li>
			</ul>
			<div id="m_optnav">
				<div class="text-center"><a href="settings.php"><i class="fa fa-wrench faa-wrench animated"></i>
						Settings</a></div>
				<div class="text-center"><a href="news.php"><i class="fas fa-newspaper"></i> News</a></div>
				<div class="text-center"><a href="hashtags.php"><i class="fas fa-hashtag"></i> List Of Hashtags</a>
				</div>
				<div class="text-center"><a href="edit_profile.php"><i class="fas fa-edit"></i>Edit profile</a></div>
				<div class="text-center"><a href="logout.php"><i class="fas fa-sign-out-alt"></i>Log Out</a></div>
			</div>
		</nav>
		<form action="search.php" method="get" id="m_search_form">
			<div class="form-group">
				<div class="input-group">
					<input type="search" required maxlength="30" minlength="1" placeholder="Search..." title="Search..."
						id="m_search" name="q" class="mx-auto">
					<div id="m_spnr"><img src="images/spnr.gif" id="m_spnr_img"></div>
					<button type="reset" class="btn btn-sm" id="m_srch_rb"><span id="m_srch_rbt">X</span></button>
				</div>
				<div id="m_suggestions" class="mx-auto"></div>
			</div>
		</form>
	</div>
	<!-- navbar for small devices end!-->
	<?php
		//close if loop for user session check
		} else {
			?>
		<div id="no_login_ws_head" class="text-center">
			<span id="bb_lb2_nlwh">buddyBonds</span><span id="bb_ws_nlwh"> WebSearch</span>
			<a class="btn btn-sm" href="index.php" role="button" id="lsup_btn">Login / SignUp Here</a>
		</div>
	<?php
		}
		//
		// main content for websearch page
		//
		if (!isset($q)) {
			?>
	<div id="search_area" style="margin-top: 10px; text-align: center;">
		<form action="" method="get" id="websearch_form" class="d-flex justify-content-center">
			<div class="form-group">
				<label for="q"></label>
				<input type="text" class="form-control" name="q" id="q" placeholder="Type Here To Search Web"
					value="<?php if (isset($q)) echo $q; ?>">
			</div>
		</form>
		<?php
		} else {
			?>
		<div id="search_area">
			<form action="" method="get" id="websearch_form">
				<div class="form-group">
					<label for="q"></label>
					<input type="text" class="form-control" name="q" id="q" placeholder="Type Here To Search Web"
						value="<?php if (isset($q)) echo $q; ?>">
				</div>
			</form>
			<?php
		}
	?>
			<div>
				<a class="btn btn-outline-primary srch_types" data-type="images" href="" role="button" id="wst_i"><i
						class="fas fa-images"></i> Images</a>
				<a class="btn btn btn-outline-danger srch_types" data-type="videos" href="" role="button" id="wst_v"><i
						class="fas fa-video"></i> Videos</a>
				<a class="btn btn btn-outline-success srch_types" data-type="news" href="" role="button" id="wst_n"><i
						class="fas fa-newspaper"></i> News</a>
				<a class="btn btn btn-outline-info srch_types" data-type="books" href="" role="button" id="wst_b"><i
						class="fas fa-book"></i> Books</a>
				<a class="btn btn btn-outline-secondary" href="javascript:void(0)" role="button" style="font-size: 14px;
					margin-right: 4px;"><i class="fas fa-search"></i> How Search Works</a>
			</div>
			<!-- end search area div here !-->
		</div>
		<!-- !-->
		<!-- display search results !-->
		<!-- !-->
		<div id="search_results">
			<!-- !-->
		</div>
		<!-- !-->
		<!-- display search results ends !-->
		<!-- !-->
		<?php
		if(isset($uid)) {
			//
			//show bottom navbar only in the case is logged in
	?>
		<!-- inner navbar for small devices ... at bottom!-->
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
		<?php
		}
	?>
		<!-- scripts start !-->
		<script src="http://code.jquery.com/jquery-2.2.4.min.js"
			integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
		<script src="js/websearch.js"></script>
		<?php
		if (isset($uid)) {
			//
			//load in specific scripts if user is logged in
			//
	?>
		<script src="js/nav.js"></script>
		<script>
			$(function () {});
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
									"../images/color-star-3-48-217610.png");
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
									"../images/color-star-3-48-217610.png");
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
									"../images/color-star-3-48-217610.png");
								ntfs_sent = true;
							}
							$(document).prop('title', ct + " • " + result.new_ntf + " New Notifications");
						}
					}
				}
				setTimeout(check_updates, 5000);
			}
		</script>
		<?php
		}
	?>
		<!--close php conn !-->
		<?php $conn=null; ?>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
			integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
		</script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
			integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
		</script>
</body>

</html>