<?php
namespace Database;

use PDO;
use PDOException;
class Database {

    static $DB_HOST = "localhost";
    static $DB_NAME = "sheda_mart";
    static $DB_USER = "root";
    static $DB_PASS = 'newpassword';

    static $connection;

    public static function getConnection() {
        self::$connection = null;
        try {
            self::$connection = new PDO("mysql:host=" . self::$DB_HOST . ";dbname=" . self::$DB_NAME, self::$DB_USER, self::$DB_PASS);
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return self::$connection;
    }
}
?>