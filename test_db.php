<?php
require 'config/db.php';

$stmt = $pdo->query("SELECT * FROM referendum");
foreach ($stmt as $row) {
    echo $row['referendum_id'] . " - " . $row['text'] . "<br>";
}
?>
