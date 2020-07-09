<?php
include 'varcsc.php'; 
date_default_timezone_set($tz);
$uid = base64_decode($_SESSION['id']);
$uun = base64_decode($_SESSION['username']);
function test_input($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}
$q = test_input($_POST['q']);
if($conn) {
	if(strlen($q) > 0) {
		$count = 0;
		$temp="%".$q."%";
		$resproarr=array();
		$respgarr=array();
		$r1=$conn->prepare("SELECT users.username,users.fullname,users.buddypic FROM buddies,users WHERE (users.username LIKE ? OR users.fullname LIKE ?) AND users.id!=? ORDER BY users.nobuddies DESC");
		$r1->bindParam(1, $temp, PDO::PARAM_STR);	
		$r1->bindParam(2, $temp, PDO::PARAM_STR);	
		$r1->bindParam(3, $uid, PDO::PARAM_INT);	
		$r1->execute();
		if($r1->rowCount() > 0) {
			while($rw1=$r1->fetch(PDO::FETCH_ASSOC)) {
				foreach($resproarr as $var1) {
					if($var1==$rw1['username']) continue 2;
				}
				array_push($resproarr,$rw1['username']);
				if($count >= 10) break;
				echo '<div class="row1">';
					$rw1['buddypic']=str_replace("uploads/".$rw1['username']."/buddypic_uploads","../uploads/".$rw1['username']."/buddypic_uploads",$rw1['buddypic']);
					if(file_exists($rw1['buddypic'])) {
						$rw1['buddypic']=str_replace("../uploads/".$rw1['username']."/buddypic_uploads","uploads/".$rw1['username']."/buddypic_uploads",$rw1['buddypic']);
						echo '<img src="'.$rw1['buddypic'].'" class="rounded-circle srch_pic" alt="Buddy picture of the user">';
					} else {
						echo '<img src="images/def_buddypic.png" class="rounded-circle srch_pic" alt="Default buddy picture">';
					}
					echo '<a href="profile.php?un='.$rw1['username'].'" class="sfn">'.$rw1["fullname"].'</a>
								<a href="profile.php?un='.$rw1['username'].'" class="text-muted sun" style="float:right">@'.$rw1["username"].'</a>';
					$count++;
				echo '</div>';
			}
		} else {
			$temp="%".$q."%";
			$r1=null;
			$r1=$conn->prepare("SELECT users.username,users.fullname,users.buddypic FROM buddies,users WHERE (users.username LIKE ? OR users.fullname LIKE ? OR users.description LIKE ?) AND users.id!=? ORDER BY users.nobuddies DESC");
			$r1->bindParam(1,$temp,PDO::PARAM_STR);	
			$r1->bindParam(2,$temp,PDO::PARAM_STR);	
			$r1->bindParam(3,$temp,PDO::PARAM_STR);	
			$r1->bindParam(4,$uid,PDO::PARAM_INT);	
			$r1->execute();
			if($r1->rowCount()>0) {
				while($rw1=$r1->fetch(PDO::FETCH_ASSOC)) {
					foreach($resproarr as $var1) {
						if($var1==$rw1['username']) continue 2;
					}
					array_push($resproarr,$rw1['username']);
					if($count >= 10) break;
					echo '<div class="row1">';
						$rw1['buddypic']=str_replace("uploads/".$rw1['username']."/buddypic_uploads","../uploads/".$rw1['username']."/buddypic_uploads",$rw1['buddypic']);
						if(file_exists($rw1['buddypic'])) {
							$rw1['buddypic']=str_replace("../uploads/".$rw1['username']."/buddypic_uploads","uploads/".$rw1['username']."/buddypic_uploads",$rw1['buddypic']);
							echo '<img src="'.$rw1['buddypic'].'" class="rounded-circle srch_pic" alt="Buddy picture of the user">';
						} else {
							echo '<img src="images/def_buddypic.png" class="rounded-circle srch_pic" alt="Default buddy picture">';
						}
						echo '<a href="profile.php?un='.$rw1['username'].'" class="sfn">'.$rw1["fullname"].'</a>
								<a href="profile.php?un='.$rw1['username'].'" class="text-muted sun" style="float:right">@'.$rw1["username"].'<span class="basis_srch_sugg text-muted"><b> &#9679 BASED ON PROFILE DESCRIPTION</b></span></a>';
					echo '</div>';
				}
			} else {
				$temp="%".$q."%";
				$r1=null;
				$r1=$conn->prepare("SELECT fullname,username,buddypic FROM users WHERE (fullname LIKE ? OR username LIKE ?) AND id!=? ORDER BY nobuddies DESC LIMIT 11");
				$r1->bindParam(1,$temp,PDO::PARAM_STR);
				$r1->bindParam(2,$temp,PDO::PARAM_STR);
				$r1->bindParam(3,$uid,PDO::PARAM_INT);
				$r1->execute();
				if($r1->rowCount()>0) {
					while($rw1=$r1->fetch(PDO::FETCH_ASSOC)) {
						foreach($resproarr as $var1) {
							if($var1==$rw1['username']) continue 2;
						}
						array_push($resproarr,$rw1['username']);
						if($count >= 10) break;
						echo '<div class="row1">';
							$rw1['buddypic']=str_replace("uploads/".$rw1['username']."/buddypic_uploads","../uploads/".$rw1['username']."/buddypic_uploads",$rw1['buddypic']);
							if(file_exists($rw1['buddypic'])) {
								$rw1['buddypic']=str_replace("../uploads/".$rw1['username']."/buddypic_uploads","uploads/".$rw1['username']."/buddypic_uploads",$rw1['buddypic']);
								echo '<img src="'.$rw1['buddypic'].'" class="rounded-circle srch_pic" alt="Buddy picture of the user">';
							} else {
								echo '<img src="images/def_buddypic.png" class="rounded-circle srch_pic" alt="Default buddy picture">';
							}
							echo '<a href="profile.php?un='.$rw1['username'].'" class="sfn">'.$rw1["fullname"].'</a>
								<a href="profile.php?un='.$rw1['username'].'" class="text-muted sun" style="float:right">@'.$rw1["username"].'</a>';
						echo '</div>';
					}
				} else {
					$r1=null;
					$temp="%".$q."%";
					$r1=$conn->prepare("SELECT fullname,username,buddypic FROM users WHERE (fullname LIKE ? OR username LIKE ? OR description LIKE ?) AND id!=? ORDER BY nobuddies DESC LIMIT 11");
					$r1->bindParam(1,$temp,PDO::PARAM_STR);
					$r1->bindParam(2,$temp,PDO::PARAM_STR);
					$r1->bindParam(3,$temp,PDO::PARAM_STR);
					$r1->bindParam(4,$uid,PDO::PARAM_INT);
					$r1->execute();
					if($r1->rowCount()>0) {
						while($rw1=$r1->fetch(PDO::FETCH_ASSOC)) {
							foreach($resproarr as $var1) {
								if($var1==$rw1['username']) continue 2;
							}	
							array_push($resproarr,$rw1['username']);
							if($count >= 10) break;
							echo '<div class="row1">';
								$rw1['buddypic']=str_replace("uploads/".$rw1['username']."/buddypic_uploads","../uploads/".$rw1['username']."/buddypic_uploads",$rw1['buddypic']);
								if(file_exists($rw1['buddypic'])) {
									$rw1['buddypic']=str_replace("../uploads/".$rw1['username']."/buddypic_uploads","uploads/".$rw1['username']."/buddypic_uploads",$rw1['buddypic']);
									echo '<img src="'.$rw1['buddypic'].'" class="rounded-circle srch_pic" alt="Buddy picture of the user">';
								} else {
									echo '<img src="images/def_buddypic.png" class="rounded-circle srch_pic" alt="Default buddy picture">';
								}
								echo '<a href="profile.php?un='.$rw1['username'].'" class="sfn">'.$rw1["fullname"].'</a>
								<a href="profile.php?un='.$rw1['username'].'" class="text-muted sun" style="float:right">@'.$rw1["username"].'<span class="basis_srch_sugg text-muted"><b> &#9679 BASED ON PROFILE DESCRIPTION</b></span></a>';
							echo '</div>';
						}
					}
				}
			}
		}
		if($r1->rowCount()<=0) echo '<div style="padding:10px" class="text-center">There are no suggestions according to your search.<br>Check your spelling or try editing your search</div>';	
		$r1=null;
	}	
} else {
	die("ERROR!!!");
}
$conn=null;
?>