<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['voter'])) {
    header("Location: ../auth/login.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("Invalid referendum.");
}

$rid = (int)$_GET['id'];
$email = $_SESSION['voter'];

# Check referendum
$r = $pdo->prepare("SELECT * FROM referendum WHERE referendum_id=? AND status='open'");
$r->execute([$rid]);
$ref = $r->fetch(PDO::FETCH_ASSOC);
if(!$ref) die("Referendum closed or not found.");

# Check if already voted
$chk = $pdo->prepare("SELECT 1 FROM voter_history WHERE voter_email=? AND referendum_id=?");
$chk->execute([$email,$rid]);
if($chk->rowCount()>0){
    die("You already voted.");
}

# Load options
$opt = $pdo->prepare("SELECT * FROM referendum_options WHERE referendum_id=?");
$opt->execute([$rid]);
$options = $opt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2><?= htmlspecialchars($ref['text']) ?></h2>

<form method="post" action="submit_vote.php">
<input type="hidden" name="rid" value="<?= $rid ?>">

<?php foreach($options as $o): ?>
    <label>
        <input type="radio" name="option_id" value="<?= $o['option_id'] ?>" required>
        <?= htmlspecialchars($o['option_text']) ?>
    </label><br>
<?php endforeach; ?>

<br>
<button type="submit">Submit Vote</button>
</form>
