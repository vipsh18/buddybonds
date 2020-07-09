<?php
include 'varcsc.php';
$tz = new DateTimeZone($tz);
$dt = new DateTime();
$dt->setTimezone($tz);
$uid=base64_decode($_SESSION['id']);
function test_input($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}
$un = test_input($_POST['un']);
$nmc = test_input($_POST['nmc']);
if($nmc=="") {
	$conn=null;
	echo '</body>';
	echo '</html>';
	exit();
}
if(!$un) {
    $conn  = null;
	header("Location:http://localhost/buddyBonds_backup/messages.php");
	exit();
}
function encrypt($msg,$key) {
	$encryption_key=base64_decode($key);
	$iv=openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
	$encrypted=openssl_encrypt($msg, 'aes-256-cbc', $encryption_key, 0, $iv);
	return base64_encode($encrypted."::".$iv);
}
function decrypt($msg,$key) {
	$encryption_key=base64_decode($key);
	list($encrypted_data, $iv) = explode('::', base64_decode($msg), 2);
	return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
}
function get_title($url){
  	$str = file_get_contents($url);
  	if(strlen($str)>0){
    	$str = trim(preg_replace('/\s+/', ' ', $str)); // supports line breaks inside <title>
    	preg_match("/\<title\>(.*)\<\/title\>/i",$str,$title); // ignore case
    	return $title[1];
  	}
}
$file=fopen("../encoded_key_msg.txt","r") or die("ERROR!");
$key=fread($file,44);
fclose($file);
//user info query
$uir=$conn->prepare("SELECT * FROM users WHERE id=?");
$uir->bindParam(1,$uid,PDO::PARAM_INT);
$uir->execute();
$uirw=$uir->fetch(PDO::FETCH_ASSOC);
$upk=$uirw['private_key'];
$uir=null;
if($conn) {
	$result=$conn->prepare("SELECT * FROM users WHERE username=?");
	$result->bindParam(1,$un,PDO::PARAM_STR);
	$result->execute();
	if($result->rowCount()<=0) {
	    $conn = null;
		header("Location:http://localhost/buddyBonds_backup/messages.php");
		exit();
	}
	$row=$result->fetch(PDO::FETCH_ASSOC);
	$id=$row['id'];
	//get current bond
	$gcb = $conn->prepare("SELECT bond FROM bonds WHERE bud_id1 = ? AND bud_id2 = ?");
	$gcb->bindParam(1, $uid, PDO::PARAM_INT);
	$gcb->bindParam(2, $id, PDO::PARAM_INT);
	$gcb->execute();
	$gcbr = $gcb->fetch(PDO::FETCH_ASSOC);
	$bond = $gcbr['bond'] + 2;
	//update bond
	$ub = $conn->prepare("UPDATE bonds SET bond = ? WHERE bud_id1 = ? AND bud_id2 = ?");
	$ub->bindParam(1, $bond, PDO::PARAM_INT);
	$ub->bindParam(2, $uid, PDO::PARAM_INT);
	$ub->bindParam(3, $id, PDO::PARAM_INT);
	$pk=$row['public_key'];
	$result=null;
	$file=fopen("../encoded_key_msg.txt","r") or die("ERROR!");
	$key=fread($file,44);
	fclose($file);
	$nmc=encrypt($nmc,$key);
	$now = date('Y-m-d H:i:s');
	$now = strtotime($now);
	$dt->setTimestamp($now);
	$imqr=$conn->prepare("INSERT INTO messages(bud_id1,bud_id2,message_content,message_time) VALUES(?,?,?,NOW())");
	$imqr->bindParam(1,$upk,PDO::PARAM_STR);
	$imqr->bindParam(2,$pk,PDO::PARAM_STR);
	$imqr->bindParam(3,$nmc,PDO::PARAM_STR);
	$reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
	if(($imqr->execute()) && ($ub->execute())) {
	    $nmc = decrypt($nmc, $key);
		if(preg_match($reg_exUrl, $nmc, $url)) {
	        $title = get_title($nmc);
	        $nmc = "<b>".$title."</b><br>".preg_replace($reg_exUrl, '<a href="'.$url[0].'" rel="nofollow" target="_blank" class="chat_link_share"><b>'.$url[0].'</b></a><br>', $nmc);
    	}
    	echo $nmc.' <br><span class="text-muted msg_time">'.$dt->format("H:i A").'</span>';
	}
	$imqr=$ub=null;
} else {
	die("ERROR!");
}
$conn=null;
?>