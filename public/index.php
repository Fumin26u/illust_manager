<?php
$home = './';

use Controllers\ImgList;
use Controllers\QueueMaking;
use Database\Reads\LatestDL;
// declare(strict_types = 1);

$msg = [];
$err = [];

// require($home . '../apiset.php');
require_once($home . '../commonlib.php');
require_once($home . "../vendor/autoload.php");
require('versions.php');

// ログインしているかどうか
$is_login = isset($_SESSION['user_id']) ? true : false;

// 送信ボタンが押された場合の処理
if (isset($_GET['id'])) {
    // $_GETのバリデーション処理
    $q = new QueueMaking();
    $query = $q->makeGetTweetsQueue($_GET);

    // バリデーションでエラーが発生した場合APIを呼ばず処理を行う
    if (isset($query[0]) && $query[0] === true) {
        $err += $query[1];
        v($err);
    } else {
        // 「前回保存した画像以降を取得」にチェックが入っている場合、DBからツイートIDを取得
        $d = new LatestDL();
        $latest_dl = $d->LatestDL();
    
        // ツイート一覧を取得
        $l = new ImgList();
        $likes = $l->imgList($query, $latest_dl);
        // echo '<pre>';
        // v($likes);
        // echo '</pre>';
    }
}

// 保存ボタンが押された場合の処理
if (isset($_POST['download'])) {
    // ログインしている場合、期間指定の終了時刻をDBに登録
    if (isset($user_id, $user_name)) {
        try {
                
            $pdo = dbConnect();

            // used_timeテーブルの確認
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

            // latest_dlテーブルの確認
            // 既にDB(used_timeテーブル)に値がセットされているかどうか
            $is_set_value = true;
            $st = $pdo->prepare('SELECT user_id FROM latest_dl WHERE user_id = :user_id AND sns_type = "T"');
            $st->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $st->execute();
            $row = $st->fetch(PDO::FETCH_ASSOC);
            if (empty($row)) $is_set_value = false;

            // DBに値が存在する場合、UPDATE
            if ($is_set_value) {
                $sql = <<<SQL
UPDATE latest_dl SET
post_id = :post_id,
created_at = NOW()
WHERE 
user_id = :user_id AND sns_type = 'T'
SQL;
            } else {
                $sql = <<<SQL
INSERT INTO latest_dl 
(user_id, post_id, sns_type, created_at)
VALUES 
(:user_id, :post_id, 'T', NOW())
SQL;    
            }
            $st = $pdo->prepare($sql);
            $st->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            // ツイートIDは数値だが、桁数が12以上なのでVARCHAR型で保存
            $st->bindValue(':post_id', h($likes[0]['post_id']), PDO::PARAM_STR);
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

    // ログインしている場合、DL回数と保存した画像の総数を更新
    if (isset($user_id, $user_name)) {
        try {

            $pdo = dbConnect();
            $imc = count($images);

            $st = $pdo->prepare('SELECT dl_count, images_count FROM user WHERE user_id = :user_id');
            $st->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $st->execute();
            $rows = $st->fetch(PDO::FETCH_ASSOC);
            v($rows);
            // DLした回数
            $dl_count = is_null($rows['dl_count']) ? 0 : $rows['dl_count']; 
            // 保存した画像の総数
            $images_count = is_null($rows['images_count']) ? 0 : $rows['images_count'];
            // カウンタを増加
            $dl_count += 1;
            $images_count += $imc;

            $pdo->beginTransaction();
            $sql = <<<SQL
UPDATE user SET 
dl_count = :dl_count,
images_count = :images_count
WHERE user_id = :user_id
SQL;
            $st = $pdo->prepare($sql);
            $st->bindValue(':dl_count', $dl_count, PDO::PARAM_INT);
            $st->bindValue(':images_count', $images_count, PDO::PARAM_INT);
            $st->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $st->execute();

            $pdo->commit();

        } catch (PDOException $e) {
            echo 'データベース接続に失敗しました';
            if (DEBUG) {
                echo $e;
            }
        }
    }
}

// 現在時刻を生成
$t = new DateTime();
$today = $t->format('Y-m-d');

$n = new DateTime();
$now = $n->format('H:i');
$nowTime = $today . 'T' . $now;

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
<?php include_once $home . '../gtag.inc'; ?>
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
    <p>以下の入力欄に取得したいユーザーのTwitter ID(@以降)・取得するツイート数を入力してください。(<em>*</em>は必須入力)</p>
    <small>
        使用する前に、<a href="<?= $home ?>t/terms_of_use.php">利用規約</a>と<a href="<?= $home ?>t/privacy_policy.php">プライバシーポリシー</a>の確認をお願いします。<br>
        [送信]ボタンを押した(またはユーザー登録を行った)時点で、利用規約とプライバシーポリシーに同意したとみなします。
    </small>
    <?php // <small>数値のTwitter IDは、<a href="https://idtwi.com/" target="_blank" rel="noopener noreferrer">idtwi</a>などから検索できます。</small> ?>
    <form action="<?= h($_SERVER['PHP_SELF']) ?>" method="GET">
        <dl class="form_list">
            <div>
                <dt>Twitter ID<em>*</em></dt>
                <dd>
                    <input type="text" name="id" value="<?= isset($_GET['id']) ? h($_GET['id']) : '' ?>" required> の
                    <input type="radio" name="object" value="likes" id="object_likes" <?= (isset($_GET['object']) && $_GET['object'] === 'likes') || empty($_GET) ? 'checked' : '' ?>><label for="object_likes">いいね一覧を取得する</label> 
                    <input type="radio" name="object" value="tweets" id="object_tweets" <?= isset($_GET['object']) && $_GET['object'] === 'tweets' ? 'checked' : '' ?>><label for="object_tweets">ツイート一覧を取得する</label> 
                </dd>
            </div>
            <div>
                <dt>取得ツイート数<em>*</em><br>(最大200)</dt>
                <dd>
                    <input type="number" name="count" value="<?= isset($_GET['count']) ? h($_GET['count']) : '100' ?>" max="200" min="10" step="10" required>
                </dd>
            </div>
            <div>
                <dt>詳細設定</dt>
                <dd>
                    <?php if ($is_login) { ?>
                    <input 
                        type="checkbox"
                        name="latest_dl"
                        id="latest_dl"
                        <?= !isset($_GET['id']) || isset($_GET['latest_dl']) ? 'checked' : '' ?>
                    >
                    <label for="latest_dl">前回保存した画像以降を取得</label><br>
                    <?php } ?>
                    <input
                        type="checkbox"
                        name="using_term"
                        id="using_term"
                        <?= !$is_login || isset($_GET['using_term']) ? 'checked' : '' ?>
                    >
                    <label for="using_term">期間指定を行う</label>
                    <input 
                        type="datetime-local" 
                        name="ed_time" 
                        value="<?= isset($_GET['ed_time']) ? h($_GET['ed_time']) : $nowTime ?>" 
                        <?= !$is_login ? ' required' : '' ?>
                    >
                    まで
                </dd>
            </div>
        </dl>      
        <input type="submit" value="送信">
    </form>
</div>
<?php if (isset($likes)) { ?>
<p><?= count($likes) ?>件のツイートが取得されました。</p>
<div class="download_area">
    <p>[保存]ボタンを押すと、ダウンロードフォルダにZipファイルで保存されます。</p>
    <form action="" method="POST">
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
        <?php } 
    } ?>
</ul>
<section id="caution">
    <h3>注意事項</h3>
    <p>画像の数が多いほど、ダウンロードに時間がかかります(画像数x1秒が目安)。また、画像数が多すぎると、ダウンロードできない場合があります。</p>
    <p>期間指定で遡れる範囲は最大1カ月前までです。</p>
    <p>ご要望・質問等ございましたら、<a href="<?= $home ?>mail/">こちらのフォーム</a>よりお願いします。</p>
</section>
<section id="versions">
    <h3>更新履歴</h3>
    <small>スクロールできます</small>
    <dl class="form_list">
        <?php foreach ($versions_log as $v) { ?>
            <div>
                <dt><?= $v['date'] ?></dt>
                <dd>
                    <p class="version">Ver. <?= $v['version'] ?></p>
                    <p><?= $v['content'] ?></p>
                </dd>
            </div>
        <?php } ?>
    </dl>
</section>
</main>
<?php include($home . '../footer.php') ?>
</body>
</html>