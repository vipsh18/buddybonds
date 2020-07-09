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
if($conn) {
	$uid=base64_decode($_SESSION['id']);
	$uun=base64_decode($_SESSION['username']);
	$explrarr=json_decode($_POST['explrarr']);
	//
	//check if user has any interests registered
	//
	$ciuhir=$conn->prepare("SELECT hashtag FROM hashtags WHERE userid=?");
	$ciuhir->bindParam(1,$uid,PDO::PARAM_INT);
	$ciuhir->execute();
	if($ciuhir->rowCount()>0) {
		while($ciuhirw=$ciuhir->fetch(PDO::FETCH_ASSOC)) {
			$hashtag="%".$ciuhirw['hashtag']."%";
			$gppr=$conn->prepare("SELECT * FROM page_posts WHERE hashtags LIKE ? ORDER BY nol DESC LIMIT 20");
			$gppr->bindParam(1,$hashtag,PDO::PARAM_STR);
			$gppr->execute();
			if($gppr->rowCount()>0) {	
				while($gpprw=$gppr->fetch(PDO::FETCH_ASSOC)) {
					foreach($explrarr as $varid) {
						if($varid==$gpprw['post_id']) {
							continue 2;
						}
					}
					array_push($explrarr,$gpprw['post_id']);
					//
					//show the post when interested and not shown before
					//
					if(file_exists($gpprw['post_content'])) {
						$extension=strtolower(pathinfo($gpprw['post_content'],PATHINFO_EXTENSION));
						if(($extension=="jpg") || ($extension=="png") || ($extension=="jpeg")) {
							echo '<a href="page_post.php?p='.$gpprw['post_id'].'"><img src="'.$gpprw['post_content'].'" class="rounded inner-image"></a>';
						} else if(($extension=="gif") || ($extension=="mp4") || ($extension=="webm") || ($extension=="ogg")) {
							echo '<a href="page_post.php?p='.$gpprw['post_id'].'"><video muted class="rounded inner-image"><source src="'.$gpprw['post_content'].'" type="video/'.$extension.'"></video></a>';
						}
					}
				}
				$gppr=null;
			} else {
				$gppr=$conn->prepare("SELECT * FROM page_posts ORDER BY nol DESC LIMIT 20");
				$gppr->bindParam(1,$uid,PDO::PARAM_INT);
				$gppr->execute();
				//
				//show trending posts
				//
				while($gpprw=$gppr->fetch(PDO::FETCH_ASSOC)) {
					foreach($explrarr as $varid) {
						if($varid==$gpprw['post_id']) {
							continue 2;
						}
					}
					array_push($explrarr,$gpprw['post_id']);
					if(file_exists($gpprw['post_content'])) {
						$extension=strtolower(pathinfo($gpprw['post_content'],PATHINFO_EXTENSION));
						if(($extension=="jpg") || ($extension=="png") || ($extension=="jpeg")) {
							echo '<a href="page_post.php?p='.$gpprw['post_id'].'"><img src="'.$gpprw['post_content'].'" class="rounded inner-image"></a>';
						} else if(($extension=="gif") || ($extension=="mp4") || ($extension=="webm") || ($extension=="ogg")) {
							echo '<a href="page_post.php?p='.$gpprw['post_id'].'"><video muted class="rounded inner-image"><source src="'.$gpprw['post_content'].'" type="video/'.$extension.'"></video></a>';
						}
					}
				}
				$gppr=null;
			}
		}
	} else {
		$gppr=$conn->prepare("SELECT * FROM page_posts ORDER BY nol DESC LIMIT 20");
		$gppr->bindParam(1,$uid,PDO::PARAM_INT);
		$gppr->execute();
		//
		//show trending posts
		//
		while($gpprw=$gppr->fetch(PDO::FETCH_ASSOC)) {
			array_push($explrarr,$gpprw['post_id']);
			if(file_exists($gpprw['post_content'])) {
				$extension=strtolower(pathinfo($gpprw['post_content'],PATHINFO_EXTENSION));
				if(($extension=="jpg") || ($extension=="png") || ($extension=="jpeg")) {
					echo '<a href="page_post.php?p='.$gpprw['post_id'].'"><img src="'.$gpprw['post_content'].'" class="rounded inner-image"></a>';
				} else if(($extension=="gif") || ($extension=="mp4") || ($extension=="webm") || ($extension=="ogg")) {
					echo '<a href="page_post.php?p='.$gpprw['post_id'].'"><video muted class="rounded inner-image"><source src="'.$gpprw['post_content'].'" type="video/'.$extension.'"></video></a>';
				}
			}
		}
		$gppr=null;
	}
	$ciuhir=null;
} else {
	die("Error!");
}
$conn=null;
?>
</body>
</html>