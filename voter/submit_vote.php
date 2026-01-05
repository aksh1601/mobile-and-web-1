<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['voter'])) {
    header("Location: ../auth/login.php");
    exit;
}

if (!isset($_POST['rid'],$_POST['option_id'])) {
    die("Invalid vote request.");
}

$rid = (int)$_POST['rid'];
$opt = (int)$_POST['option_id'];
$email = $_SESSION['voter'];

# Prevent double vote
$chk = $pdo->prepare("SELECT 1 FROM voter_history WHERE voter_email=? AND referendum_id=?");
$chk->execute([$email,$rid]);
if($chk->rowCount()>0) die("Already voted.");

$pdo->prepare("
    INSERT INTO voter_history(voter_email,referendum_id,voted_option_id)
    VALUES (?,?,?)
")->execute([$email,$rid,$opt]);

header("Location: dashboard.php");
exit;
