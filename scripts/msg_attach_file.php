<?php
include 'varcsc.php';
$uun = base64_decode($_SESSION['username']);
$uid = base64_decode($_SESSION['id']); 
function test_input($data) {
	$data=trim($data);
	$data=stripslashes($data);
	$data=htmlspecialchars($data);
	$data=strip_tags($data);
	return $data;
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
//user info query
$uir=$conn->prepare("SELECT * FROM users WHERE id=?");
$uir->bindParam(1,$uid,PDO::PARAM_INT);
$uir->execute();
$uirw=$uir->fetch(PDO::FETCH_ASSOC);
$upk=$uirw['private_key'];
$uir=null;
$picErr = $picloadErr = "";
if(!empty($_FILES['attachment']['name']) && !empty($_POST['send_to'])) {
	if(!empty($_FILES['attachment']['type'])) {
		if(!file_exists('../uploads/'.$uun."/message_uploads/")) {
			mkdir('../uploads/'.$uun.'/message_uploads/', 0777, true);
			$index_file_useless = fopen('../uploads/'.$uun.'/message_uploads/index.html', 'w');
	    	fclose($index_file_useless);
		}
		$un = test_input($_POST['send_to']);
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
		$target_dir="../uploads/".$uun."/message_uploads/".microtime()."_".$un."_".mt_rand();
		$org_file=basename($_FILES['attachment']['name']);
		$file_ext=strtolower(pathinfo($org_file,PATHINFO_EXTENSION));
		$target_file=$target_dir.".".$file_ext;
		$uploadOk=1;
		$FileType=strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
		if(file_exists($target_file)) {
			$picErr="Sorry, file already exists!";
    		$uploadOk = 0;
		}
		// Check file size
		if($_FILES["attachment"]["size"] > 30000000) { // this size is 30 MB
    		$picErr="Sorry, the file is too large!";
    		$uploadOk = 0;
		}
		// Allow certain file formats
		$allowed=array('jpeg','png','jpg','gif','txt','html','mpeg','ogg','wav','webm','mp4','mp3','3gp','pdf');
		if(!in_array($file_ext,$allowed)) {
    		$picErr="Sorry, the file format is invalid! Only jpeg/png/jpg/gif/txt/html/mpeg/ogg/wav/webm/mp3/mp4/3gp/pdf are allowed.";
    		$uploadOk = 0;
		} 
		// Check if $uploadOk is set to 0 by an error
		if($uploadOk != 0) {
			if(move_uploaded_file($_FILES["attachment"]["tmp_name"], $target_file)) {
				$target_file = str_replace("../","",$target_file);
    			$pic=test_input($target_file);
				//get current bond
				$gcb = $conn->prepare("SELECT bond FROM bonds WHERE bud_id1 = ? AND bud_id2 = ?");
				$gcb->bindParam(1, $uid, PDO::PARAM_INT);
				$gcb->bindParam(2, $id, PDO::PARAM_INT);
				$gcb->execute();
				$gcbr = $gcb->fetch(PDO::FETCH_ASSOC);
				$bond = $gcbr['bond'] + 5;
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
				$pic=encrypt($pic,$key);
				$is_file = 1;
				//insert into messages
				$imqr=$conn->prepare("INSERT INTO messages(bud_id1,bud_id2,message_content,is_file,message_time) VALUES(?,?,?,?,NOW())");
				$imqr->bindParam(1,$upk,PDO::PARAM_STR);
				$imqr->bindParam(2,$pk,PDO::PARAM_STR);
				$imqr->bindParam(3,$pic,PDO::PARAM_STR);
				$imqr->bindParam(4,$is_file,PDO::PARAM_INT);
				if(($imqr->execute()) && ($ub->execute())) {
				    $pic = decrypt($pic, $key);
					$path = "../".$pic;
					$msg_link = $pic;
					if(file_exists($path)) {
						$file_ext=strtolower(pathinfo($path,PATHINFO_EXTENSION));
						$filesize = filesize($path);
						$filesize_mb = round($filesize / 1024 / 1024, 1);
						if(($file_ext == "jpeg") || ($file_ext == "png") || ($file_ext == "jpg") || ($file_ext == "gif")) echo '<img src="'.$path.'" alt="Image Sent By You" class="sent_img">';
			            else if($file_ext=="pdf") echo '<i class="fas fa-file-pdf fa-2x"></i> You Sent A PDF File';
			            else if(($file_ext=="txt") || ($file_ext=="html")) {
			               echo '<i class="fas fa-file-alt fa-2x"></i> You Sent A ';
			               if($file_ext == "txt") echo " Text Document";
			               else echo " HTML Document";
			            } else {
			                $mime_type = mime_content_type($msg);
			                if(($mime_type=="video/webm") || ($mime_type=="video/ogg") || ($mime_type=="video/mp4") || ($file_ext=="3gp") || ($file_ext=="3gpp")) echo '<i class="fas fa-video fa-2x"></i> You Sent A Video';
			                else if(($mime_type=="audio/mpeg") || ($mime_type=="audio/wav") || ($mime_type=="audio/mp3") || ($mime_type=="audio/x-wav")) echo '<audio controls data-toggle="tooltip" title="Try downloading this video if it is not supported on this browser"><source src="'.$msg.'" type="'.$mime_type.'">This browser does not support this <code>audio</code> type yet.Click the button below to button the audio.</audio>';
			            }
						echo '<div class="text-center"><a href="'.$msg_link.'" download class="attachment_download_link btn btn-danger" role="button"><i class="fas fa-file-download attachment_download_icon" title="Click to download"></i> ';
						if($filesize_mb == 0) echo round($filesize / 1024, 2).' KB';
						else echo $filesize_mb.' MB';
						echo '</a></div>';
					} else echo 'Attachment Unavailable!';
				}
				$imqr=$ub=null;
   			} else {
   				$picloadErr="Error!";
   			}
		}
		else {
    		$picloadErr="Error!";
			// if everything is ok, try to upload file
		}
	} else {
		echo 'Error!'; 
	}
} else {
	echo 'Error!';
}
?>