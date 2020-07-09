<?php 
include 'varcsc.php';
if($conn) {
	$id=$_POST['nadbid'];
	$uid=base64_decode($_SESSION['id']);
	//check for bonds
	$cfb = $conn->prepare("SELECT bond FROM bonds WHERE bud_id1=? AND bud_id2=?");
	$cfb->bindParam(1,$uid,PDO::PARAM_INT);
	$cfb->bindParam(2,$id,PDO::PARAM_INT);
	$cfb->execute();
	$cfbr = $cfb->fetch(PDO::FETCH_ASSOC);
	$bond = $cfbr['bond'] - 10;
	if($cfb->rowCount() <= 0) {
		//make new bond
		$mnb = $conn->prepare("INSERT INTO bonds(bud_id1, bud_id2) VALUES(?,?)");
		$mnb->bindParam(1,$uid,PDO::PARAM_INT);
		$mnb->bindParam(2,$id,PDO::PARAM_INT);
		$mnb->execute();
		$bond = -10;
	} 
	//update bond
	$ub = $conn->prepare("UPDATE bonds SET bond = ? WHERE bud_id1 = ? AND bud_id2 = ?");
	$ub->bindParam(1, $bond, PDO::PARAM_INT);
	$ub->bindParam(2, $uid, PDO::PARAM_INT);
	$ub->bindParam(3, $id, PDO::PARAM_INT);
	$dfbr=$conn->prepare("DELETE FROM buddies WHERE bud_id1=? AND bud_id2=? AND active='0'");
	$dfbr->bindParam(1,$id,PDO::PARAM_INT);
	$dfbr->bindParam(2,$uid,PDO::PARAM_INT);	
	$dfnr=$conn->prepare("DELETE FROM notifications WHERE bud_id1=? AND bud_id2=? AND notification_type='buddy request'");
	$dfnr->bindParam(1,$id,PDO::PARAM_INT);
	$dfnr->bindParam(2,$uid,PDO::PARAM_INT);
	$dfar=$conn->prepare("DELETE FROM activity WHERE (bud_id1=:uid OR bud_id1=:id) AND (bud_id2=:uid OR bud_id2=:id) AND activity_type='buddy request'");
	$dfar->bindParam(":uid",$uid,PDO::PARAM_INT);
	$dfar->bindParam(":id",$id,PDO::PARAM_INT);
	if(($dfbr->execute()) && ($dfnr->execute()) && ($dfar->execute()) && ($ub->execute())) {
		echo '<button class="btns_rw_btns btn-outline-success btn" onclick=buddy('.$id.')><i class="fas fa-user"></i> Add Buddy <i class="fas fa-plus"></i></button>';
	} else {
		echo 'Some error occured!';
		exit();
	}
	$dfbr=$dfnr=$dfar=$cfb=$mnr=$ub=null;
} else {
	die("Error!");
}
$conn=null;
?>