<?php
namespace app\config;

class Database {
    private $host = 'localhost';
    private $port = '3306'; // Default MySQL port
    private $db_name = 'smart_ticketing';
    private $username = 'root';
    private $password = 'promise'; // Your MySQL password
    public $conn;

    public function getConnection() {
        $this->conn = null;
        
        try {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db_name}";
            $this->conn = new \PDO($dsn, $this->username, $this->password);
            //$dsn -> data source name
            // Set PDO attributes for proper error handling , the \ before the PDO (\PDO) shows that this class is in another file 
            $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            
            // Set character encoding
            $this->conn->exec("SET NAMES 'utf8'");
            $this->conn->exec("SET CHARACTER SET utf8");
            
        } catch(\PDOException $e) {
            // More detailed error message
            echo "Connection failed: " . $e->getMessage();
            error_log("Database error: " . $e->getMessage());
        }
        
        return $this->conn;
    }
}