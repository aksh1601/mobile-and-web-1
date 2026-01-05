<?php
session_start();
require '../config/db.php';

$msg="";

if($_SERVER['REQUEST_METHOD']=="POST"){
    $email=$_POST['email'];
    $pass=$_POST['password'];

    $q=$pdo->prepare("SELECT * FROM voters WHERE voter_email=?");
    $q->execute([$email]);
    $u=$q->fetch();

    if($u && password_verify($pass,$u['passwordhash'])){
        $_SESSION['voter']=$email;
        header("Location: ../voter/dashboard.php");
        exit;
    } else $msg="Invalid login credentials.";
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
    background:linear-gradient(135deg,#0f2027,#203a43,#2c5364);
    font-family:Segoe UI,sans-serif;
}
.card{
    background:rgba(255,255,255,0.15);
    padding:40px;
    border-radius:16px;
    width:360px;
    box-shadow:0 25px 45px rgba(0,0,0,.35);
    color:#fff;
}
input{width:100%;padding:10px;margin:8px 0;border-radius:6px;border:none;}
button{
    width:100%;padding:12px;background:#00c6ff;border:none;
    border-radius:8px;color:#fff;font-weight:600;
}
.error{text-align:center;color:#ff6b6b;margin-top:10px;}
</style>
</head>
<body>

<div class="card">
<h2 style="text-align:center;">Voter Login</h2>
<form method="post">
<input name="email" placeholder="Email" required>
<input type="password" name="password" placeholder="Password" required>
<button>Login</button>
</form>
<?php if($msg): ?><p class="error"><?= $msg ?></p><?php endif; ?>
</div>

</body>
</html>
