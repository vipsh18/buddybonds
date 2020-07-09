<?php 
session_start();
if($_GET['tz']) {
    $tz = $_GET['tz']; 
    $timezone = timezone_name_from_abbr("", $tz*60, false);
    $_SESSION['tz']=$timezone;
	setcookie("tz",$timezone,time()+(60*60*24*30),"/");
}
?>