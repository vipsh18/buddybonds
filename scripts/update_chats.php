<?php
include 'varcsc.php';
date_default_timezone_set($tz);
$uid=base64_decode($_SESSION['id']);
$uun=base64_decode($_SESSION['username']);
function decrypt($msg,$key) {
	$encryption_key=base64_decode($key);
	list($encrypted_data, $iv) = explode('::', base64_decode($msg), 2);
	return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
}
//key
$file=fopen("../encoded_key_msg.txt","r") or die("ERROR!");
$key=fread($file,44);
fclose($file);
if($conn) {
	//get your own info
	$gyoir=$conn->prepare("SELECT private_key,public_key FROM users WHERE id=?");
	$gyoir->bindParam(1,$uid,PDO::PARAM_INT);
	$gyoir->execute();
	$gyoirw=$gyoir->fetch(PDO::FETCH_ASSOC);
	$upk=$gyoirw['private_key'];
	$upuk=$gyoirw['public_key'];
	//check for messages
	$cfmr=$conn->prepare("SELECT * FROM messages WHERE (bud_id1=? OR bud_id2=?) ORDER BY message_time DESC");
	$cfmr->bindParam(1,$upk,PDO::PARAM_STR);
	$cfmr->bindParam(2,$upuk,PDO::PARAM_STR);
	$cfmr->execute();
	if($cfmr->rowCount()>0) {
		//buddy pk array...to avoid repetitions
		$bprkarr=array(); 
		$bpukarr=array();
		while($cfmrw=$cfmr->fetch(PDO::FETCH_ASSOC)) {
			//bpk is buddy public key
			if($cfmrw['bud_id1']==$upk) {
				//store buddy's keys
				$bpuk=$cfmrw['bud_id2'];
				foreach($bpukarr as $var1) {
					if($var1==$bpuk) continue 2;
				}
				array_push($bpukarr,$bpuk);
				$gunr=$conn->prepare("SELECT username,buddypic,private_key FROM users WHERE public_key=?");
				$gunr->bindParam(1,$bpuk,PDO::PARAM_STR);
				$gunr->execute();
				$gunrw=$gunr->fetch(PDO::FETCH_ASSOC);
				$bprk=$gunrw['private_key'];
				foreach ($bprkarr as $var2) {
					if($var2==$bprk) continue 2;
				}
			} else if($cfmrw['bud_id2']==$upuk) {
				//store buddy's keys
				$bprk=$cfmrw['bud_id1'];
				foreach($bprkarr as $var2) {
					if($var2==$bprk) continue 2;
				}
				array_push($bprkarr,$bprk);
				$gunr=$conn->prepare("SELECT username,buddypic,public_key FROM users WHERE private_key=?");
				$gunr->bindParam(1,$bprk,PDO::PARAM_STR);
				$gunr->execute();
				$gunrw=$gunr->fetch(PDO::FETCH_ASSOC);
				$bpuk=$gunrw['public_key'];
				foreach ($bpukarr as $var1) {
					if($var1==$bpuk) continue 2;
				}
			}		 
			if($gunrw['username']=="") continue;
			echo '<a href="chat.php?un='.$gunrw['username'].'" class="row bd_row">';
			$gunrw['buddypic']=str_replace("uploads/".$gunrw['username']."/buddypic_uploads","../uploads/".$gunrw['username']."/buddypic_uploads",$gunrw['buddypic']);
			if(file_exists($gunrw['buddypic'])) {
				$gunrw['buddypic']=str_replace("../uploads/".$gunrw['username']."/buddypic_uploads","uploads/".$gunrw['username']."/buddypic_uploads",$gunrw['buddypic']);
				echo '<img src="'.$gunrw['buddypic'].'" class="rounded-circle bd_img_cl">';
			} else {
				echo '<img src="images/def_buddypic.png" class="rounded-circle bd_img_cl">';
			}
			$msg=decrypt($cfmrw['message_content'],$key);
			echo '<span class="bd_un_cl">'.$gunrw['username'].'</span><span class="text-dark bd_last_msg ml-auto">';
			if($cfmrw['bud_id1']==$upk) {
				$seen=$cfmrw['seen'];
				if($seen=='0') echo '<span class="text-muted"><b>&#10003;</b></span> ';
				else if($seen=='1') echo '<span class="text-primary"><b>&#10003;</b></span> ';
			} else if($cfmrw['bud_id2']==$upuk) {
				echo '<span class="text-muted"> <i class="far fa-comment"></i></span> ';
			}
			if($cfmrw['is_file']==0) {
				if(strlen($msg) > 16)  {
					$msg = substr($msg, 0, 16); 
					$msg .= "...";
				}
				echo ' '.$msg;
			} else {
			    $path = "../".$msg;
			    if(file_exists($path)) {
			        $file_ext=strtolower(pathinfo($path,PATHINFO_EXTENSION));
			        if(($file_ext == "jpeg") || ($file_ext == "png") || ($file_ext == "jpg") || ($file_ext == "gif")) echo '<i class="fas fa-image"></i> Photo';
			        else if($file_ext=="pdf") echo '<i class="fas fa-file-pdf"></i> PDF';
			        else if(($file_ext=="txt") || ($file_ext=="html")) echo '<i class="fas fa-file"></i> Text Document';
			        else {
			            $mime_type = mime_content_type($path);
			            if(($mime_type=="video/webm") || ($mime_type=="video/ogg") || ($mime_type=="video/mp4") || ($file_ext=="3gp") || ($file_ext=="3gpp")) echo '<i class="fas fa-video"></i> Video';
			            else if(($mime_type=="audio/mpeg") || ($mime_type=="audio/wav") || ($mime_type=="audio/mp3") || ($mime_type=="audio/x-wav")) echo '<i class="fas fa-microphone"></i> Audio';
			        } 
			    } else {
			        echo 'Attachment unavailable!';
			    }
			}
			echo '</span>';
			if($cfmrw['bud_id2']==$upuk) {
				//get number of new messages
				$gnonmr=$conn->prepare("SELECT message_id FROM messages WHERE bud_id1=? AND bud_id2=? AND seen='0'");
				$gnonmr->bindParam(1,$bprk,PDO::PARAM_STR);
				$gnonmr->bindParam(2,$upuk,PDO::PARAM_STR);
				$gnonmr->execute();
				if($gnonmr->rowCount()>0) {
					echo '<span class="badge badge-danger bd_new_msgs">';
					echo $gnonmr->rowCount().'</span>';
				}
			}
			echo '</a>';
			$gunr=null;
		}
	} else {
		echo '<img src="images/rxn/rxns/Wow-500px.gif" id="wow" class="img-fluid">';
		echo '<div class="text-primary msg text-center">Why is it so lonely here??<br>Your chats appear here! Start messaging to view your chats.</div>';
	}
	$cfmr=null;
} else {
	die("Error!");
}
$conn=null;
?>