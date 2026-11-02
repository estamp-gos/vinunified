<?php
// /patchwebsite/customerpanel/profile.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'includes/config.php';
require_once 'includes/header.php';

$db = getDB();
$userId = $_SESSION['user_id'];

$message = '';
$error = '';

$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $phone = sanitize($_POST['phone']);
    $email = sanitize($_POST['email']);
    
    if ($email !== $user['email']) {
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $userId]);
        if ($stmt->fetch()) {
            $error = 'This email is already taken.';
        }
    }
    
    if (empty($error)) {
        $stmt = $db->prepare("UPDATE users SET name = ?, phone = ?, email = ? WHERE id = ?");
        $stmt->execute([$name, $phone, $email, $userId]);
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
        $message = 'Profile updated successfully!';
        $user = getUserById($userId);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    
    if ($currentPassword !== $user['password']) {
        $error = 'Current password is incorrect.';
    } elseif (strlen($newPassword) < 6) {
        $error = 'New password must be at least 6 characters.';
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } else {
        $stmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$newPassword, $userId]);
        $message = 'Password changed successfully!';
        $user = getUserById($userId);
    }
}

$pageTitle = 'Profile Settings';
$currentPage = 'profile.php';
include 'includes/sidebar.php';
?>

<div class="customer-content">
    <div class="welcome-section">
        <div>
            <h1>Profile Settings</h1>
            <p class="text-muted">Manage your account details</p>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="alert-modern success"><?php echo $message; ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert-modern error"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="profile-grid">
        <!-- Profile Info -->
        <div class="profile-card">
            <div class="card-header-custom">
                <i class="fas fa-user text-cyan"></i>
                <h5>Profile Information</h5>
            </div>
            <div class="card-body-custom">
                <form method="POST">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                    </div>
                    <button type="submit" class="btn-primary w-full">
                        <i class="fas fa-save"></i> Update Profile
                    </button>
                </form>
            </div>
        </div>

        <!-- Change Password -->
        <div class="profile-card">
            <div class="card-header-custom">
                <i class="fas fa-key text-magenta"></i>
                <h5>Change Password</h5>
            </div>
            <div class="card-body-custom">
                <form method="POST">
                    <input type="hidden" name="change_password" value="1">
                    <div class="form-group">
                        <label>Current Password</label>
                        <input type="password" name="current_password" required>
                    </div>
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" name="new_password" placeholder="Min 6 characters" required>
                    </div>
                    <div class="form-group">
                        <label>Confirm New Password</label>
                        <input type="password" name="confirm_password" required>
                    </div>
                    <button type="submit" class="btn-outline w-full">
                        <i class="fas fa-key"></i> Change Password
                    </button>
                </form>
            </div>
        </div>

        <!-- Account Info -->
        <div class="profile-card full-width">
            <div class="card-header-custom">
                <i class="fas fa-info-circle text-cyan"></i>
                <h5>Account Information</h5>
            </div>
            <div class="card-body-custom">
                <div class="info-grid">
                    <div class="info-item">
                        <span class="label">Member Since</span>
                        <span class="value"><?php echo formatDate($user['created_at']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Role</span>
                        <span class="value badge-role"><?php echo ucfirst($user['role']); ?></span>
                    </div>
                    <div class="info-item">
                        <span class="label">Total Orders</span>
                        <span class="value"><?php echo getCustomerOrderCount(); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>