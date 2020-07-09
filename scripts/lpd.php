<?php
include 'varcsc.php';
$tz = new DateTimeZone($tz);
$dt = new DateTime();
$dt->setTimezone($tz);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8"/>
	<meta name="referrer" content="origin-when-crossorigin"/>
	<meta name="author" content="Vipul Sharma"/>
	<meta name="user-scalable" content="no"/>
	<meta name="robots" content="noindex,nofollow"/>
</head>
<body>
<?php
?>
<style>
	a.cmnt_un {
		text-decoration:none;
	} 
</style>
<script>
$(function() {
	$('[data-toggle]').tooltip();
	//cmnt btn on click
	$('#cmnt_btn').on('click',function() {
		$('#cmnt_box').focus();
	});
	//set width of comment form acc. to larger screen width
	if(window.matchMedia("(min-width:1100px) and (max-width:2000px)").matches) {
		var $mdcw=$('#modal_details_col').width();//width of mdc
		$mdcw-=90;
		$('#cmnt_box').width($mdcw);
	}
	if(window.matchMedia("(min-width:200px) and (max-width:1100px)").matches) {
		var $mdcw=$('#modal_details_col').width();//width of mdc
		$mdcw-=85;
		$('#cmnt_box').width($mdcw);
	}
	//able and disable post cmnt btn
	$('#cmnt_box').on('keyup',function() {
		if($('#cmnt_box').val().length>=1) $('#pycb').prop('disabled',false);
		else $('#pycb').prop('disabled',true);
	});
});
</script>
<?php
if($conn) {
	$uid=base64_decode($_SESSION['id']);
	$postid=$_POST['post_id'];
	//get post details
	$gpdr=$conn->prepare("SELECT * FROM posts WHERE post_id=?");
	$gpdr->bindParam(1,$postid,PDO::PARAM_INT);
	$gpdr->execute();
	$gpdrw=$gpdr->fetch(PDO::FETCH_ASSOC);
	$id=$gpdrw['user_id'];
	//get uploader details
	$gudr=$conn->prepare("SELECT * FROM users WHERE id=?");
	$gudr->bindParam(1,$id,PDO::PARAM_INT);
	$gudr->execute();
	$gudrw=$gudr->fetch(PDO::FETCH_ASSOC);
	//get user details
	$guidr=$conn->prepare("SELECT username,buddypic FROM users WHERE id=?");
	$guidr->bindParam(1,$uid,PDO::PARAM_INT);
	$guidr->execute();
	$guidrw=$guidr->fetch(PDO::FETCH_ASSOC);
	$gudrw['buddypic']=str_replace("uploads/".$gudrw['username']."/buddypic_uploads/","../uploads/".$gudrw['username']."/buddypic_uploads/",$gudrw['buddypic']);
	if(file_exists($gudrw['buddypic'])) {
		$gudrw['buddypic']=str_replace("../uploads/".$gudrw['username']."/buddypic_uploads/","uploads/".$gudrw['username']."/buddypic_uploads/",$gudrw['buddypic']);
		echo '<img src="'.$gudrw['buddypic'].'" class="rounded-circle" id="user_disp" alt="User display picture">';
	} else echo '<img src="images/def_buddypic.png" class="rounded-circle" id="user_disp" alt="Default display picture">';
	echo '<a href="profile.php?un='.$gudrw['username'].'" id="user_name"><b>'.$gudrw['username'].'</b>';
	?>
	<script>
	    var color; 
	    if(localStorage.getItem('themecolor')) color = localStorage.getItem('themecolor');
	    else color = "#9400D3";
	    $('#user_name').css('color', color);
	</script>
	<?php
	echo '<span id="user_tooltip" data-toggle="tooltip" title="Click to visit user profile" class="text-dark"><i class="fas fa-user"></i></span></a>';
	//show text with post
	if($gpdrw['post_matter']) {
		$post_matter = base64_decode($gpdrw['post_matter']);
		if(strpos($post_matter, "@") !== false) {
			$post_matter = preg_replace_callback('/@.+?\b/', function($m) {
                $str = substr($m[0], 1);
                return sprintf("<a href='profile.php?un=%s' class='text-dark'><b>%s</b></a>", $str, $str);
            }, $post_matter);
		}
		echo '<div class="post_mat">'.$post_matter.'</div>';
	}
	echo '<div class="btn-group btn-group-toggle d-flex">';
		$clr=$conn->prepare("SELECT * FROM activity WHERE bud_id1='$uid' AND bud_id2='$id' AND activity_type='bond' AND post_id='$postid'");
		$clr->bindParam(1,$uid,PDO::PARAM_INT);
		$clr->bindParam(2,$id,PDO::PARAM_INT);
		$clr->bindParam(3,$postid,PDO::PARAM_INT);
		$clr->execute();
		if($clr->rowCount() > 0) echo '<button class="btn btn-outline-danger w-100" id="bond_btn" onclick="like('.$postid.')"><i class="fas fas fa-heartbeat btn-texts"></i> Bond</button>';
		else echo '<button class="btn btn-outline-danger w-100" id="like_btn" onclick="like('.$postid.')"><i class="far far fa-heart fa-spin btn-texts"></i> Bond</button>';
		$clr=null;
		echo '<button class="btn btn-outline-danger w-100" id="cmnt_btn" title="Comment"><i class="far fa-comment btn-texts"></i> Comment</button>';
		?>
		<button class="btn btn-outline-danger w-100" data-toggle="tooltip" title="Click To Copy Link" onclick="share_btn('<?php echo $postid; ?>')"><i class="fas fa-share-square btn-texts"></i> Share</button>
		<?php
		if($uid==$id) echo '<button class="btn btn-outline-danger lb" id="dlt_post_btn" title="Delete Post" onclick="dlt_post('.$postid.')"><i class="fas fa-trash-alt"></i></button>';
	echo '</div>';
	//show number of likes
	echo '<div id="nol">';
		//liked by names query
		if($gpdrw['nol']>0 && $gpdrw['nol']<=6) {
			$lnr=$conn->prepare("SELECT activity.activity_id,users.id,users.username FROM activity,users WHERE activity.bud_id1=users.id AND activity.post_id=? AND activity.activity_type='bond'"); //liked by names query result
			$lnr->bindParam(1,$postid,PDO::PARAM_INT);
			$lnr->execute();
			echo '<b>Bonded </b>by ';
			//liked by names query
			while($lnrw=$lnr->fetch(PDO::FETCH_ASSOC)) echo '<a href="profile.php?un='.$lnrw['username'].'" class="text-dark">'.$lnrw['username'].'</a> ';
			$lnr=null;
		} else if($gpdrw['nol']>6) echo '<b>Bonded </b>by '.$gpdrw['nol'];
	echo '</div>';
	//view all comments btn
	echo '<div id="sacb_div">';
	if($gpdrw['noc']>7) 
	    echo '<a href="javascript:void(0)" class="text-muted" title="Show all the comments on this post" id="sacb" onclick="sacb('.$postid.')">View all '.$gpdrw['noc'].' comments</a>';
	echo '</div>';
	//show comment row
	echo '<div class="row" id="cmnt_row">';
		$guidrw['buddypic']=str_replace("uploads/".$guidrw['username']."/buddypic_uploads/","../uploads/".$guidrw['username']."/buddypic_uploads/",$guidrw['buddypic']);
		if(file_exists($guidrw['buddypic'])) {
			$guidrw['buddypic']=str_replace("../uploads/".$guidrw['username']."/buddypic_uploads/","uploads/".$guidrw['username']."/buddypic_uploads/",$guidrw['buddypic']);
			echo '<img src="'.$guidrw['buddypic'].'" class="img-fluid rounded-circle cmnt_disp" alt="Page display picture">';
		} else echo '<img src="images/def_buddypic.png" class="img-fluid rounded-circle cmnt_disp" alt="Default page display picture">';
		echo '<input type="text" id="cmnt_box" placeholder=" Add your comment..." title="Wanna add some comment?" name="cmnt_box" required maxlength="400"><button class="btn btn-sm btn-success" title="Post your comment" id="pycb" disabled onclick="comment('.$postid.')">Post</button>';
		echo '<div id="cmnt_spnr" class="mx-auto"><img src="images/spnr.gif" id="cmnt_spnr_img"></div>';
	echo '</div>';
	$gudr=null;
	echo '<div id="cmnts_sec">';
		$cqr=$conn->prepare("SELECT * FROM activity,users WHERE activity.bud_id1=users.id AND activity.post_id=? AND activity.activity_type='comment' ORDER BY activity.activity_time DESC LIMIT 7");
		$cqr->bindParam(1,$postid,PDO::PARAM_INT);
		$cqr->execute();
		while($cqrw=$cqr->fetch(PDO::FETCH_ASSOC)) {
			$cqrw['buddypic']=str_replace("uploads/".$cqrw['username']."/buddypic_uploads/","../uploads/".$cqrw['username']."/buddypic_uploads/",$cqrw['buddypic']);
			echo '<div class="row cmnt_row" style="padding-top:3px;padding-bottom:3px;">';
				if(file_exists($cqrw['buddypic'])) {
					$cqrw['buddypic']=str_replace("../uploads/".$cqrw['username']."/buddypic_uploads/","uploads/".$cqrw['username']."/buddypic_uploads/",$cqrw['buddypic']);
					echo '<img src="'.$cqrw['buddypic'].'" class="rounded-circle cmnt_disp" alt="Page display picture">';
				} else {
					echo '<img src="images/def_buddypic.png" class="rounded-circle cmnt_disp" alt="Default page display picture">';
				}
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
				if(($cqrw['bud_id1'] == $uid)||($uid == $id)) {
					?>
					<a href="javascript:void(0)" class="dlt_link" style="color:#708090" title="Delete" onclick="dlt_cmnt('<?php echo $postid; ?>','<?php echo $cqrw['activity_id']; ?>')"><b><i class="far fa-trash-alt"></i></b></a>
					<?php
				}
 			echo '</div>';
		}
		$cqr=null;
	echo '</div>';
	echo '<div id="post_dtls" class="text-muted"><span>UPLOADED ON buddyBonds</span><span id="post_time"><b>';
	$post_time = strtotime($gpdrw['post_time']);
	$dt->setTimestamp($post_time);
    if(time()-$post_time>0 && time()-$post_time<86400) echo $dt->format("H:i A");
    else if(time()-$post_time>=86400 && time()-$post_time<604800) echo $dt->format("D, H:i A");
    else if(time()-$post_time>=604800 && time()-$post_time<31536000) echo $dt->format("M j, H:i A");
    else echo $dt->format("M j, Y H:i A");
	echo '</b></span></div>';	
	$gpdr=null;
} else {
	die("Error!");
}
$conn=null;
?>
</body>
</html>