<?php
// inc/config.php - Simplified Config (No DB, Only Gumroad)

define('SITE_URL', 'https://vinunified.store');
define('SITE_NAME', 'VinUnified');
define('ADMIN_EMAIL', 'rmoto7817@gmail.com'); // ✅ New admin email

// ============================================
// SMTP (optional, but recommended on the live host)
// Fill these in on the live server, or set SMTP_HOST / SMTP_USER /
// SMTP_PASS / SMTP_PORT / SMTP_ENCRYPTION / SMTP_FROM as server
// environment variables. Do NOT commit a real password to Git.
// When these are empty, send-email.php falls back to PHP mail().
// ============================================
define('SMTP_HOST', '');
define('SMTP_PORT', 465);
define('SMTP_USER', '');
define('SMTP_PASS', '');
define('SMTP_FROM', 'support@vinunified.store');
define('SMTP_ENCRYPTION', 'ssl'); // 'ssl' for port 465, 'tls' for port 587

// ============================================
// GUMROAD PAYMENT LINKS
// ============================================
define('PAYMENT_BASIC', 'https://gumroad.com/checkout?_gl=1*1u1gg8z*_ga*NTQxMDEyMjQ5LjE3OTM2MjE5MzU.*_ga_6LJN6D94N6*czE3OTM2MjE5MzQkbzEkZzEkdDE3OTM2MjE5NzAkajI0JGwwJGgw');   // £25
define('PAYMENT_PREMIUM', 'https://gumroad.com/checkout'); // £55

// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('Asia/Karachi');

// Simple logging function
function logError($message) {
    $logFile = __DIR__ . '/../logs/error.log';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message" . PHP_EOL, FILE_APPEND);
}
?>