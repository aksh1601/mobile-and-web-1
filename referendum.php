<?php
ini_set('display_errors',1);
error_reporting(E_ALL);

require "config/db.php";
header("Content-Type: application/json");

if (!isset($_GET['id'])) {
    echo json_encode(["error" => "Referendum ID missing"]);
    exit;
}

$id = intval($_GET['id']);

$rq = $conn->query("SELECT * FROM referendum WHERE referendum_id = $id");

if ($rq->num_rows == 0) {
    echo json_encode(["error" => "Referendum not found"]);
    exit;
}

$r = $rq->fetch_assoc();

$optionsQ = $conn->query("
    SELECT o.opt_id AS id, o.option_text AS text,
           COUNT(v.voted_option_id) AS votes
    FROM referendum_options o
    LEFT JOIN voter_history v
        ON o.opt_id = v.voted_option_id
    WHERE o.referendum_id = $id
    GROUP BY o.opt_id
");

$options = [];
while ($row = $optionsQ->fetch_assoc()) {
    $options[] = [
        "id" => $row['id'],
        "text" => $row['text'],
        "votes" => (int)$row['votes']
    ];
}

$response = [
    "referendum_id" => $r['referendum_id'],
    "status" => $r['status'],
    "title" => $r['text'],
    "options" => $options
];

echo json_encode($response, JSON_PRETTY_PRINT);
