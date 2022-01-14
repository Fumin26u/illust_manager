<?php
$home ='../';
require_once($home . '../commonlib.php');

$msg = [];

session_start();
// csrf対策
$_SESSION['token'] = bin2hex(random_bytes(32));
$cToken = $_SESSION['token'];

// 適切なアクセスかどうか(TRUE = 適切、 FALSE = 不適切)
$is_proper_access = true;

try {

    // URL引数tが設定されていないまたは空の場合false
    if (!isset($_GET['t']) || $_GET['t'] == '') {
        $is_proper_access = false;
    } else {
        $pdo = dbConnect();

        $token = h($_GET['t']);

        // トークンの整合
        $st = $pdo->prepare('SELECT email, req_time FROM user_pre WHERE token = :token');
        $st->bindValue(':token', $token, PDO::PARAM_STR);
        $st->execute();

        $res = $st->fetchAll(PDO::FETCH_ASSOC);
        // 指定されたトークンがDBに無い場合false
        if (empty($res)) {
            $is_proper_access = false;
        } else {
            $date_db = $res[0]['req_time'];

            // 今の時刻とDBに登録されている時刻を比較する
            $d = new DateTime();
            $date = $d->modify('-1 Hour')->format('Y-m-d H:i:s');

            // 今の時刻 - 1時間がDBに登録されている時刻より遅い場合false
            if ($date > $date_db) {
                $is_proper_access = false;
            } else {
                $email = h($res[0]['email']);
            }
        }
    }

} catch (PDOException $e) {
    echo 'データベースの接続に失敗しました。';
    if (DEBUG) {
        echo $e;
    }
}

// 仮置き
$is_proper_access = true;
$email = h($res[0]['email']);

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
<h2>ユーザー登録</h2>
<?php if (!$is_proper_access) { ?>
<div class="description improper">
    <p>この文章が表示されている場合、不適切なアクセスまたはメールアドレスに送付したリンクの有効期限切れとなります。</p>
    <p>お手数ですが、再度確認メールの送付手続きを行って下さい。</p>
    <a href="p-signup.php" class="c-btn">メール送付</a>
</div>
<?php } else { ?>
<div class="description"> 
    <p>以下の項目に内容を入力し、[登録]ボタンを押すとアカウントが登録されます。</p>
</div>
<form action="<?= $_SERVER['PHP_SELF'] ?>" action="POST">
    <dl class="form_list">
        <div>
            <dt>メールアドレス</dt>
            <dd>
                <p><?= $email ?></p>
                <input type="hidden" name="email" value="<?= $email ?>">
            </dd>
        </div>
        <div>
            <dt>ユーザーID</dt>
            <dd><input type="text" name="id" pattern="^([a-zA-Z0-9-]{6,})$" autocomplete="username" required></dd>
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
                <button type="button" id="display">表示</button>
            </dd>
        </div>
        <!-- <div>
            <dt>パスワード(再入力)</dt>
            <dd><input type="password" name="confirm" autocomplete="current-password" minlength="6" oninput="checkPass(this)" required><button type="button">表示</button></dd>
        </div> -->
    </dl>
    <input type="submit" value="登録">
</form>
<?php } ?>
</main>
<?php include_once($home . '../footer.php') ?>
</body>
<script>
{
    function checkPass(confirm) {
		var pass1 = password.value;
		var pass2 = confirm.value;

		if (pass1 != pass2) {
			confirm.setCustomValidity("入力値が一致しません。");
		} else {
			confirm.setCustomValidity('');
		}
	}

    function displayPass() {
        var p = document.getElementById('password');
        p.type = 'text';
    }

    function unDisplay() {
        var p = document.getElementById('password');
        p.type = 'password';
    }

    var d = document.getElementById('display');
    d.onmousedown = displayPass;
    d.onmouseup = unDisplay;
}
</script>
</html>