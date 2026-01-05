<?php
session_start();
require '../config/db.php';

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit;
}

$rid = $_POST['rid'];
$pdo->prepare("UPDATE referendum SET status='open' WHERE referendum_id=?")
    ->execute([$rid]);

header("Location: dashboard.php");
exit;

