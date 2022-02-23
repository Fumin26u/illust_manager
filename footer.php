<footer>
<div class="footer_container">
    <div class="footer_left">
        <a href="<?= $home ?>">
            <div class="title">TwimageDLer</div>
            <p>"いいね"した画像の自動ダウンローダー</p>
        </a>
    </div>
    <div class="footer_right">
        <ul class="link_area">
            <li>
                <a href="<?= isset($_GET['id']) ? $home . 'index.php?' . $action : $home ?>#caution">注意事項</a>
            </li>
            <li>
                <a href="<?= isset($_GET['id']) ? $home . 'index.php?' . $action : $home ?>#versions">更新履歴</a>
            </li>
            <li>
                <a href="<?= $home ?>t/terms_of_use.php">利用規約</a>
            </li>
            <li>
                <a href="<?= $home ?>t/privacy_policy.php">プライバシーポリシー</a>
            </li>
        </ul>
    </div>
</div>
</footer>
<p class="copyright">&copy;2022 TwimageDLer</p>