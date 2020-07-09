<?php 
include 'varcsc.php';
date_default_timezone_set($tz);
$uid=base64_decode($_SESSION['id']);
if($conn) {
	$id=$_GET['userid'];
	//get current bond
	$gcb = $conn->prepare("SELECT bond FROM bonds WHERE bud_id1 = ? AND bud_id2 = ?");
	$gcb->bindParam(1, $uid, PDO::PARAM_INT);
	$gcb->bindParam(2, $id, PDO::PARAM_INT);
	$gcb->execute();
	$gcbr = $gcb->fetch(PDO::FETCH_ASSOC);
	$bond = $gcbr['bond'] - 10;
	//update bond
	$ub = $conn->prepare("UPDATE bonds SET bond = ? WHERE bud_id1 = ? AND bud_id2 = ?");
	$ub->bindParam(1, $bond, PDO::PARAM_INT);
	$ub->bindParam(2, $uid, PDO::PARAM_INT);
	$ub->bindParam(3, $id, PDO::PARAM_INT);
	//get no of buddies of user1
	$gnobr1=$conn->prepare("SELECT nobuddies FROM users WHERE id=?");
	$gnobr1->bindParam(1,$id,PDO::PARAM_INT);
	$gnobr1->execute();
	$gnobrw1=$gnobr1->fetch(PDO::FETCH_ASSOC);
	$nob1=$gnobrw1['nobuddies']-1;
	$gnobrw1=null;
	//get no of buddies of user2
	$gnobr2=$conn->prepare("SELECT nobuddies FROM users WHERE id=?");
	$gnobr2->bindParam(1,$uid,PDO::PARAM_INT);
	$gnobr2->execute();
	$gnobrw2=$gnobr2->fetch(PDO::FETCH_ASSOC);
	$nob2=$gnobrw2['nobuddies']-1;
	$gnobrw2=null;
	//update no of buddies 1
	$unobr1=$conn->prepare("UPDATE users SET nobuddies=? WHERE id=?");
	$unobr1->bindParam(1,$nob1,PDO::PARAM_INT);
	$unobr1->bindParam(2,$id,PDO::PARAM_INT);
	$unobr1->execute();
	$unobr1=null;
	//update no of buddies 2
	$unobr2=$conn->prepare("UPDATE users SET nobuddies=? WHERE id=?");
	$unobr2->bindParam(1,$nob2,PDO::PARAM_INT);
	$unobr2->bindParam(2,$uid,PDO::PARAM_INT);
	$unobr2->execute();
	$unobr2=null;
	$res=$conn->prepare("DELETE FROM buddies WHERE (bud_id1=:uid OR bud_id1=:userid) AND (bud_id2=:uid OR bud_id2=:userid) AND active='1'");
	$res->bindParam(":uid",$uid,PDO::PARAM_INT);
	$res->bindParam(":userid",$id,PDO::PARAM_INT);	
	//remove from notifications buddy accept
	$res2=$conn->prepare("DELETE FROM notifications WHERE (bud_id1=:uid OR bud_id1=:userid) AND (bud_id2=:uid OR bud_id2=:userid) AND notification_type='buddy accept'");
	$res2->bindParam(":uid",$uid,PDO::PARAM_INT);
	$res2->bindParam(":userid",$id,PDO::PARAM_INT);	
	//remove from activity bbuddy accept
	$res3=$conn->prepare("SELECT FROM activity WHERE (bud_id1=:uid OR bud_id1=:userid) AND (bud_id2:uid OR bud_id2=:userid) AND activity_type='buddy_accept'");
	$res3->bindParam(":uid",$uid,PDO::PARAM_INT);
	$res3->bindParam(":userid",$id,PDO::PARAM_INT);	
	//delete from notifications (about buddy request)
	$dfnr=$conn->prepare("DELETE FROM notifications WHERE (bud_id1=:uid OR bud_id1=:id) AND (bud_id2=:uid OR bud_id2=:id) AND notification_type='buddy request'");
	$dfnr->bindParam(":uid",$uid,PDO::PARAM_INT);
	$dfnr->bindParam(":id",$id,PDO::PARAM_INT);	
	//delete from activity (about buddy request)
	$dfar=$conn->prepare("DELETE FROM activity WHERE (bud_id1=:uid OR bud_id1=:id) AND (bud_id2=:uid OR bud_id2=:id) AND activity_type='buddy request'");
	$dfar->bindParam(":uid",$uid,PDO::PARAM_INT);
	$dfar->bindParam(":id",$id,PDO::PARAM_INT);
	if(($res->execute()) && ($res2->execute()) && ($res3->execute()) && ($dfnr->execute()) && ($dfar->execute()) && ($ub->execute())) {
		echo '<b>Buddy +</b>';
	} else {
		echo 'Could not complete the action!';
		exit();
	}
	$res=$res2=$res3=$dfar=$dfnr=$ub=$gcb=null;
} else {
	die("ERROR!");
}
$conn=null;
?>