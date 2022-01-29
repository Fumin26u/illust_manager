<?php
$home = "./";
// require_once('commonlib.php');

// Pythonを呼び出す
// コマンドライン入力
$cmd = "python ./refimgs.py";
exec($cmd, $result);
var_dump($result);