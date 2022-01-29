<?php
$home = './';

// メタタグの整備
// ページタイトルの設定
$title = isset($_GET['id']) ? '@' . $_GET['id'] . 'のいいねツイート一覧 | TwimageDLer' : "TwimageDLer | \"いいね\"した画像の自動ダウンローダー";

// デスクリプション
$description = "Twitterで自分が「いいね」をした画像を一括ダウンロードできるツールです。ユーザー登録を行うことにより、更に手軽にダウンロードを行うことができます。";

$keywords = "Twitter,いいね,ダウンロード,画像,保存,一括";

$canonical = "https://imagedler.com/";
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $title ?></title>
<?php include_once $home . '../../gtag.inc'; ?>
<link rel="stylesheet" href="top.css">
<link rel="icon" href="<?= $home ?>favicon.png">
<link rel="canonical" href="<?= $canonical ?>">
<meta property="og:description" content="<?= $description ?>">
<meta name="keywords" content="<?= $keywords ?>">
<meta property="og:type" content="website">
<meta property="og:site_name" content="TwimageDLer">
<meta property="og:url" content="<?= $canonical ?>">
<meta property="og:title" content="<?= $title ?>">
<meta property="og:image" content="<?= $home ?>ogpimage.png">
</head>
<body>
<?php include_once($home . '../header.php') ?>
<main>
<h2>pixiv 検索フォーム</h2>
<p>以下の入力欄に取得したいユーザーのpixiv ID(数値)と、いつまでの投稿を取得したいかを期間指定してください。(全て必須入力)</p>
<div class="caution">
    <h3>注意事項</h3>
    <p>画像の数が多いほど、ダウンロードに時間がかかります(画像数x1秒が目安)。また、画像数が多すぎると、ダウンロードできない場合があります。</p>
    <p>期間指定で遡れる範囲は最大1カ月前までです。</p>
    <p>非公開のブックマークに登録されている画像は取得できません。</p>
</div>
<form action="<?= h($_SERVER['PHP_SELF']) ?>" method="GET">
    <dl class="form_list">
        <div>
            <dt>pixiv ID</dt>
            <dd><input type="text" name="id" value="<?= isset($_GET['id']) ? h($_GET['id']) : '' ?>" required></dd>
        </div>
        <div>
            <dt>期間指定</dt>
            <dd>
                <input 
                    type="datetime-local" 
                    name="st_time" 
                    value="<?= isset($st_time) ? $st_time : '' ?>" 
                    min="<?= $minTime ?>" 
                    required
                >
                から<br>
                <input 
                    type="datetime-local" 
                    name="ed_time" 
                    value="<?= isset($_GET['ed_time']) ? h($_GET['ed_time']) : $nowTime ?>" 
                    required
                >
                まで
            </dd>
        </div>
    </dl>      
    <input type="submit" value="送信">
</form>
<?php if (isset($likes)) { ?>
<h2>いいねした画像一覧</h2>
<p><?= count($likes) ?>個のツイートが取得されました。</p>
<div class="download_area">
    <p>[保存]ボタンを押すと、ダウンロードフォルダにZipファイルで保存されます。</p>
    <form action="./index.php?st_time=<?= h($_GET['st_time']) ?>&ed_time=<?= h($_GET['ed_time']) ?>&id=<?= h($_GET['id']) ?>" method="POST">
    <input type="submit" name="download" value="保存">
    </form>
</div>
<ul class="likes_list">
    <?php foreach($likes as $l) { ?>
        <li>
            <p class="user_name"><?= $l['user'] ?></p>
            <p><?= $l['post_time'] ?></p>
            <p class="tweet_content"><?= $l['text'] ?></p>
            <?php foreach($l['images'] as $i) { ?>
                <img src="<?= $i ?>" alt="">
            <?php } ?>
            <p>
                ツイート元リンク:
                <a href="<?= $l['url'] ?>" target="_blank" rel="noopener noreferrer"><?= $l['url'] ?></a>
            </p>
        </li>
    <?php } ?>
</ul>
<?php } ?>
</main>
<?php include($home . '../footer.php') ?>
<script src="<?= $home ?>../script.js"></script>
</body>
</html>