<?php
use Abraham\TwitterOAuth\TwitterOAuth;

// APIキー、トークンの設定
function getTweets($id) {
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
  // echo '<pre>';
  // var_dump($likes_tweet_list);
  // echo '</pre>';

  $likes = [];
  $queue = [];
  // キューにツイートを1つずつ挿入
  foreach ($likes_tweet_list as $l) {
    // 画像付きツイートでない場合、キューに挿入しない
    if (!isset($l->extended_entities)) continue;
    $queue['post_time'] = $l->created_at;
    $queue['user'] = $l->user->name;
    $queue['text'] = $l->text;
    $queue['images'] = [];
    // 画像は複数枚の可能性があるので配列に挿入
    foreach ($l->extended_entities->media as $m) {
      $queue['images'][] = $m->media_url_https;
    }
    // キューのデータを一覧に追加
    array_push($likes, $queue);
  }
  return $likes;
}
