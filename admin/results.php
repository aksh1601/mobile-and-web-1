<?php
session_start();
require '../config/db.php';

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit;
}

// Fetch all referendums
$refs = $conn->query("SELECT * FROM referendum");
?>
<!DOCTYPE html>
<html>
<head>
<title>Voting Results</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body{
    background:linear-gradient(135deg,#0f2027,#203a43,#2c5364);
    font-family:Segoe UI;color:white;margin:0;padding:40px;
}
.box{
    max-width:900px;margin:auto;
    background:rgba(255,255,255,0.08);
    border-radius:20px;padding:30px;
}
canvas{max-width:500px;margin:20px auto;display:block;}
a{color:#7dd3fc;text-decoration:none;}
</style>
</head>
<body>

<div class="box">
<h2>📊 Voting Results</h2>
<a href="dashboard.php">⬅ Back to Dashboard</a>
<hr>

<?php while($ref = $refs->fetch_assoc()): ?>

<h3><?= $ref['text'] ?></h3>

<?php
$data = [];
$labels = [];

$stmt = $conn->prepare("
SELECT o.option_text, COUNT(v.option_id) AS total 
FROM referendum_options o
LEFT JOIN voter_history v ON o.opt_id = v.option_id
WHERE o.referendum_id=?
GROUP BY o.opt_id
");
$stmt->bind_param("i", $ref['referendum_id']);
$stmt->execute();
$res = $stmt->get_result();

while($row = $res->fetch_assoc()){
    $labels[] = $row['option_text'];
    $data[] = $row['total'];
}

$chartId = "chart".$ref['referendum_id'];
?>

<canvas id="<?= $chartId ?>"></canvas>

<script>
new Chart(document.getElementById('<?= $chartId ?>'), {
    type: 'pie',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [{
            data: <?= json_encode($data) ?>,
            backgroundColor: ['#38bdf8','#22c55e','#f97316','#ef4444','#a855f7']
        }]
    }
});
</script>

<hr>

<?php endwhile; ?>

</div>
</body>
</html>
