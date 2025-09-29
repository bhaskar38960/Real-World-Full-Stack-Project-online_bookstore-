<?php
require_once '../includes/config.php';
// Include the main header but ensure only admins can access
require_once '../includes/header.php';

// Check if user is logged in AND is an admin
if (!User::isLoggedIn() || !User::isAdmin()) {
    // Redirect non-admins to the home page or login page
    redirect('../pages/login.php');
}

// In a real scenario, you would calculate these numbers
$db = Database::getInstance();
$total_users = $db->query("SELECT COUNT(*) as count FROM users")[0]['count'];
$total_books = $db->query("SELECT COUNT(*) as count FROM books")[0]['count'];
$pending_orders = $db->query("SELECT COUNT(*) as count FROM orders WHERE status = 'pending'")[0]['count'];
?>

<section class="admin-dashboard" style="padding: 2rem 0;">
    <h1 style="font-size: 2.5rem; margin-bottom: 1.5rem; color: var(--secondary-color);">Admin Dashboard</h1>
    <p style="margin-bottom: 2rem;">Welcome, <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?>. Manage the bookstore operations here.</p>

    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
        
        <div class="stat-card" style="background-color: #fff; padding: 1.5rem; border-radius: 8px; box-shadow: var(--shadow); border-left: 5px solid var(--primary-color);">
            <h3 style="font-size: 1.25rem; color: #6b7280; margin-bottom: 0.5rem;">Total Users</h3>
            <p style="font-size: 2rem; font-weight: bold;"><?= $total_users ?></p>
            <a href="users.php" style="color: var(--primary-color); text-decoration: none; font-size: 0.9rem;">View Users</a>
        </div>
        
        <div class="stat-card" style="background-color: #fff; padding: 1.5rem; border-radius: 8px; box-shadow: var(--shadow); border-left: 5px solid var(--secondary-color);">
            <h3 style="font-size: 1.25rem; color: #6b7280; margin-bottom: 0.5rem;">Total Books</h3>
            <p style="font-size: 2rem; font-weight: bold;"><?= $total_books ?></p>
            <a href="books.php" style="color: var(--secondary-color); text-decoration: none; font-size: 0.9rem;">Manage Books</a>
        </div>
        
        <div class="stat-card" style="background-color: #fff; padding: 1.5rem; border-radius: 8px; box-shadow: var(--shadow); border-left: 5px solid #ef4444;">
            <h3 style="font-size: 1.25rem; color: #6b7280; margin-bottom: 0.5rem;">Pending Orders</h3>
            <p style="font-size: 2rem; font-weight: bold;"><?= $pending_orders ?></p>
            <a href="orders.php" style="color: #ef4444; text-decoration: none; font-size: 0.9rem;">View Orders</a>
        </div>
    </div>
</section>

<?php
require_once '../includes/footer.php';
?>
