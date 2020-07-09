<?php 
include 'varcsc.php';
$uid = base64_decode($_SESSION['id']);
//change logout time
$cltq=$conn->prepare("UPDATE users SET logout_time=NOW() WHERE id=?");
$cltq->bindParam(1,$uid,PDO::PARAM_INT);
if(!$cltq->execute()) echo 'Logout time set query failed!';
?>