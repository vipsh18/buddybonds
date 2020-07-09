<?php 
include 'varcsc.php';
if($conn) {
	$uid=base64_decode($_SESSION['id']);
	$postid=$_POST['post_id'];
	//get post info
	$gpir=$conn->prepare("SELECT user_id,nol FROM posts WHERE post_id=?");
	$gpir->bindParam(1,$postid,PDO::PARAM_INT);
	$gpir->execute();
	$gpirw=$gpir->fetch(PDO::FETCH_ASSOC);
	$nol=$gpirw['nol'];
	$id=$gpirw['user_id'];
	$gpir=null;
	$clr=$conn->prepare("SELECT activity_id FROM activity WHERE bud_id1=? AND activity_type='bond' AND post_id=?");
	$clr->bindParam(1,$uid,PDO::PARAM_INT);
	$clr->bindParam(2,$postid,PDO::PARAM_INT);
	$clr->execute();
	if($clr->rowCount()>0) {
		$clrw=$clr->fetch(PDO::FETCH_ASSOC);
		$aid=$clrw['activity_id'];
		$nol=$nol-1;
		//get current bond
		$gcb = $conn->prepare("SELECT bond FROM bonds WHERE bud_id1 = ? AND bud_id2 = ?");
		$gcb->bindParam(1, $uid, PDO::PARAM_INT);
		$gcb->bindParam(2, $id, PDO::PARAM_INT);
		$gcb->execute();
		$gcbr = $gcb->fetch(PDO::FETCH_ASSOC);
		$bond = $gcbr['bond'] - 1;
		if($bond <= 0) $bond = 0;
		//update bond
		$ub = $conn->prepare("UPDATE bonds SET bond = ? WHERE bud_id1 = ? AND bud_id2 = ?");
		$ub->bindParam(1, $bond, PDO::PARAM_INT);
		$ub->bindParam(2, $uid, PDO::PARAM_INT);
		$ub->bindParam(3, $id, PDO::PARAM_INT);
		$qr1=$conn->prepare("UPDATE posts SET nol=? WHERE post_id=?");
		$qr1->bindParam(1,$nol,PDO::PARAM_INT);
		$qr1->bindParam(2,$postid,PDO::PARAM_INT);
		$qr2=$conn->prepare("DELETE FROM activity WHERE activity_id=?");
		$qr2->bindParam(1,$aid,PDO::PARAM_INT);
		$qr3=$conn->prepare("DELETE FROM notifications WHERE bud_id1=? AND post_id=? AND notification_type='bond'");
		$qr3->bindParam(1,$uid,PDO::PARAM_INT);
		$qr3->bindParam(2,$postid,PDO::PARAM_INT);
		if(($qr1->execute()) && ($qr2->execute()) && ($qr3->execute()) && ($ub->execute())) {
			echo '<i class="far fa-heart fa-spin"></i> Bond';
		} else {
			echo 'Could not complete the action1!';
			exit();
		}
	} else {
		$nol=$nol+1;
		$qr1=$qr2=$qr3=null;
		//check for bond
		$cfb = $conn->prepare("SELECT bond FROM bonds WHERE bud_id1=? AND bud_id2=?");
		$cfb->bindParam(1,$uid,PDO::PARAM_INT);
		$cfb->bindParam(2,$id,PDO::PARAM_INT);
		$cfb->execute();
		$cfbr = $cfb->fetch(PDO::FETCH_ASSOC);
		$bond = $cfbr['bond'] + 1;
		if($cfb->rowCount() <= 0) {
			//make new bond
			$mnb = $conn->prepare("INSERT INTO bonds(bud_id1, bud_id2) VALUES(?,?)");
			$mnb->bindParam(1,$uid,PDO::PARAM_INT);
			$mnb->bindParam(2,$id,PDO::PARAM_INT);
			$mnb->execute();
			$bond = 1;
		}
		//update bond
		$ub = $conn->prepare("UPDATE bonds SET bond = ? WHERE bud_id1 = ? AND bud_id2 = ?");
		$ub->bindParam(1, $bond, PDO::PARAM_INT);
		$ub->bindParam(2, $uid, PDO::PARAM_INT);
		$ub->bindParam(3, $id, PDO::PARAM_INT);
		$qr1=$conn->prepare("UPDATE posts SET nol=? WHERE post_id=?");
		$qr1->bindParam(1,$nol,PDO::PARAM_INT);
		$qr1->bindParam(2,$postid,PDO::PARAM_INT);
		$qr2=$conn->prepare("INSERT INTO activity(bud_id1,bud_id2,activity_type,post_id,activity_time) VALUES(?,?,'bond',?,NOW())");
		$qr2->bindParam(1,$uid,PDO::PARAM_INT);
		$qr2->bindParam(2,$id,PDO::PARAM_INT);
		$qr2->bindParam(3,$postid,PDO::PARAM_INT);
		$qr3=$conn->prepare("INSERT INTO notifications(bud_id1,bud_id2,notification_type,post_id,notification_time) VALUES(?,?,'bond',?,NOW())");
		$qr3->bindParam(1,$uid,PDO::PARAM_INT);
		$qr3->bindParam(2,$id,PDO::PARAM_INT);
		$qr3->bindParam(3,$postid,PDO::PARAM_INT);
		if(($qr1->execute()) && ($qr2->execute()) && ($qr3->execute()) && ($ub->execute())) {
			echo '<i class="fas fa-heartbeat"></i> Bond';
		} else {
			echo 'Could not complete the action2!';
			exit();
		}
	}
	$qr1=$qr2=$qr3=$clr=$ub=$gcb=$cfb=null;
} else {
	die("ERROR!");
}
$conn=null;
?>