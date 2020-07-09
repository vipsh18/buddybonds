<?php
include 'varcsc.php'; 
error_reporting(0);
$tz = new DateTimeZone($tz);
$dt = new DateTime();
$dt->setTimezone($tz);
if($conn) {
	$mid=$_GET['mid'];
	$res=$conn->prepare("SELECT message_time,seen,seen_time FROM messages WHERE message_id=?");
	$res->bindParam(1,$mid,PDO::PARAM_INT);
	$res->execute();
	$row=$res->fetch(PDO::FETCH_ASSOC);
	$mt=strtotime($row['message_time']);
	echo '<div><b>Message sent :</b> ';
	$dt->setTimestamp($mt);
    if(time()-$mt>0 && time()-$mt<86400) echo $dt->format("H:i A");
    else if(time()-$mt>=86400 && time()-$mt<604800) echo $dt->format("D, H:i A");
    else if(time()-$mt>=604800 && time()-$mt<31536000) echo $dt->format("M j, H:i A");
    else echo $dt->format("M j, Y H:i A");
	echo '</div>';
	echo '<div><b>Message read :</b> ';
	if($row['seen']==1) {
		$st=strtotime($row['seen_time']);
		$dt->setTimestamp($st);
        if(time()-$st>0 && time()-$st<86400) echo $dt->format("H:i A");
        else if(time()-$st>=86400 && time()-$st<604800) echo $dt->format("D, H:i A");
        else if(time()-$st>=604800 && time()-$st<31536000) echo $dt->format("M j, H:i A");
        else echo $dt->format("M j, Y H:i A");
	} else if($row['seen']==0) {
		echo "- - -";
	}	
	echo '</div>';
	$res=null;
} else {
	die("ERROR!");
}
$conn=null;
?>