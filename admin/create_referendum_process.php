<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$title = trim($_POST['title']);
$options = array_filter($_POST['options'], fn($o) => trim($o) != "");
$closesAt = $_POST['closes_at'] ?: null;

if ($title == "" || count($options) < 2) {
    die("Title and at least TWO options are required.");
}

$pdo->beginTransaction();

/* INSERT REFERENDUM WITH AUTO-CLOSE */
$stmt = $pdo->prepare("
    INSERT INTO referendum(text,status,closes_at)
    VALUES (?, 'open', ?)
");
$stmt->execute([$title, $closesAt]);

$rid = $pdo->lastInsertId();

/* INSERT OPTIONS */
$opt = $pdo->prepare("
    INSERT INTO referendum_options (referendum_id, option_text)
    VALUES (?, ?)
");

foreach ($options as $o) {
    $opt->execute([$rid, trim($o)]);
}

$pdo->commit();

header("Location: dashboard.php");
exit;
