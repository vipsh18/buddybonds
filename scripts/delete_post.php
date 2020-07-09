<?php
include 'varcsc.php';
date_default_timezone_set($tz);
if($conn) {
	$postid=$_POST['post_id'];
	//get post location
	$gplr=$conn->prepare("SELECT * FROM posts WHERE post_id=?");
	$gplr->bindParam(1,$postid,PDO::PARAM_INT);
	$gplr->execute();
	$gplrw=$gplr->fetch(PDO::FETCH_ASSOC);
	$pl="../".$gplrw['post_content'];
	$dppr=$conn->prepare("DELETE FROM posts WHERE post_id=?");
	$dppr->bindParam(1,$postid,PDO::PARAM_INT);
	//delete from activity
	$dfar=$conn->prepare("DELETE FROM activity WHERE post_id=?");
	$dfar->bindParam(1,$postid,PDO::PARAM_INT);
	//delete from notifications
	$dfnr=$conn->prepare("DELETE FROM notifications WHERE post_id=?");
	$dfnr->bindParam(1,$postid,PDO::PARAM_INT);
	if(($dppr->execute()) && ($dfar->execute()) && ($dfnr->execute())) unlink($pl);
	$gplr=$dppr=null;
} else die("Error!");
$conn=null;
?>