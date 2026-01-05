<?php
session_start();
require '../config/db.php';

/* --- Protect admin route --- */
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_POST['rid'], $_POST['title'], $_POST['options'])) {
    die("Invalid form submission.");
}

$rid     = (int)$_POST['rid'];
$title   = trim($_POST['title']);
$options = $_POST['options'];

/* --- Re-check status on backend --- */
$check = $pdo->prepare("SELECT status FROM referendum WHERE referendum_id=?");
$check->execute([$rid]);
$status = $check->fetchColumn();

if ($status === 'open') {
    die("❌ Editing is locked because this referendum is already OPEN.");
}

/* --- Update referendum title --- */
$pdo->prepare("UPDATE referendum SET text=? WHERE referendum_id=?")
    ->execute([$title, $rid]);

/* --- Update options --- */
$stmt = $pdo->prepare("UPDATE referendum_options SET option_text=? WHERE opt_id=?");

foreach ($options as $id => $txt) {
    $stmt->execute([trim($txt), (int)$id]);
}

header("Location: dashboard.php");
exit;
