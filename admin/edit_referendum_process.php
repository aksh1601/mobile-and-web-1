<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['admin'])) header("Location: login.php");

$rid = $_POST['rid'];
$title = $_POST['title'];
$options = $_POST['options'];

$pdo->prepare("UPDATE referendum SET text=? WHERE referendum_id=?")
    ->execute([$title,$rid]);

$upd = $pdo->prepare("UPDATE referendum_options SET option_text=? WHERE opt_id=?");

foreach($options as $id=>$text){
    $upd->execute([$text,$id]);
}

header("Location: dashboard.php");
exit;
