<?php
namespace Controllers;

use \ZipArchive;

class DLImages {
    public function DLImages(array $tweets, string $fileName = 'images.zip'): int {

        // リストから画像を抽出
        $images = [];
        foreach ($tweets as $t) {
            foreach ($t['images'] as $i) {
                $images[] = $i;
            }
        }
        $count_images = count($images);

        $zip = new ZipArchive();

        // DLするZipのファイル名
        $dl_file_name = $fileName;

        // 保存先ディレクトリ
        // $dl_path = $path;

        // Zipを開く
        $st = $zip->open($dl_file_name, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        // Zipに画像ファイルを挿入
        foreach ($images as $i) {

            // 拡張子を判別
            switch (substr($i, -13, 3)) {
                case 'jpg':
                    $ext = '.jpg';
                    $offset = -21;
                    break;
                case 'png':
                    $ext = '.png';
                    $offset = -21;
                    break;
                case 'jpe':
                    $ext = '.jpeg';
                    $offset = -22;
                    break;
                case 'jfi':
                    $ext = '.jfif';   
                    $offset = -22;     
                    break;        
            }

            $img_name = rtrim($i, substr($i, $offset)) . $ext;

            $fp = $img_name;
            $ch = curl_init($i);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_NOBODY, 0);

            // タイムアウトの値
            curl_setopt($ch, CURLOPT_TIMEOUT, 480);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        
            $output = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
            if ($status == 200 && mb_strlen($output) != 0){
                // ファイルの取得に成功した場合、Zipにファイルを挿入
                $zip->addFromString(basename($fp), $output);
            } 

            curl_close($ch);
            sleep(1);

        }

        $zip->close();

        // 作成したZipファイルのダウンロード
        header("Content-Type: application/zip");
        header("Content-Disposition: attachment; filename=\"".basename($dl_file_name)."\"");
        ob_end_clean();
        readfile($dl_file_name);
        unlink($dl_file_name);

        return $count_images;
    }
}