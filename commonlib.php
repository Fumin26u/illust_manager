<?php
// タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');

// タイムアウト制限を無効化
ini_set("max_execution_time", 300);

// ダンプの簡略化
function v($arg) {
    return var_dump($arg);
}

// 文字列のエスケープ
function h($str) {
    return htmlspecialchars($str);
}