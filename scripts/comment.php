<?php
include 'varcsc.php';
$tz = new DateTimeZone($tz);
$dt = new DateTime();
$dt->setTimezone($tz);
function test_input($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}
if($conn) {
	$postid = test_input($_POST['post_id']);
	$cmnt = test_input($_POST['cmnt']);
	$real_cmnt = $cmnt;
	$cmnt = base64_encode($cmnt);
	$uid = base64_decode($_SESSION['id']);
	//get no. of comments 
	$gnocr = $conn->prepare("SELECT user_id,noc,post_time FROM posts WHERE post_id=?");
	$gnocr->bindParam(1,$postid,PDO::PARAM_INT);
	$gnocr->execute();
	$gnocrw = $gnocr->fetch(PDO::FETCH_ASSOC);
	$noc = $gnocrw['noc']+1;
	$pt = $gnocrw['post_time'];
	$id = $gnocrw['user_id'];
	$gnocr = null;
	//get current bond
	$gcb = $conn->prepare("SELECT bond FROM bonds WHERE bud_id1 = ? AND bud_id2 = ?");
	$gcb->bindParam(1, $uid, PDO::PARAM_INT);
	$gcb->bindParam(2, $id, PDO::PARAM_INT);
	$gcb->execute();
	$gcbr = $gcb->fetch(PDO::FETCH_ASSOC);
	$bond = $gcbr['bond'] + 2;
	//update bond
	$ub = $conn->prepare("UPDATE bonds SET bond = ? WHERE bud_id1 = ? AND bud_id2 = ?");
	$ub->bindParam(1, $bond, PDO::PARAM_INT);
	$ub->bindParam(2, $uid, PDO::PARAM_INT);
	$ub->bindParam(3, $id, PDO::PARAM_INT);
	$qr1=$conn->prepare("UPDATE posts SET noc=? WHERE post_id=?");
	$qr1->bindParam(1,$noc,PDO::PARAM_INT);
	$qr1->bindParam(2,$postid,PDO::PARAM_INT);
	$qr2=$conn->prepare("INSERT INTO activity(bud_id1,bud_id2,activity_type,activity_content,post_id,activity_time) VALUES(?,?,'comment',?,?,NOW())");
	$qr2->bindParam(1,$uid,PDO::PARAM_INT);
	$qr2->bindParam(2,$id,PDO::PARAM_INT);
	$qr2->bindParam(3,$cmnt,PDO::PARAM_STR);
	$qr2->bindParam(4,$postid,PDO::PARAM_INT);
	$qr3=$conn->prepare("INSERT INTO notifications(bud_id1,bud_id2,notification_type,post_id,notification_time) VALUES(?,?,'comment',?,NOW())");
	$qr3->bindParam(1,$uid,PDO::PARAM_INT);
	$qr3->bindParam(2,$id,PDO::PARAM_INT);
	$qr3->bindParam(3,$postid,PDO::PARAM_INT);
	//analyze the comment
	$mentions = array();
    if(strpos($real_cmnt, "@") !== false) {
	    $a = preg_replace_callback('/@.+?\b/', function($m)  {
            $str = substr($m[0], 1);
            array_push($GLOBALS['mentions'], $str);
            return sprintf("%s", $str);
        }, $real_cmnt);
        foreach($mentions as $mun) {
            //get id for each username
            $gidfun = $conn->prepare("SELECT id FROM users WHERE username=?");
            $gidfun->bindParam(1,$mun,PDO::PARAM_STR);
            $gidfun->execute();
            $gidfunr = $gidfun->fetch(PDO::FETCH_ASSOC);
            $munid = $gidfunr['id'];
            $smn = $conn->prepare("INSERT INTO notifications(bud_id1,bud_id2,notification_type,post_id,notification_time) VALUES(?,?,'quote',?,NOW())");
		    $smn->bindParam(1,$uid,PDO::PARAM_INT);
		    $smn->bindParam(2,$munid,PDO::PARAM_INT);
		    $smn->bindParam(3,$postid,PDO::PARAM_INT);
		    $smn->execute();
        }
    }
	if(($qr1->execute()) && ($qr2->execute()) && ($qr3->execute()) && ($ub->execute())) {} else {
		echo 'Could not complete the action!';
		$conn = null;
		exit();
	}
	$qr1=$qr2=$qr3=$ub=null;
} else {
	die("ERROR!!!");
}
$conn=null;
?>