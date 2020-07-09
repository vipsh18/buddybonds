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
	?>
	<script>
		$('[data-toggle]').tooltip();
	</script>
	<?php
	$userid=$_POST['user_id'];
	$uid=base64_decode($_SESSION['id']);
	echo '<span id="smlr_hdr">SIMILAR ACCOUNTS : <span class="text-muted" data-toggle="tooltip" title="These suggestions are merely based on the description given by this user. It may result in unexpected searches if the description is inappropriate." style="cursor:pointer"><b>Suggested</b></span></span>';
	//get user description
	$gudr=$conn->prepare("SELECT description FROM users WHERE id=?");
	$gudr->bindParam(1,$userid,PDO::PARAM_INT);
	$gudr->execute();
	$gudrw=$gudr->fetch(PDO::FETCH_ASSOC);
	$descp=$gudrw['description'];
	$gudr=null;
	$pd=explode(" ",$descp);
	$x=count($pd);
	$sa=0;
	$unarr=array();
	for($i=0;$i<$x;$i++) {
		$temp=$pd[$i];
		if(($temp=="of")||($temp=="from")||($temp=="the")||($temp=="to")||($temp=="an")||($temp=="a")||($temp=="all")||($temp=="also")||($temp=="and")||($temp=="any")||($temp=="as")||($temp=="at")||($temp=="be")||($temp=="he")||($temp=="her")||($temp=="she")||($temp=="by")||($temp=="do")||($temp=="if")||($temp=="else")||($temp=="for")||($temp=="while")||($temp=="etc")||($temp=="hi")||($temp=="hey")||($temp=="his")||($temp=="i")||($temp=="am")||($temp=="in")||($temp=="it")||($temp=="you")||($temp=="me")||($temp=="are")) break;
		$temp="%".$temp."%";
		$rpdr=null;
		$rpdr=$conn->prepare("SELECT username,buddypic FROM users WHERE (username LIKE ? OR fullname LIKE ? OR description LIKE ?) AND id!=? AND id!=? LIMIT 30");
		$rpdr->bindParam(1,$temp,PDO::PARAM_STR);
		$rpdr->bindParam(2,$temp,PDO::PARAM_STR);
		$rpdr->bindParam(3,$temp,PDO::PARAM_STR);
		$rpdr->bindParam(4,$uid,PDO::PARAM_INT);
		$rpdr->bindParam(5,$userid,PDO::PARAM_INT);
		$rpdr->execute();
		if($rpdr->rowCount()>0) {
			while($rpdrw=$rpdr->fetch(PDO::FETCH_ASSOC)) {
				//store usernames in unarr array
				foreach ($unarr as $var) {
					if($var==$rpdrw['username']) continue 2;
				}
				array_push($unarr,$rpdrw['username']);
				echo '<div class="row smlr_rw">';
				$rpdrw['buddypic']=str_replace("uploads/".$rpdrw['username']."/buddypic_uploads/","../uploads/".$rpdrw['username']."/buddypic_uploads/",$rpdrw['buddypic']);
				if(file_exists($rpdrw['buddypic'])) {
					$rpdrw['buddypic']=str_replace("../uploads/".$rpdrw['username']."/buddypic_uploads/","uploads/".$rpdrw['username']."/buddypic_uploads/",$rpdrw['buddypic']);
					echo '<img src="'.$rpdrw['buddypic'].'" class="rounded-circle smlr_dp" alt="User buddy picture">';
				} else {
					echo '<img src="images/def_buddypic.png" class="rounded-circle smlr_dp" alt="User Default Buddy Picture">';
				}
				echo '<a href="profile.php?un='.$rpdrw['username'].'" class="smlr_ttl"><b>'.$rpdrw['username'].'</b></a></div>';
				$sa++;
			}
		}
	}
	if($sa==0) echo '<div class="msg text-center">No similar account found!</div>';
	$sa=$unarr=$rpdr=null;
} else {
	die("Error!");
}
$conn=null;
?>
</body>
</html>