<?php
session_start();
require '../config/db.php';

if(!isset($_SESSION['user'])){
    header("Location: ../public/login.php");
    exit;
}

$voter = $_SESSION['user'];
$votes = $_POST['vote'];

foreach($votes as $ref_id => $opt_id){

    $chk = $conn->prepare("SELECT * FROM voter_history WHERE voter_email=? AND referendum_id=?");
    $chk->bind_param("si",$voter,$ref_id);
    $chk->execute();
    $r = $chk->get_result();

    if($r->num_rows == 0){
        $ins = $conn->prepare("INSERT INTO voter_history (voter_email,referendum_id,option_id) VALUES (?,?,?)");
        $ins->bind_param("sii",$voter,$ref_id,$opt_id);
        $ins->execute();
    }
}

header("Location: dashboard.php");
exit;
?>
