<?php
session_start();

$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST['email']);
    $pass  = $_POST['password'];

    // Default Admin Credentials
    $ADMIN_EMAIL = "ec@referendum.gov.sr";
    $ADMIN_PASS  = "Shangrilavote&2025@";

    if ($email === $ADMIN_EMAIL && $pass === $ADMIN_PASS) {
        $_SESSION['admin'] = $email;
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
<title>Admin Login</title>
<style>
body{
    background:linear-gradient(135deg,#0f2027,#203a43,#2c5364);
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    font-family:Segoe UI;
}
.box{
    width:360px;
    padding:30px;
    background:rgba(255,255,255,0.1);
    border-radius:20px;
    color:white;
    box-shadow:0 0 25px rgba(0,0,0,0.4);
}
h2{
    text-align:center;
    margin-bottom:20px;
}
input{
    width:100%;
    padding:12px;
    margin:10px 0;
    border:none;
    border-radius:10px;
}
button{
    width:100%;
    padding:12px;
    border:none;
    border-radius:12px;
    background:#38bdf8;
    font-size:16px;
    font-weight:bold;
}
.error{
    color:#ff6b6b;
    text-align:center;
}
</style>
</head>
<body>

<div class="box">
<h2>🛡 Admin Login</h2>

<?php if($msg!="") echo "<p class='error'>$msg</p>"; ?>

<form method="POST">
    <input type="email" name="email" placeholder="Admin Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button>Login</button>
</form>
</div>

</body>
</html>
