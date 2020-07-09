<?php 
include 'varcsc.php';
function test_input($data) {
	$data=trim($data);
	$data=stripslashes($data);
	$data=htmlspecialchars($data);
	return $data;
}	
if($conn) {
	$descp=test_input($_POST['description']);
	$descp = base64_encode($descp);
	$uid=base64_decode($_SESSION['id']);
	//update description
	$udr=$conn->prepare("UPDATE users SET description=? WHERE id=?");
	$udr->bindParam(1,$descp,PDO::PARAM_STR);
	$udr->bindParam(2,$uid,PDO::PARAM_INT);
	if($udr->execute()) {
		//get description value
		$gdvr=$conn->prepare("SELECT description FROM users WHERE id=?");
		$gdvr->bindParam(1,$uid,PDO::PARAM_INT);
		$gdvr->execute();
		$gdvrw=$gdvr->fetch(PDO::FETCH_ASSOC);
		echo base64_decode($gdvrw['description']);
	} else echo 'Could not update the page description!';
	$udr=$gdvr=null;
} else {
	die("Error!");
}
$conn=null;
?>