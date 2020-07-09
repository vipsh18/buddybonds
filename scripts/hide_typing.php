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
date_default_timezone_set($tz);
$uid=base64_decode($_SESSION['id']);
if($conn) {
	$bid=$_POST['bid'];
	//check if buddies
	$cibr=$conn->prepare("SELECT bud_id1,bud_id2 FROM buddies WHERE (bud_id1=:uid OR bud_id1=:bid) AND (bud_id2=:bid OR bud_id2=:uid) AND active='1'");
	$cibr->bindParam(":uid",$uid,PDO::PARAM_INT);
	$cibr->bindParam(":bid",$bid,PDO::PARAM_INT);
	$cibr->execute();
	if($cibr->rowCount()>0) {
		$cibrw=$cibr->fetch(PDO::FETCH_ASSOC);
		if($cibrw['bud_id1']==$uid) {
			$utr=$conn->prepare("UPDATE buddies SET typing1='0' WHERE bud_id1=:uid AND bud_id2=:bid AND active='1'");
			$utr->bindParam(":uid",$uid,PDO::PARAM_INT);
			$utr->bindParam(":bid",$bid,PDO::PARAM_INT);
		} else if($cibrw['bud_id2']==$uid) {
			$utr=$conn->prepare("UPDATE buddies SET typing2='0' WHERE bud_id1=:bid AND bud_id2=:uid AND active='1'");
			$utr->bindParam(":uid",$uid,PDO::PARAM_INT);
			$utr->bindParam(":bid",$bid,PDO::PARAM_INT);
		}
		$utr->execute();
	}
} else die("Error!");
$conn=null;
?>
</body>
</html>