<?php 
include 'varcsc.php';
date_default_timezone_set($tz);
if($conn) {
	$postid=$_POST['post_id'];
	//get number of comments
	$gnocr=$conn->prepare("SELECT noc FROM posts WHERE post_id=?");
	$gnocr->bindParam(1,$postid,PDO::PARAM_INT);
	$gnocr->execute();
	$gnocrw=$gnocr->fetch(PDO::FETCH_ASSOC);
	if($gnocrw['noc']>7) 
	    echo '<a href="javascript:void(0)" class="text-muted" data-toggle="tooltip" title="Show all the comments on this post" id="sacb" onclick="sacb('.$postid.')">View all '.$gnocrw['noc'].' comments</a>';
	$gnocr=null;
} else {
	die("Error!");
}
$conn=null;
?>