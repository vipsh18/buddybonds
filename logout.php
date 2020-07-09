<?php
session_start(); 
error_reporting(0);
$config = parse_ini_file("../varcsc.ini");
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
if(isset($_SESSION['id'])) $uid=base64_decode($_SESSION['id']);
//
//change logout time
//
$cltq=$conn->prepare("UPDATE users SET logout_time=NOW() WHERE id=?");
$cltq->bindParam(1,$uid,PDO::PARAM_INT);
if(!$cltq->execute()) echo 'Logout time set query failed!';
$cltq=null;
	if(isset($_SESSION['id'])) {
		session_unset();
		$_SESSION = array();
		if(isset($_COOKIE[session_name()])) setcookie(session_name(),'',time()-3600);
		if(session_destroy()) {
			setcookie('id','',time()-3600,"/");
			setcookie('username','',time()-3600,"/");
			if(!isset($_SESSION['id']) && !isset($_SESSION['username'])) {
			    echo '<div class="alert alert-dismissable text-center alert-info"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><span class="text-success"><b>Logout successful!</b></span><br>You have been logged out successfully. See you soon!</div>';
				?>
				<script>
				    if(localStorage.getItem('hm_thm')) localStorage.removeItem('hm_thm');
					if(localStorage.getItem('pf_thm')) localStorage.removeItem('pf_thm');
					if(localStorage.getItem('themecolor')) localStorage.removeItem("themecolor");
				</script>
				<?php
			}
		}
		header("Location:http://localhost/buddyBonds_backup?logout=".time());
		exit();
	}
	else {
		header("Location:http://localhost/buddyBonds_backup/");
		exit();
	}
$conn = null;
?>