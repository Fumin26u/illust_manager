<?php 
use Values\DBPass;

require_once('SystemConfig.php');
function dbConnect() {
    $db = new DBPass;

    $pdo = new PDO($db->DSN, $db->DBUSER, $db->DBPASS);
    return $pdo;
}