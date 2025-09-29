<?php
// Require login
function requireLogin() {
    if (!isLoggedIn()) {
        setFlashMessage('Please login to continue', 'error');
        redirect('/pages/login.php');
    }
}

// Require admin
function requireAdmin() {
    if (!isAdmin()) {
        setFlashMessage('Access denied. Admin privileges required.', 'error');
        redirect('/index.php');
    }
}

// Redirect if logged in
function redirectIfLoggedIn() {
    if (isLoggedIn()) {
        redirect('/index.php');
    }
}
?>