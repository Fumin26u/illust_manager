<?php
namespace database\Reads;

use database\Connection\ConnectDB;

final class LatestDL {
    public function __construct() {
        $pdo = dbConnect();
    }
}