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
function get_title($url){
    $str = file_get_contents($url);
    if(strlen($str)>0){
        $str = trim(preg_replace('/\s+/', ' ', $str)); // supports line breaks inside <title>
        preg_match("/\<title\>(.*)\<\/title\>/i",$str,$title); // ignore case
        return $title[1];
    }
}
$un=test_input($_GET['un']);
$uun=base64_decode($_SESSION['username']);
$uid=base64_decode($_SESSION['id']);
if((!$un) || ($un=="")) {
    $conn = null;
	header("Location:http://localhost/buddyBonds_backup/messages.php");
	exit();
}
//get buddy information...check if the username even exists or not
$gbir=$conn->prepare("SELECT buddypic,id,logout_time,private_key,public_key FROM users WHERE username = ?");
$gbir->bindParam(1,$un,PDO::PARAM_STR);
$gbir->execute();
if($gbir->rowCount()<=0) {
    $conn = null;
	header("Location:http://localhost/buddyBonds_backup/messages.php");
	exit();
}
if($un==$uun) {
    $conn = null;
	header("Location:http://localhost/buddyBonds_backup/messages.php");
	exit();
}
function decrypt($msg,$key) {
	$encryption_key=base64_decode($key);
	list($encrypted_data, $iv) = explode('::', base64_decode($msg), 2);
	return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
}
//change logout time
$cltq=$conn->prepare("UPDATE users SET logout_time='0001-01-01 00:00:00' WHERE id=?");
$cltq->bindParam(1,$uid,PDO::PARAM_INT);
if(!$cltq->execute()) die('Execution failed:('.$cltq->errno.')'.$cltq->error);
$cltq=null;
$gbirw=$gbir->fetch(PDO::FETCH_ASSOC);
//buddy id
$id=$gbirw['id'];
$opk = $gbirw['private_key'];
$opuk = $gbirw['public_key'];
//get user (self) info
$uiqr=$conn->prepare("SELECT * FROM users WHERE id=?");
$uiqr->bindParam(1,$uid,PDO::PARAM_INT);
$uiqr->execute();
$uiqrow=$uiqr->fetch(PDO::FETCH_ASSOC);
$upk=$uiqrow['private_key'];
$upuk=$uiqrow['public_key'];
$uiqr = null;
$file=fopen("encoded_key_msg.txt","r") or die("ERROR!");
$key=fread($file,44);
fclose($file);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
	<meta name="referrer" content="origin-when-crossorigin"/>
	<meta name="author" content="Vipul Sharma"/>
	<meta name="description" content="buddyBonds web application.Chat page for user.Send messages.Receive messages.Connect and share.Social media website to make buddies,chat,upload pictures,videos and expanding your business."/>
	<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
	<title>Message - <?php echo $un; ?></title>
	<script>
		if(window.matchMedia("(min-width:200px) and (max-width:1100px)").matches) window.location="http://localhost/buddyBonds_backup/mobile_chat.php?un=<?php echo $un; ?>";
	</script>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<link rel="icon" href="images/color-star-3-72-217610.png" type="image/x-icon">
	<link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css" integrity="sha384-5sAR7xN1Nv6T6+dT2mhtzEpVJvfS3NScPQTrOxhwjIuvcA67KV2R5Jz6kr4abQsz" crossorigin="anonymous">
	<link rel="stylesheet" href="css/nav_footer.css">
	<link rel="stylesheet" href="css/chat.css">
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
<body onunload="onunload_func()">
<!--navbar for large devices !-->
<nav class="navbar sticky-top" id="nav">
	<ul class="navbar-nav">
		<li class="nav-item">
			<a href="home.php" id="navhd" data-toggle="tooltip" title="Home" class="text-secondary"><i class="fas fa-home nav_large_icons"></i> buddyBonds</a>
			<a href="messages.php" data-toggle="tooltip" title="Messages" class="big_nav_active_link"><i class="fa fa-envelope faa-shake animated nav_large_icons"></i> <span class="nav_large_texts"><b>Messages</b></span> <?php echo '<span id="updated_msgs"></span>'; ?></a>
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
		<?php 
			$sbtcr=$conn->prepare("SELECT bud_id1,bud_id2 FROM buddies WHERE (bud_id1=:uid OR bud_id2=:uid) AND active='1' ORDER BY buddy_time DESC");
			$sbtcr->bindParam(":uid",$uid,PDO::PARAM_INT);
			$sbtcr->execute();
			if($sbtcr->rowCount()>0) {
				while($sbtcrw=$sbtcr->fetch(PDO::FETCH_ASSOC)) {
					if($sbtcrw['bud_id1']==$uid) $sid = $sbtcrw['bud_id2'];
					else if($sbtcrw['bud_id2']==$uid) $sid = $sbtcrw['bud_id1'];
					$gunr=$conn->prepare("SELECT username,buddypic FROM users WHERE id=?");
					$gunr->bindParam(1,$sid,PDO::PARAM_INT);
					$gunr->execute();
					$gunrw=$gunr->fetch(PDO::FETCH_ASSOC);
					echo '<a href="chat.php?un='.$gunrw['username'].'" class="row bd_row" title="Chat with '.$gunrw['username'].'">';
					if(file_exists($gunrw['buddypic'])) echo '<img src="'.$gunrw['buddypic'].'" class="rounded-circle bd_img">';
					else echo '<img src="images/def_buddypic.png" class="rounded-circle bd_img">';
					echo '<span class="bd_un">'.$gunrw['username'].'</span><i class="far fa-comment fas_msg ml-auto" data-toggle="tooltip" title="Message"></i>';
					echo '</a>';
					$gunr=null;
				}
			} else echo '<i class="fas fa-handshake fas_nobuddy"></i><div class="inner_msg text-center">You need to make a buddy first!</div>';
		?>	
	</div>
	<div class="col-lg-6" id="chat_div">
		<?php 
		//check if they are buddies
		$cibr=$conn->prepare("SELECT buddy_key FROM buddies WHERE (bud_id1=:uid OR bud_id1=:bid) AND (bud_id2=:uid OR bud_id2=:bid) AND active='1'");
		$cibr->bindParam(":uid",$uid,PDO::PARAM_INT);
		$cibr->bindParam(":bid",$id,PDO::PARAM_INT);
		$cibr->execute();
		echo '<div id="chat_div_row">';
			if($cibr->rowCount()>0) {
				if(file_exists($gbirw['buddypic'])) echo '<img src="'.$gbirw['buddypic'].'" class="rounded-circle bd_img_">';
			    else echo '<img src="images/def_buddypic.png" class="rounded-circle bd_img_">';
			} else {
				echo '<img src="images/def_buddypic.png" class="rounded-circle bd_img_">';
			}
			echo '<a href="profile.php?un='.$un.'" id="bd_un" class="text-success" title="View '.$un.'\'s profile"><b>'.$un.'</b></a>';
			if($cibr->rowCount()>0) {
				$cibrw=$cibr->fetch(PDO::FETCH_ASSOC);
				echo '<span class="text-primary" id="bd_lt"></span>';
			}
			?>
			<span class="cdr_hdr_btns"><button class="btn" id="attach_btn" data-toggle="tooltip" title="Attach A File"><i class="fas fa-paperclip"></i></button><button class="btn" id="emoji_btn" data-toggle="popover" data-placement="bottom" data-html="true" 
				data-title='<a href="javascript:void(0)" data-target="#emoji_carousel" data-slide-to="0" class="active emoji_carousel_slide_to" title="Favourites">🕙</a><a href="javascript:void(0)" data-target="#emoji_carousel" data-slide-to="1" class="emoji_carousel_slide_to">😃</a><a href="javascript:void(0)" data-target="#emoji_carousel" data-slide-to="2" class="emoji_carousel_slide_to">👋</a><a href="javascript:void(0)" data-target="#emoji_carousel" data-slide-to="3" class="emoji_carousel_slide_to">❤</a>' 
				data-content='
				<div id="emoji_carousel" class="carousel slide" data-ride="carousel">
					<div class="carousel-inner">
						<div class="carousel-item active">
							<a href="javascript:void(0)" class="emojis" data-emoji="603">😃</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="604">😄</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="600">😀</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="601">😁</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="606">😆</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="605">😅</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="923">🤣</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="602">😂</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="642">🙂</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="643">🙃</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="609">😉</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="60A">😊</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="607">😇</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="60D">😍</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="618">😘</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="617">😗</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="60B">😋</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="61B">😛</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="61C">😜</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="911">🤑</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="917">🤗</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="92D">🤭</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="92B">🤫</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="910">🤐</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="611">😑</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="60F">😏</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="644">🙄</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="634">😴</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="912">🤒</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="92E">🤮</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="92F">🤯</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="60E">😎</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="641">☹</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="62E">😮</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="62D">😭</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="631">😱</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="624">😤</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="621">😡</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="608">😈</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="92C">🤬</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="4A9">💩</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="648">🙈</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="649">🙉</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="64A">🙊</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="48B">💋</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="498">💘</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="497">💗</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="495">💕</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="494">💔</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="4AF">💯</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="4A5">💥</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="4A8">💨</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="44B">👋</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="44C">👌</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="44D">👍</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="44E">👎</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="44A">👊</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="44F">👏</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="64C">🙌</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="64F">🙏</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="9D2">🧒</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="467">👧</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="468">👨</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="469">👩</a>
						</div>
						<div class="carousel-item">
							<a href="javascript:void(0)" class="emojis" data-emoji="603">😃</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="604">😄</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="600">😀</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="601">😁</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="606">😆</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="605">😅</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="923">🤣</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="602">😂</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="642">🙂</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="643">🙃</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="609">😉</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="60A">😊</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="607">😇</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="60D">😍</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="929">🤩</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="618">😘</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="617">😗</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="61A">😚</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="619">😙</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="60B">😋</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="61B">😛</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="61C">😜</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="92A">🤪</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="928">🤨</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="61D">😝</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="911">🤑</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="917">🤗</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="92D">🤭</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="92B">🤫</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="910">🤐</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="610">😐</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="611">😑</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="636">😶</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="60F">😏</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="644">🙄</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="62C">😬</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="60C">😌</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="62A">😪</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="634">😴</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="924">🤤</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="912">🤒</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="915">🤕</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="922">🤢</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="92E">🤮</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="927">🤧</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="92F">🤯</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="60E">😎</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="9D0">🧐</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="615">😕</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="641">☹</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="62E">😮</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="632">😲</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="633">😳</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="630">😰</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="62D">😭</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="631">😱</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="616">😖</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="623">😣</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="624">😤</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="621">😡</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="608">😈</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="92C">🤬</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="480">💀</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="4A9">💩</a>
						</div>
						<div class="carousel-item">
							<a href="javascript:void(0)" class="emojis" data-emoji="44B">👋</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="91A">🤚</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="590">🖐</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="596">🖖</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="44C">👌</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="91E">🤞</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="91F">🤟</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="919">🤙</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="448">👈</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="449">👉</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="446">👆</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="595">🖕</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="447">👇</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="44D">👍</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="44E">👎</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="44A">👊</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="44F">👏</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="64C">🙌</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="64F">🙏</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="4AF">💯</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="4A5">💥</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="4AB">💫</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="4A6">💦</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="4A8">💨</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="4A3">💣</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="485">💅</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="933">🤳</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="47B">👻</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="47D">👽</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="476">👶</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="9D2">🧒</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="467">👧</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="468">👨</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="469">👩</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="474">👴</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="475">👵</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="645">🙅</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="646">🙆</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="481">💁</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="64B">🙋</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="647">🙇</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="926">🤦</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="937">🤷</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="64E">👮</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="482">💂</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="477">👷</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="934">🤴</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="478">👸</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="473">👳</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="935">🤵</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="47C">👼</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="385">🎅</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="48F">💏</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="936">🤶</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="9D9">🧙</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="9DA">🧚</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="9DB">🧛</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="437">🐷</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="416">🐖</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="43D">🐽</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="42A">🐪</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="418">🐘</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="98F">🦏</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="400">🐀</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="43F">🐿</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="43E">🐾</a>
						</div>
						<div class="carousel-item">
							<a href="javascript:void(0)" class="emojis" data-emoji="48B">💋</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="498">💘</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="497">💗</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="495">💕</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="494">💔</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="48C">💌</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="49D">💝</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="496">💖</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="493">💓</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="49E">💞</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="49F">💟</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="9E1">🧡</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="49B">💛</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="49A">💚</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="499">💙</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="49C">💜</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="5A4">🖤</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="648">🙈</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="649">🙉</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="64A">🙊</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="63A">😺</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="638">😸</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="639">😹</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="63B">😻</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="63C">😼</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="63D">😽</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="640">🙀</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="63F">😿</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="63E">😾</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="4A4">💤</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="9E0">🧠</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="443">👃</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="4AC">💬</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="4AD">💭</a>
							‍<a href="javascript:void(0)" class="emojis" data-emoji="438">🐸</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="427">‍‍🐧</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="9D6">🧖</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="3CC">🏌</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="3C4">🏄</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="6A3">🚣</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="3CA">🏊</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="3CB">🏋</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="6B4">🚴</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="938">🤸</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="93C">🤼</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="93E">🤾</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="939">🤹</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="9D8">🧘</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="6C0">🛀</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="46D">👭</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="46B">👫</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="46C">👬</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="48F">💏</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="491">💑</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="46A">👪</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="468">👨‍👩‍👧‍👧</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="5E3">🗣</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="463">👣</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="412">🐒</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="436">🐶</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="415">🐕</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="408">🐈</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="98C">🦌</a>
							<a href="javascript:void(0)" class="emojis" data-emoji="404">🐄</a>
						</div>
					</div>
				</div>'>😃</button></span><br><form method="POST" enctype="multipart/form-data" id="attach_file_form"><input type="file" name="attachment" id="attachment"><button class="btn btn-sm btn-info" type="submit" id="attach_send" title="Sending larger images can take larger time."><b>Send</b></button><img src="images/spnr.gif" class="img-responsive" id="attaching_file_spnr"><input type="hidden" id="send_to" value="<?php echo $un; ?>" name="send_to"><span id="attach_form_error"></span></form><button class="btn btn-sm text-secondary" id="close_atf" title="Close"><i class="fas fa-times-circle"></i></button>
		    <?php
		echo '</div>';
		//update messages, set seen = '1'
		$seen = 1;
		$umss = $conn->prepare("UPDATE messages SET seen = ?, seen_time = NOW() WHERE bud_id1 = ? AND bud_id2 = ?");
		$umss->bindParam(1,$seen,PDO::PARAM_INT);
		$umss->bindParam(2,$opk,PDO::PARAM_STR);
		$umss->bindParam(3,$upuk,PDO::PARAM_STR);
		$umss->execute();
		$umss = null;
		$gmcr=$conn->prepare("SELECT * FROM messages WHERE (bud_id1=:upk AND bud_id2=:opuk) OR (bud_id1=:opk AND bud_id2=:upuk)");
		$gmcr->bindParam(":upk",$upk,PDO::PARAM_STR);
		$gmcr->bindParam(":opuk",$opuk,PDO::PARAM_STR);
		$gmcr->bindParam(":opk",$opk,PDO::PARAM_STR);
		$gmcr->bindParam(":upuk",$upuk,PDO::PARAM_STR);
		$gmcr->execute();
		$mc = $gmcr->rowCount(); //message count
		if($mc-30 <= 0) $mctg = 0;
		else $mctg = $mc-30;
		$gmcr = null;
		//main chat query
		$mcqr=$conn->prepare("SELECT * FROM messages WHERE (bud_id1=:upk AND bud_id2=:opuk) OR (bud_id1=:opk AND bud_id2=:upuk) ORDER BY message_time LIMIT $mctg,30");
		$mcqr->bindParam(":upk",$upk,PDO::PARAM_STR);
		$mcqr->bindParam(":opuk",$opuk,PDO::PARAM_STR);
		$mcqr->bindParam(":opk",$opk,PDO::PARAM_STR);
		$mcqr->bindParam(":upuk",$upuk,PDO::PARAM_STR);
		$mcqr->execute();
		echo '<button class="btn btn-info" id="goto_new_msg"><i class="fa fa-envelope faa-shake animated"></i></button>';
		$reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
		echo '<div id="chats_area">';
			echo '<div class="text-center" id="chat_loader"><img src="images/spnr.gif" alt="Loading Previous Chats..." width="50" height="50"></div>';
		    if($mcqr->rowCount() > 0) {
		        while($mcqrow=$mcqr->fetch(PDO::FETCH_ASSOC)) { 
			        if($mcqrow['bud_id1'] == $upk) {
				        $msg=decrypt($mcqrow['message_content'],$key);
				        $msg_time = strtotime($mcqrow['message_time']);
				        echo '<div class="row row_spc"><div class="col-lg-12"><div class="user_msgs"><i class="fas fa-caret-right conv_arrow"></i>';
				        if($mcqrow['is_file'] == 0) {
                            if(preg_match($reg_exUrl, $msg, $url)) {
                                $title = get_title($msg);
                                $msg = "<b>".$title."</b><br>".preg_replace($reg_exUrl, '<a href="'.$url[0].'" rel="nofollow" target="_blank" class="chat_link_share"><b>'.$url[0].'</b></a>', $msg);
                            }
					        echo $msg.'<br>';
			        	} else {
					        if(file_exists($msg)) {
					            $file_ext=strtolower(pathinfo($msg,PATHINFO_EXTENSION));
				    	        $filesize = filesize($msg);
				    	        $filesize_mb = round($filesize / 1024 / 1024, 1);
				    	        if(($file_ext == "jpeg") || ($file_ext == "png") || ($file_ext == "jpg") || ($file_ext == "gif")) echo '<img src="'.$msg.'" alt="Image Sent By You" class="sent_img">';
			                    else if($file_ext=="pdf") echo '<i class="fas fa-file-pdf fa-2x"></i> You Sent A PDF File';
			                    else if(($file_ext=="txt") || ($file_ext=="html")) {
			                        echo '<i class="fas fa-file-alt fa-2x"></i> You Sent A ';
			                        if($file_ext == "txt") echo " Text Document";
			                        else echo " HTML Document";
			                    } else {
			                        $mime_type = mime_content_type($msg);
			                        if(($mime_type=="video/webm") || ($mime_type=="video/ogg") || ($mime_type=="video/mp4") || ($file_ext=="3gp") || ($file_ext=="3gpp")) echo '<i class="fas fa-video fa-2x"></i> You Sent A Video';
			                        else if(($mime_type=="audio/mpeg") || ($mime_type=="audio/wav") || ($mime_type=="audio/mp3") || ($mime_type=="audio/x-wav")) echo '<audio controls data-toggle="tooltip" title="Try downloading this video if it is not supported on this browser"><source src="'.$msg.'" type="'.$mime_type.'">This browser does not support this <code>audio</code> type yet.Click the button below to button the audio.</audio>';
			                    }
				        	    echo '<div class="text-center"><a href="'.$msg.'" download class="attachment_download_link btn btn-sm btn-danger"><i class="fas fa-file-download attachment_download_icon" title="Click to download"></i> ';
				    	        if($filesize_mb == 0) echo round($filesize / 1024, 2).' KB';
				     	        else echo $filesize_mb.' MB';
				     	        echo '</a></div>';
				    	    } else echo 'Attachment Unavailable!';
				        }
				        echo ' <span class="text-muted msg_time">';
			    	    $dt->setTimestamp($msg_time);
                        if(time()-$msg_time>0 && time()-$msg_time<86400) echo $dt->format("H:i A");
                        else if(time()-$msg_time>=86400 && time()-$msg_time<604800) echo $dt->format("D,H:i A");
                        else if(time()-$msg_time>=604800 && time()-$msg_time<31536000) echo $dt->format("M j,H:i A");
                        else echo $dt->format("M j, Y H:i A");
				        echo '</span></div><i class="fas fa-reply reply_to_square" style="float:right;margin-right:4px" title="Reply To" data-toggle="tooltip"></i></div></div>';
			        } else if($mcqrow['bud_id1'] == $opk) {
				        $msg=decrypt($mcqrow['message_content'],$key);
				        $msg_time = strtotime($mcqrow['message_time']);
				        echo '<div class="row row_spc"><div class="col-lg-12"><div class="op_msgs"><i class="fas fa-caret-left conv_arrow"></i>';
				        if($mcqrow['is_file']==0) {
				            if(preg_match($reg_exUrl, $msg, $url)) {
                                $title = get_title($msg);
                                $msg = "<b>".$title."</b><br>".preg_replace($reg_exUrl, '<a href="'.$url[0].'" rel="nofollow" target="_blank" class="chat_link_share"><b>'.$url[0].'</b></a><br>', $msg);
                            }
					        echo $msg.'<br>';
				        } else {
					        if(file_exists($msg)) {
					            $file_ext=strtolower(pathinfo($msg,PATHINFO_EXTENSION));
				    	        $filesize = filesize($msg);
					            $filesize_mb = round($filesize / 1024 / 1024, 1);
					            if(($file_ext == "jpeg") || ($file_ext == "png") || ($file_ext == "jpg") || ($file_ext == "gif")) echo '<img src="'.$msg.'" alt="Image Sent By '.$un.'" class="sent_img">';
			                    else if($file_ext=="pdf") echo '<i class="fas fa-file-pdf fa-2x"></i> Set You a PDF File';
			                    else if(($file_ext=="txt") || ($file_ext=="html")) {
			                        echo '<i class="fas fa-file-alt fa-2x"></i> Sent You A ';
			                        if($file_ext == "txt") echo " Text Document";
			                        else echo " HTML Document";
			                    } else {
			                        $mime_type = mime_content_type($msg);
			                        if(($mime_type=="video/webm") || ($mime_type=="video/ogg") || ($mime_type=="video/mp4") || ($file_ext=="3gp") || ($file_ext=="3gpp")) echo '<i class="fas fa-video fa-2x"></i> Sent You A Video';
			                        else if(($mime_type=="audio/mpeg") || ($mime_type=="audio/wav") || ($mime_type=="audio/mp3") || ($mime_type=="audio/x-wav")) echo '<audio controls data-toggle="tooltip" title="Try downloading this video if it is not supported on this browser"><source src="'.$msg.'" type="'.$mime_type.'">This browser does not support this <code>audio</code> type yet.Click the button below to button the audio.</audio>';
			                    }
				        	    echo '<div class="text-center"><a href="'.$msg.'" download class="attachment_download_link btn btn-sm btn-danger"><i class="fas fa-file-download attachment_download_icon" title="Click to download"></i> ';
				    	        if($filesize_mb == 0) echo round($filesize / 1024, 2).' KB';
				     	        else echo $filesize_mb.' MB';
				     	        echo '</a></div>';
					        } else echo 'Attachment Unavailable!';
				        }
				        echo ' <span class="text-muted msg_time">';
				        $dt->setTimestamp($msg_time);
                        if(time()-$msg_time>0 && time()-$msg_time<86400) echo $dt->format("H:i A");
                        else if(time()-$msg_time>=86400 && time()-$msg_time<604800) echo $dt->format("D,H:i A");
                        else if(time()-$msg_time>=604800 && time()-$msg_time<31536000) echo $dt->format("M j,H:i A");
                        else echo $dt->format("M j, Y H:i A");
        				echo '</span></div><i class="fas fa-reply reply_to_square" style="float:left;margin-left:4px" title="Reply To" data-toggle="tooltip"></i></div></div>';//op_msgs is other person msgs
			        }
		        }
		    } else echo '<div id="msg_inc" class="text-center msg text-muted" style="padding:5px;">Your conversations are secured with end-to-end encryption and authentication.</div>';//msg_inc-msg if not conversed before
		    echo '<audio preload="auto" src="sounds/msg_received.mp3" id="message_rec_tone"></audio>';
		    $mcqr=null;
		echo '</div>';	
		if($cibr->rowCount() > 0) {
			echo '<textarea class="form-control fixed-bottom mx-auto" placeholder="Type Your Message Here" title="Enter your message here" id="new_msg_cont" required minlength="1" maxlength="1000" spellcheck="false"></textarea>';
			echo '<audio id="message_sent_tone" src="sounds/msg_sent.mp3" preload="auto"></audio>';
		} else {
			echo '<textarea class="form-control fixed-bottom mx-auto" placeholder="You Need To Be Buddies With '.$un.' To Message Them" title="You Need To Be Buddies With '.$un.' To Message Them" id="new_msg_cont" required minlength="1" maxlength="1000" disabled></textarea>';
			?>
			<script> $('#attach_btn').prop('disabled', true); $('#emoji_btn').prop('disabled', true); </script>
			<?php
		}
		?>	
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
					if(file_exists($uiqrow['buddypic'])) echo '<img src="'.$uiqrow['buddypic'].'" class="rounded-circle bd_img">';
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
<!-- scripts start !-->
<script src="js/nav.js"></script>
<script src="js/chat.js"></script>
<script>
var mc = "<?php echo $mc; ?>";
var id = "<?php echo $id; ?>";
var ms = 30;
$(function() {
    setTimeout(function() {
        $('#chats_area').animate({ scrollTop: $('#chats_area').prop("scrollHeight") });
    }, 500);
	$('#new_msg_cont').keyup(function(e) {
		var msg_length=$('#new_msg_cont').val().length;
		if(msg_length>=1) {
			//check if enter key is pressed
			if(e.which == 13) {
        		send_msg();
				$('#new_msg_cont').val("");
				$('#new_msg_cont').height(46);	
    		}
		} else $('#new_msg_cont').height(46);
	});
	$('#chats_area').scroll(function() {
	    var chats_area = document.getElementById('chats_area');
		var scrollTop = $(this).scrollTop();
		if(scrollTop <= 0) {
			if(mc - ms >= 1) {
				$('#chat_loader').fadeIn();
				$.ajax({
					type:"POST",
					url:"http://localhost/buddyBonds_backup/scripts/load_more_chats.php",
					data:{
						"id":id,
						"ms":ms
					}
				}).done(function(result) {
					$('#chat_loader').fadeOut();
					$('#chats_area').prepend(result);
				}).fail(function(error) {
					console.log(Error(error));
				}).always(function(result) {
					ms += 30;
				});
			}
		}
		if(chats_area.scrollHeight - chats_area.scrollTop === chats_area.clientHeight) {
			if($('#goto_new_msg').is(':visible')) $('#goto_new_msg').fadeOut();
		}
	});
	$('#new_msg_cont').keypress(function(e) {
	    textAreaAdjust(this);
		if(e.which == 13) {
		    if($(this).val().length >=1) {
        	    send_msg();
			    $(this).val("");
			    $(this).height(46);
		    }
			e.preventDefault();
    	}
		//add overflow in case too much text in a single message
		if($(this).height() >= 120) $('#new_msg_cont').css('overflow-y','scroll');
		else $(this).css('overflow','hidden');
	});
	$('#new_msg_cont').keyup(function(e) {
	    if((e.which == 8) || (e.which == 46)) textAreaAdjust(this);
	    //add overflow in case too much text in a single message
		if($(this).height() >= 120) $('#new_msg_cont').css('overflow-y','scroll');
		else $(this).css('overflow','hidden');
	});
	//set online to typing on focus
	$('#new_msg_cont').on('focus',function() {
		$.ajax({
			type:"POST",
			url:"http://localhost/buddyBonds_backup/scripts/show_typing.php",
			data:{
				"bid":"<?php echo $id; ?>"
			},
			success:function(result) {}
		});
	});
	//set typing to online on blur
	$('#new_msg_cont').on('blur',function() {
		$.ajax({
			type:"POST",
			url:"http://localhost/buddyBonds_backup/scripts/hide_typing.php",
			data:{
				"bid":"<?php echo $id; ?>"
			},
			success:function(result) {}
		});
	});
	$('#goto_new_msg').click(function() {
		$('#chats_area').animate({ scrollTop: $('#chats_area').prop("scrollHeight") });
		$('#goto_new_msg').fadeOut();
	});
});
if(window.Worker) {
    var chatWorker = new Worker("js/chat_worker.js");
    var updateWorker = new Worker("js/update_worker.js");
}
check_new_messages();
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
				$('#nav_profile_link').css('margin-left', '10px');
				$('#search').css('margin-left', '13px');
				if(!ntfs_sent) {
				    newNotification("buddyBonds","You Have A New Unseen Message On buddyBonds","../images/color-star-3-48-217610.png");
				    ntfs_sent = true;
				}
				$(document).prop('title',ct + " • " + result.new_msg + " New Notifications");
			} else if(result.new_msg <= 0 && result.new_ntf >= 1) {
				$('#updated_ntfs').addClass('badge badge-danger');
				$('#updated_ntfs').html(result.new_ntf);
				$('#nav_profile_link').css('margin-left', '10px');
				$('#search').css('margin-left', '13px');
				if(!ntfs_sent) {
				    newNotification("buddyBonds","You Have A New Unseen Notification On buddyBonds","../images/color-star-3-48-217610.png");
				    ntfs_sent = true;
				}
				$(document).prop('title',ct + " • " + result.new_ntf + " New Notifications");
            }
		}
    }
	setTimeout(check_updates, 9500);
}
//check new messages
function check_new_messages() {
	$.ajax({
		type:"GET",
		url:"http://localhost/buddyBonds_backup/scripts/check_new_messages.php?id="+id,
		dataType:"json"
	}).done(function(result) {
 		if(result.new_message == 1) {
			chatWorker.postMessage(result.message_id);
			chatWorker.onmessage = function(e) {
				var data = JSON.parse(e.data);
				$('#chats_area').append('<div class="row row_spc"><div class="col-lg-12"><div class="op_msgs"><i class="fas fa-caret-left conv_arrow"></i>' + data.message_content + data.msg_time + '</div></div></div>');
				$('#goto_new_msg').fadeIn();
				$('#message_rec_tone').trigger('play');
			}
 		}
 		$('#bd_lt').html(result.last_online);
	}).fail(function(error) {
		console.log(Error(error));
	}).always(function(result){
		setTimeout(check_new_messages, 1000);
	});
}
//send a msg
function send_msg() {
	var new_msg_cont=$('#new_msg_cont').val();
	if(new_msg_cont!="") {
		$.ajax({
			type:"POST",
			url:"http://localhost/buddyBonds_backup/scripts/send_msg.php",
			data:{
				"un":"<?php echo $un; ?>",
				"nmc":new_msg_cont
			}
		}).done(function(result) {
			$('#chats_area').append('<div class="row row_spc"><div class="col-lg-12"><div class="user_msgs"><i class="fas fa-caret-right conv_arrow"></i>' + result + '</div></div></div>');
			$('#chats_area').animate({ scrollTop: $('#chats_area').prop("scrollHeight") });
			$('#message_sent_tone').trigger('play');
		}).fail(function(error) {
			console.log(error);
		});
	}
}
//change typing to online on page leave if so
function onunload_func() {
	$.ajax({
		type:"POST",
		url:"http://localhost/buddyBonds_backup/scripts/hide_typing.php",
		data:{
			"bid":"<?php echo $id; ?>"
		},
		success:function(result) {}
	});
}
</script>
<!--close php conn !-->
<?php $conn=null; ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>