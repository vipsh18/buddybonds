<?php
include 'varcsc.php';
date_default_timezone_set($tz);
$uid = base64_decode($_SESSION['id']);
if($conn) {
	$update = new \stdClass();
	$uiqr = $conn->prepare("SELECT * FROM users WHERE id=?");
	$uiqr->bindParam(1,$uid,PDO::PARAM_INT);
	$uiqr->execute();
	$uirow = $uiqr->fetch(PDO::FETCH_ASSOC);
	$upuk = $uirow['public_key'];
	$uiqr = null;
	$res = $conn->prepare("SELECT DISTINCT bud_id1 FROM messages WHERE bud_id2=? AND seen='0'");
	$res->bindParam(1,$upuk,PDO::PARAM_STR);
	$res->execute();
	$msgs = $res->rowCount();
	$res1 = $conn->prepare("SELECT notification_type,post_id FROM notifications WHERE bud_id2=:uid AND seen='0' AND bud_id1!=:uid");
	$res1->bindParam(":uid",$uid,PDO::PARAM_INT);
	$res1->execute();
	$ntfs = $res1->rowCount();
	if($ntfs >= 1) {
		$ntfs = 0;
		$bondarr = array();
		$cmntarr = array();
		while($res1w = $res1->fetch(PDO::FETCH_ASSOC)) {
			foreach($bondarr as $var1) {
				if(($var1 == $res1w['post_id']) && ($res1w['notification_type'] == 'bond')) continue 2;	
			}
			foreach($cmntarr as $var1) {
				if(($var1 == $res1w['post_id']) && ($res1w['notification_type'] == 'comment')) continue 2;
			}
			if($res1w['notification_type'] == 'bond') array_push($bondarr, $res1w['post_id']);
			else if($res1w['notification_type'] == 'comment') array_push($cmntarr, $res1w['post_id']);
			$ntfs++;
		}
	}
	if(($msgs >= 1) || ($ntfs >= 1)) {
	    $update->new_msg = $msgs;
	    $update->new_ntf = $ntfs;
	}
    echo json_encode($update);
	$res = null;
} else {
	die("ERROR!");
}
$conn = null;
?>