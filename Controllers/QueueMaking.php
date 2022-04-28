<?php
namespace Controllers;

class QueueMaking {

    public function makeGetTweetsQueue(array $queue) {

        $values = [];
        $err = [];

        // setされていないキーの判定
        if (!(isset($queue['id'], $queue['object'], $queue['count']))) {
            $err[] = '必須項目が入力されていません。';
        }   

        // Twitter ID
        // 空であればエラー
        if ($queue['id'] === '') {
            $err[] = 'Twitter IDが指定されていません。';
        } else {
            $values['id'] = h($queue['id']); 
        }

        // object
        // likes / tweets 以外の値が入っている場合エラー
        if ($queue['object'] !== 'likes' && $queue['object'] !== 'tweets') {
            $err[] = 'どの一覧を取得するか指定してください。';
        } else {
            $values['object'] = h($queue['object']);
        }
 
        // count
        // 規定値以外の数値が入っていればエラー
        // v($queue['count']);
        if ($queue['count'] <= 0 || $queue['count'] > 200) {
            $err[] = '取得ツイート数が規定値以上です。';
        } else {
            $values['count'] = h($queue['count']);
        }

        if (!empty($err)) {
            $exception = true;
            return [$exception, $err];
        } else {
            // latest_dl
            if (isset($queue['latest_dl'])) $values['latest_dl'] = h($queue['latest_dl']);
    
            // ツイート一覧を取得する場合
            // 期間指定を行うかつ、その値が不正である場合エラー
            if ($queue['object'] === 'tweets' && isset($queue['ed_time'])) {
                $values['ed_time'] = h($queue['ed_time']) . ':00Z';
            }

            return $values;
        }
    }

}