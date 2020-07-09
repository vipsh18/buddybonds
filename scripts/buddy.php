<?php
include 'varcsc.php';
date_default_timezone_set($tz);
if($conn) {
	$id=$_POST['bid'];
	$uid=base64_decode($_SESSION['id']);
	$cibr=$conn->prepare("SELECT * FROM buddies WHERE (bud_id1=:uid OR bud_id1=:id) AND (bud_id2=:uid OR bud_id2=:id) AND active='1'");
	$cibr->bindParam(":uid",$uid,PDO::PARAM_INT);
	$cibr->bindParam(":id",$id,PDO::PARAM_INT);
	$cibr->execute();
	if($cibr->rowCount()>0) {
		//deleting your buddy 
		$cibrw=$cibr->fetch(PDO::FETCH_ASSOC);
		$bdid=$cibrw['buddies_id'];
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
		$dfbr=$conn->prepare("DELETE FROM buddies WHERE buddies_id=?");
		$dfbr->bindParam(1,$bdid,PDO::PARAM_INT);
		//update no of buddies 1
		$unobr1=$conn->prepare("UPDATE users SET nobuddies=? WHERE id=?");
		$unobr1->bindParam(1,$nob1,PDO::PARAM_INT);
		$unobr1->bindParam(2,$id,PDO::PARAM_INT);
		//update no of buddies 2
		$unobr2=$conn->prepare("UPDATE users SET nobuddies=? WHERE id=?");
		$unobr2->bindParam(1,$nob2,PDO::PARAM_INT);
		$unobr2->bindParam(2,$uid,PDO::PARAM_INT);
		//delete from notifications (about buddy accept)
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
		$dfnr1=$conn->prepare("DELETE FROM notifications WHERE (bud_id1=:uid OR bud_id1=:id) AND (bud_id2=:uid OR bud_id2=:id) AND notification_type='buddy accept'");
		$dfnr1->bindParam(":uid",$uid,PDO::PARAM_INT);
		$dfnr1->bindParam(":id",$id,PDO::PARAM_INT);	
		//delete from notifications (about buddy request)
		$dfnr=$conn->prepare("DELETE FROM notifications WHERE (bud_id1=:uid OR bud_id1=:id) AND (bud_id2=:uid OR bud_id2=:id) AND notification_type='buddy request'");
		$dfnr->bindParam(":uid",$uid,PDO::PARAM_INT);
		$dfnr->bindParam(":id",$id,PDO::PARAM_INT);
		//delete from activity (about buddy request)
		$dfar=$conn->prepare("DELETE FROM activity WHERE (bud_id1=:uid OR bud_id1=:id) AND (bud_id2=:uid OR bud_id2=:id) AND activity_type='buddy request'");
		$dfar->bindParam(":uid",$uid,PDO::PARAM_INT);
		$dfar->bindParam(":id",$id,PDO::PARAM_INT);
		//delete from notifications (about buddy accept)
		$dfar1=$conn->prepare("DELETE FROM activity WHERE (bud_id1=:uid OR bud_id1=:id) AND (bud_id2=:uid OR bud_id2=:id) AND activity_type='buddy accept'");
		$dfar1->bindParam(":uid",$uid,PDO::PARAM_INT);
		$dfar1->bindParam(":id",$id,PDO::PARAM_INT);
		if(($dfbr->execute()) && ($unobr1->execute()) && ($unobr2->execute()) && ($dfnr->execute()) && ($dfnr1->execute()) && ($dfar->execute()) && ($dfar1->execute()) &&($ub->execute())) {
			echo '<button class="btns_rw_btns btn-outline-success btn" onclick=buddy('.$id.')><i class="fas fa-user"></i> Add Buddy <i class="fas fa-plus"></i></button>';
		} else {
			echo 'Could not complete the action1!';
			exit();
		}
		$cibr=$dfbr=$unobr1=$unobr2=$gcb=$ub=null;
	} else {
		//deleting sent request here
		$cibr=$conn->prepare("SELECT * FROM buddies WHERE bud_id1=? AND bud_id2=? AND active='0'");
		$cibr->bindParam(1,$uid,PDO::PARAM_INT);
		$cibr->bindParam(2,$id,PDO::PARAM_INT);
		$cibr->execute();
		if($cibr->rowCount()>0) {
			//delete the request you sent
			//get current bond
			$gcb = $conn->prepare("SELECT bond FROM bonds WHERE bud_id1 = ? AND bud_id2 = ?");
			$gcb->bindParam(1, $uid, PDO::PARAM_INT);
			$gcb->bindParam(2, $id, PDO::PARAM_INT);
			$gcb->execute();
			$gcbr = $gcb->fetch(PDO::FETCH_ASSOC);
			$bond = $gcbr['bond'] - 2;
			//update bond
			$ub = $conn->prepare("UPDATE bonds SET bond = ? WHERE bud_id1 = ? AND bud_id2 = ?");
			$ub->bindParam(1, $bond, PDO::PARAM_INT);
			$ub->bindParam(2, $uid, PDO::PARAM_INT);
			$ub->bindParam(3, $id, PDO::PARAM_INT);
			$dfar=$conn->prepare("DELETE FROM activity WHERE bud_id1=? AND bud_id2=? AND activity_type='buddy request'");
			$dfar->bindParam(1,$uid,PDO::PARAM_INT);
			$dfar->bindParam(2,$id,PDO::PARAM_INT);
			$dfnr=$conn->prepare("DELETE FROM notifications WHERE bud_id1=? AND bud_id2=? AND notification_type='buddy request'");
			$dfnr->bindParam(1,$uid,PDO::PARAM_INT);
			$dfnr->bindParam(2,$id,PDO::PARAM_INT);
			$dfbr=$conn->prepare("DELETE FROM buddies WHERE bud_id1=? AND bud_id2=? AND active='0'");
			$dfbr->bindParam(1,$uid,PDO::PARAM_INT);
			$dfbr->bindParam(2,$id,PDO::PARAM_INT);
			if(($dfar->execute()) &&($dfnr->execute()) &&($dfbr->execute()) &&($ub->execute())) {
				echo '<button class="btns_rw_btns btn-outline-success btn" onclick=buddy('.$id.')><i class="fas fa-user"></i> Add Buddy <i class="fas fa-plus"></i></button>';
			} else {
				echo 'Could not complete the action2!';
				exit();
			}
			$cibr=$dfar=$dfnr=$dfbr=$gcb=$ub=null;
		} else {
			//accepting someone's req
			$cibr=$conn->prepare("SELECT * FROM buddies WHERE bud_id1=? AND bud_id2=? AND active='0'");
			$cibr->bindParam(1,$id,PDO::PARAM_INT);
			$cibr->bindParam(2,$uid,PDO::PARAM_INT);
			$cibr->execute();
			if($cibr->rowCount()>0) {
				//get no of buddies of user1
				$gnobr1=$conn->prepare("SELECT nobuddies FROM users WHERE id=?");
				$gnobr1->bindParam(1,$id,PDO::PARAM_INT);
				$gnobr1->execute();
				$gnobrw1=$gnobr1->fetch(PDO::FETCH_ASSOC);
				$nob1=$gnobrw1['nobuddies']+1;
				$gnobrw1=null;
				//check for bonds
				$cfb = $conn->prepare("SELECT bond FROM bonds WHERE bud_id1=? AND bud_id2=?");
				$cfb->bindParam(1,$uid,PDO::PARAM_INT);
				$cfb->bindParam(2,$id,PDO::PARAM_INT);
				$cfb->execute();
				$cfbr = $cfb->fetch(PDO::FETCH_ASSOC);
				$bond = $cfbr['bond'] + 5;
				if($cfb->rowCount() <= 0) {
					//make new bond
					$mnb = $conn->prepare("INSERT INTO bonds(bud_id1, bud_id2) VALUES(?,?)");
					$mnb->bindParam(1,$uid,PDO::PARAM_INT);
					$mnb->bindParam(2,$id,PDO::PARAM_INT);
					$mnb->execute();
					$bond = 5;
				} 
				//update bond
				$ub = $conn->prepare("UPDATE bonds SET bond = ? WHERE bud_id1 = ? AND bud_id2 = ?");
				$ub->bindParam(1, $bond, PDO::PARAM_INT);
				$ub->bindParam(2, $uid, PDO::PARAM_INT);
				$ub->bindParam(3, $id, PDO::PARAM_INT);
				//get no of buddies of user2
				$gnobr2=$conn->prepare("SELECT nobuddies FROM users WHERE id=?");
				$gnobr2->bindParam(1,$uid,PDO::PARAM_INT);
				$gnobr2->execute();
				$gnobrw2=$gnobr2->fetch(PDO::FETCH_ASSOC);
				$nob2=$gnobrw2['nobuddies']+1;
				$gnobrw2=null;
				//get buddy key
				$gpk1r=$conn->prepare("SELECT public_key FROM users WHERE id=?");
				$gpk1r->bindParam(1,$id,PDO::PARAM_INT);
				$gpk1r->execute();
				$gpk1rw=$gpk1r->fetch(PDO::FETCH_ASSOC);
				$gpk2r=$conn->prepare("SELECT public_key FROM users WHERE id=?");
				$gpk2r->bindParam(1,$uid,PDO::PARAM_INT);
				$gpk2r->execute();
				$gpk2rw=$gpk2r->fetch(PDO::FETCH_ASSOC);
				$bk=$gpk2rw['public_key'].".".$gpk1rw['public_key'];
				$gpk1r=$gpk2r=null;
				//update no of buddies 1
				$unobr1=$conn->prepare("UPDATE users SET nobuddies=? WHERE id=?");
				$unobr1->bindParam(1,$nob1,PDO::PARAM_INT);
				$unobr1->bindParam(2,$id,PDO::PARAM_INT);
				//update no of buddies 2
				$unobr2=$conn->prepare("UPDATE users SET nobuddies=? WHERE id=?");
				$unobr2->bindParam(1,$nob2,PDO::PARAM_INT);
				$unobr2->bindParam(2,$uid,PDO::PARAM_INT);
				//accept request
				$iiar=$conn->prepare("INSERT INTO activity(bud_id1,bud_id2,activity_type,activity_time) VALUES(?,?,'buddy accept',NOW())");
				$iiar->bindParam(1,$uid,PDO::PARAM_INT);
				$iiar->bindParam(2,$id,PDO::PARAM_INT);
				$iinr=$conn->prepare("INSERT INTO notifications(bud_id1,bud_id2,notification_type,notification_time) VALUES(?,?,'buddy accept',NOW())");
				$iinr->bindParam(1,$uid,PDO::PARAM_INT);
				$iinr->bindParam(2,$id,PDO::PARAM_INT);
				$ubr=$conn->prepare("UPDATE buddies SET active='1',buddy_key=?,buddy_time=NOW() WHERE bud_id1=? AND bud_id2=?");
				$ubr->bindParam(1,$bk,PDO::PARAM_STR);
				$ubr->bindParam(2,$id,PDO::PARAM_INT);
				$ubr->bindParam(3,$uid,PDO::PARAM_INT);
				$dbrfnr=$conn->prepare("DELETE FROM notifications WHERE (bud_id1=:uid OR bud_id1=:id) AND (bud_id2=:id OR bud_id2=:uid) AND notification_type='buddy request'");
				$dbrfnr->bindParam(":uid",$uid,PDO::PARAM_INT);
				$dbrfnr->bindParam(":id",$id,PDO::PARAM_INT);
				if(($iiar->execute()) && ($iinr->execute()) && ($ubr->execute()) && ($unobr1->execute()) && ($unobr2->execute()) && ($dbrfnr->execute()) && ($ub->execute())) {
					echo '<button class="btns_rw_btns btn btn-outline-success btn" onclick="unbuddy('.$id.')"><i class="fas fa-handshake"></i> Buddies <i class="fas fa-check"></i></button>';
					echo '<button class="btns_rw_btns btn btn-outline-danger" id="see_mtl" onclick="see_mutual()"><i class="fas fa-heart"></i> See Mutual</button>';
				} else {
					echo 'Could not complete the action3!';
					exit();
				}
				$iiar=$iinr=$ubr=$cibr=$unobr1=$unobr2=$ub=$cfb=$mnb=null;
			} else {
				//send request
				//check for bonds
				$cfb = $conn->prepare("SELECT bond FROM bonds WHERE bud_id1=? AND bud_id2=?");
				$cfb->bindParam(1,$uid,PDO::PARAM_INT);
				$cfb->bindParam(2,$id,PDO::PARAM_INT);
				$cfb->execute();
				if($cfb->rowCount() > 0) {
				    $cfbr = $cfb->fetch(PDO::FETCH_ASSOC);
				    $bond = $cfbr['bond'] + 5;
				}
				if($cfb->rowCount() <= 0) {
					//make new bond
					$mnb = $conn->prepare("INSERT INTO bonds(bud_id1, bud_id2) VALUES(?,?)");
					$mnb->bindParam(1,$uid,PDO::PARAM_INT);
					$mnb->bindParam(2,$id,PDO::PARAM_INT);
					$mnb->execute();
					$bond = 5;
				} 
				//update bond
				$ub = $conn->prepare("UPDATE bonds SET bond = ? WHERE bud_id1 = ? AND bud_id2 = ?");
				$ub->bindParam(1, $bond, PDO::PARAM_INT);
				$ub->bindParam(2, $uid, PDO::PARAM_INT);
				$ub->bindParam(3, $id, PDO::PARAM_INT);
				$iiar=$conn->prepare("INSERT INTO activity(bud_id1,bud_id2,activity_type,activity_time) VALUES(?,?,'buddy request',NOW())");
				$iiar->bindParam(1,$uid,PDO::PARAM_INT);
				$iiar->bindParam(2,$id,PDO::PARAM_INT);
				$iinr=$conn->prepare("INSERT INTO notifications(bud_id1,bud_id2,notification_type,notification_time) VALUES(?,?,'buddy request',NOW())");
				$iinr->bindParam(1,$uid,PDO::PARAM_INT);
				$iinr->bindParam(2,$id,PDO::PARAM_INT);
				$iibr=$conn->prepare("INSERT INTO buddies(bud_id1,bud_id2) VALUES(?,?)");
				$iibr->bindParam(1,$uid,PDO::PARAM_INT);
				$iibr->bindParam(2,$id,PDO::PARAM_INT);
				if(($iiar->execute()) && ($iinr->execute()) &&($iibr->execute()) && ($ub->execute())) {
					echo '<button class="btns_rw_btns btn-outline-success btn" onclick=buddy('.$id.')><i class="fas fa-heart"></i> Buddy requested <i class="fas fa-check"></i></button>';
				} else {
					echo 'Could not complete the action4!';
					exit();	
				}
				$iiar=$iinr=$iibr=$cfb=$mnb=$ub=null;
			}
		}
	}
} else {
	die("ERROR!");
}
$conn=null;
?>