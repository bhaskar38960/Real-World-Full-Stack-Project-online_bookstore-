<?php
/**
 * Configuration file for the Online Bookstore project.
 * Define database credentials and site-wide constants here.
 */

// Start session management
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- PATH FIXING LOGIC ---
// Determine the path prefix needed to get back to the root directory
$path_prefix = '';
// Get the path of the currently executing script
$current_script_path = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
// Check if the script is in a subdirectory (like 'pages/' or 'admin/')
if (strpos($current_script_path, '/pages/') !== false || strpos($current_script_path, '/admin/') !== false) {
    // If the script is in a first-level subdirectory, we need '../'
    $path_prefix = '../';
}
// ---------------------------------

// Database Credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // Default XAMPP username
define('DB_PASS', '');           // Default XAMPP password (empty)
define('DB_NAME', 'online_bookstore');

// Autoload function to automatically include class files
spl_autoload_register(function ($class_name) {
    // Path to classes is always relative to config.php
    $class_file = __DIR__ . '/../classes/' . $class_name . '.php';
    if (file_exists($class_file)) {
        require_once $class_file;
    }
});

// Utility function for redirection
function redirect($url) {
    // Use the global path prefix for safer redirection
    global $path_prefix;
    header("Location: " . $path_prefix . $url);
    exit();
}

// Include the authentication handler
require_once 'auth.php';
?>
