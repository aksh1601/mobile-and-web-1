<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
require_once __DIR__ . '/../config/db.php';

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);

    if ($title != "") {
        $stmt = $pdo->prepare("INSERT INTO referendums(title) VALUES (?)");
        $stmt->execute([$title]);
        header("Location: dashboard.php");
        exit;
    } else {
        $msg = "Title cannot be empty.";
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Add Referendum</title></head>
<body>

<h2>Create Referendum</h2>
<p style="color:red"><?= $msg ?></p>

<form method="POST">
    <input type="text" name="title" placeholder="Referendum title" required>
    <button type="submit">Create</button>
</form>

<a href="dashboard.php">⬅ Back</a>

</body>
</html>
