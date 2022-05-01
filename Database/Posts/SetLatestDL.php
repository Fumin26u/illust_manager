<?php
namespace Database\Posts;

use \PDO;

class SetLatestDL {
    public function SetLatestDL($isset_latest_dl, string $post_id, string $twi_id): void {

        $pdo = dbConnect();
        $pdo->beginTransaction();

        if ($isset_latest_dl !== '') {
            $sql = <<<SQL
UPDATE latest_dl SET
post_id = :post_id,
created_at = NOW()
WHERE 
user_id = :user_id AND twi_id = :twi_id
SQL;
        } else {
            $sql = <<<SQL
INSERT INTO latest_dl 
(user_id, post_id, twi_id, created_at)
VALUES 
(:user_id, :post_id, :twi_id, NOW())
SQL;    
        }
        $st = $pdo->prepare($sql);
        $st->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        // ツイートIDは数値だが、桁数が12以上なのでVARCHAR型で保存
        $st->bindValue(':post_id', $post_id, PDO::PARAM_STR);
        $st->bindValue(':twi_id', $twi_id, PDO::PARAM_STR);
        $st->execute();
        $pdo->commit();

        $pdo = null;
    }
}