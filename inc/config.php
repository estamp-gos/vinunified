<?php
// inc/config.php - Simplified Config (No DB, Only Freemius)

define('SITE_URL', 'https://vinunified.store');
define('SITE_NAME', 'VinUnified');
define('ADMIN_EMAIL', 'rmoto7817@gmail.com'); // ✅ New admin email

// ============================================
// FREEMIUS PAYMENT LINKS (NO PADDLE / DOCUKIT)
// ============================================
define('PAYMENT_BASIC', 'https://checkout.freemius.com/product/27824/plan/49921/');   // $30
define('PAYMENT_PREMIUM', 'https://checkout.freemius.com/product/27824/plan/45987/'); // $78
define('PAYMENT_ULTIMATE', 'https://checkout.freemius.com/product/27824/plan/47537/'); // $50

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