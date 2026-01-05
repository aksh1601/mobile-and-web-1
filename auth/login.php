<?php
session_start();
require '../config/db.php';

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);
    $pass  = $_POST['password'];

    $stmt = $pdo->prepare("SELECT passwordhash FROM voters WHERE voter_email=?");
    $stmt->execute([$email]);
    $row = $stmt->fetch();

    if (!$row || !password_verify($pass, $row['passwordhash'])) {
        $msg = "Invalid email or password.";
    } else {
        $_SESSION['voter'] = $email;
        header("Location: ../voter/dashboard.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Voter Login</title>

<style>
body{
    margin:0;
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:linear-gradient(135deg,#141e30,#243b55);
    font-family:Segoe UI,sans-serif;
}

.card{
    width:380px;
    background:rgba(255,255,255,0.15);
    backdrop-filter:blur(14px);
    padding:40px;
    border-radius:18px;
    box-shadow:0 25px 45px rgba(0,0,0,.35);
    color:#fff;
}

.card h2{
    text-align:center;
    margin-bottom:20px;
    letter-spacing:1px;
}

.input-group{margin-bottom:14px;}
.input-group label{font-size:13px;opacity:.9;}
.input-group input{
    width:100%;
    padding:10px;
    border:none;
    border-radius:6px;
    outline:none;
}

.btn{
    width:100%;
    padding:12px;
    border:none;
    border-radius:8px;
    background:#00c6ff;
    color:#fff;
    font-weight:600;
    cursor:pointer;
    margin-top:10px;
}

.btn:hover{background:#009fe0;}

.msg{
    text-align:center;
    margin-top:12px;
    font-weight:600;
    color:#ff7675;
}

.footer{
    text-align:center;
    margin-top:14px;
    font-size:13px;
    opacity:.85;
}
.footer a{color:#00c6ff;text-decoration:none;}
.footer a:hover{text-decoration:underline;}
</style>
</head>

<body>

<div class="card">
<h2>🗳 Voter Login</h2>

<form method="post">
    <div class="input-group">
        <label>Email</label>
        <input type="email" name="email" autocomplete="username" required>
    </div>

    <div class="input-group">
        <label>Password</label>
        <input type="password" name="password" autocomplete="current-password" required>
    </div>

    <button class="btn">Login</button>
</form>

<?php if($msg): ?>
<p class="msg"><?= $msg ?></p>
<?php endif; ?>

<div class="footer">
    New voter? <a href="register.php">Register here</a>
</div>

</div>

</body>
</html>
