<?php
session_start();
ini_set('display_errors',1);
error_reporting(E_ALL);
require_once __DIR__ . '/config/db.php';

$msg = "";

$refs = $pdo->query("SELECT * FROM referendums WHERE status='active'")->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['vote'])) {
    $uid = $_SESSION['user_id'];
    $rid = $_POST['rid'];
    $oid = $_POST['option'];

    try {
        $stmt = $pdo->prepare("INSERT INTO votes (user_id, referendum_id, option_id) VALUES (?,?,?)");
        $stmt->execute([$uid,$rid,$oid]);
        $msg = "Vote submitted successfully!";
    } catch (PDOException $e) {
        $msg = "You already voted on this referendum.";
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Vote</title></head>
<body>

<h2>Active Referendums</h2>
<p style="color:green"><?= $msg ?></p>

<?php foreach ($refs as $r): ?>
<form method="POST">
    <h3><?= htmlspecialchars($r['title']) ?></h3>
    <input type="hidden" name="rid" value="<?= $r['id'] ?>">

    <?php
    $ops = $pdo->prepare("SELECT * FROM referendum_options WHERE referendum_id=?");
    $ops->execute([$r['id']]);
    foreach ($ops as $o):
    ?>
        <label>
            <input type="radio" name="option" value="<?= $o['id'] ?>" required>
            <?= htmlspecialchars($o['option_text']) ?>
        </label><br>
    <?php endforeach; ?>

    <button name="vote">Submit Vote</button>
</form>
<hr>
<?php endforeach; ?>

</body>
</html>
