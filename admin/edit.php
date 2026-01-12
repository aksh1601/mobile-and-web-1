<?php
session_start();
require "../config/db.php";

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

$id = intval($_GET['id'] ?? 0);
if($id <= 0){
    header("Location: dashboard.php");
    exit();
}

/* Fetch referendum */
$ref = $conn->query("SELECT * FROM referendum WHERE referendum_id=$id")->fetch_assoc();
if(!$ref){
    header("Location: dashboard.php");
    exit();
}

/* edit only if referendum is closed */
if($ref['status'] !== 'closed'){
    header("Location: dashboard.php");
    exit();
}

/* Save changes */
if($_SERVER["REQUEST_METHOD"] === "POST"){
    $text = trim($_POST['text']);

    $stmt = $conn->prepare("UPDATE referendum SET text=? WHERE referendum_id=?");
    $stmt->bind_param("si",$text,$id);
    $stmt->execute();

    /* Update options */
    if(isset($_POST['options'])){
        foreach($_POST['options'] as $opt_id=>$opt_text){
            $opt_text = trim($opt_text);
            $upd = $conn->prepare("UPDATE referendum_options SET option_text=? WHERE opt_id=?");
            $upd->bind_param("si",$opt_text,$opt_id);
            $upd->execute();
        }
    }

    /* Delete options */
    if(!empty($_POST['delete'])){
        foreach($_POST['delete'] as $del){
            $conn->query("DELETE FROM referendum_options WHERE opt_id=".intval($del));
        }
    }

    /* Add new options */
    if(!empty($_POST['new_options'])){
        foreach($_POST['new_options'] as $n){
            $n = trim($n);
            if($n!=""){
                $ins = $conn->prepare("INSERT INTO referendum_options(referendum_id,option_text) VALUES(?,?)");
                $ins->bind_param("is",$id,$n);
                $ins->execute();
            }
        }
    }

    header("Location: dashboard.php");
    exit();
}

/* Load options */
$opts = $conn->query("SELECT * FROM referendum_options WHERE referendum_id=$id");
?>
<!DOCTYPE html>
<html>
<head>
<title>Edit Referendum</title>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700&display=swap" rel="stylesheet">
<style>
body{
    background:radial-gradient(circle at top,#4facfe,#00f2fe);
    font-family:Montserrat;
    padding:40px;
    color:white;
}
.card{
    background:rgba(255,255,255,.15);
    padding:30px;
    border-radius:26px;
    max-width:800px;
    margin:auto;
}
input,textarea{
    width:100%;
    padding:14px;
    border-radius:16px;
    border:none;
    margin-bottom:14px;
}
.row{display:flex;align-items:center;gap:12px;}
.trash{
    background:#ff5f5f;
    border:none;
    color:white;
    padding:12px 14px;
    border-radius:14px;
}
.add{background:#32d26e;border:none;color:white;padding:10px 16px;border-radius:14px;}
.save{background:#3b6eea;border:none;color:white;padding:12px 20px;border-radius:16px;margin-top:15px;}
.back{
    display:inline-block;
    margin-bottom:20px;
    color:white;
    text-decoration:none;
    font-weight:600;
}
</style>
<script>
function addOption(){
    let box=document.getElementById("newOptions");
    let i=document.createElement("input");
    i.name="new_options[]";
    i.placeholder="New option";
    box.appendChild(i);
}
</script>
</head>
<body>

<a class="back" href="dashboard.php">← Back to Dashboard</a>

<div class="card">
<h2>Edit Referendum</h2>

<form method="post">
<textarea name="text"><?php echo htmlspecialchars($ref['text']); ?></textarea>

<h3>Edit Options</h3>

<?php while($o=$opts->fetch_assoc()): ?>
<div class="row">
    <input name="options[<?php echo $o['opt_id']; ?>]" value="<?php echo htmlspecialchars($o['option_text']); ?>">
    <button class="trash" type="submit" name="delete[]" value="<?php echo $o['opt_id']; ?>">🗑</button>
</div>
<?php endwhile; ?>

<h3>Add New Options</h3>
<div id="newOptions"></div>
<button type="button" class="add" onclick="addOption()">+ Add Option</button>

<br><br>
<button class="save" type="submit">Save Changes</button>
</form>
</div>

</body>
</html>
