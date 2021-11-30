<?php
use Abraham\TwitterOAuth\TwitterOAuth;

require_once('dlImages.php');

// APIキー、トークンの設定
function getTweets($id, $time) {

  // APIキーとトークン
  $API_KEY = 'b7fAIh1aBSkWVN26iVe1o3Sbu';
  $API_KEY_SECRET = '3pWPuIhOV57PL0LPL3F3TO5yiiw6mMwrRUzcYVbd4XSSYsZsWQ';
  $ACCESS_TOKEN = 'AAAAAAAAAAAAAAAAAAAAAOpSVQEAAAAAHxXl57plinhQ2va7JhTvWzaeACs%3DzLBwdukgubrRcHzeTXuIPieu5qvlV25CZDXGrWuLG4XotosnNQ';

  // 「いいね」ツイート一覧のエンドポイント(URL)
  $endPoint = 'favorites/list';

  // APIキーとトークンを用いてTwitterOAuthに接続
  $connection = new TwitterOAuth($API_KEY, $API_KEY_SECRET, $ACCESS_TOKEN);

  // Twitter ID(数値)を取得し、いいねのエンドポイント(URL)を代入
  $account = $id;
  $point = $endPoint;

  // 「いいね」したツイート一覧を取得
  $likes_tweet_list = $connection->get($point, ['user_id' => $account, 'count' => 100]);

  // GETで取得した日付のフォーマット
  $getTime = date('Y-m-d H:i:s', strtotime((string) $time));

  $likes = [];
  $queue = [];
  // 全ての画像URLの一覧(ダウンロード時に利用)
  $images = [];
  // キューにツイートを1つずつ挿入
  foreach ($likes_tweet_list as $l) {

    // そのツイートの投稿日時を取得
    $posted_date = date('Y-m-d H:i:s', strtotime((string) $l->created_at));

    // 投稿日時がGETで取得した日付より古い場合、キューへの挿入を終了
    if ($posted_date < $getTime) break;

    // 画像付きツイートでない場合、キューに挿入しない
    if (!isset($l->extended_entities)) continue;
    $queue['post_time'] = $posted_date;
    $queue['user'] = $l->user->name;
    $queue['text'] = $l->text;
    $queue['images'] = [];

    // 画像は複数枚の可能性があるので配列に挿入
    foreach ($l->extended_entities->media as $m) {
      $queue['images'][] = $m->media_url_https;
      // 画像URL一覧の配列にも同様に挿入
      $images[] = $m->media_url_https;
    }

    // キューのデータを一覧に追加
    array_push($likes, $queue);

  }

  // URL引数$_POSTが設定された場合、ローカルに画像一覧をダウンロード
  if (isset($_POST['download'])) dlImages($images);

  return $likes;
}
