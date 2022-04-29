<?php
namespace Database\Reads;

use \PDO;

class UsedTime {
    public function UsedTime(): bool {
        $pdo = dbConnect();

        $st = $pdo->prepare('SELECT user_id FROM used_time WHERE user_id = :user_id AND sns_type = "T"');
        $st->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $st->execute();
        $row = $st->fetch(PDO::FETCH_ASSOC);
        $is_set_value = empty($row) ? false : true;

        $pdo = null;
        return $is_set_value;
    }
}