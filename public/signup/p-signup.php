<?php
$home ='../';
require_once($home . '../commonlib.php');

$msg = [];

session_start();
// csrf対策
$_SESSION['token'] = bin2hex(random_bytes(32));
$token = $_SESSION['token'];

try {
    $pdo = dbConnect();

    $err = [];
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // メールアドレスが入力されているかどうか
        if (empty($_POST['email'])) $err[] = 'メールアドレスを入力してください。';
    
        // メールアドレスが正しい形式かどうか
        $email = h($_POST['email']);
    
        if (!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email)) {
            $err[] = '不正なメールアドレスの形式です。';
        } else {
            // 正しくメールアドレスが入力されていた場合、DB接続
            $sql = "SELECT user_id FROM user WHERE email = :email";
            $st = $pdo->prepare($sql);
            $st->bindValue(':email', $email, PDO::PARAM_STR);
            $st->execute();
    
            $res = $st->fetchAll(PDO::FETCH_ASSOC);
            if (isset($res['id'])) $err[] = '既に使用されているメールアドレスです。';
        }
    
        // エラーが無い場合仮登録を行う
        $msg += $err;
        if (count($err) == 0) {
            $token = hash('sha256', uniqid(rand(), TRUE));
            v($token);
            $url = 'http://localhost/LikedImageDLer/public/signup/signup.php?t=' . $token;
    
            $sql = "INSERT INTO user_pre (token, email, req_time, is_submitted) VALUES (:token, :email, NOW(), 0)";
            $st = $pdo->prepare($sql);
            $st->bindValue(':token', $token, PDO::PARAM_STR);
            $st->bindValue(':email', $email, PDO::PARAM_STR);
            v($st->execute());
    
            $msg[] = '確認メールを送信しました。';   
        }
    }

} catch (PDOException $e) {
    echo 'データベース接続に失敗しました。';
    if (DEBUG) {
        echo $e;
    }
}

$title = 'ユーザー登録 | TwimageDLer';
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
<form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
    <dl class="form_list">
        <div>
            <dt>メールアドレス</dt>
            <dd><input type="email" name="email" maxlength="80"></dd>
        </div>
    </dl>
    <input type="submit">
</form>
</main>
<?php include_once($home . '../footer.php') ?>
</body>
</html>