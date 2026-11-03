<?php
// process.php - Simplified (No DB, Direct Gumroad Redirect)

session_start();
require_once 'inc/config.php';
require_once 'inc/functions.php';
require_once 'inc/send-email.php';
require_once 'inc/country-detection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Sanitize data
$data = [
    'full_name' => sanitizeInput($_POST['full_name'] ?? ''),
    'email' => sanitizeInput($_POST['email'] ?? ''),
    'phone' => sanitizeInput($_POST['phone'] ?? ''),
    'vin' => strtoupper(sanitizeInput($_POST['vin'] ?? '')),
    'license_plate' => sanitizeInput($_POST['license_plate'] ?? ''),
    'vehicle_type' => sanitizeInput($_POST['vehicle_type'] ?? ''),
    'package' => sanitizeInput($_POST['package'] ?? ''),
    'country' => getUserCountry()
];

// Validation
if (empty($data['full_name']) || empty($data['email']) || empty($data['phone'])) {
    $_SESSION['error'] = 'Please fill all required fields';
    header('Location: index.php');
    exit;
}

if (empty($data['vin']) && empty($data['license_plate'])) {
    $_SESSION['error'] = 'Please enter either VIN or License Plate';
    header('Location: index.php');
    exit;
}

if (!isset($_POST['agreement'])) {
    $_SESSION['error'] = 'Please agree to the terms';
    header('Location: index.php');
    exit;
}

// ✅ Send email to admin ONLY (rmoto7817@gmail.com)
$emailSent = sendAdminNotification($data);

if (!$emailSent) {
    // sendAdminNotification() already logged the reason and saved the
    // submission to logs/submissions.log, so the entry is not lost.
    // The visitor is still redirected to checkout below.
    logError("Admin email failed, submission saved to fallback log for: " . $data['email']);
}

// ✅ Store in session (for reference)
$_SESSION['form_data'] = $data;

// ✅ Redirect to Gumroad based on package
$paymentUrl = '';
switch($data['package']) {
    case 'basic':
        $paymentUrl = PAYMENT_BASIC;    // £25
        break;
    case 'premium':
        $paymentUrl = PAYMENT_PREMIUM;  // £55
        break;
    default:
        $paymentUrl = PAYMENT_BASIC;
}

header('Location: ' . $paymentUrl);
exit;
?>