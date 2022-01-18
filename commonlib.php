<?php
require_once('system-conf.php');

// タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');

// DB接続
function dbConnect() {
    $pdo = new PDO(DSN, DBUSER, DBPASS);
    return $pdo;
}

// ClickJacking対策
header('X-FRAME-OPTIONS: SAMEORIGIN');

// タイムアウト制限を無効化
ini_set("max_execution_time", 480);

// ダンプの簡略化
function v($arg) {
    return var_dump($arg);
}

// 文字列のエスケープ
function h($str) {
    return htmlspecialchars($str);
}

// メール関連
// 改行コードの置換
function brReplace($text) {
    $pattern = '/\r(?!\n)|(?<!\r)\n/';
    $text = preg_replace($pattern, "\r\n", $text);
    return $text;
}

// ピリオドの置換
function periodReplace($text) {
    $pattern = '/^\.\r$/m';
    $text = preg_replace($pattern, "..\r", $text);
    return $text;
}