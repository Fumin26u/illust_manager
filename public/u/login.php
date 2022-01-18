<?php
$home ='../';
$login = true;

require_once($home . '../commonlib.php');

$msg = [];

try {

    $pdo = dbConnect();

    $err = [];
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // user_nameからユーザ情報を取得
        $st = $pdo->prepare('SELECT user_name, password, premium FROM user WHERE user_name = :user_name AND is_auth = TRUE');
        $st->bindValue(':user_name', h($_POST['user_name']), PDO::PARAM_STR);
        // $st->bindValue(':password', $pass_hash, PDO::PARAM_STR);
        $st->execute();

        $rows = $st->fetch(PDO::FETCH_ASSOC);
        // 返された配列が空の場合、ユーザ名が存在しない
        if (empty($rows)) {
            $err[] = '入力されたユーザー名は存在しません。';
        // パスワードの照合
        } else if(!password_verify(h($_POST['password']), $rows['password'])) {
            $err[] = '入力されたパスワードが間違っています。';
        } else {
            // 上記エラーが無い場合、セッションにユーザ名とプレミアム会員判定を挿入
            session_start();

            $_SESSION['user_name'] = $rows['user_name'];
            $_SESSION['premium'] = $rows['premium'];

            // トップページにリダイレクト
            header('location: ../', true, 303);
        }
    }

    $msg += $err;

} catch (PDOException $e) {
    echo 'データベース接続に失敗しました。';
    if (DEBUG) echo $e;
}

$title = 'ログイン | TwimageDLer';
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $title ?></title>
<link rel="stylesheet" href="signup.css">
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
    <input type="submit" value="ログイン">
</form>
</main>
<?php include_once($home . '../footer.php') ?>
</body>
</html>