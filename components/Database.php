<?php

class Database {

    private static $db;

    private $pdo;

    public function __construct() {
        $this->pdo = self::getConnection();
    }

    public static function getConnection() {
        if (!isset(self::$db)) {
            $paramsPath = ROOT . '/config/db_config.php';
            $params = include($paramsPath);
            $dsn = "{$params['type']}:host={$params['host']};dbname={$params['dbname']}";
            try {
                self::$db = new PDO($dsn, $params['user'], $params['password']);
            }
            catch (PDOException $e) {
                die(json_encode(['error' => 'Error to connect DB: ' . $e->getMessage()]));
            }
        }
        return self::$db;
    }

    public function select($sql, $featch_all = false) {
        if($featch_all) $result = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        else $result = $this->pdo->query($sql)->fetch();
        return $result;
    }

    public function run($sql) {
        return $this->pdo->query($sql);
    }
}