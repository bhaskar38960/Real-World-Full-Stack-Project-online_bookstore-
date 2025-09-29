<?php
/**
 * Database Class
 * Handles the PDO connection and basic query execution.
 */
class Database {
    private static $instance = null;
    private $conn;

    /**
     * Private constructor to prevent direct instantiation (Singleton pattern).
     */
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME;
            $this->conn = new PDO($dsn, DB_USER, DB_PASS);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // In a real application, log the error and show a generic message.
            die("Connection failed: " . $e->getMessage());
        }
    }

    /**
     * Gets the single instance of the Database class (Singleton).
     * @return Database
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    /**
     * Gets the underlying PDO connection object.
     * @return PDO
     */
    public function getConnection() {
        return $this->conn;
    }

    /**
     * Executes a SELECT query and returns the results.
     * @param string $sql The SQL query.
     * @param array $params Optional array of parameters for prepared statements.
     * @return array The fetched data.
     */
    public function query($sql, $params = []) {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Executes an INSERT, UPDATE, or DELETE query.
     * @param string $sql The SQL query.
     * @param array $params Optional array of parameters for prepared statements.
     * @return int The number of affected rows.
     */
    public function execute($sql, $params = []) {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /**
     * Returns the ID of the last inserted row.
     * @return string
     */
    public function lastInsertId() {
        return $this->conn->lastInsertId();
    }
}
?>
