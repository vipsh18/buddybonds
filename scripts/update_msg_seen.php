<?php
include 'varcsc.php';
date_default_timezone_set($tz);
$uid=base64_decode($_SESSION['id']);
if($conn) {
	$id=$_POST['bid'];
	//select users from uid
	$uiqr=$conn->prepare("SELECT * FROM users WHERE id=?");
	$uiqr->bindParam(1,$uid,PDO::PARAM_INT);
	$uiqr->execute();
	$uiqrw=$uiqr->fetch(PDO::FETCH_ASSOC);
	$upuk=$uiqrw['public_key'];
	$uiqr=null;
	//select users from id
	$ouiqr=$conn->prepare("SELECT * FROM users WHERE id=?");
	$ouiqr->bindParam(1,$id,PDO::PARAM_INT);
	$ouiqr->execute();
	$ouirow=$ouiqr->fetch(PDO::FETCH_ASSOC);
	$opk=$ouirow['private_key'];
	$ouiqr=null;
	//um means update messages...set seen=1
	$umq=$conn->prepare("UPDATE messages SET seen='1',seen_time=NOW() WHERE bud_id1=? AND bud_id2=? AND seen='0'");
	$umq->bindParam(1,$opk,PDO::PARAM_STR);
	$umq->bindParam(2,$upuk,PDO::PARAM_STR);
	$umq->execute();
	$umq=null;
} else {
	die("ERROR!");
}
$conn=null;
?>