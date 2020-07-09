<?php
session_start();
$config = parse_ini_file("varcsc.ini");
$db_server = $config['db_server'];
$db_user = $config['db_user'];
$db_pass = $config['db_pass'];
$db_name = $config['db_name'];
try {
	$conn = new PDO("mysql:host=$db_server;dbname=$db_name", $db_user, $db_pass);
	//set PDO error mode to exception
	$conn -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
	echo 'Connection failed:'.$e -> getMessage();
}
if(isset($_SESSION['id']) && isset($_SESSION['username']) && isset($_SESSION['tz'])) {
	if(isset($_COOKIE['id']) && isset($_COOKIE['username']) && isset($_COOKIE['tz'])) {
		$cookie_id = base64_decode($_COOKIE['id']);
		$cookie_username = base64_decode($_COOKIE['username']);
		$tz = $_COOKIE['tz'];
		$cid = $_COOKIE['id'];
		$cun = $_COOKIE['username'];
	} else {
		$sid = $_SESSION['id'];
		$sun = $_SESSION['username'];
		$tz = $_SESSION['tz'];
		setcookie('id', $sid, time()+(60*24*60*30), "/");
		setcookie('username', $sun, time()+(60*60*24*30), "/");
		setcookie('tz', $tz, time()+(60*60*24*30), "/");
		$cookie_id = base64_decode($sid);
		$cookie_username = base64_decode($sun);
		$tz  =  $_COOKIE['tz'];
	}
} else if(!isset($_SESSION['id'])||!isset($_SESSION['username'])||!isset($_SESSION['tz'])) {
	if(isset($_COOKIE['id']) && isset($_COOKIE['username']) && isset($_COOKIE['tz'])) {
		$cookie_username = base64_decode($_COOKIE['username']);
		$cookie_id = base64_decode($_COOKIE['id']);
		$cid = $_COOKIE['id'];
		$cun = $_COOKIE['username'];
		$tz = $_COOKIE['tz'];
		$_SESSION['id'] = $cid;
		$_SESSION['username'] = $cun;
		$_SESSION['tz'] = $tz;
	} else {
		header("Location:http://localhost/buddyBonds_backup");
		exit();
	}
} 
?>