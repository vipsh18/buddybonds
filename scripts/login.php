<?php
session_start();
if(!isset($_POST['username']) || !isset($_POST['password'])) {
	header("Location:http://localhost/buddyBonds_backup");
	exit();
}
$config = parse_ini_file("varcsc.ini");
$db_server = $config['db_server'];
$db_user = $config['db_user'];
$db_pass = $config['db_pass'];
$db_name = $config['db_name'];
try {
	$conn = new PDO("mysql:host=$db_server;dbname=$db_name",$db_user,$db_pass);
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
$unErr = $pswErr = $Err = "";
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = test_input($_POST['username']);
    $password = test_input($_POST['password']);
	if(empty($username)) $unErr = "Username cannot be empty !";
	else if(preg_match("/[^a-z0-9A-Z_]/",$username)) $unErr = "Username cannot contain invalid characters !";
	else $unErr = "";
	if(empty($password)) {
		$pswErr = "Password cannot be empty !";
	} else {
		$pswErr = "";
		$password = hash('sha256',$password);
		$password = base64_encode($password);
	}
}	
$res=$conn->prepare("SELECT * FROM users WHERE username=? AND password=?");
$res->bindParam(1, $username, PDO::PARAM_STR);
$res->bindParam(2, $password, PDO::PARAM_STR);
$res->execute();
if($res->rowCount() == 1) $Err = "";
else {
	//check if username exists
	$ciue = $conn->prepare("SELECT id FROM users WHERE username=?");
	$ciue->bindParam(1, $username, PDO::PARAM_STR);
	$ciue->execute();
	if($ciue->rowCount() < 1) $Err = 'Username <b>'.$username.'</b> is not signed up with us !';
	else $Err = 'Incorrect password for <b>'.$username.'</b>';
	$ciue = null;
}
//
//check for no errors, login if none
// 
    if((!$unErr) && (!$pswErr) && (!$Err)) {
        $row = $res->fetch(PDO::FETCH_ASSOC);
		$id = $row['id'];
		$nov = $row['nov'];
		$res = null;
		$cid = base64_encode($id);
		$cun = base64_encode($username);
		$_SESSION['username'] = $cun;
		$_SESSION['id'] = $cid;
		$_SESSION['tz'] = $_COOKIE['tz'];
		setcookie('id', $cid, time()+(60*60*24*30), "/");
		setcookie('username', $cun, time()+(60*60*24*30), "/");
		$cookie_id = base64_decode($id);
		$nov = $nov + 1;
		$unovqr = $conn->prepare("UPDATE users SET nov=? WHERE id=?");
		$unovqr->bindParam(1, $nov, PDO::PARAM_INT);
		$unovqr->bindParam(2, $id, PDO::PARAM_INT);
		if($unovqr->execute()) {
			$unovqr = null;
            echo "0";
		}
    } else {
        echo '<div class="alert alert-info text-center"><span class="text-danger">';
        echo $unErr;
        if(strlen($pswErr) >= 1)
            if(strlen($unErr) >= 1) echo '<br>';
            echo $pswErr;
        if(strlen($Err) >= 1)
            if(strlen($unErr) >= 1|| strlen($pswErr) >= 1) echo '<br>';
            echo $Err;
        echo '</span></div>';
    }
	$conn=null;
?>