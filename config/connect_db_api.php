<?php

class Database
{
    private $host = "171.100.56.194";
    private $port = "3307";  // กำหนดพอร์ตเป็น 3307
    private $db_name = "sac_lotto";
    private $username = "myadmin";
    private $password = "myadmin";
    public $conn;

    public function getConnection()
    {
        $this->conn = null;

        try {
            // เพิ่มพอร์ตใน DSN
            $this->conn = new PDO("mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}

