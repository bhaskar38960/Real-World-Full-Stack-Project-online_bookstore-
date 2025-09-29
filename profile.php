<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

requireLogin();

$pageTitle = 'My Profile - ' . SITE_NAME;
$user = new User();
$userId = getCurrentUserId();
$userData = $user->getUserById($userId);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $fullName = sanitize($_POST['full_name']);
        $phone = sanitize($_POST['phone']);
        $address = sanitize($_POST['address']);
        
        if (empty($fullName)) {
            $errors[] = 'Full name is required';
        }
        
        if (empty($errors)) {
            $data = [
                'full_name' => $fullName,
                'phone' => $phone,
                'address' => $address
            ];
            
            if ($user->updateProfile($userId, $data)) {
                $_SESSION['full_name'] = $fullName;
                setFlashMessage('Profile updated successfully', 'success');
                redirect('/pages/profile.php');
            } else {
                $errors[] = 'Failed to update profile';
            }
        }
    }
    
    if (isset($_POST['change_password'])) {
        $currentPassword = $_POST['current_password'];
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];
        
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $errors[] = 'All password fields are required';
        } elseif (strlen($newPassword) < 6) {
            $errors[] = 'New password must be at least 6 characters';
        } elseif ($newPassword !== $confirmPassword) {
            $errors[] = 'New passwords do not match';
        } else {
            // Verify current password
            if ($user->login($userData['username'], $currentPassword)) {
                if ($user->updatePassword($userId, $newPassword)) {
                    setFlashMessage('Password changed successfully', 'success');
                    redirect('/pages/profile.php');
                } else {
                    $errors[] = 'Failed to change password';
                }
            } else {
                $errors[] = 'Current password is incorrect';
            }
        }
    }
}

include '../includes/header.php';
?>

<div class="profile-page">
    <h2>My Profile</h2>
    
    <?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <?php foreach ($errors as $error): ?>
            <p><?php echo $error; ?></p>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    
    <div class="profile-container">
        <div class="profile-section">
            <h3>Profile Information</h3>
            <form method="POST" action="">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" value="<?php echo htmlspecialchars($userData['username']); ?>" readonly>
                </div>
                
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" value="<?php echo htmlspecialchars($userData['email']); ?>" readonly>
                </div>
                
                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="full_name" value="<?php echo htmlspecialchars($userData['full_name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Phone</label>
                    <input type="tel" name="phone" value="<?php echo htmlspecialchars($userData['phone'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" rows="4"><?php echo htmlspecialchars($userData['address'] ?? ''); ?></textarea>
                </div>
                
                <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
            </form>
        </div>
        
        <div class="profile-section">
            <h3>Change Password</h3>
            <form method="POST" action="">
                <div class="form-group">
                    <label>Current Password *</label>
                    <input type="password" name="current_password" required>
                </div>
                
                <div class="form-group">
                    <label>New Password *</label>
                    <input type="password" name="new_password" required>
                </div>
                
                <div class="form-group">
                    <label>Confirm New Password *</label>
                    <input type="password" name="confirm_password" required>
                </div>
                
                <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
            </form>
        </div>
        
        <div class="profile-section">
            <h3>Account Information</h3>
            <p><strong>Member Since:</strong> <?php echo date('F j, Y', strtotime($userData['created_at'])); ?></p>
            <p><strong>Account Type:</strong> <?php echo $userData['is_admin'] ? 'Administrator' : 'Customer'; ?></p>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>