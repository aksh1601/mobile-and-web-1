<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$title   = trim($_POST['title']);
$options = $_POST['options'];

if ($title == '' || count($options) < 2) {
    die("Title and at least 2 options are required.");
}

$pdo->beginTransaction();

$stmt = $pdo->prepare("INSERT INTO referendum(text,status) VALUES(?, 'closed')");
$stmt->execute([$title]);

$rid = $pdo->lastInsertId();

$optStmt = $pdo->prepare(
    "INSERT INTO referendum_options(referendum_id, option_text) VALUES(?,?)"
);

foreach ($options as $opt) {
    if (trim($opt) != '') {
        $optStmt->execute([$rid, trim($opt)]);
    }
}

$pdo->commit();

header("Location: dashboard.php");
exit;
