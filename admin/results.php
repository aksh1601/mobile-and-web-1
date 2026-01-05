<?php
require '../config/db.php';

$refs = $pdo->query("SELECT * FROM referendum")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
<title>Referendum Results</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body{
    font-family: Arial, sans-serif;
    text-align:center;
}
.chart-box{
    width:400px;
    margin:50px auto;
}
.vote-counts{
    font-size:18px;
    margin-top:10px;
}
</style>
</head>

<body>

<h1>Referendum Results</h1>

<?php foreach ($refs as $r): ?>

<?php
$stmt = $pdo->prepare("
SELECT o.option_text, COUNT(v.voted_option_id) AS votes
FROM referendum_options o
LEFT JOIN voter_history v 
  ON o.opt_id = v.voted_option_id 
 AND o.referendum_id = v.referendum_id
WHERE o.referendum_id = ?
GROUP BY o.opt_id
");
$stmt->execute([$r['referendum_id']]);

$labels = [];
$votes  = [];

while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
    $labels[] = $row['option_text'];
    $votes[]  = $row['votes'];
}
?>

<div class="chart-box">
    <h2><?= htmlspecialchars($r['text']) ?> (<?= $r['status'] ?>)</h2>

    <canvas id="chart<?= $r['referendum_id'] ?>"></canvas>

    <!-- TEXT COUNTS -->
    <div class="vote-counts">
        <?php for($i=0;$i<count($labels);$i++): ?>
            <?= htmlspecialchars($labels[$i]) ?> : <?= $votes[$i] ?> votes<br>
        <?php endfor; ?>
    </div>
</div>

<script>
new Chart(document.getElementById("chart<?= $r['referendum_id'] ?>"), {
    type: 'pie',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [{
            data: <?= json_encode($votes) ?>,
            backgroundColor: ['#3498db','#e74c3c','#2ecc71','#f1c40f']
        }]
    },
    options:{
        responsive:true,
        plugins:{
            legend:{ position:'top' }
        }
    }
});
</script>

<?php endforeach; ?>

</body>
</html>
