<header>
<div class="logo_area">
	<a href="<?= $home ?>">
		<div class="title">TwimageDLer</div>
		<h1>"いいね"した画像の自動ダウンローダー</h1>
	</a>
</div>
<div class="signup_area">
    <?php if(!isset($_SESSION['user_name'])) { ?>
	<a href="<?= $home ?>u/login.php" id="login" class="c-btn">ログイン</a>
	<a href="<?= $home ?>u/p-signup.php" id="signup" class="c-btn">登録</a>
    <?php } else { ?>
        <span class="user_name"><?= h($_SESSION['user_name']) ?>さん</span>
        <a href="<?= $home ?>u/profile" class="c-btn">アカウント情報</a>
        <form action="<?= h($_SERVER['PHP_SELF']) ?>" method="GET" class="logout_form">
            <input type="submit" name="logout" id="logout" class="c-btn" value="ログアウト">
        </form>
    <?php } ?>
</div>
</header>