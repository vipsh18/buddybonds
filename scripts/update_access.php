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
$id=$_POST['id'];
$uid=base64_decode($_SESSION['id']);
$uun=base64_decode($_SESSION['username']);
if($conn) {
	?>
	<script>
		$(function() {
			$('[data-toggle]').tooltip();
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
			//
			//show user's posts
			//
			$('#posts_btn').on('click',function() {
				var id='<?php echo $id; ?>';
				$('#pro_spnr').show();
				$('#posts').css('border-bottom','2px solid #1E90FF');
				$('#buddies').css('border-bottom','0px');
				$('#pages').css('border-bottom','0px');
				$.ajax({
					type:"POST",
					url:"http://localhost/buddyBonds_backup/scripts/user_posts.php",
					data:{
						"id":id
					},
					success:function(result) {
						$('#pro_spnr').hide();
						$('#posts_area').html(result);	
					}
				});
			});
			//
			//show user's posts small
			//
			$('#posts_btn_sm').on('click',function() {
				var id='<?php echo $id; ?>';
				$('#pro_spnr').show();
				$('#posts_btn_sm').css('border-bottom','2px solid #1E90FF');
				$('#buddies_btn_sm').css('border-bottom','0px');
				$('#pages_btn_sm').css('border-bottom','0px');
				$.ajax({
					type:"POST",
					url:"http://localhost/buddyBonds_backup/scripts/user_posts.php",
					data:{
						"id":id
					},
					success:function(result) {
						$('#pro_spnr').hide();
						$('#posts_area').html(result);	
					}
				});
			});
			//
			//show user's buddies
			//
			$('#buddies_btn').on('click',function() {
				var id='<?php echo $id; ?>';
				$('#pro_spnr').show();
				$('#buddies').css('border-bottom','2px solid #1E90FF');
				$('#posts').css('border-bottom','0px');
				$('#pages').css('border-bottom','0px');
				$.ajax({
					type:"POST",
					url:"http://localhost/buddyBonds_backup/scripts/user_buddy_list.php",
					data:{
						"id":id
					},
					success:function(result) {
						$('#pro_spnr').hide();
						$('#posts_area').html(result);
					}
				});
			});
			//
			//show user's buddies small
			//
			$('#buddies_btn_sm').on('click',function() {
				var id='<?php echo $id; ?>';
				$('#pro_spnr').show();
				$('#buddies_btn_sm').css('border-bottom','2px solid #1E90FF');
				$('#posts_btn_sm').css('border-bottom','0px');
				$('#pages_btn_sm').css('border-bottom','0px');
				$.ajax({
					type:"POST",
					url:"http://localhost/buddyBonds_backup/scripts/user_buddy_list.php",
					data:{
						"id":id
					},
					success:function(result) {
						$('#pro_spnr').hide();
						$('#posts_area').html(result);
					}
				});
			});
		});
	</script>
	<?php
	//
	//get info from id result
	//
	$gifir=$conn->prepare("SELECT * FROM users WHERE id=?");
	$gifir->bindParam(1,$id,PDO::PARAM_INT);
	$gifir->execute();
	$gifirw=$gifir->fetch(PDO::FETCH_ASSOC);
	$getun=$gifirw['username'];
	$gifir=null;
	//
	//check if buddies already
	//
	$cibr=$conn->prepare("SELECT * FROM buddies WHERE (bud_id1=:uid OR bud_id1=:id) AND (bud_id2=:uid OR bud_id2=:id) AND active='1'");
	$cibr->bindParam(":uid",$uid,PDO::PARAM_INT);
	$cibr->bindParam(":id",$id,PDO::PARAM_INT);
	$cibr->execute();
	if($cibr->rowCount()<=0) {
		echo '<div class="msg text-center">You need to be buddies with '.$getun.' to view more details about them.</div>';
	} else if($cibr->rowCount()>0) {
		echo '<div id="navrow" class="row">';
			//
			//get number of posts
			//
			$upqr=$conn->prepare("SELECT * FROM posts WHERE user_id=?");
			$upqr->bindParam(1,$id,PDO::PARAM_INT);
			$upqr->execute();
			echo '<div class="col-lg-6 text-center chnl_links" id="posts" style="border-bottom:2px solid #1E90FF"><a href="javascript:void(0)" id="posts_btn" data-toggle="tooltip" title="Shows ';
			if($getun==$uun) {
				echo 'your';
			} else {
				echo $getun.'\'s';
			}
			echo ' posts"><b><i class="fas fa-list-ul"></i> POSTS <span class="badge badge-success" id="postsno">'.$upqr->rowCount().'</span></b></a></div>';
			//
			//get number of buddies
			//
			$gnubr=$conn->prepare("SELECT * FROM buddies WHERE (bud_id1=:id OR bud_id2=:id) AND active='1'");
			$gnubr->bindParam(":id",$id,PDO::PARAM_INT);
			$gnubr->execute();
			echo '<div class="col-lg-6 text-center chnl_links" id="buddies"><a href="javascript:void(0)" id="buddies_btn" data-toggle="tooltip" title="Shows ';
			if($getun==$uun) {
				echo 'your';
			} else {
				echo $getun.'\'s';
			}
			echo ' buddies"><b><i class="fas fa-handshake"></i> BUDDIES <span class="badge badge-success" id="buddiesno">'.$gnubr->rowCount().'</span></b></a></div>';
		echo '</div>';
		echo '<div id="navrow_sm">';
			echo '<button class="btn btn-light chnl_links text-center" style="border-bottom:2px solid #1E90FF" id="posts_btn_sm"><a href="javascript:void(0)"><i class="fas fa-list-ul"></i> <span class="badge badge-success" id="postsno_sm">'.$upqr->rowCount().'</span></a></button>';
			$upqr=null;
			echo '<button class="btn btn-light chnl_links text-center" id="buddies_btn_sm"><a href="javascript:void(0)"><i class="fas fa-handshake"></i> <span class="badge badge-success" id="buddiesno_sm">'.$gnubr->rowCount().'</span></a></button>';
			$gnubr=null;
		echo '</div>';
		echo '<div id="pro_spnr"><img src="images/spnr.gif" id="pro_spnr_img"></div>';
		//
		// posts area
		// show user's posts
		//	
		echo '<div id="posts_area">';
			//
			//get the user's posts
			//
			$pr=$conn->prepare("SELECT * FROM posts WHERE user_id=? ORDER BY post_time DESC");
			$pr->bindParam(1,$id,PDO::PARAM_INT);
			$pr->execute();
			if($pr->rowCount()>0) {
				echo '<div class="flex-container">';
					while($prw=$pr->fetch(PDO::FETCH_ASSOC)) {
						//
						//random id for image 
						//
						$rifi=$prw['post_id'];
						$fltr=$prw['fltr'];
						$extension=strtolower(pathinfo($prw['post_content'],PATHINFO_EXTENSION));
						if(($extension=="jpg") || ($extension=="jpeg") || ($extension=="png")) {
							?>
							<img src="<?php echo $prw['post_content']; ?>" class="inner-image rounded" onclick="modal('<?php echo $rifi; ?>','<?php echo $fltr; ?>')" id="<?php echo $rifi; ?>" alt="Uploaded picture" onload="fltr('<?php echo $fltr; ?>','<?php echo $rifi; ?>')">';
							<?php
						}
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
					if($uid==$id) {
						echo 'You have';
					} else {
						echo $getun.' has';
					}
					echo ' not uploaded any posts yet!';
				echo '</div>';
			}
		echo '</div>';
	}
	$cibr=null;
} else {
	die("Error!");
}
$conn=null;
?>
</body>
</html>