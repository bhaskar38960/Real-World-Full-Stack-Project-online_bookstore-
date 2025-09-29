<?php
/**
 * User Class
 * Handles user authentication (register, login, logout).
 */
class User {
    private $db;
    private $conn;
    private $table_name = "users";

    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }

    /**
     * Registers a new user.
     * @param string $username
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function register($username, $email, $password) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $sql = "INSERT INTO " . $this->table_name . " (username, email, password) VALUES (?, ?, ?)";

        try {
            $this->db->execute($sql, [$username, $email, $hashed_password]);
            return true;
        } catch (PDOException $e) {
            // Handle duplicate entry (username/email already exists)
            error_log("User registration failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Logs in a user.
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function login($email, $password) {
        $sql = "SELECT id, password, username, role FROM " . $this->table_name . " WHERE email = ?";
        $user_data = $this->db->query($sql, [$email]);

        if (!empty($user_data)) {
            $user = $user_data[0];
            if (password_verify($password, $user['password'])) {
                // Login successful, set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                return true;
            }
        }
        return false;
    }

    /**
     * Logs out the current user.
     */
    public static function logout() {
        session_unset();
        session_destroy();
    }

    /**
     * Checks if the user is logged in.
     * @return bool
     */
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    /**
     * Checks if the logged-in user is an admin.
     * @return bool
     */
    public static function isAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }

    /**
     * Gets user data by ID.
     * @param int $id
     * @return array|null
     */
    public function getById($id) {
        $sql = "SELECT id, username, email, role FROM " . $this->table_name . " WHERE id = ?";
        $result = $this->db->query($sql, [$id]);
        return $result ? $result[0] : null;
    }
}
?>
