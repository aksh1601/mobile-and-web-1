<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
require "config.php";
$msg="";

if($_SERVER["REQUEST_METHOD"]==="POST"){
    if($_POST['email']===ADMIN_EMAIL && $_POST['password']===ADMIN_PASSWORD){
        $_SESSION['admin']=true;
        header("Location: dashboard.php");
        exit();
    }else{
        $msg="Invalid admin credentials.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Admin Login</title>
<style>
body{
    background:radial-gradient(circle at top,#4facfe,#00f2fe);
    height:100vh;display:flex;align-items:center;justify-content:center;
    font-family:Montserrat;
}
.card{
    background:rgba(255,255,255,.15);
    backdrop-filter:blur(18px);
    padding:40px;border-radius:22px;color:white;width:360px;
}
input{width:100%;padding:12px;border:none;border-radius:12px;margin:10px 0;}
button{width:100%;padding:12px;border:none;border-radius:12px;background:#1f6feb;color:white;}
.msg{text-align:center;margin-top:10px;color:#ffe082;}
</style>
</head>
<body>

<div class="card">
<h2>Admin Login</h2>
<form method="post">
<input type="email" name="email" placeholder="Admin Email" required>
<input type="password" name="password" placeholder="Password" required>
<button>Login</button>
</form>
<p class="msg"><?php echo $msg; ?></p>
</div>

</body>
</html>
