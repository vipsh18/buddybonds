<?php 
include 'varcsc.php';
if($conn) {
    if(isset($_GET['theme_id'])) $id = $_GET['theme_id'];
	//get theme from db
	$gtbr = $conn->prepare("SELECT theme FROM users WHERE id=?");
	$gtbr->bindParam(1,$id,PDO::PARAM_INT);
	$gtbr->execute();
	$gtbrw = $gtbr->fetch(PDO::FETCH_ASSOC);
	echo $gtbrw['theme'];
	$gtbr = null;
} else die("Error!");
$conn=null;
?>