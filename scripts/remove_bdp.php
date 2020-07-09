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
error_reporting(0);
if($conn) {
	$uid=base64_decode($_SESSION['id']);
	$rpdpr=$conn->prepare("UPDATE users SET buddypic='' WHERE id=?");
	$rpdpr->bindParam(1,$uid,PDO::PARAM_INT);
	if($rpdpr->execute()) echo '<img src="images/def_buddypic.png" class="rounded-circle img-fluid ubs">';
	$rpdpr=null;
} else die("Error!");
$conn=null;
?>
</body>
</html>