<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['voter'])) {
    header("Location: ../auth/login.php");
    exit;
}

$email = $_SESSION['voter'];

$refs = $pdo->query("SELECT * FROM referendum WHERE status='open'")->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Voter Dashboard</h1>
<p>Logged in as <?= htmlspecialchars($email) ?></p>

<?php foreach($refs as $r): ?>
    <p>
        <?= htmlspecialchars($r['text']) ?>
        <a href="vote.php?id=<?= $r['referendum_id'] ?>">Vote</a>
    </p>
<?php endforeach; ?>

<a href="../auth/logout.php">Logout</a>
