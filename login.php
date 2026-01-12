<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
require "config/db.php";
$msg = "";

if($_SERVER["REQUEST_METHOD"]==="POST"){

$email = trim($_POST['email']);
$pass  = $_POST['password'];

$stmt = $conn->prepare("SELECT * FROM voters WHERE voter_email=?");
$stmt->bind_param("s",$email);
$stmt->execute();
$res = $stmt->get_result();

if($res->num_rows===1){
    $row = $res->fetch_assoc();
    if(password_verify($pass,$row['passwordhash'])){
        $_SESSION['email']=$email;
        header("Location: voter/dashboard.php");
        exit();
    }else{
        $msg="Incorrect password.";
    }
}else{
    $msg="Email not found.";
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Voter Login</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700&display=swap" rel="stylesheet">

<style>
*{box-sizing:border-box;font-family:'Montserrat',sans-serif;}
body{
    margin:0;height:100vh;
    background:radial-gradient(circle at top,#4facfe,#00f2fe);
    display:flex;align-items:center;justify-content:center;
}
.bg-orbs::before,.bg-orbs::after{
    content:"";position:absolute;border-radius:50%;
    width:300px;height:300px;filter:blur(120px);opacity:.6;
}
.bg-orbs::before{background:#7f00ff;top:10%;left:15%;}
.bg-orbs::after{background:#00c6ff;bottom:10%;right:15%;}
.card{
    width:380px;padding:45px;border-radius:28px;
    background:rgba(255,255,255,.15);
    backdrop-filter:blur(20px);
    box-shadow:0 40px 80px rgba(0,0,0,.35);
    color:white;
}
h2{text-align:center;margin-bottom:28px;}
.field{margin:14px 0;}
.field input{
    width:100%;padding:15px;border:none;border-radius:14px;
    background:rgba(255,255,255,.18);color:white;
}
.field input:focus{outline:none;box-shadow:0 0 0 2px #00f2fe;}
.btn{
    width:100%;padding:16px;border:none;border-radius:18px;
    background:linear-gradient(135deg,#00f2fe,#4facfe);
    color:#003049;font-size:18px;margin-top:14px;
}
.msg{text-align:center;color:#e0f7ff;margin-top:12px;}
.link{text-align:center;margin-top:20px;}
.link a{color:#e0f7ff;text-decoration:none;font-weight:500;}
</style>
</head>
<body>

<div class="bg-orbs"></div>

<div class="card">
<h2>Voter Login</h2>

<form method="post">
<div class="field"><input type="email" name="email" placeholder="Email" required></div>
<div class="field"><input type="password" name="password" placeholder="Password" required></div>
<button class="btn" type="submit">Login</button>
</form>

<p class="msg"><?php echo htmlspecialchars($msg); ?></p>

<div class="link">
<a href="register.php">New user? Register</a>
</div>
</div>

</body>
</html>
