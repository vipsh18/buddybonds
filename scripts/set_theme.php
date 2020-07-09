<?php
include 'varcsc.php';
if($conn) {
    if(isset($_GET['themecolor'])) $themecolor = $_GET['themecolor'];
    else $themecolor = "#9400D3";
	$uid = base64_decode($_SESSION['id']);
	//set theme in db result
	$stdr=$conn->prepare("UPDATE users SET theme=? WHERE id=?");
	$stdr->bindParam(1,$themecolor,PDO::PARAM_STR);
	$stdr->bindParam(2,$uid,PDO::PARAM_INT);
	$stdr->execute();
	$stdr=null;
} else die("Errro!");
$conn=null;
?>