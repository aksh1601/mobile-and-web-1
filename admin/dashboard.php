<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/db.php';

try {
    $stmt = $pdo->query("SELECT * FROM referendums");
    $referendums = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("DB ERROR: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
</head>
<body>

<h1>Admin Dashboard</h1>

<a href="add_referendum.php">➕ Create Referendum</a> |
<a href="add_option.php">➕ Add Option</a>

<hr>

<?php if (empty($referendums)): ?>
    <p>No referendums created yet.</p>
<?php endif; ?>

<?php foreach ($referendums as $r): ?>
    <h3><?= htmlspecialchars($r['title']) ?> (<?= $r['status'] ?>)</h3>

    <?php
    $ops = $pdo->prepare("SELECT option_text FROM referendum_options WHERE referendum_id = ?");
    $ops->execute([$r['id']]);
    $options = $ops->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <ul>
        <?php foreach ($options as $o): ?>
            <li><?= htmlspecialchars($o['option_text']) ?></li>
        <?php endforeach; ?>
    </ul>
    <hr>
<?php endforeach; ?>

</body>
</html>
