<?php
session_start();
require "../config/db.php";

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

/* Summary */
$totalRef   = $conn->query("SELECT COUNT(*) c FROM referendum")->fetch_assoc()['c'];
$openRef    = $conn->query("SELECT COUNT(*) c FROM referendum WHERE status='open'")->fetch_assoc()['c'];
$closedRef  = $conn->query("SELECT COUNT(*) c FROM referendum WHERE status='closed'")->fetch_assoc()['c'];
$totalVotes = $conn->query("SELECT COUNT(*) c FROM voter_history")->fetch_assoc()['c'];

/* Referendum Actions */
if(isset($_POST['action'], $_POST['rid'])){
    $rid = intval($_POST['rid']);

    if($_POST['action']=='open')
        $conn->query("UPDATE referendum SET status='open', is_closed=0 WHERE referendum_id=$rid");

    if($_POST['action']=='close')
        $conn->query("UPDATE referendum SET status='closed', is_closed=1 WHERE referendum_id=$rid");

    $s = $conn->query("SELECT status FROM referendum WHERE referendum_id=$rid")->fetch_assoc();
    echo $s['status'];
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
body{margin:0;padding:30px;font-family:Montserrat;background:linear-gradient(#4facfe,#00f2fe);color:white;}
.topbar{display:flex;gap:15px;margin-bottom:25px;}
.btn{background:#1f6feb;padding:10px 16px;border-radius:12px;color:white;text-decoration:none}
.summary{display:grid;grid-template-columns:repeat(4,1fr);gap:15px;margin-bottom:30px;}
.sumcard{background:rgba(255,255,255,.18);padding:20px;border-radius:20px;text-align:center;font-size:18px}
.card{background:rgba(255,255,255,.18);padding:25px;border-radius:25px;margin-bottom:30px;display:grid;grid-template-columns:1fr 260px;gap:25px;}
.actions button{border:none;border-radius:10px;padding:8px 14px;color:white;margin-right:8px;cursor:pointer}
.edit{background:#0a2540}
.open{background:#2ecc71}
.close{background:#e74c3c}
canvas{max-width:240px;max-height:240px}
</style>
</head>
<body>

<h1>Admin Dashboard</h1>

<div class="topbar">
<a class="btn" href="voters.php">Registered Voters</a>
<a class="btn" href="create.php">Create Referendum</a>
<a class="btn" href="logout.php">Logout</a>
</div>

<div class="summary">
<div class="sumcard">Total<br><b><?= $totalRef ?></b></div>
<div class="sumcard">Open<br><b><?= $openRef ?></b></div>
<div class="sumcard">Closed<br><b><?= $closedRef ?></b></div>
<div class="sumcard">Votes Cast<br><b><?= $totalVotes ?></b></div>
</div>

<?php
$refs=$conn->query("SELECT * FROM referendum ORDER BY referendum_id DESC");
$ci=0;
while($r=$refs->fetch_assoc()):
$rid=$r['referendum_id'];
$res=$conn->query("
SELECT o.option_text,COUNT(v.voted_option_id) total
FROM referendum_options o
LEFT JOIN voter_history v ON o.opt_id=v.voted_option_id
WHERE o.referendum_id=$rid GROUP BY o.opt_id");

$labels=[];$data=[];
while($row=$res->fetch_assoc()){ $labels[]=$row['option_text']; $data[]=$row['total']; }
?>
<div class="card">
<div>
<h3><?= $r['text'] ?></h3>
<b>Status: <span id="status<?= $rid ?>"><?= strtoupper($r['status']) ?></span></b>

<div class="actions" style="margin-top:10px" id="btns<?= $rid ?>">
<?php if($r['status']=='closed'): ?>
<button class="edit" onclick="location.href='edit.php?id=<?= $rid ?>'">Edit</button>
<button class="open" onclick="actionRef(<?= $rid ?>,'open')">Open</button>
<?php else: ?>
<button class="close" onclick="actionRef(<?= $rid ?>,'close')">Close</button>
<?php endif; ?>
</div>

<?php foreach($labels as $i=>$l): ?>
<div><?= $l ?> : <?= $data[$i] ?></div>
<?php endforeach; ?>
</div>

<canvas id="c<?= $ci ?>"></canvas>
</div>

<script>
new Chart(document.getElementById("c<?= $ci ?>"),{
type:'pie',
data:{labels:<?= json_encode($labels) ?>,
datasets:[{data:<?= json_encode($data) ?>}]},
options:{responsive:false}
});
</script>
<?php $ci++; endwhile; ?>

<script>
function actionRef(id,action){
fetch('dashboard.php',{
method:'POST',
headers:{'Content-Type':'application/x-www-form-urlencoded'},
body:'rid='+id+'&action='+action
})
.then(r=>r.text())
.then(status=>{
document.getElementById('status'+id).innerText=status.toUpperCase();
let btns=document.getElementById('btns'+id);
if(status=='open')
 btns.innerHTML="<button class='close' onclick=\"actionRef("+id+",'close')\">Close</button>";
else
 btns.innerHTML="<button class='edit' onclick=\"location.href='edit.php?id="+id+"'\">Edit</button>\
 <button class='open' onclick=\"actionRef("+id+",'open')\">Open</button>";
});
}
</script>

</body>
</html>
