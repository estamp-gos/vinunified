<?php
// /patchwebsite/register.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';
require_once 'includes/security.php';
require_once 'includes/mail.php';
require_once 'includes/validation.php';  // ✅ ADD THIS LINE

if (isLoggedIn()) {
    if (isAdmin()) {
        redirect(ADMIN_URL);
    } else {
        redirect(CUSTOMER_URL);
    }
}

$error = '';
$success = '';
$formData = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF
    if (!verifyCSRF()) {
        $error = 'Security validation failed. Please try again.';
    } else {
        $formData = [
            'name' => sanitize($_POST['name'] ?? ''),
            'email' => sanitize($_POST['email'] ?? ''),
            'phone' => sanitize($_POST['phone'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'confirm_password' => $_POST['confirm_password'] ?? ''
        ];

        // Validate
        $validationErrors = validateRegistration($formData);
        
        if (!empty($validationErrors)) {
            $error = implode('<br>', $validationErrors);
        } else {
            $result = registerUser(
                $formData['name'],
                $formData['email'],
                $formData['password'],
                $formData['phone']
            );
            
            if ($result['success']) {
                // Send welcome email
                sendWelcomeEmail($formData['email'], $formData['name']);
                
                setFlash('success', 'Account created successfully! Welcome to PunchyArts.');
                redirect(CUSTOMER_URL);
            } else {
                $error = $result['message'];
            }
        }
    }
}

$pageTitle = 'Sign Up - ' . SITE_NAME;

include 'includes/header.php';
?>

<section class="auth-page">
    <div class="container">
        <div class="auth-card">
            <div class="logo-text"><?php echo SITE_NAME; ?> <span>•</span></div>
            <h4>Create Account</h4>
            <p>Join PunchyArts today</p>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="POST">
                <?php echo csrfField(); ?>
                <div class="form-group">
                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="Your name" 
                           value="<?php echo htmlspecialchars($formData['name'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" placeholder="you@example.com" 
                           value="<?php echo htmlspecialchars($formData['email'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <input type="tel" name="phone" class="form-control" placeholder="+1 (800) 123-4567" 
                           value="<?php echo htmlspecialchars($formData['phone'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control" placeholder="Min 6 characters" required>
                    <small class="text-muted">Must contain at least 1 uppercase, 1 lowercase, and 1 number.</small>
                </div>
                <div class="form-group">
                    <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                    <input type="password" name="confirm_password" class="form-control" placeholder="Confirm password" required>
                </div>
                <button type="submit" class="btn-hero-primary w-100">
                    <i class="fas fa-user-plus"></i> Sign Up
                </button>
            </form>
            <p class="mt-3 text-center">Already have an account? <a href="<?php echo SITE_URL; ?>login">Login</a></p>
            <p class="mt-2 text-center"><a href="<?php echo SITE_URL; ?>">&larr; Back to Home</a></p>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>