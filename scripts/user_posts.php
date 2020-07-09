<?php
include 'varcsc.php';
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
date_default_timezone_set($tz); 
if($conn) {	
	?>
	<script>
		$(function() {
			$('.close').on('click',function() {
				$('#myModal').fadeOut();
			});
			if(window.matchMedia("(min-width:1000px) and (max-width:2000px)").matches) {
				$('.close').on('click',function() {
					$('#nav').fadeIn();
				});
			}
			if(window.matchMedia("(min-width:200px) and (max-width:1000px)").matches) {
				$('.close').on('click',function() {
					$('#m_nav').fadeIn();
				});
			}
			if(window.matchMedia("(min-width:200px) and (max-width:900px)").matches) {
				var $saw=$('#posts_area').width();
				$saw-=10;
				$('.inner-image').width($saw/3);
			}
		});
	</script>
	<?php
	$uid=base64_decode($_SESSION['id']);
	$id=$_POST['id'];
	//get the user's posts
	$pr=$conn->prepare("SELECT * FROM posts WHERE user_id=? ORDER BY post_time DESC");
	$pr->bindParam(1,$id,PDO::PARAM_INT);
	$pr->execute();
	//get user's details
	$gudr=$conn->prepare("SELECT * FROM users WHERE id=?");
	$gudr->bindParam(1,$id,PDO::PARAM_INT);
	$gudr->execute();
	$gudrw=$gudr->fetch(PDO::FETCH_ASSOC);
	if($pr->rowCount()>0) {
		echo '<div class="flex-container">';
			while($prw=$pr->fetch(PDO::FETCH_ASSOC)) {
				//random id for image 
				$rifi=$prw['post_id'];
				$extension=strtolower(pathinfo($prw['post_content'],PATHINFO_EXTENSION));
				if(($extension=="jpg") || ($extension=="jpeg") || ($extension=="png")) echo '<img src="'.$prw['post_content'].'" class="inner-image rounded" onclick="modal('.$rifi.')" id="'.$rifi.'" alt="Uploaded picture by user '.$gudrw['username'].'">';
			}
			$pr=null;
		echo '</div>';
		echo '<div id="myModal" class="modal">';
			echo '<span class="close">&times;</span>';
			echo '<div class="row" id="modal_row">';
				echo '<div class="col-lg-6 mx-auto" id="modal_content_col"><img class="modal-content img-fluid rounded" id="postpic"></div>';
				echo '<div class="col-lg-6" id="modal_details_col"><div id="modal_details"><div id="lp_spnr" class="mx-auto"><img id="lp_spnr_img" src="images/spnr.gif"></div></div></div>';
			echo '</div>';
		echo '</div>';
	} else {
		echo '<div class="text-center msg">';
			if($uid==$id) echo 'You have';
			else echo $gudrw['username'].' has';
			echo ' not uploaded any posts yet!';
		echo '</div>';
	}
	$gudr=null;
} else {
	die("error!");
}
$conn=null;
?>
</body>
</html>