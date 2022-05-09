<?php

use Database\Posts\Signup;
use Database\Reads\ReadsPreSignup;

$home ='../';
require_once($home . '../commonlib.php');
require_once($home . "../vendor/autoload.php");

$msg = [];

// 適切なアクセスかどうか(TRUE = 適切、 FALSE = 不適切)
$is_proper_access = true;
// 本登録されたかどうか
$is_submitted = false;

// データがPOSTされた際の処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($_SESSION['cToken'] !== $_POST['cToken']) {

        $msg[] = '不正なアクセスが行われました';

    } else {

        $submitUser = new Signup($_POST);
        $signup_err = $submitUser->submitUser();
    
        if (empty($signup_err)) {
            $is_submitted = true;
        } else {
            $msg += $signup_err;
        }

    }

}

// 仮登録データの読み込み
$preSignup = new ReadsPreSignup($_GET);
$email = $preSignup->readsPreSignup();
if ($email === '') {
    $is_proper_access = false;
} else {
    $token = h($_GET['t']);
}

$cToken = bin2hex(random_bytes(32));
$_SESSION['cToken'] = $cToken;

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
<h2>ユーザー登録</h2>
<?php if (!$is_proper_access) { ?>
<div class="description improper">
    <p>この文章が表示されている場合、不適切なアクセスまたはメールアドレスに送付したリンクの有効期限切れとなります。</p>
    <p>お手数ですが、再度確認メールの送付手続きを行って下さい。</p>
    <a href="p-signup.php" class="c-btn">メール送付</a>
</div>
<?php } else if (!$is_submitted) { ?>
<div class="description"> 
    <p>以下の項目に内容を入力し、[登録]ボタンを押すとアカウントが登録されます。</p>
    <?php foreach ($msg as $m) { ?>
        <p><?= $m ?></p>
    <?php } ?>
</div>
<form action="<?= h($_SERVER['PHP_SELF']) . '?t=' . $token ?>" method="POST">
    <dl class="form_list">
        <div>
            <dt>メールアドレス</dt>
            <dd>
                <p><?= $email ?></p>
                <input type="hidden" name="email" value="<?= $email ?>">
            </dd>
        </div>
        <div>
            <dt>ユーザー名</dt>
            <dd><input type="text" name="user_name" pattern="^([a-zA-Z0-9-]{6,})$" autocomplete="username" value="<?= isset($_POST['user_name']) ? h($_POST['user_name']) : '' ?>" required></dd>
            <small style="display: block;">※半角英数字(小文字・大文字可)6文字以上で入力</small>
        </div>
        <div>
            <dt>パスワード</dt>
            <dd>
                <input 
                    type="password" 
                    name="password" 
                    id="password" 
                    autocomplete="current-password" 
                    minlength="6" 
                    required
                >
                <!-- <button type="button" id="display">表示</button> -->
            </dd>
        </div>
        <div>
            <dt>パスワード(再入力)</dt>
            <dd>
                <input 
                    type="password" 
                    name="confirm" 
                    autocomplete="current-password" 
                    minlength="6" 
                    required
                >
                <!-- <button type="button">表示</button> -->
            </dd>
        </div>
    </dl>
    <input type="hidden" name="cToken" value="<?= $cToken ?>">
    <input type="submit" value="登録">
</form>
<?php } else { ?>
<div class="description submitted">
    <h2>登録しました</h2>
    <p>ユーザー登録ありがとうございます。以下からログインしてください。</p>
    <a href="login.php" class="c-btn">ログイン</a>
</div>
<?php } ?>
</main>
<?php include_once($home . '../footer.php') ?>
</body>
<script>
{
    var d = document.getElementById('display');
    d.onmousedown = displayPass;
    d.onmouseup = unDisplay;
}
</script>
</html>