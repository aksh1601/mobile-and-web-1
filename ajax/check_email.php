<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

require '../config/db.php';

$email = trim($_GET['email'] ?? '');

if ($email == "") {
    echo "<span style='color:red'>Email required</span>";
    exit;
}

$stmt = $conn->prepare("SELECT voter_email FROM voters WHERE voter_email=?");
if(!$stmt){
    die("Prepare failed: ".$conn->error);
}

$stmt->bind_param("s",$email);
$stmt->execute();
$res = $stmt->get_result();

if($res->num_rows > 0)
    echo "<span style='color:red'>Email already registered</span>";
else
    echo "<span style='color:green'>Email available ✔</span>";
