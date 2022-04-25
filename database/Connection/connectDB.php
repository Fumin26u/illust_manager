<?php 
// namespace Database\Connection;

// use \PDO;
// use \PDOException;

class ConnectDB extends DBInfo {
	public function __construct() {
		try {
			$pdo = new PDO(DSN, DBUSER, DBPASS);
		} catch (PDOException $e) {
			echo 'データベース接続に失敗しました。<br>';
			if (DEBUG) echo $e;
		}
		return $pdo;
	}
}