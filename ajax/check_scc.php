<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
require '../config/db.php';

$scc = trim($_GET['scc'] ?? '');

if ($scc == "") {
    echo "<span style='color:red'>SCC required</span>";
    exit;
}

$stmt = $conn->prepare("SELECT used FROM scc_code WHERE scc=?");
if(!$stmt){
    echo "DB error";
    exit;
}
$stmt->bind_param("s",$scc);
$stmt->execute();
$res = $stmt->get_result();

if($res->num_rows == 0){
    echo "<span style='color:red'>Invalid SCC</span>";
} else {
    $row = $res->fetch_assoc();
    if($row['used'] == 1)
        echo "<span style='color:red'>SCC already used</span>";
    else
        echo "<span style='color:green'>SCC valid ✔</span>";
}
