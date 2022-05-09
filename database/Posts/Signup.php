<?php
namespace Database\Posts;

use \PDO;
use PDOException;
use Database\Reads\SignupValidation;

class Signup extends SignupValidation {

	private array $post;

	public function __construct($post) {
		 
		$this->post = $post;

	}

	public function submitUser() {
		$err = [];

		$validation = new SignupValidation(h($this->post['password']), h($this->post['confirm']));
		$validation_err = $validation->signupValidation();

		if (!empty($validation_err)) {

			$err += $validation_err;

		} else {

			try {

				$pdo = dbConnect();

				$pdo->beginTransaction();
	
				$sql = <<<SQL
INSERT INTO user (
	user_name, password, email, premium, is_auth, created_at
) VALUES (
	:user_name, :password, :email, 'N', TRUE, NOW()
)
SQL;
				$st = $pdo->prepare($sql);
				$st->bindValue(':user_name', h($this->post['user_name']), PDO::PARAM_STR);    
				$st->bindValue(':password', password_hash(h($this->post['password']), PASSWORD_DEFAULT), PDO::PARAM_STR);
				$st->bindValue(':email', h($this->post['email']), PDO::PARAM_STR);
				$st->execute();
	
				// 仮ユーザテーブルの本登録かどうかをTRUEに変更
				$st = $pdo->prepare('UPDATE user_pre SET is_submitted = TRUE WHERE email = :email');
				$st->bindValue(':email', h($this->post['email']), PDO::PARAM_STR);
				$st->execute();
	
				$pdo->commit();

			} catch(PDOException $e) {

				if (DEBUG) echo $e;

				$err[] = 'ユーザー登録に失敗しました。お手数ですが、時間を置いて再度お試しいただけますようよろしくお願いします。';

			}			
		}
		return $err;
	}
}