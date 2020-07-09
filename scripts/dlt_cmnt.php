<?php
include 'varcsc.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8"/>
	<meta name="referrer" content="origin-when-crossorigin"/>
	<meta name="author" content="Vipul Sharma"/>
	<meta name="user-scalable" content="no"/>
	<meta name="robots" content="noindex,nofollow"/>
</head>
<body>
<?php
if($conn) {
	$postid=$_POST['post_id'];
	$actid=$_POST['act_id'];
	$uid=base64_decode($_SESSION['id']);
	//fetch activity
	$fatr=$conn->prepare("SELECT * FROM activity WHERE activity_id=?");
	$fatr->bindParam(1,$actid,PDO::PARAM_INT);
	$fatr->execute();
	$fatrw=$fatr->fetch(PDO::FETCH_ASSOC);
	$acttym=$fatrw['activity_time'];
	$fatr=null;
	//fetch from notifications
	$ffnr=$conn->prepare("SELECT * FROM notifications WHERE bud_id1=? AND notification_type='comment' AND post_id=? AND notification_time='$acttym'");
	$ffnr->bindParam(1,$uid,PDO::PARAM_INT);
	$ffnr->bindParam(2,$postid,PDO::PARAM_INT);
	$ffnr->execute();
	$ffnrw=$ffnr->fetch(PDO::FETCH_ASSOC);
	$nid=$ffnrw['notification_id'];
	$ffnr=null;
	$snocr=$conn->prepare("SELECT * FROM posts WHERE post_id=?");
	$snocr->bindParam(1,$postid,PDO::PARAM_INT);
	$snocr->execute();
	$snocrw=$snocr->fetch(PDO::FETCH_ASSOC);
	$noc=$snocrw['noc'];
	$snocr=null;
	$noc-=1;
	//delete from activity table
	$dfar=$conn->prepare("DELETE FROM activity WHERE activity_id=?");
	$dfar->bindParam(1,$actid,PDO::PARAM_INT);
	//delete from notification table
	$dfnr=$conn->prepare("DELETE FROM notifications WHERE notification_id=?");
	$dfnr->bindParam(1,$nid,PDO::PARAM_INT);
	//update posts
	$upr=$conn->prepare("UPDATE posts SET noc=? WHERE post_id=?");
	$upr->bindParam(1,$noc,PDO::PARAM_INT);
	$upr->bindParam(2,$postid,PDO::PARAM_INT);
	if(($dfar->execute()) && ($dfnr->execute()) && ($upr->execute())) {} else {
		echo 'Something\'s wrong!';
	}
	$dfar=$dfnr=$upr=null;
} else {
	die("Error!");
}
$conn=null;
?>
</body>
</html>