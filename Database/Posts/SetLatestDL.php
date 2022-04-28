<?php
namespace database\Posts;

use \PDO;

class SetLatestDL {
    public function SetLatestDL($st, string $post_id): void {

        $pdo = dbConnect();
        $pdo->beginTransaction();

        if ($st !== false) {
            $sql = <<<SQL
UPDATE latest_dl SET
post_id = :post_id,
created_at = NOW()
WHERE 
user_id = :user_id AND sns_type = 'T'
SQL;
        } else {
            $sql = <<<SQL
INSERT INTO latest_dl 
(user_id, post_id, sns_type, created_at)
VALUES 
(:user_id, :post_id, 'T', NOW())
SQL;    
        }
        $st = $pdo->prepare($sql);
        $st->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        // ツイートIDは数値だが、桁数が12以上なのでVARCHAR型で保存
        $st->bindValue(':post_id', h($post_id), PDO::PARAM_STR);
        $st->execute();
        $pdo->commit();

        $pdo = null;
    }
}