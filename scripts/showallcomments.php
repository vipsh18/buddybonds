<?php 
include 'varcsc.php';
$tz = new DateTimeZone($tz);
$dt = new DateTime();
$dt->setTimezone($tz);
if($conn) {
	$pid=$_POST['post_id'];
	$micran=$_POST['micran'];
	$ubid=$_POST['ubid'];
	//comment query
	$cr=$conn->prepare("SELECT activity.activity_id,activity.bud_id1,activity.activity_content,activity.activity_time,users.id,users.username,users.buddypic FROM activity,users WHERE activity.bud_id1=users.id AND activity.activity_type='comment' AND activity.post_id=? ORDER BY activity.activity_time");
	$cr->bindParam(1,$pid,PDO::PARAM_INT);
	$cr->execute();
	if($cr->rowCount()>0) { 
		while($crw=$cr->fetch(PDO::FETCH_ASSOC)) {
			echo '<div class="row">';
			$getun=$crw['username'];
			$crw['buddypic']=str_replace("uploads/".$getun."/buddypic_uploads/","../uploads/".$getun."/buddypic_uploads/",$crw['buddypic']);
			if(file_exists($crw['buddypic'])) {
				$crw['buddypic']=str_replace("../uploads/".$getun."/buddypic_uploads/","uploads/".$getun."/buddypic_uploads/",$crw['buddypic']);
				echo '<img src="'.$crw['buddypic'].'" class="comm_pic rounded-circle" alt="Buddy picture of the user.">';
			} else {
				echo '<img src="images/def_buddypic.png" class="comm_pic rounded-circle" alt="Default buddy picture for the user.">';
			}
			echo '<a href="profile.php?un='.$crw['username'].'" class="cmnt_un text-dark"><b>'.$crw['username'].'</b></a>';
			echo '<span class="comm_show">'.$crw['activity_content'].'</span>';
			echo '<span class="text-muted time_show">';
			$acti_time = $crw['activity_time'];
			$dt->setTimestamp($acti_time);
            if(time()-$acti_time>0 && time()-$acti_time<86400) echo $dt->format("H:i A");
            else if(time()-$acti_time>=86400 && time()-$acti_time<604800) echo $dt->format("D, H:i A");
            else if(time()-$acti_time>=604800 && time()-$acti_time<31536000) echo $dt->format("M j, H:i A");
            else echo $dt->format("M j, Y H:i A");
			echo '.</span>';
			if($crw['bud_id1']==$cookie_id) {
				?>
				<a href="javascript:void(0)" class="dlt_link" style="color:#708090" onclick="dlt_cmnt('<?php echo $pid; ?>','<?php echo $micran; ?>','<?php echo $ubid; ?>','<?php echo $crw['activity_id']; ?>')" title="Delete"><b><i class="far fa-trash-alt"></i></b></a>
				<?php
			}
			echo '</div>';
		}
		$cr=null;
	}
} else {
	die("ERROR!");
}
$conn=null;
?>