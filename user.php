<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

requireLogin();
requireAdmin();

$pageTitle = 'Manage Users - Admin';
$user = new User();

// Handle user deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $userId = intval($_POST['user_id']);
    if ($userId != getCurrentUserId()) {
        if ($user->deleteUser($userId)) {
            setFlashMessage('User deleted successfully', 'success');
            redirect('/admin/users.php');
        }
    } else {
        setFlashMessage('You cannot delete your own account', 'error');
    }
}

$users = $user->getAllUsers();

include '../includes/header.php';
?>

<div class="admin-page">
    <h2>Manage Users</h2>
    
    <div class="admin-nav">
        <a href="index.php">Dashboard</a>
        <a href="books.php">Manage Books</a>
        <a href="orders.php">Manage Orders</a>
        <a href="users.php" class="active">Manage Users</a>
    </div>
    
    <div class="table-section">
        <h3>All Users</h3>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Full Name</th>
                    <th>Role</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $userItem): ?>
                <tr>
                    <td><?php echo $userItem['id']; ?></td>
                    <td><?php echo htmlspecialchars($userItem['username']); ?></td>
                    <td><?php echo htmlspecialchars($userItem['email']); ?></td>
                    <td><?php echo htmlspecialchars($userItem['full_name']); ?></td>
                    <td>
                        <?php if ($userItem['is_admin']): ?>
                            <span class="role-badge admin">Admin</span>
                        <?php else: ?>
                            <span class="role-badge customer">Customer</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo date('M j, Y', strtotime($userItem['created_at'])); ?></td>
                    <td>
                        <?php if ($userItem['id'] != getCurrentUserId()): ?>
                            <form method="POST" action="" style="display:inline;">
                                <input type="hidden" name="user_id" value="<?php echo $userItem['id']; ?>">
                                <button type="submit" name="delete_user" class="btn btn-danger btn-sm" 
                                        onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>