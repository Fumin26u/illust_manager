<?php
$home = '../';

require ($home . 'vendor/autoload.php');
require ($home . 'apiset.php');

// ダンプの簡略化
function v($arg) {
    return var_dump($arg);
}

// 文字列のエスケープ
function h($str) {
    return htmlspecialchars($str);
}

// URL引数idが空だった場合、初期表示にする
if (isset($_GET['id']) && $_GET['id'] == '') {
    header('../', true, 303);
    exit;
}

if (isset($_GET['id'])) {
    $likes = getTweets($_GET['id'], $_GET['st_time'], $_GET['ed_time']);
}

// ページタイトルの設定
$title = isset($_GET['id']) ? 'ID: ' . $_GET['id'] . 'のいいねツイート一覧' : 'いいねツイート取得システム';

// 現在時刻を生成
$t = new DateTime();
$today = $t->format('Y-m-d');
$n = new DateTime();
$now = $n->format('H:i');
$nowTime = $today . 'T' . $now;

// echo('<pre>');
// v($likes);
// echo('</pre>');
?>
<!DOCTYPE html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $title ?></title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<main>
    <h1>いいねした画像一覧</h1>
    <p>以下の入力欄に取得したいユーザーのTwitter IDと、いつまでの投稿を取得したいかを入力してください。(全て必須入力)</p>
    <small>数値のTwitter IDは、<a href="https://idtwi.com/" target="_blank" rel="noopener noreferrer">idtwi</a>などから検索できます。</small>
    <form action="<?= $_SERVER['PHP_SELF'] ?>" method="GET">
        <dl class="form_list">
            <div>
                <dt>期間指定</dt>
                <dd>
                    <input type="datetime-local" name="st_time" value="<?= isset($_GET['st_time']) ? h($_GET['st_time']) : '' ?>" required>から<br><input type="datetime-local" name="ed_time" value="<?= isset($_GET['ed_time']) ? h($_GET['ed_time']) : $nowTime ?>" required>まで
                </dd>
            </div>
            <div>
                <dt>Twitter ID (数値)</dt>
                <dd><input type="number" name="id" value="<?= isset($_GET['id']) ? h($_GET['id']) : '' ?>" required></dd>
            </div>
        </dl>      
        <input type="submit" value="送信">
    </form>
    <?php if (isset($likes)) { ?>
    <p><?= count($likes) ?>個のツイートが取得されました。</p>
    <div class="download_area">
        <p>[保存]ボタンを押すと、ダウンロードフォルダにZipファイルで保存されます。</p>
        <form action="./index.php?time=<?= h($_GET['time']) ?>&id=<?= h($_GET['id']) ?>" method="POST">
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
            </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
    <?php } ?>
</main>
<script src="<?= $home ?>script.js"></script>
</body>
