<?php
require_once('commonlib.php');
require_once($home . "../../vendor/autoload.php");
use Abraham\TwitterOAuth\TwitterOAuth;

// APIキー、トークンの設定
function getTweets($id, $count, $latest_dl, $using_term, $st_time = false, $ed_time = false) {

    // APIキーとトークン
    include_once('../../apikey.php');

    // APIキーとトークンを用いてTwitterOAuthに接続
    $connection = new TwitterOAuth($API_KEY, $API_KEY_SECRET, $ACCESS_TOKEN, $ACCESS_TOKEN_SECRET);

    // 「いいね」ツイート一覧のエンドポイント(URL)
    $endPoint = 'favorites/list';

    // APIv2の場合
    // $connection->setApiVersion('2');
    // $endPoint = 'https://api.twitter.com/2/users/' . $id . '/liked_tweets';

    // Twitter ID(数値)を取得し、いいねのエンドポイント(URL)を代入
    $account = $id;
    $point = $endPoint;

    // 取得する最大ツイート数(引数で指定)
    $counter = (int) $count;

    // ループ毎に取得するツイート数
    $each_count = 50;

    $likes = [];
    $max_id = 1496095626936954891;
    while ($counter > 0) {
        // カウンタの進行
        if ($counter < $each_count) $each_count = $counter;
        $counter -= $each_count;

        // 「いいね」したツイート一覧を取得
        $likes_tweet_list = $connection->get($point, ['screen_name' => $account, 'count' => $each_count, 'max_id' => $max_id]);
    
        // echo '<pre>';
        // var_dump($likes_tweet_list);
        // echo '</pre>';
    
        // 期間指定を行う場合、GETで取得した日付のフォーマットをする
        if ($using_term) {
            $st_getTime = date('Y-m-d H:i:s', strtotime((string) $st_time));
            $ed_getTime = date('Y-m-d H:i:s', strtotime((string) $ed_time));
        }
    
        $queue = [];
        $co = 0;
        // キューにツイートを1つずつ挿入
        foreach ($likes_tweet_list as $l) {
            // 取得内容の被り回避用カウンタ
            $co++;
            // 取得した投稿の配列の最後の場合、取得する最大投稿IDを、現在の投稿ID(-1)に変更
            if ($co == $each_count) $max_id = (int) $l->id_str - 1;
    
            // 前回保存した画像以降を取得する場合の処理
            // そのツイートの投稿IDが引数で渡された数値と同じ場合、キューへの挿入を終了
            if ($latest_dl !== false && $l->id_str == $latest_dl) break 2;
    
            // そのツイートの投稿日時を取得
            $posted_date = date('Y-m-d H:i:s', strtotime((string) $l->created_at));
            // 期間指定を行う場合、キューへの挿入判定をする
            // 投稿日時がGETで取得した日付外の場合、キューに挿入しない
            if ($using_term && ($posted_date < $st_getTime || $posted_date > $ed_getTime)) continue;
    
            // 画像付きツイートでない場合、キューに挿入しない
            if (!isset($l->extended_entities)) continue;
            $queue['post_id'] = $l->id_str;
            $queue['post_time'] = $posted_date;
            $queue['user'] = $l->user->name;
            $text = $l->text;
            // $queue['text'] = substr($text, 0, strcspn($text, 'https://t.co/'));
            $queue['text'] = substr($text, 0, -24);
            $queue['images'] = [];
            $queue['url'] = $l->extended_entities->media[0]->url;
    
            // 画像は複数枚の可能性があるので配列に挿入
            foreach ($l->extended_entities->media as $m) {
                $queue['images'][] = $m->media_url_https;;
            }
    
            // キューのデータを一覧に追加
            array_push($likes, $queue);
        }
    }

    return $likes;
}


