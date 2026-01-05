<?php
session_start();
require '../config/db.php';

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit;
}

$refs = $pdo->query("SELECT * FROM referendum")->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Admin Dashboard</h1>

<?php foreach($refs as $r): ?>

<h3><?= htmlspecialchars($r['text']) ?> (<?= $r['status'] ?>)</h3>

<?php
$opt = $pdo->prepare("SELECT * FROM referendum_options WHERE referendum_id=?");
$opt->execute([$r['referendum_id']]);
$options = $opt->fetchAll(PDO::FETCH_ASSOC);

foreach($options as $o){
    $c = $pdo->prepare("SELECT COUNT(*) FROM voter_history WHERE referendum_id=? AND voted_option_id=?");
    $c->execute([$r['referendum_id'],$o['option_id']]);
    $count = $c->fetchColumn();

    echo htmlspecialchars($o['option_text'])." : ".$count." votes<br>";
}
?>

<hr>

<?php endforeach; ?>
