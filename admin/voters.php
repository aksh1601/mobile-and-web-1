<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$result = $conn->query("SELECT voter_email, fullname, dob FROM voters WHERE voter_email != 'ec@referendum.gov.sr'");
?>
<!DOCTYPE html>
<html>
<head>
<title>Registered Voters</title>
<style>
body{
    background:linear-gradient(135deg,#0f2027,#203a43,#2c5364);
    font-family:Segoe UI;color:white;margin:0;padding:0;
}
.box{
    max-width:900px;margin:40px auto;padding:30px;
    background:rgba(255,255,255,0.1);
    border-radius:18px;
}
table{
    width:100%;border-collapse:collapse;margin-top:20px;
}
th{
    background:rgba(255,255,255,0.2);
    padding:12px;text-align:left;
}
td{
    padding:12px;border-bottom:1px solid rgba(255,255,255,0.2);
}
a{color:#7dd3fc;text-decoration:none;}
</style>
</head>
<body>

<div class="box">
<h2>👥 Registered Voters</h2>
<a href="dashboard.php">← Back to Dashboard</a>

<table>
<tr>
    <th>Email</th>
    <th>Full Name</th>
    <th>DOB</th>
</tr>

<?php while($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= htmlspecialchars($row['voter_email']) ?></td>
    <td><?= htmlspecialchars($row['fullname']) ?></td>
    <td><?= htmlspecialchars($row['dob']) ?></td>
</tr>
<?php endwhile; ?>

</table>
</div>

</body>
</html>
