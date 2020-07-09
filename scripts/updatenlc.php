<?php 
include 'varcsc.php';
date_default_timezone_set($tz);
if($conn) {
	$postid=$_POST['post_id'];
	//get likes
	$glr=$conn->prepare("SELECT * FROM posts WHERE post_id=?");
	$glr->bindParam(1,$postid,PDO::PARAM_INT);
	$glr->execute();
	$glrw=$glr->fetch(PDO::FETCH_ASSOC);
	if($glrw['nol']>0&&$glrw['nol']<6) {
		//liked by names query
		$lnr=$conn->prepare("SELECT activity.activity_id,users.id,users.username FROM activity,users WHERE activity.bud_id1=users.id AND activity.post_id=? AND activity.activity_type='bond'"); 
		$lnr->bindParam(1,$postid,PDO::PARAM_INT);
		$lnr->execute();
		echo '<b>Bonded </b>by ';
		while($lnrw=$lnr->fetch(PDO::FETCH_ASSOC)) echo '<a href="profile.php/'.$lnrw['username'].'" class="text-dark">'.$lnrw['username'].'</a> ';
	} else if($glrw['nol']>=6) echo '<b>Bonded </b>by '.$glrw['nol'];
	$glr=$lnr=null;
} else {
	die("ERROR!");
}
$conn=null;
?>