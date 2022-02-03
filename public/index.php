<?php
$home = './';

require($home . '../apiset.php');

// URL引数idが空だった場合、初期表示にする
if (isset($_GET['id']) && $_GET['id'] == '') {
    header('./', true, 303);
    exit;
}

if (isset($_GET['id'])) $likes = getTweets($_GET['id'], $_GET['st_time'], $_GET['ed_time']);

// 保存ボタンが押された場合の処理
if (isset($_POST['download'])) {
    // ログインしている場合、期間指定の終了時刻をDBに登録
    if (isset($user_id, $user_name)) {
        try {
                
            $pdo = dbConnect();

            // 既にDB(used_timeテーブル)に値がセットされているかどうか
            $is_set_value = true;
            $st = $pdo->prepare('SELECT user_id FROM used_time WHERE user_id = :user_id AND sns_type = "T"');
            $st->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $st->execute();
            $row = $st->fetch(PDO::FETCH_ASSOC);
            if (empty($row)) $is_set_value = false;

            // URL引数から終了時刻を取得
            $ed_getTime = h($_GET['ed_time']);
            
            $pdo->beginTransaction();

            // DBに値が存在する場合、UPDATE
            if ($is_set_value) {
                $sql = <<<SQL
UPDATE used_time SET
latest_time = :latest_time
WHERE 
user_id = :user_id AND sns_type = 'T'
SQL;
            } else {
                $sql = <<<SQL
INSERT INTO used_time 
(user_id, latest_time, sns_type) 
VALUES 
(:user_id, :latest_time, 'T')
SQL;
            }
            $st = $pdo->prepare($sql);
            $st->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $st->bindValue(':latest_time', $ed_getTime, PDO::PARAM_STR);
            $st->execute();

            $pdo->commit();   

        } catch (PDOException $e) {
            echo 'データベース接続に失敗しました';
            if (DEBUG) {
                echo $e;
            }
        }
    }
    
    // リストから画像を抽出
    $images = [];
    foreach ($likes as $l) {
        foreach ($l['images'] as $i) {
            $images[] = $i;
        }
    }
    // 画像をダウンロード
    require_once($home . '../dlImages.php');
    dlImages($images);
}

// ログインしている場合、期間指定の開始時刻の読み込みを行う
if (isset($user_id, $user_name)) {
    try {

        $pdo = dbConnect();

        $st = $pdo->prepare('SELECT latest_time FROM used_time WHERE user_id = :user_id AND sns_type = "T"');
        $st->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $st->execute();
        $row = $st->fetch(PDO::FETCH_ASSOC);

        // ログインしているユーザIDのデータが存在する場合、時刻を設定
        if (!empty($row)) {
            $t = $row['latest_time'];
            $st_time = str_replace(' ', 'T', $t);
        }
        // URL引数st_timeが設定されている場合、その値に更新
        if (isset($_GET['st_time'])) $st_time = h($_GET['st_time']);

    } catch (PDOException $e) {
        echo 'データベース接続に失敗しました';
        if (DEBUG) {
            echo $e;
        }
    }
}

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
    <h2>検索フォーム</h2>
    <p>以下の入力欄に取得したいユーザーのTwitter ID(@以降の文字)と、いつまでの投稿を取得したいかを期間指定してください。(全て必須入力)</p>
    <div class="caution">
        <h3>注意事項</h3>
        <p>画像の数が多いほど、ダウンロードに時間がかかります(画像数x1秒が目安)。また、画像数が多すぎると、ダウンロードできない場合があります。</p>
        <p>期間指定で遡れる範囲は最大1カ月前までです。</p>
        <p>ご要望・質問等ございましたら、<a href="<?= $home ?>mail/">こちらのフォーム</a>よりお願いします。</p>
    </div>
    <?php // <small>数値のTwitter IDは、<a href="https://idtwi.com/" target="_blank" rel="noopener noreferrer">idtwi</a>などから検索できます。</small> ?>
    <form action="<?= h($_SERVER['PHP_SELF']) ?>" method="GET">
        <dl class="form_list">
            <div>
                <dt>Twitter ID</dt>
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
</main>
<?php include($home . '../footer.php') ?>
<script src="<?= $home ?>../script.js"></script>
</body>
</html>