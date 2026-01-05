<?php
require '../config/db.php';

header("Content-Type: application/json");

$status = $_GET['status'] ?? 'open';

/* Validate status */
if (!in_array($status, ['open','closed'])) {
    http_response_code(400);
    echo json_encode(["error"=>"Invalid status"]);
    exit;
}

/* Fetch referendums */
$refs = $pdo->prepare("
    SELECT referendum_id, text, status 
    FROM referendum 
    WHERE status = ?
");
$refs->execute([$status]);

$result = [];

while ($r = $refs->fetch(PDO::FETCH_ASSOC)) {

    /* Fetch options + votes */
    $opts = $pdo->prepare("
        SELECT o.opt_id, o.option_text,
               COUNT(v.voted_option_id) AS votes
        FROM referendum_options o
        LEFT JOIN voter_history v 
          ON o.opt_id = v.voted_option_id
         AND v.referendum_id = o.referendum_id
        WHERE o.referendum_id = ?
        GROUP BY o.opt_id
    ");
    $opts->execute([$r['referendum_id']]);

    $options = [];
    foreach ($opts as $o) {
        $options[] = [
            "option_id" => $o['opt_id'],
            "text"      => $o['option_text'],
            "votes"     => (int)$o['votes']
        ];
    }

    $result[] = [
        "referendum_id" => $r['referendum_id'],
        "question"      => $r['text'],
        "status"        => $r['status'],
        "options"       => $options
    ];
}

echo json_encode($result, JSON_PRETTY_PRINT);
