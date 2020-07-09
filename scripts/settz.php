<?php 
session_start();
$tz=$_GET['tz'];
if($tz) {
	$_SESSION['tz']=$tz;
	setcookie("tz",$tz,time()+(60*60*24*30),"/");
} else echo 'We could not detect your location! Please try again!';
?>