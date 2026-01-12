<?php
require __DIR__ . "/config/db.php";
header("Content-Type: application/json");

$status = $_GET['status'] ?? '';

if ($status !== 'open' && $status !== 'closed') {
    echo json_encode(["error" => "Invalid status"]);
    exit();
}

$refs = $conn->query("SELECT * FROM referendum WHERE status='$status'");
$output = ["referendums" => []];

while ($r = $refs->fetch_assoc()) {

    $rid = $r['referendum_id'];

    $opts = $conn->query("
        SELECT o.opt_id AS id,
               o.option_text AS text,
               COUNT(v.voted_option_id) AS votes
        FROM referendum_options o
        LEFT JOIN voter_history v
          ON o.opt_id = v.voted_option_id
        WHERE o.referendum_id = $rid
        GROUP BY o.opt_id
    ");

    $options = [];
    while ($o = $opts->fetch_assoc()) {
        $options[] = $o;
    }

    $output["referendums"][] = [
        "referendum_id" => $rid,
        "status" => $r['status'],
        "title" => $r['text'],
        "options" => $options
    ];
}

echo json_encode($output, JSON_PRETTY_PRINT);
