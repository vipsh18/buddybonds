<?php
include 'varcsc.php';
$uid=base64_decode($_SESSION['id']);
if($conn) {
	$re=$conn->prepare("UPDATE notifications SET seen='1' WHERE bud_id2=? AND seen='0'");
	$re->bindParam(1,$uid,PDO::PARAM_INT);
	$re->execute();
	$re=null;
} else die("ERROR!");
$conn=null;
?>
