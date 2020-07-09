<?php
include 'varcsc.php';
if($conn) {
	$id=$_POST['id'];
	$uid=base64_decode($_SESSION['id']);
	//get cookie info
	$gcir=$conn->prepare("SELECT buddypic FROM users WHERE id=?");
	$gcir->bindParam(1,$uid,PDO::PARAM_INT);
	$gcir->execute();
	$gcirw=$gcir->fetch(PDO::FETCH_ASSOC);
	$gcirw['buddypic']=str_replace("uploads/".base64_decode($_SESSION['username'])."/buddypic_uploads","../uploads/".base64_decode($_SESSION['username'])."/buddypic_uploads",$gcirw['buddypic']);
	if(file_exists($gcirw['buddypic'])) {
		$gcirw['buddypic']=str_replace("../uploads/".base64_decode($_SESSION['username'])."/buddypic_uploads","uploads/".base64_decode($_SESSION['username'])."/buddypic_uploads",$gcirw['buddypic']);
		echo '<img src="'.$gcirw['buddypic'].'" class="rounded-circle img-fluid mtl_pic">';
	} else echo '<img src="images/def_buddypic.png" class="rounded-circle img-fluid mtl_pic">';
	//get user info
	$guir=$conn->prepare("SELECT username,buddypic FROM users WHERE id=?");
	$guir->bindParam(1,$id,PDO::PARAM_INT);
	$guir->execute();
	$guirw=$guir->fetch(PDO::FETCH_ASSOC);
	echo '<span id="mtl_bud_un" class="text-success"><b>Your bond &#10084; with <span class="text-primary">'.$guirw['username'].'</span></b></span>';
	$guirw['buddypic']=str_replace("uploads/".$guirw['username']."/buddypic_uploads","../uploads/".$guirw['username']."/buddypic_uploads",$guirw['buddypic']);
	if(file_exists($guirw['buddypic'])) {
		$guirw['buddypic']=str_replace("../uploads/".$guirw['username']."/buddypic_uploads","uploads/".$guirw['username']."/buddypic_uploads",$guirw['buddypic']);
		echo '<img src="'.$guirw['buddypic'].'" class="rounded-circle img-fluid mtl_pic">';
	} else echo '<img src="images/def_buddypic.png" class="rounded-circle img-fluid mtl_pic">';
	//get buddy info 
	$gbir=$conn->prepare("SELECT * FROM buddies WHERE (bud_id1=:uid AND bud_id2=:id) OR (bud_id1=:id AND bud_id2=:uid) AND active='1'");
	$gbir->bindParam(":uid",$uid,PDO::PARAM_INT);
	$gbir->bindParam(":id",$id,PDO::PARAM_INT);
	$gbir->execute();
	$gbirw=$gbir->fetch(PDO::FETCH_ASSOC);
	echo '<br><span class="mtl_bud_span text-muted"><b>Buddies since </b>';
	$lt = strtotime($gbirw['buddy_time']);
	if(time()-$lt>0 && time()-$lt<86400) echo date('H:i A',$lt+19800);
	else if(time()-$lt>=86400 && time()-$lt<604800) echo date('D,H:i A',$lt+19800);
	else if(time()-$lt>=604800 && time()-$lt<31536000) echo date('M d,H:i A',$lt+19800);
    else echo date('M d, Y H:i A',$lt+19800);
	echo '</span><br>';
	echo '<span class="mtl_bud_span"><b>Mutual buddies </b>:</span><br>';
	//get all my buddies
	$gambr=$conn->prepare("SELECT * FROM buddies WHERE (bud_id1=:uid OR bud_id2=:uid) AND active='1'");
	$gambr->bindParam(":uid",$uid,PDO::PARAM_INT);
	$gambr->execute();
	$x=0;
	while($gambrw=$gambr->fetch(PDO::FETCH_ASSOC)) {
	    //get buddies' id
		if($uid==$gambrw['bud_id1']) $userid=$gambrw['bud_id2'];
		else if($uid==$gambrw['bud_id2']) $userid=$gambrw['bud_id1'];
		//check if mutual
		$cimr=$conn->prepare("SELECT * FROM buddies WHERE ((bud_id1=:id AND bud_id2=:userid) OR (bud_id1=:userid AND bud_id2=:id)) AND active='1'");
		$cimr->bindParam(":id",$id,PDO::PARAM_INT);
		$cimr->bindParam(":userid",$userid,PDO::PARAM_INT);
		$cimr->execute();
		if($cimr->rowCount()>0) {
			//get mutual buddy info
			$gmbir=$conn->prepare("SELECT username,buddypic FROM users WHERE id=?");
			$gmbir->bindParam(1,$userid,PDO::PARAM_INT);
			$gmbir->execute();
			$gmbirw=$gmbir->fetch(PDO::FETCH_ASSOC);
			$gmbirw['buddypic']=str_replace("uploads/".$gmbirw['username']."/buddypic_uploads","../uploads/".$gmbirw['username']."/buddypic_uploads",$gmbirw['buddypic']);
			echo '<div class="mtl_row">';
			if(file_exists($gmbirw['buddypic'])) {
				$gmbirw['buddypic']=str_replace("../uploads/".$gmbirw['username']."/buddypic_uploads","uploads/".$gmbirw['username']."/buddypic_uploads",$gmbirw['buddypic']);
				echo '<img src="'.$gmbirw['buddypic'].'" class="rounded-circle img-fluid mtl_bud_disp">';
			} else echo '<img src="images/def_buddypic.png" class="rounded-circle img-fluid mtl_bud_disp">';
			echo '<a href="profile.php?un='.$gmbirw['username'].'" class="mtl_un text-primary"><b>'.$gmbirw['username'].'</b></a></div>';
			$x++;
		} else continue;
	}
	if($x == 0) echo '<div class="text-dark mtl_bud_span">You do not have any mutual buddies.</div>';
} else die("Error!");
$conn=null;
?>