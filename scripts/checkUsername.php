<?php 
session_start();
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
$config=parse_ini_file("varcsc.ini");
$db_server=$config['db_server'];
$db_user=$config['db_user'];
$db_pass=$config['db_pass'];
$db_name=$config['db_name'];
try {
	$conn=new PDO("mysql:host=$db_server;dbname=$db_name",$db_user,$db_pass);
	//set PDO error mode to exception
	$conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
	echo 'Connection failed:'.$e->getMessage();
}
function test_input($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}
$reg_username=test_input($_POST['reg_username']);
if($conn) {
	if(isset($_SESSION['username'])) {
		if(base64_decode($_SESSION['username'])==$reg_username) {
			echo '<i class="far fa-check-circle" title="Username is available!"></i>';
			$conn=null;
			exit();
		}
	}
	$res=$conn->prepare("SELECT username FROM users WHERE username=?");
	$res->bindParam(1,$reg_username,PDO::PARAM_STR);
	$res->execute();
	if($res->rowCount()>0) {
		echo '<i class="far fa-times-circle" title="Username is already taken!"></i>';
	} else { echo '<i class="far fa-check-circle" title="Username is available!"></i>'; 
	}
	$res=null;
} 
else {
	die('Unable to connect to database!!');
}
$conn=null;
?>
</body>
</html>