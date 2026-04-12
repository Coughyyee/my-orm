<?php

namespace Szymo\MyOrm\Database;

use PDO;
use PDOException;

class DB
{
    // private database connection variables.
    private string $host = 'localhost';
    private string $dbname = 'orm-test';
    private string $username = 'root';
    private string $password = '';

    /**
     * @var PDO $db Static referance to the PDO database connection.
     */
    public static PDO $db;

    public function __construct()
    {
        // run database connection.
        $this->connect();
    }

    /**
     * Create a PDO connection to the mysql database. 
     * @return void can exit on failure with error message.
     */
    private function connect()
    {
        try {
            self::$db = new PDO("mysql:host={$this->host};dbname={$this->dbname}", $this->username, $this->password);
            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }
}