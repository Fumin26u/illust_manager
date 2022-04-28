<?php 
require_once('connectDB.php');
// DB接続
function dbConnect() {
    $pdo = new PDO(DSN, DBUSER, DBPASS);
    return $pdo;
}