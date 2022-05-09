<?php 
namespace Database\Reads;

use \PDO;

class SignupValidation {

	private string $password;
	private string $confirm;

	public function __construct($password, $confirm) {

		$this->password = $password;
		$this->confirm = $confirm;

	}

	protected function signupValidation(): array {

		$err = [];

		// パスワードの同値チェック
		if ($this->password !== $this->confirm) {

			$err[] = '入力されたパスワードが一致しません。';

		} else {

			// IDが既に使用されているかチェック
			$pdo = dbConnect();

			$st = $pdo->query('SELECT user_name FROM user');
			$rows = $st->fetchAll(PDO::FETCH_ASSOC);

			if (array_search($_POST['user_name'], $rows) !== false) $err[] = '既に使用されているIDです。';

			$pdo = null;

		}

		return $err;
		
	}

}