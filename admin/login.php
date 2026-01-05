<?php
session_start();

$ADMIN_EMAIL = "ec@referendum.gov.sr";
$ADMIN_PASS  = "Shangrilavote&2025@";

$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if ($_POST['email'] === $ADMIN_EMAIL && $_POST['password'] === $ADMIN_PASS) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: dashboard.php");
        exit;
    } else {
        $msg = "Invalid admin credentials!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Election Commission Login</title>
<style>
body{
    height:100vh;display:flex;justify-content:center;align-items:center;
    background:linear-gradient(135deg,#5f2c82,#49a09d);font-family:Segoe UI;
}
.card{background:#fff;padding:40px;border-radius:14px;width:380px}
.card h2{text-align:center;color:#5f2c82}
input{width:100%;padding:10px;margin-bottom:12px}
button{width:100%;padding:12px;background:#5f2c82;color:#fff;border:none}
.msg{text-align:center;color:red}
</style>
</head>
<body>
<div class="card">
<h2>🏛 Admin Login</h2>
<form method="post">
<input name="email" type="email" placeholder="Admin Email" required>
<input name="password" type="password" placeholder="Password" required>
<button>Login</button>
</form>
<?php if($msg) echo "<p class='msg'>$msg</p>"; ?>
</div>
</body>
</html>
