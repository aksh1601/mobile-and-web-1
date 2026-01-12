<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
require "config/db.php";
$msg="";

if($_SERVER["REQUEST_METHOD"]==="POST"){

$email = trim($_POST['email']);
$name  = trim($_POST['fullname']);
$dob   = $_POST['dob'];
$rawPass = $_POST['password'];

if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
    $msg = "Invalid email format.";
}
elseif(strlen($rawPass) < 6){
    $msg = "Password must be at least 6 characters.";
}
else{

$pass = password_hash($rawPass,PASSWORD_DEFAULT);

$birth = new DateTime($dob);
$today = new DateTime('today');
$age   = $birth->diff($today)->y;

if($age < 18){
    $msg = "You must be at least 18 years old.";
}
else{

$e = $conn->prepare("SELECT voter_email FROM voters WHERE voter_email=?");
$e->bind_param("s",$email);
$e->execute();
if($e->get_result()->num_rows > 0){
    $msg = "Email already registered.";
}
else{

$mode = $_POST['scc_mode'] ?? "manual";
$scc  = "";

if($mode === "manual"){
    $scc = strtoupper(trim($_POST['scc_manual']));
    if($scc == "") $msg = "Enter SCC code.";
}
else{

    if(!isset($_FILES['qrfile']) || $_FILES['qrfile']['error'] != 0){
        $msg = "Upload QR code.";
    }
    elseif(!in_array($_FILES['qrfile']['type'], ['image/png','image/jpeg'])){
        $msg = "QR must be PNG or JPG.";
    }
    elseif($_FILES['qrfile']['size'] > 2*1024*1024){
        $msg = "QR image must be under 2MB.";
    }
    else{
        $tmp = $_FILES['qrfile']['tmp_name'];
        $cmd = "/opt/homebrew/bin/zbarimg --raw " . escapeshellarg($tmp);
        $scc = strtoupper(trim(shell_exec($cmd)));
        if($scc == "") $msg = "QR unreadable.";
    }
}

if($msg == ""){
    $chk = $conn->prepare("SELECT * FROM scc_code WHERE SCC=? AND used=0");
    $chk->bind_param("s",$scc);
    $chk->execute();
    $res = $chk->get_result();

    if($res->num_rows == 0){
        $msg = "Invalid or used SCC.";
    }
    else{
        $stmt = $conn->prepare(
            "INSERT INTO voters(voter_email,fullname,dob,passwordhash,scc)
             VALUES(?,?,?,?,?)"
        );
        $stmt->bind_param("sssss",$email,$name,$dob,$pass,$scc);
        if($stmt->execute()){
            $conn->query("UPDATE scc_code SET used=1 WHERE SCC='$scc'");
            $msg = "Registration successful!";
        }
    }
}
}
}
}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Registration</title>
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
    position:relative;width:420px;padding:45px;border-radius:28px;
    background:rgba(255,255,255,.15);backdrop-filter:blur(20px);
    box-shadow:0 40px 80px rgba(0,0,0,.35);color:white;
}
h2{text-align:center;margin-bottom:28px;font-weight:700;}
.field{margin:14px 0;}
.field input{
    width:100%;padding:15px;border:none;border-radius:14px;
    background:rgba(255,255,255,.18);color:white;font-size:15px;
}
.toggle{
    display:flex;border-radius:18px;background:rgba(255,255,255,.2);
    margin:22px 0;overflow:hidden;
}
.toggle button{
    flex:1;padding:12px;border:none;background:none;
    color:#e0f7ff;font-weight:600;cursor:pointer;
}
.toggle .active{
    background:linear-gradient(135deg,#00f2fe,#4facfe);color:#003049;
}
.btn{
    width:100%;padding:16px;border:none;border-radius:18px;
    background:linear-gradient(135deg,#00f2fe,#4facfe);
    color:#003049;font-size:18px;margin-top:14px;cursor:pointer;
}
.msg{text-align:center;margin-top:12px;}
.link-buttons{
    display:flex;gap:15px;margin-top:22px;
}
.user-btn,.admin-btn{
    flex:1;text-align:center;padding:12px 0;border-radius:16px;
    font-weight:600;text-decoration:none;
}
.user-btn{background:linear-gradient(135deg,#00f2fe,#4facfe);color:#003049;}
.admin-btn{background:linear-gradient(135deg,#ff9a00,#ff5e00);color:#2b0f00;}
</style>
<script>
function manual(){document.getElementById("mode").value="manual";
document.getElementById("manual").style.display="block";
document.getElementById("qr").style.display="none";}
function qr(){document.getElementById("mode").value="qr";
document.getElementById("qr").style.display="block";
document.getElementById("manual").style.display="none";}
</script>
</head>
<body>
<div class="bg-orbs"></div>
<div class="card">
<h2>Digital Voter ID</h2>
<form method="post" enctype="multipart/form-data">
<input type="hidden" name="scc_mode" id="mode" value="manual">
<div class="field"><input type="email" name="email" placeholder="Email" required></div>
<div class="field"><input type="text" name="fullname" placeholder="Full Name" required></div>
<div class="field"><input type="date" name="dob" required></div>
<div class="field"><input type="password" name="password" placeholder="Password" required></div>
<div class="toggle">
<button type="button" onclick="manual()" class="active">Manual SCC</button>
<button type="button" onclick="qr()">QR Upload</button>
</div>
<div id="manual" class="field"><input type="text" name="scc_manual" placeholder="Enter SCC Code"></div>
<div id="qr" class="field" style="display:none"><input type="file" name="qrfile"></div>
<button class="btn" type="submit">Register</button>
</form>
<p class="msg"><?php echo htmlspecialchars($msg); ?></p>
<div class="link-buttons">
<a href="login.php" class="user-btn">User Login</a>
<a href="admin/login.php" class="admin-btn">Admin Login</a>
</div>
</div>
</body>
</html>
