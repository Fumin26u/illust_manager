# (Pythonの勉強も兼ねて)SEO対策の為のデータ収集
# 今回はページタイトル、URL、キーワード、デスクリプションの4つを収集し、結果をexcelファイルに出力する
# ↑キーワードに関しては、単語が出た回数を可視化できると尚良い
# 主にキーワードとデスクリプションを見て、どのようなキーワード、文章の傾向が最近では検索上位に入りやすいかを調べる。
# プログラムは「Google検索結果をエクセルに自動出力するプログラム(https://myafu-python.com/google-result/)」を一部改良したもの。
import openpyxl
import time
from selenium import webdriver

# [Config] ここで取得する内容を変える
# 検索する文字列
search_string = '松本市 結婚'
# 取得するサイトの数
get_sites = 30
# ページ遷移した時の待機時間(数値が大きいほど安定性が高いが、その分時間がかかる)
INTERVAL = 2
# キーワードの切り落とし処理(設定した数値以下のカウントのキーワードはexcelに書き込まない)
cut_word = 0

# Seleniumを使うための設定とgoogleの画面への遷移
URL = "https://www.google.com/"
firefox_path = "../geckodriver"
chrome_path = "../chromedriver"
options = webdriver.ChromeOptions()
options.add_argument('--ignore-certificate-errors')
options.add_argument('--ignore-ssl-errors')
driver = webdriver.Chrome(executable_path=chrome_path)
driver.maximize_window()
time.sleep(INTERVAL)
driver.get(URL)
time.sleep(INTERVAL)

# 文字を入力して検索
driver.find_element_by_name('q').send_keys(search_string)
driver.find_elements_by_name('btnK')[1].click() 
time.sleep(INTERVAL)

# 検索結果の一覧を取得する
noDesc = 0
noKeywords = 0
results = []
keyLists = {}

flag = False
while True:
    urls = []
    g_ary = driver.find_elements_by_css_selector('.tF2Cxc')
    for g in g_ary:
        url = g.find_element_by_class_name('yuRUbf').find_element_by_tag_name('a').get_attribute('href')
        urls.append(url)

    for url in urls:
        result = {}
        driver.get(url)
        time.sleep(INTERVAL)
        result['title'] = driver.title
        result['url'] = driver.current_url
        try:
            result['description'] = driver.find_element_by_name('description').get_attribute('content')
        except:
            # デスクリプションが設定されてないサイトのカウンタ
            noDesc += 1
            result['description'] = ''
        try:
            result['keywords'] = driver.find_element_by_name('keywords').get_attribute('content')
        except:
            # キーワードが設定されていないサイトのカウンタ
            noKeywords += 1
            result['keywords'] = ''
        # キーワードをコンマごとに分解する
        keys = result['keywords'].split(',')
        for key in keys:
            # それぞれのキーワードが複合した回数をカウントする
            if (key in keyLists):
                keyLists[key] += 1
            else:
                keyLists[key] = 1
        results.append(result)
        print(len(results))
        driver.back()
        time.sleep(INTERVAL)
        if len(results) >= get_sites:
            flag = True
            break
    if flag:
        break
    driver.find_element_by_id('pnnext').click()
    time.sleep(INTERVAL)

# キーワードの切り落とし
keyLists2 = {}
for key, value in keyLists.items():
    if value > cut_word:
        keyLists2[key] = value

word_keys = list(keyLists2.keys())
word_values = list(keyLists2.values())

# ワークブックの作成とヘッダ入力
workbook = openpyxl.Workbook()
sheet = workbook.active
sheet['A1'].value = 'タイトル'
sheet['B1'].value = 'URL'
sheet['C1'].value = '説明'
sheet['D1'].value = 'キーワード'
sheet['G2'].value = 'キーワード名'
sheet['H2'].value = '回数'
sheet['J2'].value = 'デスクリプション無し'
sheet['K2'].value = 'キーワード無し'

# シートにデータを書き出す
for row, result in enumerate(results, 2):
    sheet[f"A{row}"] = result['title']
    sheet[f"B{row}"] = result['url']
    sheet[f"C{row}"] = result['description']
    sheet[f"D{row}"] = result['keywords']

for row in range(2, len(word_keys)+2):
    sheet[f"G{row}"] = word_keys[row-2]
    sheet[f"H{row}"] = word_values[row-2]

sheet[f"J3"] = str(noDesc)
sheet[f"K3"] = str(noKeywords)

workbook.save(f"{search_string}.xlsx")
driver.close()