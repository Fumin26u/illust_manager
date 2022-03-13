<?php
$home ='../';
require_once($home . '../commonlib.php');

$msg = [];

// ログインしていない場合、トップページにリダイレクト
if (!isset($_SESSION['user_id'])) {
    header('../', true, 303);
    exit;
}

try {
    $pdo = dbConnect();

    // 表示するユーザーデータを読み込む
    $sql = <<<SQL
SELECT user_name, email, premium, dl_count, images_count, created_at, used_time.latest_time FROM user
INNER JOIN used_time ON used_time.user_id = user.user_id
WHERE user.user_id = :user_id 
AND used_time.sns_type = 'T' 
SQL;
    $st = $pdo->prepare($sql);
    $st->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $st->execute();
    $user_info = $st->fetch(PDO::FETCH_ASSOC);
    // プレミアム会員かどうか
    switch ($user_info['premium']) {
        case 'P':
            $user_info['premium'] = 'プレミアム会員';
            break;
        default:
            $user_info['premium'] = '一般会員';
            break;
    }
    // DL回数と保存した画像の枚数はNULLの場合0に置き換える
    $user_info['dl_count'] = is_null($user_info['dl_count']) ? 0 : $user_info['dl_count'];
    $user_info['images_count'] = is_null($user_info['images_count']) ? 0 : $user_info['images_count'];
    // 日付関連の置き換え
    // $user_info['created_at'] = date('Y年m月d日 h時i分', $user_info['created_at']);
    // $user_info['latest_time'] = date('Y年m月d日 h時i分', $user_info['latest_time']);

} catch (PDOException $e) {
    echo 'データベース接続に失敗しました';
    if (DEBUG) {
        echo $e;
    }
}

$url = 'https://imagedler.com/u/signup.php?t=';

// デスクリプション
$title = $user_info['user_name'] . 'さんのユーザー情報';

$description = "Twitterで自分が「いいね」をした画像を一括ダウンロードできるツールです。ユーザー登録を行うことにより、更に手軽にダウンロードを行うことができます。";

$keywords = "Twitter,いいね,ダウンロード,画像,保存,一括";

$canonical = "https://imagedler.com/u/profile.php";
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $title ?></title>
<?php include_once $home . '../../gtag.inc'; ?>
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
<body>
<?php include_once($home . '../header.php') ?>
<main>

</main>
<?php include_once($home . '../footer.php') ?>
</body>
</html>