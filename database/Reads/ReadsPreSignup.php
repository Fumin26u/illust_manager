<?php
namespace Database\Reads;

use \PDO;
use PDOException;
use \DateTime;

class ReadsPreSignup {

	private array $get;
	private string $token;

	public function __construct($get) {
		
		$this->get = $get;

	}
	
	public function readsPreSignup(): string {

		$preEmail = '';

		if (isset($this->get['t']) && $this->get['t'] !== '') {

			$this->token = h($this->get['t']);

			$pdo = dbConnect();

			$st = $pdo->prepare('SELECT email, req_time FROM user_pre WHERE token = :token');
			$st->bindValue(':token', $this ->token, PDO::PARAM_STR);
			$st->execute();

			$res = $st->fetchAll(PDO::FETCH_ASSOC);
			$pdo = null;

			// 指定されたトークンがDBに無い場合false
			if (!empty($res)) {

				$date_db = $res[0]['req_time'];

				// 今の時刻とDBに登録されている時刻を比較する
				$d = new DateTime();
				$date = $d->modify('-1 Hour')->format('Y-m-d H:i:s');

				// 今の時刻 - 1時間がDBに登録されている時刻より早ければメアドを登録
				if ($date < $date_db) $preEmail = h($res[0]['email']);

			}

		}

		return $preEmail;

	}

}