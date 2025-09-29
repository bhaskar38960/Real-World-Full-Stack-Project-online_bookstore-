<?php
require_once '../includes/config.php';
global $path_prefix;

// Log the user out
User::logout();

// Redirect back to the homepage
redirect('index.php');
?>