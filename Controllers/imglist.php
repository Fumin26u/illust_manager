<?php
namespace Controllers;

// $home = './';
// declare(strict_types = 1);

// require_once($home . "../vendor/autoload.php");

use Abraham\TwitterOAuth\TwitterOAuth;
use Controllers\APIKey;

class ImgList extends APIKey {
    private function setCurl($req) {
        $BEARER_TOKEN = 'AAAAAAAAAAAAAAAAAAAAAOpSVQEAAAAAFCRrxMWTLcVmsMU5RF1S8uTJKQs%3D0Qpr1mfNOV9Ls3RvhmzOu2uwocMYpKKOrEtI9OZtpblpo4GmIi';

        // リクエストヘッダの作成
        $header = [
            'Authorization: Bearer ' . $BEARER_TOKEN,
            'Content-Type: application/json',
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $req);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        return $curl;
    }

	public function imgList(array $query) {
		/* 
        params of $query
		id: Twitter ID
        count: 取得するツイート数
        latest_dl: 前回取得した画像以降を取得するかどうか
        object: いいね・ツイート どちらの一覧を取得するか
        using_term: (ツイート一覧を取得する場合のみ) チェックが入っている場合日付、入ってない場合False
        */

        // Connect to TwitterOAuth
        $api_key = new APIKey;
        $connection = new TwitterOAuth($api_key->API_KEY, $api_key->API_KEY_SECRET, $api_key->ACCESS_TOKEN, $api_key->ACCESS_TOKEN_SECRET);

        // Set APIVersion to 2
        $connection->setApiVersion('2');

        /* ------------------------------

        スクリーンネーム(@以降のID)を数値のIDに変換

        ------------------------------ */
        $endPoint = 'https://api.twitter.com/2/users/by/username/';
        $screen_name = $query['id'];
        $request_url = $endPoint . $screen_name;   
    
        // APIへの問い合わせと数値のuser_idの取り出し
        $curl = $this->setCurl($request_url);
        $response = curl_exec($curl);
        $res = json_decode($response, true);
        $user_id = $res['data']['id'];

        /* ------------------------------

            画像ツイート一覧を取得

        ------------------------------ */
        // エンドポイントの判定
        if ($query['object'] === 'likes') {

            $endPoint = 'https://api.twitter.com/2/users/' . $user_id . '/liked_tweets';

            $query = [
                'max_results' => 10,
                'expansions' => 'author_id,entities.mentions.username',
                'tweet.fields' => 'created_at,entities',
                'user.fields' => 'username',
            ];

            $request_url = $endPoint . '?' . http_build_query($query);

        } else if ($query['object'] === 'tweets') {

            $endPoint = 'https://api.twitter.com/2/users/' . $user_id . '/tweets';

            $query = [
                'max_results' => 100,
                'expansions' => 'author_id,entities.mentions.username',
                'tweet.fields' => 'created_at,entities',
                'user.fields' => 'username',
            ];

            if (isset($query['using_term'])) $query['end_time'] = $query['using_term'];

            $request_url = $endPoint . '?' . http_build_query($query);

        }

        // APIへの問い合わせとツイート情報の取り出し
        $curl = $this->setCurl($request_url);
        $response = curl_exec($curl);
        $res = json_decode($response, true);

        return ($res);
	}
}