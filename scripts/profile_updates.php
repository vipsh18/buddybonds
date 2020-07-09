<?php
include 'varcsc.php';
$pid = $_GET['pid'];
$uid = base64_decode($_SESSION['id']);
$update = new \stdClass();
//get no of posts
$gnopr = $conn->prepare("SELECT * FROM posts WHERE user_id=?");
$gnopr->bindParam(1,$pid,PDO::PARAM_INT);
$gnopr->execute();
$posts = $gnopr->rowCount();
$update->posts = $posts;
$gnopr = null;
//get no of buddies
$gnobr=$conn->prepare("SELECT * FROM buddies WHERE (bud_id1=:id OR bud_id2=:id) AND active='1'");
$gnobr->bindParam(":id",$pid,PDO::PARAM_INT);
$gnobr->execute();
$buddies = $gnobr->rowCount();
$update->buddies = $buddies;
$gnobr = null;
if($pid != $uid) {
    //get profile btns
    $btns = '<div id="btns_rw_vsep">';
	//check if buddies already
	$cibr=$conn->prepare("SELECT * FROM buddies WHERE (bud_id1=:uid OR bud_id1=:pid) AND (bud_id2=:uid OR bud_id2=:pid) AND active='1'");
	$cibr->bindParam(":uid",$uid,PDO::PARAM_INT);
    $cibr->bindParam(":pid",$pid,PDO::PARAM_INT);
	$cibr->execute();
	if($cibr->rowCount()>0) {
		$btns.= '<span id="buddy_btn"><button class="btns_rw_btns btn btn-outline-success btn" onclick="unbuddy('.$pid.')"><i class="fas fa-handshake" id="fa_bdy_hand"></i> Buddies <i class="fas fa-check" id="fa_bdy_check"></i></button></span>';
		$btns.= '<button class="btn btn-outline-danger btns_rw_btns" id="see_mtl" onclick="see_mutual()"><i class="fas fa-heart" id="fa_bdy_heart"></i> Mutual</button>';
	} else {
		$cibr1=$conn->prepare("SELECT * FROM buddies WHERE bud_id1=? AND bud_id2=? AND active='0'");
		$cibr1->bindParam(1,$uid,PDO::PARAM_INT);
		$cibr1->bindParam(2,$pid,PDO::PARAM_INT);
		$cibr1->execute();
		if($cibr1->rowCount()>0) {
			$btns.= '<span id="buddy_btn"><button class="btns_rw_btns btn-outline-success btn" onclick="buddy('.$pid.')"><i class="fas fa-heart" id="fa_bdy_heart"></i> Buddy requested <i class="fas fa-check"></i></button></span>';
		} else {
			$cibr2=$conn->prepare("SELECT * FROM buddies WHERE bud_id1=? AND bud_id2=? AND active='0'");
			$cibr2->bindParam(1,$pid,PDO::PARAM_INT);
			$cibr2->bindParam(2,$uid,PDO::PARAM_INT);
			$cibr2->execute();
			if($cibr2->rowCount()>0) {
				$btns.= '<span id="buddy_btn"><button class="btns_rw_btns btn-outline-success btn" onclick="buddy('.$pid.')"><i class="fas fa-plus"></i> Confirm Request <i class="fas fa-user" id="fa_bdy_user1"></i></button></span>';
				$btns.= '<span id="delete_bdreq_btn"><button class="btns_rw_btns btn-outline-danger btn" onclick="delete_bdreq()"><i class="fas fa-trash-alt"></i> Delete request <i class="fas fa-user" id="fa_bdy_user2"></i></button></span>';
			} else {
			    $btns.= '<span id="buddy_btn"><button class="btns_rw_btns btn-outline-success btn" onclick="buddy('.$pid.')"><i class="fas fa-user" id="fa_bdy_user1"></i> Add Buddy <i class="fas fa-plus"></i></button></span>';
			}
			$cibr2 = null;
		}
		$cibr1 = null;
	}
	$cibr=null;
	$btns.= '<button class="btn btn-outline-primary btns_rw_btns" id="smlr_btn" onclick="similar_accounts()"><i class="fas fa-users" id="fa_bdy_users"></i> Similar accounts <i class="fas fa-laptop" id="fa_bdy_laptop"></i></button>';
	$btns.= '</div>';
	$update->btns = $btns;
}
echo json_encode($update);
$conn = null;
?>