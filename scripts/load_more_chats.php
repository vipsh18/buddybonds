<?php 
include 'varcsc.php';
if(!$_POST['id'] || !$_POST['ms']) {
	header("Location:http://localhost/buddyBonds_backup/messages.php");
	$conn=null;
	exit();
}
$id = $_POST['id'];
$ms = $_POST['ms'];
$uid =base64_decode($_SESSION['id']);
$tz = new DateTimeZone($tz);
$dt = new DateTime();
$dt->setTimezone($tz);
if($conn) {
	function decrypt($msg,$key) {
		$encryption_key=base64_decode($key);
		list($encrypted_data, $iv) = explode('::', base64_decode($msg), 2);
		return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
	}
	$file=fopen("../encoded_key_msg.txt","r") or die("ERROR!");
	$key=fread($file,44);
	fclose($file);
	//user info query
	$uiqr=$conn->prepare("SELECT * FROM users WHERE id = ?");
	$uiqr->bindParam(1,$uid,PDO::PARAM_INT);
	$uiqr->execute();
	$uiqrow=$uiqr->fetch(PDO::FETCH_ASSOC);
	$upk=$uiqrow['private_key'];
	$upuk=$uiqrow['public_key'];
	$uiqr=null;
	//details query for the tile of the chat head 
	$dqr=$conn->prepare("SELECT * FROM users WHERE id = ?");
	$dqr->bindParam(1,$id,PDO::PARAM_INT);
	$dqr->execute();
	if($dqr->rowCount() <= 0) {
		header("Location:http://localhost/buddyBonds_backup/messages.php");
		exit();
	}
	$dqrow=$dqr->fetch(PDO::FETCH_ASSOC);
	$id=$dqrow['id'];
	$opk=$dqrow['private_key'];
	$opuk=$dqrow['public_key'];
	$dqr=null;
	//get message count
	$gmcr=$conn->prepare("SELECT * FROM messages WHERE (bud_id1=:upk AND bud_id2=:opuk) OR (bud_id1=:opk AND bud_id2=:upuk)");
	$gmcr->bindParam(":upk",$upk,PDO::PARAM_STR);
	$gmcr->bindParam(":opuk",$opuk,PDO::PARAM_STR);
	$gmcr->bindParam(":opk",$opk,PDO::PARAM_STR);
	$gmcr->bindParam(":upuk",$upuk,PDO::PARAM_STR);
	$gmcr->execute();
	$mc = $gmcr->rowCount();
	$n = 30;
	$mctg = $mc-$ms;
	$mctg -= 30;
	if($mctg <= 0) {
		$n += $mctg;
		$mctg = 0;
	}
	$reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
	//main chat query
	$mcqr=$conn->prepare("SELECT * FROM messages WHERE (bud_id1=:upk AND bud_id2=:opuk) OR (bud_id1=:opk AND bud_id2=:upuk) ORDER BY message_time LIMIT $mctg,$n");
	$mcqr->bindParam(":upk",$upk,PDO::PARAM_STR);
	$mcqr->bindParam(":opuk",$opuk,PDO::PARAM_STR);
	$mcqr->bindParam(":opk",$opk,PDO::PARAM_STR);
	$mcqr->bindParam(":upuk",$upuk,PDO::PARAM_STR);
	$mcqr->execute();
	while($mcqrow=$mcqr->fetch(PDO::FETCH_ASSOC)) { 
		if($mcqrow['bud_id1']==$upk) {
			$msg=decrypt($mcqrow['message_content'],$key);
			$msg_time=strtotime($mcqrow['message_time']);
			echo '<div class="row row_spc"><div class="col-lg-12"><div class="user_msgs"><i class="fas fa-caret-right conv_arrow"></i>';
			if($mcqrow['is_file']==0) {
				if(preg_match($reg_exUrl, $msg, $url)) {
                    $title = get_title($msg);
                    $msg = "<b>".$title."</b><br>".preg_replace($reg_exUrl, '<a href="'.$url[0].'" rel="nofollow" target="_blank" class="chat_link_share"><b>'.$url[0].'</b></a><br>', $msg);
                }
				echo $msg.'<br>';
			} else {
			    $path = "../".$msg;
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
			            $mime_type = mime_content_type($path);
			            if(($mime_type=="video/webm") || ($mime_type=="video/ogg") || ($mime_type=="video/mp4") || ($file_ext=="3gp") || ($file_ext=="3gpp")) echo '<i class="fas fa-video fa-2x"></i> You Sent A Video';
			            else if(($mime_type=="audio/mpeg") || ($mime_type=="audio/wav") || ($mime_type=="audio/mp3") || ($mime_type=="audio/x-wav")) echo '<audio controls data-toggle="tooltip" title="Try downloading this video if it is not supported on this browser"><source src="'.$msg.'" type="'.$mime_type.'">This browser does not support this <code>audio</code> type yet.Click the button below to button the audio.</audio>';
			        }
				    echo '<div class="text-center"><a href="'.$path.'" download class="attachment_download_link btn btn-sm btn-danger"><i class="fas fa-file-download attachment_download_icon" title="Click to download"></i> ';
				    if($filesize_mb == 0) echo round($filesize / 1024, 2).' KB';
				    else echo $filesize_mb.' MB';
				    echo '</a></div>';
				} else echo 'Attachment Unavailable!';
			}
			echo ' <span class="text-muted msg_time">';
			$dt->setTimestamp($msg_time);
			if(time()-$msg_time>0 && time()-$msg_time<86400) echo $dt->format("H:i A");
            else if(time()-$msg_time>=86400 && time()-$msg_time<604800) echo $dt->format("D,H:i A");
            else if(time()-$msg_time>=604800 && time()-$msg_time<31536000) echo $dt->format("M j,H:i A");
            else echo $dt->format("M j, Y H:i A");
			echo '</span></div></div></div>';
		} else if($mcqrow['bud_id1']==$opk) {
		    $msg=decrypt($mcqrow['message_content'],$key);
			$msg_time=strtotime($mcqrow['message_time']);
			echo '<div class="row row_spc"><div class="col-lg-12"><div class="op_msgs"><i class="fas fa-caret-left conv_arrow"></i>';
			if($mcqrow['is_file']==0) {
				if(preg_match($reg_exUrl, $msg, $url)) {
                    $title = get_title($msg);
                    $msg = "<b>".$title."</b><br>".preg_replace($reg_exUrl, '<a href="'.$url[0].'" rel="nofollow" target="_blank" class="chat_link_share"><b>'.$url[0].'</b></a><br>', $msg);
                }
				echo $msg.'<br>';
			} else {
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
			            $mime_type = mime_content_type($path);
			            if(($mime_type=="video/webm") || ($mime_type=="video/ogg") || ($mime_type=="video/mp4") || ($file_ext=="3gp") || ($file_ext=="3gpp")) echo '<i class="fas fa-video fa-2x"></i> You Sent A Video';
			            else if(($mime_type=="audio/mpeg") || ($mime_type=="audio/wav") || ($mime_type=="audio/mp3") || ($mime_type=="audio/x-wav")) echo '<audio controls data-toggle="tooltip" title="Try downloading this video if it is not supported on this browser"><source src="'.$msg.'" type="'.$mime_type.'">This browser does not support this <code>audio</code> type yet.Click the button below to button the audio.</audio>';
			        }
				    echo '<div class="text-center"><a href="'.$path.'" download class="attachment_download_link btn btn-sm btn-danger"><i class="fas fa-file-download attachment_download_icon" title="Click to download"></i> ';
				    if($filesize_mb == 0) echo round($filesize / 1024, 2).' KB';
				    else echo $filesize_mb.' MB';
				    echo '</a></div>';
				} else echo 'Attachment Unavailable!';
			}
			echo ' <span class="text-muted msg_time">';
			$dt->setTimestamp($msg_time);
			if(time()-$msg_time>0 && time()-$msg_time<86400) echo $dt->format("H:i A");
            else if(time()-$msg_time>=86400 && time()-$msg_time<604800) echo $dt->format("D,H:i A");
            else if(time()-$msg_time>=604800 && time()-$msg_time<31536000) echo $dt->format("M j,H:i A");
            else echo $dt->format("M j, Y H:i A");
			echo '</span></div></div></div>';
		}
	}
} else die("Error!");
$conn = null;
?>