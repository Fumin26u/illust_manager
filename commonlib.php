<?php
require_once($home . '../system-conf.php');

// タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');

// DB接続
function dbConnect() {
    $pdo = new PDO(DSN, DBUSER, DBPASS);
    return $pdo;
}

// ClickJacking対策
header('X-FRAME-OPTIONS: SAMEORIGIN');

// タイムアウト制限時間
ini_set("max_execution_time", 600);

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

// ログインページ以外の場合、SESSIONを開始
if (!isset($login)) {
    session_start();

    // SESSIONにユーザ名があれば、グローバル変数に挿入
    if (isset($_SESSION['user_name'])) {
        global $user_id, $user_name, $is_premium;
        $user_id = h($_SESSION['user_id']);
        $user_name = h($_SESSION['user_name']);
        $is_premium = h($_SESSION['premium']);
    }
}

// ログアウト処理
if(isset($_GET['logout'])) {
    $_SESSION = [];
    header('location: ' . $home, true, 303);
}