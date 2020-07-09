<?php 
include 'varcsc.php';
if($conn) {
	$postid=$_POST['post_id'];
	//
	//get number of comments
	//
	$gnocr=$conn->prepare("SELECT noc FROM posts WHERE post_id=?");
	$gnocr->bindParam(1,$postid,PDO::PARAM_INT);
	$gnocr->execute();
	$gnocrw=$gnocr->fetch(PDO::FETCH_ASSOC);
	if($gnocrw['noc']>2) echo '<a href="post.php?p='.$postid.'" class="text-muted ucmnt_btn">View all '.$gnocrw['noc'].' comments</a>';
	$gnocr=null;
} else {
	die("ERROR!");	
}
$conn=null;
?>