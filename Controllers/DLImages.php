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

        // Zipを開く
        $st = $zip->open($dl_file_name, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        // Zipに画像ファイルを挿入
        foreach ($images as $i) {

            // 拡張子を判別
            switch (substr($i, -4, 4)) {
                case '.jpg':
                    $trim_str = '.jpg';
                    $format_name = 'jpg';
                    break;
                case '.png':
                    $trim_str = '.png';
                    $format_name = 'png';
                    break;
                case 'jpeg':
                    $trim_str = '.jpeg';
                    $format_name = 'jpeg';
                    break;
                case 'jfif':
                    $trim_str = '.jfif';
                    $format_name = 'jfif';
                    break;        
            }

            $img_url = rtrim($i, $trim_str) . '?format=' . $format_name . '&name=orig';

            $fp = $i;
            $ch = curl_init($img_url);
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