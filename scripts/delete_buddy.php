<?php
include 'varcsc.php';
if($conn) {
	$id=$_POST['id'];
	$uid=base64_decode($_SESSION['id']);
	//get username
	$gunr=$conn->prepare("SELECT * FROM users WHERE id=?");
	$gunr->bindParam(1,$id,PDO::PARAM_INT);
	$gunr->execute();
	$gunrw=$gunr->fetch(PDO::FETCH_ASSOC);
	$getun=$gunrw['username'];
	$gunr=null;
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
	$delbr=$conn->prepare("DELETE FROM buddies WHERE (bud_id1=:uid OR bud_id1=:id) AND (bud_id2=:uid OR bud_id2=:id) AND active='1'");
	$delbr->bindParam(":uid",$uid,PDO::PARAM_INT);
	$delbr->bindParam(":id",$id,PDO::PARAM_INT);	
	$delbr->execute();
	$delbr=null;
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
	//delete from notifications (about buddy request)
	$dfnr=$conn->prepare("DELETE FROM notifications WHERE (bud_id1=:uid OR bud_id1=:id) AND (bud_id2=:uid OR bud_id2=:id) AND notification_type='buddy request'");
	$dfnr->bindParam(":uid",$uid,PDO::PARAM_INT);
	$dfnr->bindParam(":id",$id,PDO::PARAM_INT);
	$dfnr->execute();
	$dfnr=null;
	//delete from notifications (about buddy accept)
	$dfnr1=$conn->prepare("DELETE FROM notifications WHERE (bud_id1=:uid OR bud_id1=:id) AND (bud_id2=:uid OR bud_id2=:id) AND notification_type='buddy accept'");
	$dfnr1->bindParam(":uid",$uid,PDO::PARAM_INT);
	$dfnr1->bindParam(":id",$id,PDO::PARAM_INT);
	$dfnr1->execute();
	$dfnr1=null;
	//delete from activity (about buddy request)
	$dfar=$conn->prepare("DELETE FROM activity WHERE (bud_id1=:uid OR bud_id1=:id) AND (bud_id2=:uid OR bud_id2=:id) AND activity_type='buddy request'");
	$dfar->bindParam(":uid",$uid,PDO::PARAM_INT);
	$dfar->bindParam(":id",$id,PDO::PARAM_INT);
	$dfar->execute();
	$dfar=null;
	//delete from notifications (about buddy accept)
	$dfar1=$conn->prepare("DELETE FROM activity WHERE (bud_id1=:uid OR bud_id1=:id) AND (bud_id2=:uid OR bud_id2=:id) AND activity_type='buddy accept'");
	$dfar1->bindParam(":uid",$uid,PDO::PARAM_INT);
	$dfar1->bindParam(":id",$id,PDO::PARAM_INT);
	$dfar1->execute();
	$dfar1=null;
	//get your buddy info
	$qr=$conn->prepare("SELECT * FROM buddies WHERE (bud_id1=:uid OR bud_id2=:uid) AND active='1'");
	$qr->bindParam(":uid",$uid,PDO::PARAM_INT);
	$qr->execute();
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
	$ub->execute();
	echo '<div id="buddy_list">';
	if($qr->rowCount()>0) {
		while($qrw=$qr->fetch(PDO::FETCH_ASSOC)) {
			if($qrw['bud_id1']==$uid) {
				$bdid2=$qrw['bud_id2'];
				//buddy dtls query
				$bqr=$conn->prepare("SELECT * FROM users WHERE id=?");
				$bqr->bindParam(1,$bdid2,PDO::PARAM_INT);
				$bqr->execute();
				$bqrw=$bqr->fetch(PDO::FETCH_ASSOC);
				$bqrw['buddypic']=str_replace("uploads/".$bqrw['username']."/buddypic_uploads","../uploads/".$bqrw['username']."/buddypic_uploads",$bqrw['buddypic']);
				echo '<div class="bdrow">';
					if(file_exists($bqrw['buddypic'])) {
						$bqrw['buddypic']=str_replace("../uploads/".$bqrw['username']."/buddypic_uploads","uploads/".$bqrw['username']."/buddypic_uploads",$bqrw['buddypic']);
						echo '<img src="'.$bqrw['buddypic'].'" class="rounded-circle blbp">';
					} else {
						echo '<img src="http://localhost/buddyBonds_backup/images/def_buddypic.png" class="rounded-circle blbp">';
					}
					echo '<a href="profile.php?un='.$bqrw['username'].'" class="bdlink">';
					echo '<span class="bdfn text-dark"><b>'.$bqrw['fullname'].'</b></span>';
					echo '<span class="bdun text-muted">@'.$bqrw['username'].'</span>';
					echo '</a>';
					if($uid==$id) {
						echo '<a href="javascript:void(0)" class="text-muted dlt_btn" title="Unbuddy user" onclick="dlt_bdy('.$bdid2.')"><i class="fas fa-trash-alt"></i></a>';
					}
				echo '</div>';
				$bqr=null;
			} else if($qrw['bud_id2']==$uid) {
				$bdid1=$qrw['bud_id1'];
				//buddy dtls query
				$bqr=$conn->prepare("SELECT * FROM users WHERE id=?");
				$bqr->bindParam(1,$bdid1,PDO::PARAM_INT);
				$bqr->execute();
				$bqrw=$bqr->fetch(PDO::FETCH_ASSOC);
				$bqrw['buddypic']=str_replace("uploads/".$bqrw['username']."/buddypic_uploads","../uploads/".$bqrw['username']."/buddypic_uploads",$bqrw['buddypic']);
				echo '<div class="bdrow">';
					if(file_exists($bqrw['buddypic'])) {
						$bqrw['buddypic']=str_replace("../uploads/".$bqrw['username']."/buddypic_uploads","uploads/".$bqrw['username']."/buddypic_uploads",$bqrw['buddypic']);
						echo '<img src="'.$bqrw['buddypic'].'" class="rounded-circle blbp">';
					} else {
						echo '<img src="http://localhost/buddyBonds_backup/images/def_buddypic.png" class="rounded-circle blbp">';
					}
					echo '<a href="profile.php?un='.$bqrw['username'].'" class="bdlink">';
					echo '<span class="bdfn text-dark"><b>'.$bqrw['fullname'].'</b></span>';
					echo '<span class="bdun text-muted">@'.$bqrw['username'].'</span>';
					echo '</a>';
					if($uid==$id) echo '<a href="javascript:void(0)" class="text-muted dlt_btn" title="Unbuddy user" onclick="dlt_bdy('.$bdid1.')"><i class="fas fa-trash-alt"></i></a>';
				echo '</div>';
				$bqr=null;
			}
		}
	} else {
		echo '<div class="text-center msg">You have no buddies yet!</div>';
	} 
	$qr=null;
	echo '</div>';
	$qr = $gcb = $ub = null;
} else {
	die("Error!");
}
$conn=null;
?>