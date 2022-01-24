-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- ホスト: 127.0.0.1
-- 生成日時: 2022-01-24 09:21:05
-- サーバのバージョン： 10.4.11-MariaDB
-- PHP のバージョン: 7.3.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
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
-- テーブルの構造 `exc_accounts`
--

CREATE TABLE `exc_accounts` (
  `user_id` int(6) NOT NULL COMMENT 'ユーザID',
  `sns_type` char(1) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'SNS種別',
  `acc_name` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '除外アカウント名'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `used_time`
--

CREATE TABLE `used_time` (
  `user_id` int(6) NOT NULL COMMENT 'ユーザID',
  `latest_time` datetime NOT NULL COMMENT '最終利用時刻',
  `sns_type` char(1) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'SNS種別'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- テーブルのデータのダンプ `used_time`
--

INSERT INTO `used_time` (`user_id`, `latest_time`, `sns_type`) VALUES
(2, '2022-01-24 16:48:00', 'T');

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
  `is_auth` tinyint(1) NOT NULL COMMENT '認証済みかどうか',
  `created_at` datetime NOT NULL COMMENT '作成時刻',
  `updated_at` datetime DEFAULT NULL COMMENT '更新時刻'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- テーブルのデータのダンプ `user`
--

INSERT INTO `user` (`user_id`, `user_name`, `password`, `email`, `premium`, `is_auth`, `created_at`, `updated_at`) VALUES
(2, 'Fumiya0719', '$2y$10$MPZb8BF6t0vG9Vvd4R08ce0necMYQHKm8JtBPnJvLekyygUir.h9O', 'tanni_fumiya0238@yahoo.co.jp', 'N', 1, '2022-01-17 16:13:00', NULL);

-- --------------------------------------------------------

--
-- テーブルの構造 `user_pre`
--

CREATE TABLE `user_pre` (
  `user_id` int(6) NOT NULL COMMENT 'ユーザID',
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'URLトークン',
  `email` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'メールアドレス',
  `req_time` datetime NOT NULL COMMENT 'リクエスト送信時刻',
  `is_submitted` tinyint(1) NOT NULL COMMENT '本登録かどうか'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- テーブルのデータのダンプ `user_pre`
--

INSERT INTO `user_pre` (`user_id`, `token`, `email`, `req_time`, `is_submitted`) VALUES
(1, '763698fae27e5eb55c3bd5e920dece0d02c83daf750e85700f6c663832946794', 'tanni_fumiya0238@yahoo.co.jp', '2022-01-13 14:50:01', 0),
(2, '6444e5b5045756a58eb3cc33d5f1ec53bda671a8f336c8994d7d65004add6aea', 'tanni_fumiya0238@yahoo.co.jp', '2022-01-13 15:51:45', 0),
(3, '5a4f4fae4a70ac1a672bc8ac59d88e42512be84f52d90366b066f01d3911aa9d', 'tosufumiya0719@gmail.com', '2022-01-17 16:29:27', 0);

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `exc_accounts`
--
ALTER TABLE `exc_accounts`
  ADD PRIMARY KEY (`user_id`);

--
-- テーブルのインデックス `used_time`
--
ALTER TABLE `used_time`
  ADD PRIMARY KEY (`user_id`);

--
-- テーブルのインデックス `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- テーブルのインデックス `user_pre`
--
ALTER TABLE `user_pre`
  ADD PRIMARY KEY (`user_id`);

--
-- ダンプしたテーブルのAUTO_INCREMENT
--

--
-- テーブルのAUTO_INCREMENT `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(6) NOT NULL AUTO_INCREMENT COMMENT 'ユーザID', AUTO_INCREMENT=3;

--
-- テーブルのAUTO_INCREMENT `user_pre`
--
ALTER TABLE `user_pre`
  MODIFY `user_id` int(6) NOT NULL AUTO_INCREMENT COMMENT 'ユーザID', AUTO_INCREMENT=4;

--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `exc_accounts`
--
ALTER TABLE `exc_accounts`
  ADD CONSTRAINT `exc_accounts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- テーブルの制約 `used_time`
--
ALTER TABLE `used_time`
  ADD CONSTRAINT `used_time_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
