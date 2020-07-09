<?php
include 'varcsc.php';
if(!$_POST['mid']) {
	header("Location:http://localhost/buddyBonds_backup/messages.php");
	$conn=null;
	exit();
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
$tz = new DateTimeZone($tz);
$dt = new DateTime();
$dt->setTimezone($tz);
$mid = $_POST['mid'];
$uid = base64_decode($_SESSION['id']);
$message = new \stdClass();
$gm = $conn->prepare("SELECT message_content,is_file,message_time FROM messages WHERE message_id = ?");
$gm->bindParam(1,$mid,PDO::PARAM_INT);
$gm->execute();
$gmr = $gm->fetch(PDO::FETCH_ASSOC);
$file=fopen("../encoded_key_msg.txt","r") or die("ERROR!");
$key=fread($file,44);
fclose($file);
$msg = decrypt($gmr['message_content'],$key);
$reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
if($gmr['is_file'] == 0) {
	if(preg_match($reg_exUrl, $msg, $url)) {
        $title = get_title($msg);
        $msg = "<b>".$title."</b><br>".preg_replace($reg_exUrl, '<a href="'.$url[0].'" rel="nofollow" target="_blank" class="chat_link_share"><b>'.$url[0].'</b></a><br>', $msg);
    }
} else {
	$path = "../".$msg;
	$msg_link = $msg;
	if(file_exists($path)) {
		$file_ext=strtolower(pathinfo($path,PATHINFO_EXTENSION));
		$filesize = filesize($path);
		$filesize_mb = round($filesize / 1024 / 1024, 1);
		if(($file_ext == "jpeg") || ($file_ext == "png") || ($file_ext == "jpg") || ($file_ext == "gif")) $msg = '<img src="'.$path.'" alt="Image Sent By '.$un.'" class="sent_img">';
		else if($file_ext=="pdf") $msg = '<i class="fas fa-file-pdf fa-2x"></i> Set You a PDF File';
		else if(($file_ext=="txt") || ($file_ext=="html")) {
			$msg =  '<i class="fas fa-file-alt fa-2x"></i> Sent You A ';
			if($file_ext == "txt") $msg.=" Text Document";
			else $msg.=" HTML Document";
		} else {
			$mime_type = mime_content_type($msg);
			if(($mime_type=="video/webm") || ($mime_type=="video/ogg") || ($mime_type=="video/mp4") || ($file_ext=="3gp") || ($file_ext=="3gpp")) $msg = '<i class="fas fa-video fa-2x"></i> Sent You A Video';
			else if(($mime_type=="audio/mpeg") || ($mime_type=="audio/wav") || ($mime_type=="audio/mp3") || ($mime_type=="audio/x-wav")) $msg = '<audio controls data-toggle="tooltip" title="Try downloading this video if it is not supported on this browser"><source src="'.$msg.'" type="'.$mime_type.'">This browser does not support this <code>audio</code> type yet.Click the button below to button the audio.</audio>';
		}
		$msg.='<div class="text-center"><a href="'.$msg_link.'" download class="attachment_download_link btn btn-danger" role="button"><i class="fas fa-file-download attachment_download_icon" title="Click to download"></i> ';
		if($filesize_mb == 0) $msg.=round($filesize / 1024, 2).' KB';
		else $msg.=$filesize_mb.' MB';
	    $msg.='</a></div>';
	} else $msg.='Attachment Unavailable!';
	$msg.='<br>';
}
$seen = 1;
$ums = $conn->prepare("UPDATE messages SET seen = ?,seen_time=NOW() WHERE message_id = ?");
$ums->bindParam(1,$seen,PDO::PARAM_INT);
$ums->bindParam(2,$mid,PDO::PARAM_INT);
$ums->execute();
$mt = strtotime($gmr['message_time']);
$dt->setTimestamp($mt);
$msg_time = ' <span class="text-muted msg_time">'.$dt->format("H:i A").'</span>';
$message->message_content = $msg;
$message->is_file = $gmr['is_file'];
$message->msg_time = $msg_time;
echo json_encode($message);
$conn = null;
?>