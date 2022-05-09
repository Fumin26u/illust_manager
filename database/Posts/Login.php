<?php
namespace Database\Posts;

use \PDO;
use PDOException;

class Login {

	private array $post;

	public function __construct($post) {

		$this->post = $post;

	}

	public function login(): array {

		$err = [];

		try {
			$pdo = dbConnect();
			$pdo->beginTransaction();
	
			// user_nameからユーザ情報を取得
			$st = $pdo->prepare('SELECT user_id, user_name, password, premium FROM user WHERE user_name = :user_name AND is_auth = TRUE');
			$st->bindValue(':user_name', h($this->post['user_name']), PDO::PARAM_STR);
			$st->execute();
	
			$rows = $st->fetch(PDO::FETCH_ASSOC);
			$pdo->commit();
			
			// 返された配列が空の場合、ユーザ名が存在しない
			if (empty($rows)) {
				$err[] = '入力されたユーザー名は存在しません。';
			// パスワードの照合
			} else if(!password_verify(h($this->post['password']), $rows['password'])) {
				$err[] = '入力されたパスワードが間違っています。';
			}
	
			$pdo = null;
		} catch(PDOException $e) {

			if (DEBUG) echo $e;
			$err[] = 'ユーザー認証に失敗しました。お手数ですが、時間を置いて再度お試しいただけますようよろしくお願いします。';

		}

		return $err;

	}

}