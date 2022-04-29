<?php
namespace Database\Reads;

use \PDO;

class LatestDL {
    public function LatestDL(): string {
        $pdo = dbConnect();

        // latest_dlテーブルの確認
        $st = $pdo->prepare('SELECT post_id FROM latest_dl WHERE user_id = :user_id AND sns_type = "T"');
        $st->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $st->execute();
        $row = $st->fetch(PDO::FETCH_ASSOC);

        $pdo = null;
        // latest_dlテーブルに前回保存した画像の投稿IDがある場合、変数に挿入
        return !empty($row) || $row['post_id'] !== '' ? $row['post_id'] : false;
    }
}