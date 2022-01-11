<?php
$home = './';

require($home. '../commonlib.php');
require($home . '../apiset.php');

// URL引数idが空だった場合、初期表示にする
if (isset($_GET['id']) && $_GET['id'] == '') {
    header('./', true, 303);
    exit;
}

if (isset($_GET['id'])) {
    $likes = getTweets($_GET['id'], $_GET['st_time'], $_GET['ed_time']);
}

// ページタイトルの設定
$title = isset($_GET['id']) ? '@' . $_GET['id'] . 'のいいねツイート一覧 | TwimageDLer' : "TwimageDLer | \"いいね\"した画像の自動ダウンローダー";

// 現在時刻を生成
$t = new DateTime();
$today = $t->format('Y-m-d');

$n = new DateTime();
$now = $n->format('H:i');
$nowTime = $today . 'T' . $now;

// 遡れる最低年月日
$m = new DateTime();
$minDay = $m->modify("-1 months")->format('Y-m-d'); 
$minTime = $minDay . 'T' . $now;

// echo('<pre>');
// v($likes);
// echo('</pre>');
?>
<!DOCTYPE html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $title ?></title>
<link rel="stylesheet" href="top.css">
</head>
<body>
<?php include_once($home . '../header.php') ?>
<main>
    <h2>検索フォーム</h2>
    <p>以下の入力欄に取得したいユーザーのTwitter ID(@以降の文字)と、いつまでの投稿を取得したいかを期間指定してください。(全て必須入力)</p>
    <div class="caution">
        <h3>注意事項</h3>
        <p>画像の数が多いほど、ダウンロードに時間がかかります(画像数x1秒が目安)。また、画像数が多すぎると、ダウンロードできない場合があります。</p>
        <p>期間指定で遡れる範囲は最大1カ月前までです。</p>
    </div>
    <?php // <small>数値のTwitter IDは、<a href="https://idtwi.com/" target="_blank" rel="noopener noreferrer">idtwi</a>などから検索できます。</small> ?>
    <form action="<?= $_SERVER['PHP_SELF'] ?>" method="GET">
        <dl class="form_list">
            <div>
                <dt>Twitter ID</dt>
                <dd><input type="text" name="id" value="<?= isset($_GET['id']) ? h($_GET['id']) : '' ?>" required></dd>
            </div>
            <div>
                <dt>期間指定</dt>
                <dd>
                    <input type="datetime-local" name="st_time" value="<?= isset($_GET['st_time']) ? h($_GET['st_time']) : '' ?>" min="<?= $minTime ?>" required>から<br><input type="datetime-local" name="ed_time" value="<?= isset($_GET['ed_time']) ? h($_GET['ed_time']) : $nowTime ?>" required>まで
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
    <table>
        <tbody>
        <tr>
            <th>ツイート時間</th>
            <th>ツイート者</th>
            <th>ツイート内容</th>
            <th>ツイート画像</th>
        </tr>
        <?php foreach($likes as $l) { ?>
            <tr>
            <td><?= $l['post_time'] ?></td>
            <td><?= $l['user'] ?></td>
            <td><?= $l['text'] ?></td>
            <td>
                <?php foreach($l['images'] as $i) { ?>
                <img src="<?= $i ?>" alt="">
                <?php } ?>
                <p>
                    ツイート元リンク:
                    <a href="<?= $l['url'] ?>" target="_blank" rel="noopener noreferrer"><?= $l['url'] ?></a>
                </p>
            </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
    <?php } ?>
</main>
<?php include($home . '../footer.php') ?>
<script src="<?= $home ?>../script.js"></script>
</body>
