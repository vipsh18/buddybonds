<?php 
include 'varcsc.php';
if($conn) {
    $uid = base64_decode($_SESSION['id']);
    //get account type
    $gatr = $conn->prepare("SELECT account_type FROM users WHERE id=?");
    $gatr->bindParam(1,$uid,PDO::PARAM_INT);
    $gatr->execute();
    $gatrw = $gatr->fetch(PDO::FETCH_ASSOC);
    $gat = $gatrw['account_type'];
    if($gat == "private") $gat = "open";
    else if($gat == "open") $gat = "private";
    //shift account type
    $satr = $conn->prepare("UPDATE users SET account_type = ? WHERE id=?");
    $satr->bindParam(1,$gat,PDO::PARAM_STR);
    $satr->bindParam(2,$uid,PDO::PARAM_INT);
    $satr->execute();
} else {
    die("ERROR!");
}
$conn = null;
?>