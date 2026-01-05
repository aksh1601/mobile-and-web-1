<?php
session_start();
require '../config/db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Create Referendum</title>
<style>
body{font-family:Arial;text-align:center;}
input{width:320px;padding:6px;margin:5px;}
</style>
</head>
<body>

<h1>Create Referendum</h1>

<form method="post" action="create_referendum_process.php">

    <input type="text" name="title" placeholder="Referendum Title" required><br>

    <input type="text" name="options[]" placeholder="Option 1" required><br>
    <input type="text" name="options[]" placeholder="Option 2" required><br>
    <input type="text" name="options[]" placeholder="Option 3 (optional)"><br>

    <!-- NEW AUTO CLOSE FIELD -->
    <label>Close At (optional):</label><br>
    <input type="datetime-local" name="closes_at"><br><br>

    <button type="submit">Create Referendum</button>
</form>

<br>
<a href="dashboard.php">Back to Dashboard</a>

</body>
</html>
