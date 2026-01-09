<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
require_once __DIR__ . '/../config/db.php';

$msg = "";

$refs = $pdo->query("SELECT * FROM referendums WHERE status='active'")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rid = $_POST['referendum_id'];
    $opt = trim($_POST['option']);

    if ($opt != "") {
        $stmt = $pdo->prepare("INSERT INTO referendum_options (referendum_id, option_text) VALUES (?,?)");
        $stmt->execute([$rid, $opt]);
        $msg = "Option added successfully!";
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Add Option</title></head>
<body>

<h2>Add Referendum Option</h2>
<p style="color:green"><?= $msg ?></p>

<form method="POST">
<select name="referendum_id" required>
<?php foreach ($refs as $r): ?>
    <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['title']) ?></option>
<?php endforeach; ?>
</select><br><br>

<input type="text" name="option" placeholder="Option text" required>
<button type="submit">Add Option</button>
</form>

<a href="dashboard.php">⬅ Back</a>

</body>
</html>
