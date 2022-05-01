<?php
namespace Database\Reads;

use \PDO;

class LatestDL {
    public function LatestDL(string $twi_id): string {
        $pdo = dbConnect();

        // latest_dlテーブルの確認
        $st = $pdo->prepare('SELECT post_id FROM latest_dl WHERE user_id = :user_id AND twi_id = :twi_id');
        $st->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $st->bindValue(':twi_id', $twi_id, PDO::PARAM_STR);
        $st->execute();
        $row = $st->fetch(PDO::FETCH_ASSOC);

        $pdo = null;
        // latest_dlテーブルに前回保存した画像の投稿IDがある場合、変数に挿入
        return $row !== false ? $row['post_id'] : '';
    }
}