<?php
session_start();
require "../config/db.php";

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

$msg="";

if($_SERVER["REQUEST_METHOD"]=="POST"){
    $text = trim($_POST['question']);
    $options = $_POST['options'];

    if($text=="" || count($options)<2){
        $msg="Please enter a question and at least two options.";
    }else{
        $stmt = $conn->prepare("INSERT INTO referendum(text) VALUES(?)");
        $stmt->bind_param("s",$text);
        $stmt->execute();
        $rid = $stmt->insert_id;

        $optStmt = $conn->prepare("INSERT INTO referendum_options(referendum_id, option_text) VALUES(?,?)");
        foreach($options as $o){
            if(trim($o)!=""){
                $optStmt->bind_param("is",$rid,$o);
                $optStmt->execute();
            }
        }
        $msg="Referendum created successfully!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Create Referendum</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700&display=swap" rel="stylesheet">
<style>
body{
    background:radial-gradient(circle at top,#4facfe,#00f2fe);
    font-family:Montserrat,sans-serif;
    padding:50px;
    color:white;
}
.card{
    max-width:650px;
    margin:auto;
    background:rgba(255,255,255,.18);
    backdrop-filter:blur(20px);
    border-radius:30px;
    padding:35px;
    box-shadow:0 40px 80px rgba(0,0,0,.35);
}
input,textarea{
    width:100%;
    padding:14px;
    border:none;
    border-radius:14px;
    margin-top:10px;
    background:rgba(255,255,255,.2);
    color:white;
}
button{
    width:100%;
    margin-top:20px;
    padding:16px;
    border:none;
    border-radius:16px;
    background:linear-gradient(135deg,#00f2fe,#4facfe);
    color:#003049;
    font-size:18px;
    cursor:pointer;
}
.msg{text-align:center;margin-top:15px;}
a{color:white;text-decoration:none;}
</style>

<script>
function addOption(){
    const box = document.getElementById("options");
    const input = document.createElement("input");
    input.type="text";
    input.name="options[]";
    input.placeholder="Option text";
    box.appendChild(input);
}
</script>
</head>
<body>

<div class="card">
<h2>Create Referendum</h2>
<form method="post">
<textarea name="question" placeholder="Enter referendum question" required></textarea>

<div id="options">
<input type="text" name="options[]" placeholder="Option 1" required>
<input type="text" name="options[]" placeholder="Option 2" required>
</div>

<button type="button" onclick="addOption()">+ Add Option</button>
<button type="submit">Create Referendum</button>
</form>

<p class="msg"><?php echo $msg; ?></p>
<a href="dashboard.php">← Back to Dashboard</a>
</div>

</body>
</html>
