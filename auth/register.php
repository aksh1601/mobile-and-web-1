<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
require '../config/db.php';

$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email    = trim($_POST['email']);
    $fullname = trim($_POST['fullname']);
    $dob      = $_POST['dob'];
    $pass     = $_POST['password'];
    $mode     = $_POST['scc_mode'] ?? 'qr';
    $scc      = "";

    /* ===== AGE CHECK ===== */
    $today = new DateTime();
    $birthDate = new DateTime($dob);
    $age = $today->diff($birthDate)->y;

    if ($age < 18) {
        $msg = "You must be at least 18 years old to register.";
    }

    /* ===== GET SCC ===== */
    if ($msg === "") {

        if ($mode === "manual") {
            $scc = trim($_POST['scc_manual'] ?? "");
            if ($scc === "") $msg = "Please enter SCC code.";
        } else {

            if (!isset($_FILES['qrfile']) || $_FILES['qrfile']['error'] != 0) {
                $msg = "Please upload SCC QR Code.";
            } else {

                $uploadDir = __DIR__ . "/../uploads/";
                if (!is_dir($uploadDir)) mkdir($uploadDir,0777,true);

                $dest = $uploadDir . time() . ".png";
                move_uploaded_file($_FILES['qrfile']['tmp_name'], $dest);

                $cmd = "/opt/homebrew/bin/zbarimg --raw " . escapeshellarg($dest);
                $scc = trim(shell_exec($cmd));

                if ($scc === "") $msg = "QR code unreadable.";
            }
        }
    }

    if ($msg === "") {

        /* ===== VALIDATE SCC ===== */
        $q = $pdo->prepare("SELECT used FROM scc_code WHERE scc=?");
        $q->execute([$scc]);
        $row = $q->fetch();

        if (!$row) $msg = "Invalid SCC!";
        elseif ($row['used']) $msg = "SCC already used!";
        else {

            /* ===== CHECK EMAIL ===== */
            $q2 = $pdo->prepare("SELECT 1 FROM voters WHERE voter_email=?");
            $q2->execute([$email]);

            if ($q2->rowCount() > 0) $msg = "Email already registered!";
            else {

                $hash = password_hash($pass,PASSWORD_BCRYPT);

                $pdo->prepare("
                    INSERT INTO voters(voter_email,fullname,dob,passwordhash,scc)
                    VALUES (?,?,?,?,?)
                ")->execute([$email,$fullname,$dob,$hash,$scc]);

                $pdo->prepare("UPDATE scc_code SET used=1 WHERE scc=?")->execute([$scc]);

                $msg = "Registration successful!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Voter Registration</title>
<style>
body{
    margin:0;
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:linear-gradient(135deg,#1e3c72,#2a5298);
    font-family:Segoe UI,sans-serif;
}
.card{
    background:rgba(255,255,255,0.15);
    backdrop-filter:blur(12px);
    padding:35px 40px;
    border-radius:16px;
    width:390px;
    box-shadow:0 25px 45px rgba(0,0,0,.3);
    color:#fff;
}
h2{text-align:center;margin-bottom:20px;}
.input-group{margin-bottom:12px;}
.input-group label{font-size:13px;}
.input-group input{
    width:100%;
    padding:10px;
    border:none;
    border-radius:6px;
}
.toggle{
    text-align:center;
    margin:12px 0;
}
.toggle span{
    cursor:pointer;
    margin:0 10px;
    text-decoration:underline;
}
.hidden{display:none;}
.btn{
    width:100%;
    padding:12px;
    background:#00c6ff;
    border:none;
    border-radius:8px;
    color:#fff;
    font-weight:600;
}
.msg{text-align:center;margin-top:10px;font-weight:600;}
.error{color:#ff6b6b;}
.success{color:#7CFFB2;}
</style>

<script>
function setMode(m){
    document.getElementById('mode').value=m;
    document.getElementById('qr').classList.toggle('hidden',m!=='qr');
    document.getElementById('manual').classList.toggle('hidden',m!=='manual');
}
</script>
</head>

<body>

<div class="card">
<h2>🗳 Voter Registration</h2>

<form method="post" enctype="multipart/form-data">
<input type="hidden" name="scc_mode" id="mode" value="qr">

<div class="input-group"><label>Email</label><input name="email" required></div>
<div class="input-group"><label>Full Name</label><input name="fullname" required></div>
<div class="input-group"><label>DOB</label><input type="date" name="dob" required></div>
<div class="input-group"><label>Password</label><input type="password" name="password" required></div>

<div class="toggle">
<span onclick="setMode('qr')">Use QR For SCC</span> | 
<span onclick="setMode('manual')">Enter SCC</span>
</div>

<div id="qr">
<div class="input-group"><input type="file" name="qrfile" accept="image/*"></div>
</div>

<div id="manual" class="hidden">
<div class="input-group"><input name="scc_manual" placeholder="Enter SCC Code"></div>
</div>

<button class="btn">Register</button>
</form>

<?php if($msg): ?>
<p class="msg <?= str_contains($msg,'successful')?'success':'error' ?>"><?= $msg ?></p>
<?php endif; ?>

</div>
</body>
</html>
