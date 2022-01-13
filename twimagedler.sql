-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- ホスト: 127.0.0.1
-- 生成日時: 2022-01-13 03:34:50
-- サーバのバージョン： 10.4.17-MariaDB
-- PHP のバージョン: 7.4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `twimagedler`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `user`
--

CREATE TABLE `user` (
  `user_id` int(6) NOT NULL COMMENT 'ユーザID',
  `user_name` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ユーザ名',
  `password` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ユーザパスワード',
  `email` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'メールアドレス',
  `premium` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N' COMMENT 'プレミアム会員可否',
  `latest_time` datetime DEFAULT NULL COMMENT '最終利用時刻'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
