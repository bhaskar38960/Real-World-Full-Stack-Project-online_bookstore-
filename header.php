<?php
// Ensure config is loaded, which now defines $path_prefix
// Use relative path to includes folder
require_once __DIR__ . '/config.php';

// Access the global path prefix variable
global $path_prefix;

// Instantiate User class to access static methods if needed, though they can be called directly
$is_logged_in = User::isLoggedIn();
$is_admin = User::isAdmin();
$username = $_SESSION['username'] ?? 'Guest';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Bookstore | A great selection of books</title>
    <!-- CSS path fixed using $path_prefix -->
    <link rel="stylesheet" href="<?= $path_prefix ?>assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<header class="header">
    <div class="container">
        <nav class="nav">
            <!-- Logo Link -->
            <div class="logo"><a href="<?= $path_prefix ?>index.php">BookStore</a></div>

            <button class="menu-toggle" id="menu-toggle" aria-label="Toggle navigation">
                <i class="fas fa-bars"></i>
            </button>

            <div class="nav-links" id="nav-links">
                <!-- Primary Navigation -->
                <a href="<?= $path_prefix ?>index.php">Home</a>
                <a href="<?= $path_prefix ?>pages/books.php">Books</a>
                
                <?php if ($is_logged_in): ?>
                    <!-- Logged In Links -->
                    
                    <!-- Highlighted Cart Link -->
                    <a href="<?= $path_prefix ?>pages/cart.php" class="cart-link"><i class="fas fa-shopping-cart"></i> Cart</a>
                    
                    <!-- Highlighted Profile Link -->
                    <a href="<?= $path_prefix ?>pages/profile.php" class="profile-link">
                        <i class="fas fa-user-circle"></i> 
                        <?= htmlspecialchars($username) ?>
                    </a>
                    
                    <a href="<?= $path_prefix ?>pages/orders.php">My Orders</a>
                    
                    <?php if ($is_admin): ?>
                        <a href="<?= $path_prefix ?>admin/index.php" class="admin-link">Admin Panel</a>
                    <?php endif; ?>
                    <a href="<?= $path_prefix ?>pages/logout.php">Logout</a>
                <?php else: ?>
                    <!-- Guest Links -->
                    <a href="<?= $path_prefix ?>pages/cart.php" class="cart-link"><i class="fas fa-shopping-cart"></i> Cart</a>
                    <a href="<?= $path_prefix ?>pages/login.php">Login</a>
                    <a href="<?= $path_prefix ?>pages/register.php">Register</a>
                <?php endif; ?>
            </div>
        </nav>
    </div>
</header>

<main class="container">

<script>
    // Simple JavaScript for responsive menu toggle
    document.addEventListener('DOMContentLoaded', () => {
        const toggle = document.getElementById('menu-toggle');
        const links = document.getElementById('nav-links');

        if (toggle && links) {
            toggle.addEventListener('click', () => {
                links.classList.toggle('active');
            });
        }
    });
</script>
