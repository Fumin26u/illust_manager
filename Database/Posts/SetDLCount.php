<?php
namespace Database\Posts;

use \PDO;

class SetDLCount {
    public function SetDLCount(int $count): void {

        $pdo = dbConnect();
    
        $st = $pdo->prepare('SELECT dl_count, images_count FROM user WHERE user_id = :user_id');
        $st->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $st->execute();
        $rows = $st->fetch(PDO::FETCH_ASSOC);

        // DLした回数
        $dl_count = is_null($rows['dl_count']) ? 0 : $rows['dl_count']; 
        // 保存した画像の総数
        $images_count = is_null($rows['images_count']) ? 0 : $rows['images_count'];
        // カウンタを増加
        $dl_count += 1;
        $images_count += $count;

        $pdo->beginTransaction();

        $sql = <<<SQL
UPDATE user SET 
dl_count = :dl_count,
images_count = :images_count
WHERE user_id = :user_id
SQL;
        $st = $pdo->prepare($sql);
        $st->bindValue(':dl_count', $dl_count, PDO::PARAM_INT);
        $st->bindValue(':images_count', $images_count, PDO::PARAM_INT);
        $st->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $st->execute();

        $pdo->commit();
    
        $pdo = null;
    }
}