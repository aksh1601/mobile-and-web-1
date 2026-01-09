<?php
require '../config/db.php';
$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST['email']);
    $fullname = trim($_POST['fullname']);
    $dob = $_POST['dob'];
    $password = $_POST['password'];
    $scc = trim($_POST['scc_manual'] ?? "");

    if ($email == "" || $fullname == "" || $dob == "" || $password == "") {
        $msg = "All fields are required.";
    } else {

        // Age check
        $age = date_diff(date_create($dob), date_create('today'))->y;
        if ($age < 18) {
            $msg = "You must be 18 or older to register.";
        } else {

            // Email duplicate check
            $check = $conn->prepare("SELECT voter_email FROM voters WHERE voter_email=?");
            $check->bind_param("s", $email);
            $check->execute();
            $res = $check->get_result();

            if ($res->num_rows > 0) {
                $msg = "Email already registered.";
            } else {

                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO voters (voter_email, fullname, dob, passwordhash, ssc) VALUES (?,?,?,?,?)");
                $stmt->bind_param("sssss", $email, $fullname, $dob, $hash, $scc);

                if ($stmt->execute()) $msg = "success";
                else $msg = "Registration failed.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Voter Registration</title>
</head>
<body style="margin:0;font-family:sans-serif;
background:linear-gradient(135deg,#0f2027,#203a43,#2c5364);
display:flex;justify-content:center;align-items:center;height:100vh;">

<div style="width:460px;background:#425b66;border-radius:25px;
padding:35px;color:#fff;box-shadow:0 0 30px rgba(0,0,0,0.3);">

<h2 style="text-align:center;">📦 Voter Registration</h2>

<form method="post" enctype="multipart/form-data">

<label>Email</label>
<input type="email" name="email" required
style="width:100%;padding:12px;border-radius:12px;border:none;margin-bottom:12px;">

<label>Full Name</label>
<input type="text" name="fullname" required
style="width:100%;padding:12px;border-radius:12px;border:none;margin-bottom:12px;">

<label>Date of Birth</label>
<input type="date" name="dob" required
style="width:100%;padding:12px;border-radius:12px;border:none;margin-bottom:12px;">

<label>Password</label>
<input type="password" name="password" required
style="width:100%;padding:12px;border-radius:12px;border:none;margin-bottom:15px;">

<!-- Toggle -->
<div style="display:flex;background:#2f3f4b;border-radius:40px;overflow:hidden;margin:15px 0;">
    <button type="button" onclick="showManual()" id="btnManual"
    style="flex:1;padding:10px;border:none;background:#5ec8ff;color:#fff;font-weight:bold;">Manual SCC</button>
    <button type="button" onclick="showQR()" id="btnQR"
    style="flex:1;padding:10px;border:none;background:transparent;color:#fff;font-weight:bold;">Upload QR</button>
</div>

<div id="manualBox">
<input type="text" name="scc_manual" placeholder="Enter SCC code"
style="width:100%;padding:12px;border-radius:12px;border:none;margin-bottom:12px;">
</div>

<div id="qrBox" style="display:none;">
<input type="file" name="qrfile"
style="width:100%;padding:10px;border-radius:10px;border:none;margin-bottom:12px;background:#fff;">
</div>

<button type="submit"
style="width:100%;padding:14px;border:none;border-radius:14px;
background:#6bb6ff;color:#002;font-size:18px;">Register</button>
</form>

<?php
if($msg=="success"){
    echo "<p style='color:#6fff6f;text-align:center;margin-top:10px;'>Registration successful!</p>
          <a href='../public/login.php' style='color:#9ad6ff;display:block;text-align:center;'>Login</a>";
}
else if($msg!=""){
    echo "<p style='color:#ff7070;text-align:center;margin-top:10px;'>$msg</p>";
}
?>

</div>

<script>
function showManual(){
document.getElementById("manualBox").style.display="block";
document.getElementById("qrBox").style.display="none";
document.getElementById("btnManual").style.background="#5ec8ff";
document.getElementById("btnQR").style.background="transparent";
}
function showQR(){
document.getElementById("manualBox").style.display="none";
document.getElementById("qrBox").style.display="block";
document.getElementById("btnQR").style.background="#5ec8ff";
document.getElementById("btnManual").style.background="transparent";
}
</script>

</body>
</html>
