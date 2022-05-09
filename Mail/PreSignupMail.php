<?php
namespace Mail;

class PreSignupMail {

	public function SendPreSignupMail(string $email, string $signup_url): string {

		$mail_content = <<<EOM

＝＝＝＝＝＝＝＝＝＝仮登録通知＝＝＝＝＝＝＝＝＝＝

TwimageDLerのご利用ありがとうございます。
1時間以内に、以下のリンクから本登録をお願いします。

$signup_url

本メールは送信専用です。返信は受付できませんのでご了承ください。

EOM;
		
		// メール送信の実行
		$to = $email;
		$from = 'no-reply@twimagedler.com';
	
		// メールヘッダ
		$header = 'From: ' . mb_encode_mimeheader('TwimageDLer', 'UTF-8') . '<' . $from . '>';
	
		// タイトル
		$title = '【仮登録通知】| TwimageDLer';
	
		// 本文
		$message = '';
		$message .= brReplace(periodReplace($mail_content));
	
		// 送信＋判定
		$is_sent_mail = mb_send_mail($to, $title, $message, $header);
	
		if ($is_sent_mail) {
			$msg = '仮登録通知を送信しました。';
		} else {
			$msg = 'メール送信に失敗しました。お手数ですが、時間を置いて再度お試しいただけますようよろしくお願いします。';
		}

		return $msg;

	}

}