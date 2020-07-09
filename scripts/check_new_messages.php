<?php 
include 'varcsc.php';
if(!$_GET['id']) {
	header("Location:http://localhost/buddyBonds_backup/messages.php");
	$conn=null;
	exit();
}
$tz = new DateTimeZone($tz);
$dt = new DateTime();
$dt->setTimezone($tz);
$id = $_GET['id'];
$uid = base64_decode($_SESSION['id']);
$update = new \stdClass();
//get request sender info
$uiqr=$conn->prepare("SELECT public_key FROM users WHERE id=?");
$uiqr->bindParam(1,$uid,PDO::PARAM_INT);
$uiqr->execute();
$uiqrow=$uiqr->fetch(PDO::FETCH_ASSOC);
$upuk=$uiqrow['public_key'];
//get other person's info
$dqr=$conn->prepare("SELECT logout_time,private_key,public_key FROM users WHERE id=?");
$dqr->bindParam(1,$id,PDO::PARAM_INT);
$dqr->execute();
$dqrow = $dqr->fetch(PDO::FETCH_ASSOC);
$opk = $dqrow['private_key'];
$lt = $dqrow['logout_time'];	
//check typing status
$ctsr=$conn->prepare("SELECT bud_id1,bud_id2,typing1,typing2 FROM buddies WHERE (bud_id1=:uid OR bud_id1=:id) AND (bud_id2=:uid OR bud_id2=:id) AND active='1'");
$ctsr->bindParam(":uid",$uid,PDO::PARAM_INT);
$ctsr->bindParam(":id",$id,PDO::PARAM_INT);
$ctsr->execute();
if($ctsr->rowCount()>0) {
	$ctsrw=$ctsr->fetch(PDO::FETCH_ASSOC);
	if($lt=="0001-01-01 00:00:00") {
		if($ctsrw['bud_id1']==$uid) $typing_status=$ctsrw['typing2'];
		else if($ctsrw['bud_id2']==$uid) $typing_status=$ctsrw['typing1'];
		if($typing_status=='1') $update->last_online = "Typing...";
		else $update->last_online = "Online";
    } else {
	    $lt=strtotime($lt);
		$dt->setTimestamp($lt);
        if(time()-$lt>0 && time()-$lt<86400) {
            $dt = $dt->format("H:i A");
            $update->last_online = "Last Seen ".$dt;
        }
        else if(time()-$lt>=86400 && time()-$lt<604800) {
            $dt = $dt->format("D, H:i A");
            $update->last_online = "Last Seen ".$dt;
        }
        else if(time()-$lt>=604800 && time()-$lt<31536000) {
            $dt = $dt->format("M j, H:i A");
            $update->last_online = "Last Seen ".$dt;
        }
        else {
            $dt = $dt->format("M j, Y H:i A");
            $update->last_online = "Last Seen ".$dt;
        }
    }
}
$ctsr = null;
$glmt = $conn->prepare("SELECT message_id FROM messages WHERE bud_id1=? AND bud_id2=? AND seen='0' ORDER BY message_time DESC LIMIT 1");
$glmt->bindParam(1,$opk,PDO::PARAM_STR);
$glmt->bindParam(2,$upuk,PDO::PARAM_STR);
$glmt->execute();
if($glmt->rowCount() > 0) {
	$glmtr = $glmt->fetch(PDO::FETCH_ASSOC);
	$update->new_message = 1;
	$update->message_id = $glmtr['message_id'];
} else $update->new_message = 0;
echo json_encode($update);
$conn = null;
?>