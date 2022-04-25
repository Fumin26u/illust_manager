<?php
$home = './';
// declare(strict_types = 1);

require($home . '../commonlib.php');
require_once($home . "../vendor/autoload.php");
require('versions.php');
use Abraham\TwitterOAuth\TwitterOAuth;

// APIキーとトークン
include_once('../apikey.php');

// APIキーとトークンを用いてTwitterOAuthに接続
$connection = new TwitterOAuth($API_KEY, $API_KEY_SECRET, $ACCESS_TOKEN, $ACCESS_TOKEN_SECRET);

$query = [
    'max_results' => 10,
    'expansions' => 'author_id,entities.mentions.username',
    'tweet.fields' => 'created_at,entities',
    'user.fields' => 'username',
    'pagination_token' => '7140dibdnow9c7btw481cseys7tc6aovjekz21eqk5elu'
];

$id = '1473630539462901761';
$connection->setApiVersion('2');
$endPoint = 'https://api.twitter.com/2/users/' . $id . '/liked_tweets';

$url = $endPoint . '?' . http_build_query($query);
v($url);

$token = 'AAAAAAAAAAAAAAAAAAAAAOpSVQEAAAAAFCRrxMWTLcVmsMU5RF1S8uTJKQs%3D0Qpr1mfNOV9Ls3RvhmzOu2uwocMYpKKOrEtI9OZtpblpo4GmIi';
$header = [
    'Authorization: Bearer ' . $token,
    'Content-Type: application/json',
];

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($curl);
$res = json_decode($response, true);

curl_close($curl);
echo '<pre>';
v($res);
echo '</pre>';
