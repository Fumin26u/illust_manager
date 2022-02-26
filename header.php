<header>
<div class="header_container">
    <div class="header_left">
        <a href="<?= $home ?>">
            <div class="title">TwimageDLer</div>
            <h1>"いいね"した画像の自動ダウンローダー</h1>
        </a>
        <div class="link_area">
            <a href="<?= isset($_GET['id']) ? $home . 'index.php?' . $action : $home ?>#caution">注意事項</a>
            <a href="<?= isset($_GET['id']) ? $home . 'index.php?' . $action : $home ?>#versions">更新履歴</a>
        </div>
    </div>
    <div class="header_right">
        <?php if(!isset($_SESSION['user_name'])) { ?>
        <a href="<?= $home ?>u/login.php" id="login" class="c-btn">ログイン</a>
        <a href="<?= $home ?>u/p-signup.php" id="signup" class="c-btn">登録</a>
        <?php } else { ?>
            <span class="user_name"><?= h($_SESSION['user_name']) ?>さん</span>
            <!-- <a href="u/profile" class="c-btn">アカウント情報</a> -->
            <form action="<?= h($_SERVER['PHP_SELF']) ?>" method="GET" class="logout_form">
                <input type="submit" name="logout" id="logout" class="c-btn" value="ログアウト">
            </form>
        <?php } ?>
    </div>
</div>
</header>