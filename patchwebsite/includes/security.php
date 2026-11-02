<?php
// /patchwebsite/includes/security.php

// ============================================
// SECURITY FUNCTIONS
// ============================================

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (empty($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    if (empty($_SESSION[CSRF_TOKEN_NAME]) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

/**
 * CSRF input field
 */
function csrfField() {
    $token = generateCSRFToken();
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

/**
 * Verify CSRF from POST
 */
function verifyCSRF() {
    if (!isset($_POST['csrf_token'])) {
        return false;
    }
    return verifyCSRFToken($_POST['csrf_token']);
}

// ============================================
// XSS PROTECTION
// ============================================

/**
 * Escape output for HTML
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Escape output for JavaScript
 */
function jsEscape($string) {
    return json_encode($string);
}

/**
 * Escape output for URL
 */
function urlEscape($string) {
    return urlencode($string);
}

// ============================================
// SQL INJECTION PROTECTION
// ============================================

/**
 * Sanitize input for SQL (use prepared statements instead)
 */
function sanitizeInput($input) {
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    return trim($input);
}

// ============================================
// HEADER SECURITY
// ============================================

/**
 * Set security headers
 */
function setSecurityHeaders() {
    // Prevent XSS
    header("X-XSS-Protection: 1; mode=block");
    
    // Prevent MIME type sniffing
    header("X-Content-Type-Options: nosniff");
    
    // Frame options
    header("X-Frame-Options: SAMEORIGIN");
    
    // Referrer policy
    header("Referrer-Policy: strict-origin-when-cross-origin");
    
    // HSTS (enable only on HTTPS)
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
    }
    
    // Content Security Policy (basic)
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; img-src 'self' data: https://img.stitchnhide.com; font-src 'self' https://cdnjs.cloudflare.com; connect-src 'self'; frame-src 'self' https://www.tawk.to;");
}

// ============================================
// RATE LIMITING
// ============================================

/**
 * Check rate limit
 */
function checkRateLimit($key, $limit = 60, $window = 60) {
    $bucket = $_SESSION['rate_limit'][$key] ?? ['count' => 0, 'reset' => time() + $window];
    
    if (time() > $bucket['reset']) {
        $bucket = ['count' => 0, 'reset' => time() + $window];
    }
    
    if ($bucket['count'] >= $limit) {
        return false;
    }
    
    $bucket['count']++;
    $_SESSION['rate_limit'][$key] = $bucket;
    return true;
}

// ============================================
// INPUT VALIDATION
// ============================================

/**
 * Validate and sanitize email
 */
function validateEmailStrict($email) {
    $email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate phone number
 */
function validatePhoneStrict($phone) {
    // USA phone validation
    return preg_match('/^\+?1?[\s\-]?\(?[0-9]{3}\)?[\s\-]?[0-9]{3}[\s\-]?[0-9]{4}$/', $phone);
}

/**
 * Validate URL
 */
function validateUrlStrict($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

// ============================================
// FILE SECURITY - MAIN VALIDATION FUNCTION
// ============================================

/**
 * Validate file upload - SINGLE DEFINITION
 * NOTE: getFileExtension(), getFileSize() are in functions.php
 */
function validateFileUpload($file) {
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors = [
            UPLOAD_ERR_INI_SIZE => 'File is larger than server upload limit.',
            UPLOAD_ERR_FORM_SIZE => 'File is larger than form upload limit.',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded.',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'No temporary folder found.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION => 'File upload stopped by extension.'
        ];
        return ['valid' => false, 'error' => $errors[$file['error']] ?? 'Unknown upload error.'];
    }
    
    // Check file size - uses getFileSize() from functions.php
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['valid' => false, 'error' => 'File is too large. Maximum size is ' . getFileSize(MAX_FILE_SIZE)];
    }
    
    // Check extension - uses getFileExtension() from functions.php
    $ext = getFileExtension($file['name']);
    if (!in_array($ext, ALLOWED_EXTENSIONS)) {
        return ['valid' => false, 'error' => 'File type not allowed. Supported: ' . implode(', ', ALLOWED_EXTENSIONS)];
    }
    
    // Check MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mime, ALLOWED_MIME_TYPES)) {
        return ['valid' => false, 'error' => 'Invalid file type. MIME: ' . $mime];
    }
    
    return ['valid' => true];
}

// ============================================
// SESSION SECURITY
// ============================================

/**
 * Regenerate session ID to prevent fixation
 */
function regenerateSession() {
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
    }
}

/**
 * Check session timeout
 */
function checkSessionTimeout() {
    if (isset($_SESSION['logged_in_at'])) {
        if (time() - $_SESSION['logged_in_at'] > SESSION_LIFETIME) {
            logoutUser();
            return false;
        }
        $_SESSION['logged_in_at'] = time();
    }
    return true;
}

// ============================================
// INITIALIZE SECURITY
// ============================================

// Set security headers (call at start of each page)
// setSecurityHeaders();

// Regenerate session on login
// regenerateSession();
?>