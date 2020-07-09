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
		$url=file_get_contents("https://newsapi.org/v2/top-headlines?category=sports&apiKey=d526d1decd964bb8be209820fc4ec219");
		$urlarray=json_decode($url,true);
		$articles=$urlarray['articles'];
		for($i=0;$i<count($articles);$i++) {
			$sites=$urlarray['articles'][$i];
			echo '<div><img src="'.$sites['urlToImage'].'" class="img-thumbnail" id="news_img" alt="No display image for news"><a href="'.$sites['url'].'" id="url" class="text-dark">'.$sites['title'].'</a></div><hr>';
		}
?>
</body>
</html>