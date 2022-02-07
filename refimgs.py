# ドライバ: スクレイピング用のドライバ
from selenium import webdriver
# urllib: URL引数を読み込む
import urllib.parse
# タイマー: 同時複数リクエストを制限
import time
# 日付: いつまでのブックマークを取得するか
import datetime
# pixivpy: pixivからデータを抽出するAPI
from pixivpy3 import *

# import APIkey
import sys
import os
sys.path.append(os.path.abspath('..'))
import apikey_pi

# Auth接続
api = AppPixivAPI()
api.auth(refresh_token = apikey_pi.REFRESH_TOKEN)
aapi = AppPixivAPI()
aapi.auth(refresh_token = apikey_pi.REFRESH_TOKEN)

# PHP側に送信する用の配列
res = []

# データを一時的に保存するキュー
queue = {}
# ブックマークを取得
bookmarks = aapi.user_bookmarks_illust(78079062, 'public')
# print(bookmarks['next_url'])

# 画像をurl配列に挿入
# cont_reference = True
# while cont_reference:
for i, b in enumerate(bookmarks['illusts']):
    # res.insert(i)
    
    # メタ情報の追加
    queue['post_time'] = b['create_date']
    # queue['user_id'] = b['user']['id']
    queue['user'] = b['user']['name']
    queue['title'] = b['title']
    queue['text'] = b['caption']
    queue['images'] = []
    queue['url'] = 'https://www.pixiv.net/artworks/' + str(b['id'])

    # 画像の挿入
    # 画像が1枚の場合
    if len(b['meta_pages']) == 0:
        queue['images'].append(b['image_urls']['large'])
    # 画像が複数枚の場合
    else:
        for m in b['meta_pages']:
            queue['images'].append(m['image_urls']['original'])      

    # キューを結果に挿入
    res.append(queue.copy())

print(res[29]['post_time'])



