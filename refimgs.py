# テスト用: Excelファイルに画像URL一覧を添付する用
import openpyxl
# タイマー: 同時複数リクエストを制限
import time
# ドライバ: スクレイピング用のドライバ
from selenium import webdriver
# urllib: URL引数を読み込む
import urllib.parse

test = [10, 100, 1000]
print(test)