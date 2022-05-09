<?php
$home = '../';

use Database\Posts\PreSignup;

$msg = [];

require_once($home . '../commonlib.php');
require_once($home . "../vendor/autoload.php");

$url = 'https://imagedler.com/u/signup.php?t=';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $signup = new PreSignup($_POST);
    $msg[] = $signup->preSubmitAccount();
}

$title = 'ユーザー登録 | TwimageDLer';
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $title ?></title>
<link rel="stylesheet" href="signup.css">
<link rel="icon" href="<?= $home ?>favicon.png">
</head>
<body>
<?php include_once($home . '../header.php') ?>
<main>
<div class="message">
    <?php foreach ($msg as $m) { ?>
        <p><?= $m ?></p>
    <?php } ?>
</div>
<div class="description">
    <h2>ユーザー登録</h2>
    <p>[送信]ボタンを押すと、入力したメールアドレス宛に確認メールが届きます。</p>
    <p>送信してから1時間以内に、確認メールに添付されているリンクをクリックし、本登録画面へ進んでください。</p>
</div>
<form action="<?= h($_SERVER['PHP_SELF']) ?>" method="POST">
    <dl class="form_list">
        <div>
            <dt>メールアドレス</dt>
            <dd><input type="email" name="email" maxlength="80" required></dd>
        </div>
    </dl>
    <input type="hidden" name="token" value="<?= $cToken ?>">
    <input type="submit">
</form>
</main>
<?php include_once($home . '../footer.php') ?>
</body>
</html>