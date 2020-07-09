<?php
include 'varcsc.php'; 
$tz = new DateTimeZone($tz);
$dt = new DateTime();
$dt->setTimezone($tz);
if($conn) {
	$uid=base64_decode($_SESSION['id']);
	$update = new \stdClass();
	$un=$_POST['un'];
	$glor=$conn->prepare("SELECT id,logout_time FROM users WHERE username=?");
	$glor->bindParam(1,$un,PDO::PARAM_STR);
	$glor->execute();
	$glorw=$glor->fetch(PDO::FETCH_ASSOC);
	$bid=$glorw['id'];
	$lt=$glorw['logout_time'];
	//check typing status
	$ctsr=$conn->prepare("SELECT bud_id1,bud_id2,typing1,typing2 FROM buddies WHERE (bud_id1=:uid OR bud_id1=:bid) AND (bud_id2=:uid OR bud_id2=:bid) AND active='1'");
	$ctsr->bindParam(":uid",$uid,PDO::PARAM_INT);
	$ctsr->bindParam(":bid",$bid,PDO::PARAM_INT);
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
	    echo json_encode($update);
	}
	$glor=null;
} else {
	die("Error!");
}
$conn=null;
?>