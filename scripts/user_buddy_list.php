<?php
include 'varcsc.php';
if($conn) {
	$id=$_POST['id'];
	$uid=base64_decode($_SESSION['id']);
	//get username
	$gunr=$conn->prepare("SELECT * FROM users WHERE id=?");
	$gunr->bindParam(1,$id,PDO::PARAM_INT);
	$gunr->execute();
	$gunrw=$gunr->fetch(PDO::FETCH_ASSOC);
	$getun=$gunrw['username'];
	$gunr=null;
	$qr=$conn->prepare("SELECT * FROM buddies WHERE (bud_id1=:id OR bud_id2=:id) AND active='1'");
	$qr->bindParam(":id",$id,PDO::PARAM_INT);
	$qr->execute();
	echo '<div id="buddy_list">';
	if($qr->rowCount()>0) {
		while($qrw=$qr->fetch(PDO::FETCH_ASSOC)) {
			if($qrw['bud_id1']==$id) {
				$bdid2=$qrw['bud_id2'];
				//buddy dtls query
				$bqr=$conn->prepare("SELECT * FROM users WHERE id=?");
				$bqr->bindParam(1,$bdid2,PDO::PARAM_INT);
				$bqr->execute();
				$bqrw=$bqr->fetch(PDO::FETCH_ASSOC);
				$bqrw['buddypic']=str_replace("uploads/".$bqrw['username']."/buddypic_uploads","../uploads/".$bqrw['username']."/buddypic_uploads",$bqrw['buddypic']);
				echo '<div class="bdrow">';
					if(file_exists($bqrw['buddypic'])) {
						$bqrw['buddypic']=str_replace("../uploads/".$bqrw['username']."/buddypic_uploads","uploads/".$bqrw['username']."/buddypic_uploads",$bqrw['buddypic']);
						echo '<img src="'.$bqrw['buddypic'].'" class="rounded-circle blbp" alt="Buddy picture for user '.$bqrw['username'].'">';
					} else {
						echo '<img src="http://localhost/buddyBonds_backup/images/def_buddypic.png" class="rounded-circle blbp" alt="Default buddy picture">';
					}
					echo '<a href="profile.php?un='.$bqrw['username'].'" class="bdlink">';
					echo '<span class="bdfn text-dark">'.$bqrw['fullname'].'</span>';
					echo '<span class="bdun text-muted">@'.$bqrw['username'].'</span>';
					echo '</a>';
					$bqr=null;
					if($uid==$id) {
						echo '<a href="javascript:void(0)" class="text-muted dlt_btn" title="Unbuddy user" onclick="dlt_bdy('.$bdid2.')"><i class="fas fa-trash-alt"></i></a>';
					}
				echo '</div>';
			} else if($qrw['bud_id2']==$id) {
				$bdid1=$qrw['bud_id1'];
				//buddy dtls query
				$bqr=$conn->prepare("SELECT * FROM users WHERE id=?");
				$bqr->bindParam(1,$bdid1,PDO::PARAM_INT);
				$bqr->execute();
				$bqrw=$bqr->fetch(PDO::FETCH_ASSOC);
				$bqrw['buddypic']=str_replace("uploads/".$bqrw['username']."/buddypic_uploads","../uploads/".$bqrw['username']."/buddypic_uploads",$bqrw['buddypic']);
				echo '<div class="bdrow">';
				if(file_exists($bqrw['buddypic'])) {
					$bqrw['buddypic']=str_replace("../uploads/".$bqrw['username']."/buddypic_uploads","uploads/".$bqrw['username']."/buddypic_uploads",$bqrw['buddypic']);
					echo '<img src="'.$bqrw['buddypic'].'" class="rounded-circle blbp" alt="Buddy picture for user '.$bqrw['username'].'">';
				} else {
					echo '<img src="http://localhost/buddyBonds_backup/images/def_buddypic.png" class="rounded-circle blbp" alt="Default buddy picture">';
				}
				echo '<a href="profile.php?un='.$bqrw['username'].'" class="bdlink">';
				echo '<span class="bdfn text-dark">'.$bqrw['fullname'].'</span>';
				echo '<span class="bdun text-muted">@'.$bqrw['username'].'</span>';
				echo '</a>';
				$bqr=null;
				if($uid==$id) echo '<a href="javascript:void(0)" class="text-muted dlt_btn" title="Unbuddy user" onclick="dlt_bdy('.$bdid1.')"><i class="fas fa-trash-alt"></i></a>';
				echo '</div>';
			}
		}
		$qr=null;
	} else {
		echo '<div class="text-center msg">';
			if($uid==$id) echo 'You have';
			else echo $getun.' has';
			echo ' no buddies yet!';
		echo '</div>';
	} 
	echo '</div>';
} else {
	die("Error!");
}
$conn=null;
?>