<?php
// /patchwebsite/forgot-password.php
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';
require_once 'includes/security.php';
require_once 'includes/mail.php';

$message = '';
$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF
    if (!verifyCSRF()) {
        $error = 'Security validation failed. Please try again.';
    } else {
        $email = sanitize($_POST['email'] ?? '');

        if (empty($email)) {
            $error = 'Please enter your email address.';
        } elseif (!validateEmail($email)) {
            $error = 'Please enter a valid email address.';
        } else {
            $db = getDB();
            $stmt = $db->prepare("SELECT id, name FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user) {
                // Generate reset token
                $token = generateResetToken($email);
                $resetLink = SITE_URL . 'reset-password?email=' . urlencode($email) . '&token=' . $token;
                
                // Send reset email
                sendPasswordResetEmail($email, $user['name'], $resetLink);
                
                $message = 'Password reset link has been sent to your email. Please check your inbox.';
                $email = ''; // Clear email
            } else {
                $error = 'No account found with this email.';
            }
        }
    }
}

$pageTitle = 'Forgot Password - ' . SITE_NAME;

include 'includes/header.php';
?>

<section class="auth-page">
    <div class="container">
        <div class="auth-card">
            <div class="logo-text"><?php echo SITE_NAME; ?> <span>•</span></div>
            <h4>Forgot Password</h4>
            <p>Enter your email to reset your password</p>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if ($message): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle"></i> <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="POST">
                <?php echo csrfField(); ?>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="you@example.com" 
                           value="<?php echo htmlspecialchars($email); ?>" required autofocus>
                </div>
                <button type="submit" class="btn-hero-primary w-100">
                    <i class="fas fa-paper-plane"></i> Send Reset Link
                </button>
            </form>
            <p class="mt-3 text-center"><a href="<?php echo SITE_URL; ?>login">Back to Login</a></p>
            <p class="mt-2 text-center"><a href="<?php echo SITE_URL; ?>">&larr; Back to Home</a></p>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>