<?php
// inc/config.php - Simplified Config (No DB, Only Gumroad)

// Load a .env file from the project root into the environment, if one
// exists. This is what makes a .env file in the project root actually work:
// PHP does not read .env files on its own, so without this loader the SMTP
// settings in .env were silently ignored and email fell back to mail().
// Real server environment variables always win over .env values, and
// values from .env are never logged or output anywhere.
(function () {
    $envFile = __DIR__ . '/../.env';
    if (!is_readable($envFile)) {
        return;
    }
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return;
    }
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') {
            continue;
        }
        if (strpos($line, 'export ') === 0) {
            $line = trim(substr($line, 7));
        }
        $pos = strpos($line, '=');
        if ($pos === false) {
            continue;
        }
        $key = trim(substr($line, 0, $pos));
        $value = trim(substr($line, $pos + 1));
        if (strlen($value) >= 2) {
            $first = $value[0];
            $last = $value[strlen($value) - 1];
            if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                $value = substr($value, 1, -1);
            }
        }
        if ($key === '' || getenv($key) !== false) {
            continue;
        }
        putenv($key . '=' . $value);
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
})();

define('SITE_URL', 'https://vinunified.store');
define('SITE_NAME', 'VinUnified');
define('ADMIN_EMAIL', 'rmoto7817@gmail.com'); // ✅ New admin email

// ============================================
// SMTP (optional, but recommended on the live host)
// Fill these in on the live server, or set SMTP_HOST / SMTP_USER /
// SMTP_PASS / SMTP_PORT / SMTP_ENCRYPTION / SMTP_FROM as server
// environment variables (MAIL_HOST / MAIL_USERNAME / MAIL_PASSWORD /
// MAIL_PORT / MAIL_ENCRYPTION / MAIL_FROM / MAIL_FROM_NAME are also
// accepted). Do NOT commit a real password to Git.
// For Google SMTP: host smtp.gmail.com, port 465, encryption ssl,
// username = the Gmail address, password = a Gmail app password, and
// From = the same Gmail address (Gmail rejects any other From).
// When no SMTP username/password are set, send-email.php falls back
// to PHP mail().
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