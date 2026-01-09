<?php
session_start();
require '../config/db.php';

if(!isset($_SESSION['user'])){
    header("Location: ../public/login.php");
    exit;
}

$email = $_SESSION['user'];
$name = $conn->query("SELECT fullname FROM voters WHERE voter_email='$email'")
             ->fetch_assoc()['fullname'];

$refs = $conn->query("SELECT * FROM referendum ORDER BY referendum_id DESC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Voter Dashboard</title>
<style>
body{background:#0f2027;color:white;font-family:Segoe UI;}
.box{max-width:750px;margin:40px auto;background:#203a43;padding:25px;border-radius:16px;}
.card{background:#2c5364;padding:20px;border-radius:12px;margin-bottom:20px;}
button{padding:10px 20px;border:none;border-radius:10px;background:#38bdf8;}
</style>
</head>

<body>
<div class="box">
<h2>Welcome, <?= $name ?></h2>
<a href="../public/logout.php">Logout</a><hr>

<?php while($r = $refs->fetch_assoc()): ?>
<div class="card">
<h3><?= $r['text'] ?></h3>

<?php
$rid = $r['referendum_id'];

$check = $conn->query("SELECT * FROM voter_history 
                       WHERE voter_email='$email' AND referendum_id=$rid");

if($check->num_rows>0){
    echo "<p style='color:orange'>You already voted.</p>";
    continue;
}

$opts = $conn->query("SELECT * FROM referendum_options WHERE referendum_id=$rid");

if($opts->num_rows==0){
    echo "<p style='color:yellow'>No options added for this referendum.</p>";
    continue;
}
?>

<form method="POST" action="vote.php">
<?php while($o=$opts->fetch_assoc()): ?>
<label>
<input type="radio" name="option_id" value="<?= $o['opt_id'] ?>" required>
<?= $o['option_text'] ?>
</label><br>
<?php endwhile; ?>

<input type="hidden" name="referendum_id" value="<?= $rid ?>">
<br><button>Submit Vote</button>
</form>
</div>
<?php endwhile; ?>
</div>
</body>
</html>
