<?php phpinfo(); ?>

<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
require 'config/db.php';

$res = $conn->query("SHOW TABLES");

if($res){
    echo "Database connected successfully!";
}else{
    echo "DB query failed.";
}
