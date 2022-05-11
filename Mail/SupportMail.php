<?php
namespace Mail;

class SupportMail {

	private array $post;

	public function __construct($post) {
		$this->post = $post;
	}

	public function SendSupportMail(): bool {
		$email = isset($_POST['email']) && $_POST['email'] !== '' ? h($_POST['email']) : '記載なし';
        $mail_content = <<<EOM

＝＝＝＝＝＝＝＝＝＝お問い合わせを受信しました＝＝＝＝＝＝＝＝＝＝

以下の内容でお問い合わせを受信しました。

**フォーム内容**
{$_POST['type']}

**メールアドレス**
{$email}

**名前**
{$_POST['name']}

**お問い合わせ内容**
{$_POST['content']}

EOM;
            // メール送信の実行
            $to = 'tosufumiya0719@gmail.com';
            // $from = isset($_POST['email']) && $_POST['email'] !== '' ? h($_POST['email']) : h($_POST['name']);
            $from = 'no-reply@twimagedler.com';
    
            // メールヘッダ
            $header = 'From: ' . mb_encode_mimeheader('TwimageDLer', 'UTF-8') . '<' . $from . '>';
    
            // タイトル
            $title = '【お問い合わせを受信しました】| TwimageDLer';
    
            // 本文
            $message = '';
            $message .= brReplace(periodReplace($mail_content));
    
            // 送信＋判定
            $is_sent_mail = mb_send_mail($to, $title, $message, $header);
    
        // メールアドレスがセットされていた場合、そのメールアドレス宛に通知を送信
        if (isset($_POST['email']) && $_POST['email'] !== '') {
            $mail_content = <<<EOM

＝＝＝＝＝＝＝＝＝＝お問い合わせ完了通知＝＝＝＝＝＝＝＝＝＝

TwimageDLerのご利用ありがとうございます。
以下の内容でお問い合わせを承りました。

**フォーム内容**
{$_POST['type']}

**メールアドレス**
{$email}

**名前**
{$_POST['name']}

**お問い合わせ内容**
{$_POST['content']}

本メールは送信専用です。返信は受付できませんのでご了承ください。

EOM;
            // メール送信の実行
            $to = $email;
            $from = 'no-reply@twimagedler.com';
    
            // メールヘッダ
            $header = 'From: ' . mb_encode_mimeheader('TwimageDLer', 'UTF-8') . '<' . $from . '>';
    
            // タイトル
            $title = '【お問い合わせ完了通知】| TwimageDLer';
    
            // 本文
            $message = '';
            $message .= brReplace(periodReplace($mail_content));
    
            // 送信＋判定
            $is_sent_mail = mb_send_mail($to, $title, $message, $header);
        }

		return $is_sent_mail;
	}
}