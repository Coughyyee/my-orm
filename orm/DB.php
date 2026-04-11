<?php

class DB
{
    public static PDO $db;

    public function __construct()
    {
        $this->connect();
    }

    private function connect()
    {
        try {
            self::$db = new PDO('mysql:host=localhost;dbname=orm-test', 'root', '');
            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }
}