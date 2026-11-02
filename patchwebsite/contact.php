<?php
// /patchwebsite/contact.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/security.php';
require_once 'includes/mail.php';
require_once 'includes/validation.php';
require_once 'includes/upload.php';

$message = '';
$error = '';
$formData = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $error = 'Security validation failed. Please try again.';
        error_log("CSRF Validation Failed in contact.php");
    } else {
        $formData = [
            'name' => sanitize($_POST['name'] ?? ''),
            'email' => sanitize($_POST['email'] ?? ''),
            'phone' => sanitize($_POST['phone'] ?? ''),
            'subject' => sanitize($_POST['subject'] ?? ''),
            'message' => sanitize($_POST['message'] ?? '')
        ];

        // Handle file upload
        $uploadedFile = null;
        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = uploadFile($_FILES['attachment'], UPLOAD_PATH_TEMP);
            if ($uploadResult['success']) {
                $uploadedFile = [
                    'name' => $_FILES['attachment']['name'],
                    'tmp_name' => $uploadResult['path'],
                    'url' => SITE_URL . 'uploads/temp/' . $uploadResult['filename']
                ];
                error_log("File uploaded: " . $uploadResult['filename']);
            } else {
                $error = 'File upload error: ' . $uploadResult['error'];
                error_log("File upload error: " . $uploadResult['error']);
            }
        }

        // Validate
        $validationErrors = validateContact($formData);
        
        if (!empty($validationErrors)) {
            $error = implode('<br>', $validationErrors);
        } elseif (empty($error)) {
            // Send email to ADMIN
            $adminSent = sendContactMessageAdmin(
                $formData['name'],
                $formData['email'],
                $formData['subject'],
                $formData['message'],
                $uploadedFile
            );

            // Send copy to CUSTOMER
            $customerSent = sendContactMessageCustomer(
                $formData['name'],
                $formData['email'],
                $formData['subject'],
                $formData['message'],
                $uploadedFile
            );

            if ($adminSent || $customerSent) {
                $message = 'Thank you! Your message has been sent. We will get back to you within 24 hours. 🇺🇸';
                $formData = [];
                // ✅ FILE WILL NOT BE DELETED - IT STAYS IN TEMP FOLDER FOREVER
                // DO NOT delete temp file
                // if ($uploadedFile && file_exists($uploadedFile['tmp_name'])) {
                //     unlink($uploadedFile['tmp_name']);
                // }
            } else {
                $error = 'Sorry, there was an error sending your message. Please try again.';
                error_log("Email sending failed - Admin: " . ($adminSent ? 'OK' : 'FAIL') . ", Customer: " . ($customerSent ? 'OK' : 'FAIL'));
            }
        }
    }
}

$pageTitle = 'Contact - ' . SITE_NAME;
$activePage = 'contact';

include 'includes/header.php';
?>

<!-- Contact Section -->
<style>
    .pre-footer{ display: none !important;}
</style>
<section style="padding-top: 120px; padding-bottom: 80px;">
    <div class="container">
        <div class="section-title">
            <h2>Get In <span>Touch</span></h2>
            <p>We'd love to hear from you</p>
            <div class="line"></div>
        </div>

        <div class="row g-5">
            <!-- Contact Info -->
            <div class="col-lg-5">
                <div class="card-punchy">
                    <h4><i class="fas fa-map-marker-alt text-cyan"></i> Our Location</h4>
                    <p class="text-muted"><?php echo SITE_ADDRESS; ?></p>
                    
                    <h4 class="mt-4"><i class="fas fa-envelope text-cyan"></i> Email</h4>
                    <p class="text-muted"><?php echo SITE_EMAIL; ?></p>
                    
                    <h4 class="mt-4"><i class="fas fa-phone text-cyan"></i> Phone</h4>
                    <p class="text-muted"><?php echo SITE_PHONE; ?></p>
                    <p class="text-muted small"><?php echo SITE_PHONE_USA_ALT ?? '+1 (310) 555-7890'; ?></p>
                    
                    <h4 class="mt-4"><i class="fas fa-clock text-cyan"></i> Business Hours</h4>
                    <p class="text-muted">Monday - Friday: 9:00 AM - 6:00 PM EST</p>
                    <p class="text-muted">Saturday - Sunday: Closed</p>

                    <div class="mt-4">
                        <h4>Follow Us</h4>
                        <div class="social">
                            <a href="<?php echo FACEBOOK_URL; ?>" target="_blank"><i class="fab fa-facebook fa-2x me-2"></i></a>
                            <a href="<?php echo INSTAGRAM_URL; ?>" target="_blank"><i class="fab fa-instagram fa-2x me-2"></i></a>
                            <a href="<?php echo YOUTUBE_URL; ?>" target="_blank"><i class="fab fa-youtube fa-2x me-2"></i></a>
                            <a href="<?php echo LINKEDIN_URL ?? '#'; ?>" target="_blank"><i class="fab fa-linkedin fa-2x"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="col-lg-7">
                <div class="card-punchy">
                    <?php if ($message): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="fas fa-check-circle"></i> <?php echo $message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data" action="<?php echo SITE_URL; ?>contact">
                        <?php echo csrfField(); ?>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="Your name" 
                                       value="<?php echo htmlspecialchars($formData['name'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" placeholder="Your email" 
                                       value="<?php echo htmlspecialchars($formData['email'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="tel" name="phone" class="form-control" placeholder="+1 (800) 123-4567" 
                                       value="<?php echo htmlspecialchars($formData['phone'] ?? ''); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Subject <span class="text-danger">*</span></label>
                                <input type="text" name="subject" class="form-control" placeholder="Subject" 
                                       value="<?php echo htmlspecialchars($formData['subject'] ?? ''); ?>" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Message <span class="text-danger">*</span></label>
                                <textarea name="message" class="form-control" rows="6" placeholder="Your message..." required><?php echo htmlspecialchars($formData['message'] ?? ''); ?></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Attach File (optional)</label>
                                <input type="file" name="attachment" class="form-control" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.zip,.rar">
                                <small class="text-muted">Max 50MB. Supported: JPG, PNG, PDF, DOC, ZIP, RAR</small>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn-hero-primary w-100">
                                    <i class="fas fa-paper-plane"></i> Send Message
                                </button>
                            </div>
                            <div class="col-12">
                                <small class="text-muted">* Required fields. We'll respond within 24 hours.</small>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>