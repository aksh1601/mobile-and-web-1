<?php
session_start();
require "../config/db.php";

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

$result = $conn->query("SELECT voter_email, fullname, dob FROM voters ORDER BY fullname");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Registered Voters</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700&display=swap" rel="stylesheet">

<style>
*{box-sizing:border-box;font-family:'Montserrat',sans-serif;}
body{
    margin:0;
    min-height:100vh;
    background:radial-gradient(circle at top,#4facfe,#00f2fe);
    color:white;
    padding:50px;
}
.container{
    max-width:1100px;
    margin:auto;
}
.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}
.header a{
    color:white;
    text-decoration:none;
    font-weight:600;
}
.card{
    background:rgba(255,255,255,.15);
    backdrop-filter:blur(20px);
    border-radius:26px;
    padding:25px 30px;
    box-shadow:0 30px 70px rgba(0,0,0,.25);
    animation:fade .7s ease;
}
@keyframes fade{
    from{opacity:0;transform:translateY(10px);}
    to{opacity:1;transform:translateY(0);}
}
table{
    width:100%;
    border-collapse:collapse;
    margin-top:10px;
}
th,td{
    padding:14px 12px;
    border-bottom:1px solid rgba(255,255,255,.25);
}
tr:hover{background:rgba(255,255,255,.08);}
.empty{
    text-align:center;
    padding:40px;
    font-size:18px;
    opacity:.8;
}
</style>
</head>

<body>
<div class="container">

<div class="header">
    <h2>Registered Voters</h2>
    <a href="dashboard.php">← Back to Dashboard</a>
</div>

<div class="card">
<table>
<tr>
    <th>Email</th>
    <th>Full Name</th>
    <th>Date of Birth</th>
</tr>

<?php
if($result->num_rows==0){
    echo "<tr><td colspan='3' class='empty'>No voters registered yet.</td></tr>";
}else{
    while($row = $result->fetch_assoc()){
        echo "<tr>
                <td>{$row['voter_email']}</td>
                <td>{$row['fullname']}</td>
                <td>{$row['dob']}</td>
              </tr>";
    }
}
?>

</table>
</div>
</div>
</body>
</html>

