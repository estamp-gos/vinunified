<?php
// /patchwebsite/includes/config.php

// ============================================
// DATABASE CONFIGURATION - USA BRAND
// ============================================

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'u854130977_forpatch');
define('DB_USER', 'u854130977_adminsaifk');
define('DB_PASS', 'Admin123#sgjfdsgjhf782634%&^%&^');

// ============================================
// SITE URL CONFIGURATION
// ============================================
define('SITE_URL', 'https://vinunified.store/patchwebsite/');
define('ADMIN_URL', SITE_URL . 'adminpanel/');
define('CUSTOMER_URL', SITE_URL . 'customerpanel/');
define('ASSETS_URL', SITE_URL . 'assets/');
define('UPLOADS_URL', SITE_URL . 'uploads/');

// ============================================
// ADMIN & CONTACT - FIXED
// ============================================
define('ADMIN_EMAIL', 'patch@vinunified.store');
define('ADMIN_NAME', 'PunchyArts Admin');

// USA Contact
define('SITE_PHONE_USA', '+1 (800) 123-4567');
define('SITE_PHONE_USA_ALT', '+1 (310) 555-7890');
define('SITE_ADDRESS_USA', '123 Creative Blvd, Los Angeles, CA 90001, USA');
define('SITE_TIMEZONE', 'America/New_York');

// ============================================
// SMTP CONFIGURATION (Titan Mail)
// ============================================
define('SMTP_HOST', 'smtp.titan.email');
define('SMTP_PORT', 465);
define('SMTP_USER', 'patch@vinunified.store');
define('SMTP_PASS', 'invoraSol-2025$');
define('SMTP_FROM', 'patch@vinunified.store');
define('SMTP_FROM_NAME', 'PunchyArts');
define('SMTP_ENCRYPTION', 'ssl');

// ============================================
// FILE UPLOAD SETTINGS
// ============================================
define('MAX_FILE_SIZE', 50 * 1024 * 1024); // 50MB
define('MAX_FILES_PER_ORDER', 10);
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'pdf', 'ai', 'eps', 'psd', 'dst', 'pes', 'emb', 'cnd', 'zip', 'rar']);
define('ALLOWED_MIME_TYPES', [
    'image/jpeg', 'image/png', 'application/pdf', 
    'application/postscript', 'application/eps', 'image/vnd.adobe.photoshop',
    'application/zip', 'application/x-rar-compressed', 'application/x-rar'
]);

// ============================================
// UPLOAD DIRECTORIES - FIXED WITH ABSOLUTE PATHS
// ============================================
// Get the document root path
$rootPath = $_SERVER['DOCUMENT_ROOT'] . '/patchwebsite/';

define('UPLOAD_PATH_ARTWORK', $rootPath . 'uploads/artwork/');
define('UPLOAD_PATH_PRODUCTION', $rootPath . 'uploads/production/');
define('UPLOAD_PATH_PORTFOLIO', $rootPath . 'uploads/portfolio/');
define('UPLOAD_PATH_BLOG', $rootPath . 'uploads/blog/');
define('UPLOAD_PATH_TEMP', $rootPath . 'uploads/temp/');
define('UPLOAD_PATH_THUMBNAILS', $rootPath . 'uploads/thumbnails/');

// ============================================
// SECURITY
// ============================================
define('SALT_LENGTH', 32);
define('CSRF_TOKEN_NAME', 'csrf_token');
define('SESSION_LIFETIME', 3600 * 24 * 7); // 7 days
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_TIME', 900); // 15 minutes

// ============================================
// TIMEZONE & ERROR REPORTING
// ============================================
date_default_timezone_set(SITE_TIMEZONE);

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

// ============================================
// SESSION
// ============================================
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
    session_start();
}

// ============================================
// DATABASE CONNECTION FUNCTION
// ============================================
function getDB() {
    try {
        $conn = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_PERSISTENT => false
            ]
        );
        return $conn;
    } catch (PDOException $e) {
        error_log("Database Connection Failed: " . $e->getMessage());
        die("Database Connection Failed. Please try again later.");
    }
}

// ============================================
// SETTINGS FUNCTIONS
// ============================================
function getSetting($key) {
    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        return $result ? $result['setting_value'] : null;
    } catch (PDOException $e) {
        return null;
    }
}

function getAllSettings() {
    try {
        $db = getDB();
        $stmt = $db->query("SELECT setting_key, setting_value FROM settings");
        $settings = [];
        while ($row = $stmt->fetch()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    } catch (PDOException $e) {
        return [];
    }
}

// ============================================
// LOAD SITE SETTINGS
// ============================================
$settings = getAllSettings();

// USA Brand Settings (only define if not already defined)
if (!defined('SITE_NAME')) define('SITE_NAME', $settings['site_name'] ?? 'PunchyArts');
if (!defined('SITE_TAGLINE')) define('SITE_TAGLINE', $settings['site_tagline'] ?? 'Premium Embroidery & Vector Tracing Services - USA');
if (!defined('SITE_EMAIL')) define('SITE_EMAIL', $settings['site_email'] ?? ADMIN_EMAIL);
if (!defined('SITE_PHONE')) define('SITE_PHONE', $settings['site_phone'] ?? SITE_PHONE_USA);
if (!defined('SITE_ADDRESS')) define('SITE_ADDRESS', $settings['site_address'] ?? SITE_ADDRESS_USA);
if (!defined('FACEBOOK_URL')) define('FACEBOOK_URL', $settings['social_facebook'] ?? '#');
if (!defined('INSTAGRAM_URL')) define('INSTAGRAM_URL', $settings['social_instagram'] ?? '#');
if (!defined('YOUTUBE_URL')) define('YOUTUBE_URL', $settings['social_youtube'] ?? '#');
if (!defined('LINKEDIN_URL')) define('LINKEDIN_URL', $settings['social_linkedin'] ?? '#');
if (!defined('TWITTER_URL')) define('TWITTER_URL', $settings['social_twitter'] ?? '#');
if (!defined('META_DESCRIPTION')) define('META_DESCRIPTION', $settings['meta_description'] ?? 'PunchyArts - Professional Embroidery Digitizing, Vector Tracing, Character Art & Custom Patches - Based in USA');
if (!defined('META_KEYWORDS')) define('META_KEYWORDS', $settings['meta_keywords'] ?? 'embroidery digitizing, vector tracing, character art, custom patches, USA');

// Currency - Only define if not already defined
if (!defined('CURRENCY_SYMBOL')) define('CURRENCY_SYMBOL', '$');
if (!defined('CURRENCY_CODE')) define('CURRENCY_CODE', 'USD');

// ============================================
// CREATE UPLOAD DIRECTORIES IF NOT EXISTS
// ============================================
$uploadDirs = [
    UPLOAD_PATH_ARTWORK,
    UPLOAD_PATH_PRODUCTION,
    UPLOAD_PATH_PORTFOLIO,
    UPLOAD_PATH_BLOG,
    UPLOAD_PATH_TEMP,
    UPLOAD_PATH_THUMBNAILS
];

foreach ($uploadDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// ============================================
// LOGS DIRECTORY
// ============================================
if (!is_dir(__DIR__ . '/../logs')) {
    mkdir(__DIR__ . '/../logs', 0755, true);
}
?>