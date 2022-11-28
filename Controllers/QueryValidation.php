<?php
namespace Controllers;

use Values\TwitterObjects;

class QueryValidation {

    public function queryValidation(array $query) {

        $values = [];
        $err = [];

        // setされていないキーの判定
        if (!(isset($query['id'], $query['object'], $query['count']))) {
            $err[] = '必須項目が入力されていません。';
        }   

        // Twitter ID
        // 空であればエラー
        if ($query['id'] === '') {
            $err[] = 'Twitter IDが指定されていません。';
        } else {
            $values['id'] = h($query['id']); 
        }

        // object
        // TwitterObjectsクラスで指定されている値以外が入っていればエラー
        if (array_search($query['object'], TwitterObjects::$TwitterObjects) !== false) {
            $err[] = 'どの一覧を取得するか指定してください。';
        } else {
            $values['object'] = h($query['object']);
        }
 
        // count
        // 規定値以外の数値が入っていればエラー
        // v($query['count']);
        if ($query['count'] <= 0 || $query['count'] > 300) {
            $err[] = '取得ツイート数が規定値外です。';
        } else {
            $values['count'] = h($query['count']);
        }

        if (!empty($err)) {

            $return = [
                'status' => 'ERROR',
                'content' => $err,
            ];

        } else {

            // latest_dl
            if (isset($query['latest_dl'])) $values['latest_dl'] = h($query['latest_dl']);
    
            // ツイート一覧を取得する場合
            // 期間指定を行うかつ、その値が不正である場合エラー
            if ($query['object'] === 'tweets' && isset($query['ed_time'])) {
                $values['ed_time'] = h($query['ed_time']) . ':00Z';
            }

            $return = [
                'status' => 'SUCCESS',
                'content' => $values
            ];

        }

        return $return;
    }

}