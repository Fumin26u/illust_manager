<?php
$home ='../';
require_once($home . '../commonlib.php');

$msg = [];

// ログインしていない場合、トップページにリダイレクト
if (!isset($_SESSION['user_id'])) {
    header('../', true, 303);
    exit;
}

// ユーザー情報の初期化
$user_info = [
    'user_name' => [
        'title' => 'ユーザー名',
        'value' => ''
    ],
    'email' => [
        'title' => 'メールアドレス',
        'value' => ''
    ],
    'created_at' => [
        'title' => 'アカウント作成時刻',
        'value' => ''
    ],
    'premium' => [
        'title' => '会員登録状況',
        'value' => ''
    ],
    'dl_count' => [
        'title' => 'ダウンロード回数',
        'value' => ''
    ],
    'images_count' => [
        'title' => '保存した画像の総数',
        'value' => ''
    ],
    'latest_time' => [
        'title' => '最新の利用時刻',
        'value' => ''
    ],
];

try {
    $pdo = dbConnect();

    // 表示するユーザーデータを読み込む
    $sql = <<<SQL
SELECT user_name, email, created_at, premium, dl_count, images_count FROM user
WHERE user.user_id = :user_id 
SQL;
    $st = $pdo->prepare($sql);
    $st->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $st->execute();
    $rows = $st->fetch(PDO::FETCH_ASSOC);
    // プレミアム会員かどうか
    switch ($rows['premium']) {
        case 'P':
            $rows['premium'] = 'プレミアム会員';
            break;
        default:
            $rows['premium'] = '一般会員';
            break;
    }
    // DL回数と保存した画像の枚数はNULLの場合0に置き換える
    $rows['dl_count'] = is_null($rows['dl_count']) ? 0 . '回': $rows['dl_count'] . '回';
    $rows['images_count'] = is_null($rows['images_count']) ? 0 . '枚' : $rows['images_count'] . '枚';
    // 日付関連の置き換え
    // $rows['created_at'] = date('Y年m月d日 h時i分', $rows['created_at']);
    // $rows['latest_time'] = date('Y年m月d日 h時i分', $rows['latest_time']);

    // 最終利用時刻を読み込む
    $st = $pdo->prepare('SELECT latest_time FROM used_time WHERE user_id = :user_id');
    $st->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $st->execute();
    $row = $st->fetch(PDO::FETCH_ASSOC);
    $rows['latest_time'] = $row !== false ? $row[0] : 'まだ利用していません。';

    // ユーザー情報に挿入
    foreach ($user_info as $k => $u) {
        $user_info[$k]['value'] = $rows[$k];
    }
 
} catch (PDOException $e) {
    echo 'データベース接続に失敗しました';
    if (DEBUG) {
        echo $e;
    }
}

$url = 'https://imagedler.com/u/signup.php?t=';

// デスクリプション
$title = $user_info['user_name']['value'] . 'さんのユーザー情報';

$description = "Twitterで自分が「いいね」をした画像を一括ダウンロードできるツールです。ユーザー登録を行うことにより、更に手軽にダウンロードを行うことができます。";

$keywords = "Twitter,いいね,ダウンロード,画像,保存,一括";

$canonical = "https://imagedler.com/u/profile.php";
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $title ?></title>
<?php include_once $home . '../gtag.inc'; ?>
<link rel="stylesheet" href="signup.css">
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
<body class="profile">
<?php include_once($home . '../header.php') ?>
<main>
    <h2><?= $user_info['user_name']['value'] ?>さんのユーザー情報</h2>
    <dl class="form_list">
        <?php foreach ($user_info as $u) { ?>
            <div>
                <dt><?= $u['title'] ?></dt>
                <dd><?= $u['value'] ?></dd>
            </div>
        <?php } ?>
    </dl>
</main>
<?php include_once($home . '../footer.php') ?>
</body>
</html>