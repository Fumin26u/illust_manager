<?php
$home = './';

use Controllers\DLImages;
use Controllers\GetTweets;
use Controllers\QueryValidation;
use Database\Posts\SetDLCount;
use Database\Reads\LatestDL;
use Database\Posts\SetLatestDL;
use Values\TwitterObjects;
use Values\Versions;

$msg = [];
$err = [];

require_once($home . '../commonlib.php');
require_once($home . "../vendor/autoload.php");

// ログインしているかどうか
$is_login = isset($_SESSION['user_id']) ? true : false;

// 送信ボタンが押された場合の処理
if (isset($_GET['id']) && $_SERVER['REQUEST_METHOD'] === 'GET') {

    // $_GETのバリデーション処理
    $q = new QueryValidation();
    $tweets_query = $q->queryValidation($_GET);
    // $tweets = new GetTweets($_GET);

    // バリデーションでエラーが発生した場合APIを呼ばず処理を行う
    if ($tweets_query['status'] === 'ERROR') {

        $err[] = $tweets_query['content'];

    } else if ($tweets_query['status'] === 'SUCCESS') {

        // 「前回保存した画像以降を取得」にチェックが入っている場合、DBからツイートIDを取得
        $d = new LatestDL();
        $latest_dl = isset($_GET['latest_dl']) ? $d->LatestDL(h($_GET['id'])) : '';
    
        // ツイート一覧を取得
        $l = new GetTweets();
        $_SESSION['tweets'] = $l->getTweets($tweets_query['content'], $latest_dl);
        $tweets = $_SESSION['tweets'];

        // echo '<pre>';
        // v($_SESSION['tweets']);
        // echo '</pre>';

    }
}

// 保存ボタンが押された場合の処理
if (isset($_POST['download']) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['tweets'])) {

    if ($_SESSION['cToken'] !== $_POST['cToken']) {

        $msg[] = '不正なアクセスが行われました';

    } else {

        $tweets = $_SESSION['tweets'];
    
        // 画像をダウンロード
        $dlImages = new DLImages;
        $images_count = $dlImages->DLImages($tweets);
    
        // ログインしている場合の処理
        if (isset($_SESSION['user_id'], $_SESSION['user_name'])) {
    
            try {
    
                // latest_dlテーブルの確認
                // 既にDB(latest_dlテーブル)に値がセットされているかどうか
                $d = new LatestDL();
                $isset_latest_dl = isset($_GET['latest_dl']) ? $d->LatestDL(h($_GET['id'])) : '';
    
                // 期間指定の終了時刻の登録
                $s = new SetLatestDL();
                $s->SetLatestDL($isset_latest_dl, $tweets[0]['post_id'], h($_GET['id']));
    
                // DL回数と保存した画像の総数を更新
                $s = new SetDLCount;
                $s->SetDLCount($images_count);
    
            } catch (PDOException $e) {
                echo 'データベース接続に失敗しました';
                if (DEBUG) {
                    echo $e;
                }
            }
        }
    
        // セッションに一時保存したツイート情報を破棄
        $_SESSION['tweets'] = null;
    }
}

// csrf対策
$cToken = bin2hex(random_bytes(32));
$_SESSION['cToken'] = $cToken;

// 現在時刻を生成
$t = new DateTime();
$today = $t->format('Y-m-d');

$n = new DateTime();
$now = $n->format('H:i');
$nowTime = $today . 'T' . $now;

// メタタグの整備
// ページタイトルの設定
$title = isset($_GET['id']) ? '@' . $_GET['id'] . 'のいいねツイート一覧 | TwimageDLer' : "TwimageDLer | Twitterの画像自動ダウンローダー";

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
    <form action="<?= h($_SERVER['PHP_SELF']) ?>" method="GET">
        <dl class="form_list">
            <div>
                <dt>Twitter ID<em>*</em></dt>
                <dd>
                    <input 
                    type="text" 
                    name="id" 
                    value="<?= isset($_GET['id']) ? h($_GET['id']) : '' ?>" 
                    required
                    >の<br class="br">
                    <?php foreach(TwitterObjects::$TwitterObjects as $key => $value) { ?>
                    <input 
                        type="radio" 
                        name="object" 
                        value="<?= $key ?>" 
                        id="<?= $key ?>" 
                        <?= (isset($_GET['object']) && $_GET['object'] === $key) || empty($_GET) && $key === 'liked_tweets' ? 'checked' : '' ?>
                        <?= $key === 'bookmarks' ? 'disabled' : '' ?>
                    >
                    <label for="<?= $key ?>"><?= $value ?>一覧を取得する</label><br class="br"> 
                    <?php } ?>
                </dd>
            </div>
            <div>
                <dt>取得ツイート数<em>*</em><br>(最大200)</dt>
                <dd>
                    <input 
                        type="number" 
                        name="count" 
                        value="<?= isset($_GET['count']) ? h($_GET['count']) : '100' ?>" max="200" 
                        min="10" 
                        step="10" 
                        required
                    >
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
                    <div id="using_term_area">
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
                    </div>
                </dd>
            </div>
        </dl>      
        <input type="submit" value="送信">
    </form>
</div>
<?php if (!empty($err)) { 
    foreach ($err as $e) { ?>
    <p class="error_notice"><?= $e ?></p>
<?php }
} ?>
<?php if (isset($tweets)) { ?>
<p><?= count($tweets) ?>件のツイートが取得されました。</p>
<div class="download_area">
    <p>[保存]ボタンを押すと、ダウンロードフォルダにZipファイルで保存されます。</p>
    <form action="" method="POST">
    <input type="hidden" name="cToken" value="<?= $cToken ?>">
    <input type="submit" name="download" value="保存">
    </form>
</div>
<ul class="likes_list">
    <?php foreach($tweets as $t) { ?>
        <li>
            <p class="user_name"><?= $t['user'] ?></p>
            <p><?= $t['post_time'] ?></p>
            <p class="tweet_content"><?= $t['text'] ?></p>
            <?php foreach($t['images'] as $i) { ?>
                <img src="<?= $i ?>" alt="">
            <?php } ?>
            <p>
                ツイート元リンク:
                <a href="<?= $t['url'] ?>" target="_blank" rel="noopener noreferrer"><?= $t['url'] ?></a>
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
        <?php foreach (Versions::$versions_log as $v) { ?>
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