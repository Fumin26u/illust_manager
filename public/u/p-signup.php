<?php
$home ='../';
require_once($home . '../commonlib.php');

$msg = [];

session_start();
// csrf対策
$_SESSION['token'] = bin2hex(random_bytes(32));
$cToken = $_SESSION['token'];

// $url = 'https://fuminsv.sakura.ne.jp/idtest/public/u/signup.php?t=';
// $url = 'https://imagedler.com/u/signup.php?t=';
$url = 'http://localhost/LikedImageDLer/public/u/signup.php?t=';

// dbに登録されたかどうか(メール送信判定)
$is_submitted_db = false;

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
            $signup_url = $url . $token;

            $is_preSubmitted = false;

            $pdo->beginTransaction();

            $st = $pdo->prepare('SELECT user_id FROM user_pre WHERE email = :email');
            $st->bindValue(':email', $email, PDO::PARAM_STR);
            $st->execute();
            
            $res = $st->fetch(PDO::FETCH_ASSOC);
            if (isset($res['user_id'])) $is_preSubmitted = true;

            // 既に仮登録を行っているかつ、本登録が行われていないメアドで登録された場合、更新を行う
            if ($is_preSubmitted) {
                $sql = "UPDATE user_pre SET token = :token, req_time = NOW() WHERE email = :email";
            } else {
                $sql = "INSERT INTO user_pre (token, email, req_time, is_submitted) VALUES (:token, :email, NOW(), 0)";
            }

            $st = $pdo->prepare($sql);
            $st->bindValue(':token', $token, PDO::PARAM_STR);
            $st->bindValue(':email', $email, PDO::PARAM_STR);
            $st->execute();
    
            $pdo->commit();
            $is_submitted_db = true;
        }
    }

} catch (PDOException $e) {
    echo 'データベース接続に失敗しました。';
    if (DEBUG) {
        echo $e;
    }
}

// 仮登録が行われた場合、メール送信
if ($is_submitted_db) {
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
        $msg[] = '仮登録通知を送信しました。';
    } else {
        $msg[] = 'メール送信に失敗しました。お手数ですが、時間を置いて再度お試しいただけますようよろしくお願いします。';
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
    <input type="hidden" name="token" value="<?= $cToken ?>">
    <input type="submit">
</form>
</main>
<?php include_once($home . '../footer.php') ?>
</body>
</html>