<?php 
include 'varcsc.php';
$tz = new DateTimeZone($tz);
$dt = new DateTime();
$dt->setTimezone($tz);
if($conn) {
	$postid=$_POST['post_id'];
	$uid=base64_decode($_SESSION['id']);
	//get post details
	$gpdr=$conn->prepare("SELECT * FROM posts WHERE post_id=?");
	$gpdr->bindParam(1,$postid,PDO::PARAM_INT);
	$gpdr->execute();
	$gpdrw=$gpdr->fetch(PDO::FETCH_ASSOC);
	$id=$gpdrw['user_id'];
	$gpdr=null;
	$cqr=$conn->prepare("SELECT * FROM activity,users WHERE activity.bud_id1=users.id AND activity.post_id=? AND activity.activity_type='comment' ORDER BY activity.activity_time DESC");
	$cqr->bindParam(1,$postid,PDO::PARAM_INT);
	$cqr->execute();
		while($cqrw=$cqr->fetch(PDO::FETCH_ASSOC)) {
			$cqrw['buddypic']=str_replace("uploads/".$cqrw['username']."/buddypic_uploads/","../uploads/".$cqrw['username']."/buddypic_uploads/",$cqrw['buddypic']);
			echo '<div class="row cmnt_row" style="padding-top:3px;padding-bottom:3px;">';
				if(file_exists($cqrw['buddypic'])) {
					$cqrw['buddypic']=str_replace("../uploads/".$cqrw['username']."/buddypic_uploads/","uploads/".$cqrw['username']."/buddypic_uploads/",$cqrw['buddypic']);
					echo '<img src="'.$cqrw['buddypic'].'" class="rounded-circle cmnt_disp" alt="User buddy picture">';
				} else echo '<img src="images/def_buddypic.png" class="rounded-circle cmnt_disp" alt="Default user buddy picture">';
				echo '<a href="profile.php?un='.$cqrw['username'].'" class="cmnt_un text-dark"><b>'.$cqrw['username'].'</b></a>';
				$comment = base64_decode($cqrw['activity_content']);
				echo '<span class="cmnt_cont">';
				if(strpos($comment, "@") !== false) {
				    $comment = preg_replace_callback('/@.+?\b/', function($m) {
                        $str = substr($m[0], 1);
                        return sprintf("<a href='profile.php?un=%s' class='text-dark'><b>%s</b></a>", $str, $str);
                    }, $comment);
				}
				echo $comment.'</span>';
				echo '<span class="cmnt_time text-muted">';
				$acti_time = strtotime($cqrw['activity_time']);
				$dt->setTimestamp($acti_time);
                if(time()-$acti_time>0 && time()-$acti_time<86400) echo $dt->format("H:i A");
                else if(time()-$acti_time>=86400 && time()-$acti_time<604800) echo $dt->format("D, H:i A");
                else if(time()-$acti_time>=604800 && time()-$acti_time<31536000) echo $dt->format("M j, H:i A");
                else echo $dt->format("M j, Y H:i A");
				echo '.</span>';
				if(($cqrw['bud_id1']==$uid)||($uid==$id)) {
					?>
					<a href="javascript:void(0)" class="dlt_link" style="color:#708090" title="Delete" onclick="dlt_cmnt('<?php echo $postid; ?>','<?php echo $cqrw['activity_id']; ?>')"><b><i class="far fa-trash-alt"></i></b></a>
					<?php
				}
 			echo '</div>';
	}
	$cqr=null;
} else {
	die("Error!");
}
$conn=null;
?>
</body>
</html>