<?php
session_start();
require "../config/db.php";

if(!isset($_SESSION['email'])){
    header("Location: ../login.php");
    exit();
}

$email = $_SESSION['email'];

$q = $conn->prepare("SELECT fullname FROM voters WHERE voter_email=?");
$q->bind_param("s",$email);
$q->execute();
$fullname = $q->get_result()->fetch_assoc()['fullname'] ?? '';

$msg = $_SESSION['vote_msg'] ?? "";
unset($_SESSION['vote_msg']);

if($_SERVER["REQUEST_METHOD"]==="POST"){

    $opt = intval($_POST['option_id']);
    $ref = intval($_POST['ref_id']);

    /* prevents double voting */
    $chk = $conn->prepare("SELECT 1 FROM voter_history WHERE voter_email=? AND referendum_id=?");
    $chk->bind_param("si",$email,$ref);
    $chk->execute();

    if($chk->get_result()->num_rows > 0){
        $_SESSION['vote_msg']="⚠ You have already voted.";
        header("Location: dashboard.php"); exit();
    }

    /* Save vote */
    $stmt = $conn->prepare("INSERT INTO voter_history(voter_email,referendum_id,voted_option_id) VALUES(?,?,?)");
    $stmt->bind_param("sii",$email,$ref,$opt);
    $stmt->execute();

    /* AUTO CLOSE LOGIC */
    $total = $conn->query("SELECT COUNT(*) AS t FROM voters")->fetch_assoc()['t'];
    $voted = $conn->query("SELECT COUNT(*) AS v FROM voter_history WHERE referendum_id=$ref")->fetch_assoc()['v'];

    if($total > 0 && ($voted / $total) == 0.5){
        $conn->query("UPDATE referendum SET status='closed', is_closed=1 WHERE referendum_id=$ref");
    }

    $_SESSION['vote_msg']="✅ Your vote was recorded.";
    header("Location: dashboard.php"); exit();
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Voter Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600&display=swap" rel="stylesheet">
<style>
body{background:radial-gradient(circle at top,#4facfe,#00f2fe);padding:40px;font-family:Montserrat;color:white;}
.card{background:rgba(255,255,255,.18);padding:25px;border-radius:22px;margin-bottom:22px;}
.vote-btn{margin-top:14px;padding:10px 20px;border:none;border-radius:14px;background:#1f6feb;color:white;}
.msg{text-align:center;font-weight:600;margin-bottom:20px;}
.badge{padding:4px 12px;border-radius:12px;font-size:13px;}
.open{background:#2ecc71;color:black;}
.closed{background:#e74c3c;}
</style>
</head>
<body>

<h2>Welcome <?php echo "$fullname ($email)"; ?></h2>
<a href="../logout.php" style="color:white;">Logout</a>

<?php if($msg) echo "<div class='msg'>$msg</div>"; ?>

<h3>🟢 Active Referendums</h3>

<?php
$active = $conn->query("SELECT * FROM referendum WHERE status='open'");
while($r=$active->fetch_assoc()){
    echo "<div class='card'><h3>{$r['text']} <span class='badge open'>OPEN</span></h3>";
    $opts = $conn->query("SELECT * FROM referendum_options WHERE referendum_id=".$r['referendum_id']);
    echo "<form method='post'>";
    while($o=$opts->fetch_assoc()){
        echo "<div><input type='radio' required name='option_id' value='{$o['opt_id']}'> {$o['option_text']}</div>";
    }
    echo "<input type='hidden' name='ref_id' value='{$r['referendum_id']}'>
          <button class='vote-btn'>Submit Vote</button></form></div>";
}
?>


<h3>🔴 Closed Referendums</h3>

<?php
$closed = $conn->query("SELECT * FROM referendum WHERE status='closed'");
while($r=$closed->fetch_assoc()):
?>
<div class="card">
<h3><?= $r['text'] ?> <span class="badge closed">CLOSED</span></h3>
<p>Voting for this referendum has ended.</p>
</div>
<?php endwhile; ?>

</body>
</html>
