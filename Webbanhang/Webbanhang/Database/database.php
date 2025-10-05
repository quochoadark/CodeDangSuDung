<?php
class database {
    private $host = "localhost";
    private $username = "root";
    private $password = "Hoa01218816812";
    private $dbname = "webnangcao";
    private $port = 3306;
    public $conn;

    public function __construct() {
        $this->conn = new mysqli(
            $this->host,
            $this->username,
            $this->password,
            $this->dbname,
            $this->port
        );

        if ($this->conn->connect_error) {
            die("Kết nối thất bại: " . $this->conn->connect_error);
        }
    }
}
?>
