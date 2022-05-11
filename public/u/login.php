<?php

use Database\Posts\Login;

$home ='../';
$login = true;

require_once($home . '../commonlib.php');
require_once($home . "../vendor/autoload.php");

$msg = [];
$err = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = new Login($_POST);
    $login_response = $login->login();

    $err += $login_response[0];
    $rows = $login_response[1];

    if (empty($err)) {
        // 上記エラーが無い場合、セッションにユーザ名とプレミアム会員判定を挿入
        session_start();

        $_SESSION['user_id'] = $rows['user_id'];
        $_SESSION['user_name'] = $rows['user_name'];
        $_SESSION['premium'] = $rows['premium'];

        // トップページにリダイレクト
        header('location: ../', true, 303);

    }
}

$cToken = bin2hex(random_bytes(32));
$_SESSION['cToken'] = $cToken;

$msg += $err;

$title = 'ログイン | TwimageDLer';
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
<h2>ログイン</h2>
<div class="description">
    <?php foreach ($msg as $m) { ?>
        <p><?= $m ?></p>
    <?php } ?>
</div>
<form action="<?= h($_SERVER['PHP_SELF']) ?>" method="POST">
    <dl class="form_list">
        <div>
            <dt>ユーザー名</dt>
            <dd>
                <input 
                    type="text" 
                    name="user_name" 
                    pattern="^([a-zA-Z0-9-]{6,})$" 
                    autocomplete="username" 
                    value="<?= isset($_POST['user_name']) ? h($_POST['user_name']) : '' ?>" 
                    required
                >
            </dd>
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
    </dl>
    <input type="hidden" name="cToken" value="<?= $cToken ?>">
    <input type="submit" value="ログイン">
</form>
</main>
<?php include_once($home . '../footer.php') ?>
</body>
</html>