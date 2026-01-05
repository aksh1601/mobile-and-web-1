<?php
session_start();
require '../config/db.php';

/* --- Protect admin route --- */
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['rid'])) {
    die("Missing referendum ID.");
}

$rid = (int)$_GET['rid'];

/* --- Check referendum status --- */
$check = $pdo->prepare("SELECT text, status FROM referendum WHERE referendum_id=?");
$check->execute([$rid]);
$r = $check->fetch(PDO::FETCH_ASSOC);

if (!$r) {
    die("Referendum not found.");
}

if ($r['status'] === 'open') {
    die("❌ Editing is locked because this referendum is already OPEN.");
}

/* --- Fetch options --- */
$opts = $pdo->prepare("SELECT opt_id, option_text FROM referendum_options WHERE referendum_id=?");
$opts->execute([$rid]);
$options = $opts->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<title>Edit Referendum</title>
</head>
<body>

<h2>Edit Referendum</h2>

<form method="post" action="update_referendum.php">
<input type="hidden" name="rid" value="<?= $rid ?>">

Title:<br>
<input type="text" name="title" value="<?= htmlspecialchars($r['text']) ?>" required><br><br>

Options:<br>
<?php foreach($options as $o): ?>
    <input type="text" name="options[<?= $o['opt_id'] ?>]"
           value="<?= htmlspecialchars($o['option_text']) ?>" required><br>
<?php endforeach; ?>

<br>
<button type="submit">Update Referendum</button>
</form>

<br><a href="dashboard.php">Back to Dashboard</a>

</body>
</html>
