<?php
// /patchwebsite/login.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';
require_once 'includes/security.php';

// If already logged in, redirect
if (isLoggedIn()) {
    if (isAdmin()) {
        redirect(ADMIN_URL);
    } else {
        redirect(CUSTOMER_URL);
    }
}

$error = '';
$email = '';

// Check for rate limiting
if (!checkRateLimit('login', 10, 300)) {
    $error = 'Too many login attempts. Please wait 5 minutes before trying again.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($error)) {
    // Verify CSRF
    if (!verifyCSRF()) {
        $error = 'Security validation failed. Please try again.';
    } else {
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']) ? true : false;

        if (empty($email) || empty($password)) {
            $error = 'Please fill in all fields.';
        } else {
            $result = loginUser($email, $password, $remember);
            
            if ($result['success']) {
                regenerateSession();
                if ($result['user']['role'] === 'admin') {
                    redirect(ADMIN_URL);
                } else {
                    redirect(CUSTOMER_URL);
                }
            } else {
                $error = $result['message'];
            }
        }
    }
}

$pageTitle = 'Login - ' . SITE_NAME;

include 'includes/header.php';
?>

<section class="auth-page">
    <div class="container">
        <div class="auth-card">
            <div class="logo-text"><?php echo SITE_NAME; ?> <span>•</span></div>
            <h4>Welcome Back</h4>
            <p>Sign in to your account</p>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php displayFlash(); ?>

            <form method="POST">
                <?php echo csrfField(); ?>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="you@example.com" 
                           value="<?php echo htmlspecialchars($email); ?>" required autofocus>
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <input type="checkbox" name="remember" id="remember">
                        <label for="remember" class="text-muted small ms-1">Remember me</label>
                    </div>
                    <a href="<?php echo SITE_URL; ?>forgot-password" class="text-cyan small">Forgot Password?</a>
                </div>
                <button type="submit" class="btn-hero-primary w-100">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>
            <p class="mt-3 text-center">Don't have an account? <a href="<?php echo SITE_URL; ?>register">Sign Up</a></p>
            <p class="mt-2 text-center"><a href="<?php echo SITE_URL; ?>">&larr; Back to Home</a></p>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>