<?php
require '../config/db.php';

$refs = $conn->query("SELECT * FROM referendum");
?>

<h2>Referendum Results</h2>

<?php while($r = $refs->fetch_assoc()): ?>
<h3><?= $r['text'] ?></h3>

<?php
$res = $conn->prepare("
SELECT ro.option_text, COUNT(vh.option_id) as votes
FROM referendum_options ro
LEFT JOIN voter_history vh ON ro.opt_id = vh.option_id
WHERE ro.referendum_id=?
GROUP BY ro.opt_id
");
$res->bind_param("i",$r['referendum_id']);
$res->execute();
$data = $res->get_result();

while($row = $data->fetch_assoc()){
    echo $row['option_text']." : ".$row['votes']." votes<br>";
}
?>
<hr>
<?php endwhile; ?>
