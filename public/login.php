<?php
session_start();
require '../config/db.php';

$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST['email']);
    $pass  = $_POST['password'];

    $stmt = $conn->prepare("SELECT passwordhash FROM voters WHERE voter_email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $row = $res->fetch_assoc();

        if (password_verify($pass, $row['passwordhash'])) {

            session_regenerate_id(true);   // 🔥 THIS FIXES THE BUG
            $_SESSION['user'] = $email;

            header("Location: ../voter/dashboard.php");
            exit;
        } else {
            $msg = "Invalid password.";
        }
    } else {
        $msg = "Email not registered.";
    }
}
?>


<!DOCTYPE html>
<html>
<head>
<title>Voter Login</title>
<style>
body{
    background: linear-gradient(135deg,#0f2027,#203a43,#2c5364);
    height:100vh; display:flex; justify-content:center; align-items:center;
    font-family:Segoe UI;
}
.box{
    width:360px; padding:30px;
    background:rgba(255,255,255,0.1);
    border-radius:16px; color:white;
}
input{width:100%; padding:12px; margin:8px 0;border:none;border-radius:10px;}
button{width:100%;padding:12px;border:none;border-radius:12px;background:#38bdf8;color:black;font-weight:bold;}
a{color:#7dd3fc;text-decoration:none;}
</style>
</head>
<body>

<div class="box">
<h2>📦 Voter Login</h2>

<?php if($msg!="") echo "<p style='color:orange'>$msg</p>"; ?>

<form method="POST">
<input type="email" name="email" placeholder="Email" required>
<input type="password" name="password" placeholder="Password" required>
<button name="login">Login</button>
</form>

<p style="margin-top:10px;">New voter? <a href="../auth/register.php">Register here</a></p>
</div>

</body>
</html>
