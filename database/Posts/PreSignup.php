<?php 
namespace Database\Posts;

use \PDO;
use PDOException;
use Mail\PreSignupMail;

class PreSignup extends PreSignupMail {

	private array $post;
	private string $email;

	public function __construct(array $post) {

		$this->post = $post;

	}

	private function postValidation(): array {

		$err = [];

		if (!isset($this->post['email'])) {

			$err[] = 'メールアドレスを入力してください。';

		} else {

			$this->email = h($this->post['email']);

		}
    
        // メールアドレスが正しい形式かどうか
        if (!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $this->email)) {

            $err[] = '不正なメールアドレスの形式です。';

        } else {

            // 正しくメールアドレスが入力されていた場合、DB接続
			$pdo = dbConnect();

            $sql = "SELECT user_id FROM user WHERE email = :email";
            $st = $pdo->prepare($sql);
            $st->bindValue(':email', $this->email, PDO::PARAM_STR);
            $st->execute();
    
            $res = $st->fetchAll(PDO::FETCH_ASSOC);
            if (isset($res['id'])) $err[] = '既に使用されているメールアドレスです。';

			$pdo = null;

        }

		return $err;

	}

	public function preSubmitAccount(): array {

		$is_submitted_db = false;
		
		$err = $this->postValidation();
		$msg = [];
		
		if (empty($err)) {
			
			$token = hash('sha256', uniqid(rand(), TRUE));
			$url = 'https://imagedler.com/u/signup.php?t=';
            $signup_url = $url . $token;
			
			try {
				
				$pdo = dbConnect();
				$pdo->beginTransaction();
				
				$st = $pdo->prepare('SELECT user_id FROM user_pre WHERE email = :email');
				$st->bindValue(':email', $this->email, PDO::PARAM_STR);
				$st->execute();
				
				$res = $st->fetch(PDO::FETCH_ASSOC);
				$is_preSubmitted = isset($res['user_id']) ? true : false;
	
				// 既に仮登録を行っているかつ、本登録が行われていないメアドで登録された場合、更新を行う
				if ($is_preSubmitted) {
					$sql = "UPDATE user_pre SET token = :token, req_time = NOW() WHERE email = :email";
				} else {
					$sql = "INSERT INTO user_pre (token, email, req_time, is_submitted) VALUES (:token, :email, NOW(), 0)";
				}
	
				$st = $pdo->prepare($sql);
				$st->bindValue(':token', $token, PDO::PARAM_STR);
				$st->bindValue(':email', $this->email, PDO::PARAM_STR);
				$st->execute();
		
				$pdo->commit();
				$is_submitted_db = true;

			} catch(PDOException $e) {

				if (DEBUG) echo $e;

				$err[] = '仮登録に失敗しました。お手数ですが、時間を置いて再度お試しいただけますようよろしくお願いします。';

			}

		}

		v($is_submitted_db);
		exit;

		if ($is_submitted_db) {

			$msg[] = PreSignupMail::SendPreSignupMail($this->mail, $signup_url);
			return $msg;

		} else {

			return $err;

		}	

	}
}