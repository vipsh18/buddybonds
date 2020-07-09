<?php 
include 'varcsc.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8"/>
	<meta name="referrer" content="origin-when-crossorigin"/>
	<meta name="author" content="Vipul Sharma"/>
	<meta name="user-scalable" content="no"/>
	<meta name="robots" content="noindex,nofollow"/>
</head>
<body>
<?php
date_default_timezone_set($tz);
$uid=base64_decode($_SESSION['id']);
function test_input($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}
if($conn) {
	$srch=test_input($_POST['srch']);
	if(strlen($srch)>0) {
		$temp=$srch."%";
		$srcharr=array();
		$r1=$conn->prepare("SELECT users.username,users.fullname,users.buddypic FROM buddies,users WHERE (buddies.bud_id1=users.id OR buddies.bud_id2=users.id) AND (buddies.bud_id1=? OR buddies.bud_id2=?) AND (users.username LIKE ? OR users.fullname LIKE ?) AND users.id!=? AND buddies.active='1' ORDER BY users.nobuddies DESC");
		$r1->bindParam(1,$uid,PDO::PARAM_INT);
		$r1->bindParam(2,$uid,PDO::PARAM_INT);
		$r1->bindParam(3,$temp,PDO::PARAM_STR);	
		$r1->bindParam(4,$temp,PDO::PARAM_STR);	
		$r1->bindParam(5,$uid,PDO::PARAM_INT);
		$r1->execute();
		if($r1->rowCount()>0) {
			while($rw1=$r1->fetch(PDO::FETCH_ASSOC)) {
				foreach($srcharr as $var1) {
					if($var1==$rw1['username']) {
						continue 2;
					}
				}
				array_push($srcharr,$rw1['username']);
				echo '<div class="srch_row">';
					$rw1['buddypic']=str_replace("uploads/".$rw1['username']."/buddypic_uploads","../uploads/".$rw1['username']."/buddypic_uploads",$rw1['buddypic']);
					if(file_exists($rw1['buddypic'])) {
						$rw1['buddypic']=str_replace("../uploads/".$rw1['username']."/buddypic_uploads","uploads/".$rw1['username']."/buddypic_uploads",$rw1['buddypic']);
						echo '<img src="'.$rw1['buddypic'].'" class="rounded-circle srch_img" alt="Buddy picture of the user">';
					} else {
						echo '<img src="images/def_buddypic.png" class="rounded-circle srch_img" alt="Default buddy picture">';
					}
					echo '<a href="chat.php?un='.$rw1['username'].'" class="srch_fn">'.$rw1["fullname"].'</a>
								<a href="chat.php?un='.$rw1['username'].'" class="text-muted srch_un">@'.$rw1["username"].'</a>';
				echo '</div>';
			}
		} else {
			$temp="%".$srch."%";
			$r1=null;
			$r1=$conn->prepare("SELECT users.username,users.fullname,users.buddypic FROM buddies,users WHERE (buddies.bud_id1=users.id OR buddies.bud_id2=users.id) AND (buddies.bud_id1=? OR buddies.bud_id2=?) AND (users.username LIKE ? OR users.fullname LIKE ?) AND users.id!=? AND buddies.active='1' ORDER BY users.nobuddies DESC");
			$r1->bindParam(1,$uid,PDO::PARAM_INT);	
			$r1->bindParam(2,$uid,PDO::PARAM_INT);	
			$r1->bindParam(3,$temp,PDO::PARAM_STR);	
			$r1->bindParam(4,$temp,PDO::PARAM_STR);	
			$r1->bindParam(5,$uid,PDO::PARAM_INT);	
			$r1->execute();
			if($r1->rowCount()>0) {
				while($rw1=$r1->fetch(PDO::FETCH_ASSOC)) {
					foreach($srcharr as $var1) {
						if($var1==$rw1['username']) {
							continue 2;
						}
					}
					array_push($srcharr,$rw1['username']);
					echo '<div class="srch_row">';
						$rw1['buddypic']=str_replace("uploads/".$rw1['username']."/buddypic_uploads","../uploads/".$rw1['username']."/buddypic_uploads",$rw1['buddypic']);
						if(file_exists($rw1['buddypic'])) {
							$rw1['buddypic']=str_replace("../uploads/".$rw1['username']."/buddypic_uploads","uploads/".$rw1['username']."/buddypic_uploads",$rw1['buddypic']);
							echo '<img src="'.$rw1['buddypic'].'" class="rounded-circle srch_img" alt="Buddy picture of the user">';
						} else {
							echo '<img src="images/def_buddypic.png" class="rounded-circle srch_img" alt="Default buddy picture">';
						}
						echo '<a href="chat.php?un='.$rw1['username'].'" class="srch_fn">'.$rw1["fullname"].'</a>
								<a href="chat.php?un='.$rw1['username'].'" class="text-muted srch_un">@'.$rw1["username"].'</a>';
					echo '</div>';
				}
			} else {
				echo '<div style="padding:10px" class="text-center">Nothing to show.<br>Try editing your search.</div>';
			}
		}
	}
} else {
	die("Error!");
}
$conn=null;
?>
</body>
</html>