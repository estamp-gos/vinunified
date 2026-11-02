<?php
// /patchwebsite/includes/functions.php

// ============================================
// SANITIZATION & VALIDATION
// ============================================

function sanitize($input) {
    if (is_array($input)) {
        return array_map('sanitize', $input);
    }
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

function sanitizeEmail($email) {
    return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePhone($phone) {
    // Basic phone validation for USA numbers
    return preg_match('/^[\+]?[0-9\s\-\(\)]{10,15}$/', $phone);
}

function validateUrl($url) {
    return filter_var($url, FILTER_VALIDATE_URL);
}

// ============================================
// ORDER FUNCTIONS
// ============================================

function generateOrderNumber() {
    return 'PA-' . date('Y') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
}

function getOrderStatusLabel($status) {
    $labels = [
        'submitted' => 'Submitted',
        'reviewing' => 'Under Review',
        'quoted' => 'Quoted',
        'in_production' => 'In Production',
        'completed' => 'Completed',
        'shipped' => 'Shipped',
        'delivered' => 'Delivered'
    ];
    return $labels[$status] ?? ucfirst($status);
}

function getStatusBadge($status) {
    $badges = [
        'submitted' => 'bg-secondary',
        'reviewing' => 'bg-warning text-dark',
        'quoted' => 'bg-info text-dark',
        'in_production' => 'bg-primary',
        'completed' => 'bg-success',
        'shipped' => 'bg-purple',
        'delivered' => 'bg-success'
    ];
    return $badges[$status] ?? 'bg-secondary';
}

function getStatusColor($status) {
    $colors = [
        'submitted' => '#6c757d',
        'reviewing' => '#ffc107',
        'quoted' => '#0dcaf0',
        'in_production' => '#0d6efd',
        'completed' => '#198754',
        'shipped' => '#6f42c1',
        'delivered' => '#198754'
    ];
    return $colors[$status] ?? '#6c757d';
}

function getServiceName($type) {
    $names = [
        'digitizing' => 'Embroidery Digitizing',
        'vector' => 'Vector Tracing',
        'character' => 'Character Art',
        'patches' => 'Custom Patches'
    ];
    return $names[$type] ?? ucfirst($type);
}

function getServiceSlug($name) {
    $slugs = [
        'Embroidery Digitizing' => 'embroidery-digitizing',
        'Vector Tracing' => 'vector-tracing',
        'Character Art' => 'character-art',
        'Custom Patches' => 'custom-patches'
    ];
    return $slugs[$name] ?? strtolower(str_replace(' ', '-', $name));
}

function getPaymentBadge($status) {
    $badges = [
        'pending' => 'bg-warning text-dark',
        'paid' => 'bg-success',
        'failed' => 'bg-danger',
        'refunded' => 'bg-info'
    ];
    return $badges[$status] ?? 'bg-secondary';
}

function getPaymentLabel($status) {
    $labels = [
        'pending' => 'Pending',
        'paid' => 'Paid',
        'failed' => 'Failed',
        'refunded' => 'Refunded'
    ];
    return $labels[$status] ?? ucfirst($status);
}

// ============================================
// TEXT FUNCTIONS
// ============================================

function truncate($text, $length = 100, $suffix = '...') {
    $text = strip_tags($text);
    if (strlen($text) <= $length) return $text;
    return substr($text, 0, $length) . $suffix;
}

function excerpt($text, $length = 150) {
    return truncate($text, $length);
}

function slugify($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9\-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return trim($string, '-');
}

// ============================================
// DATE FUNCTIONS
// ============================================

function formatDate($date, $format = 'M d, Y') {
    if (!$date) return '-';
    return date($format, strtotime($date));
}

function formatDateTime($date) {
    if (!$date) return '-';
    return date('M d, Y h:i A', strtotime($date));
}

function formatDateUsa($date) {
    if (!$date) return '-';
    return date('F d, Y', strtotime($date));
}

function timeAgo($timestamp) {
    $diff = time() - strtotime($timestamp);
    $periods = ['second', 'minute', 'hour', 'day', 'week', 'month', 'year'];
    $lengths = [1, 60, 3600, 86400, 604800, 2630880, 31570560];
    
    for ($i = count($lengths) - 1; $i >= 0; $i--) {
        if ($diff >= $lengths[$i]) {
            $num = floor($diff / $lengths[$i]);
            return $num . ' ' . $periods[$i] . ($num > 1 ? 's' : '') . ' ago';
        }
    }
    return 'Just now';
}

// ============================================
// URL FUNCTIONS
// ============================================

function base_url($path = '') {
    return rtrim(SITE_URL, '/') . '/' . ltrim($path, '/');
}

function admin_url($path = '') {
    return rtrim(ADMIN_URL, '/') . '/' . ltrim($path, '/');
}

function customer_url($path = '') {
    return rtrim(CUSTOMER_URL, '/') . '/' . ltrim($path, '/');
}

function asset_url($path = '') {
    return rtrim(ASSETS_URL, '/') . '/' . ltrim($path, '/');
}

function redirect($url) {
    if (headers_sent()) {
        echo '<script>window.location.href="' . $url . '";</script>';
    } else {
        header('Location: ' . $url);
    }
    exit;
}

function redirectBack() {
    $url = $_SERVER['HTTP_REFERER'] ?? SITE_URL;
    redirect($url);
}

function currentUrl() {
    return (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

// ============================================
// FLASH MESSAGES
// ============================================

function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message, 'time' => time()];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function hasFlash() {
    return isset($_SESSION['flash']);
}

function displayFlash() {
    $flash = getFlash();
    if ($flash) {
        $alertClass = match($flash['type']) {
            'success' => 'alert-success',
            'error' => 'alert-danger',
            'warning' => 'alert-warning',
            default => 'alert-info'
        };
        echo '<div class="alert ' . $alertClass . ' alert-dismissible fade show" role="alert">';
        echo $flash['message'];
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        echo '</div>';
    }
}

// ============================================
// AUTH FUNCTIONS
// ============================================

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function isCustomer() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'customer';
}

function getUserName() {
    return $_SESSION['user_name'] ?? 'User';
}

function getUserId() {
    return $_SESSION['user_id'] ?? null;
}

function getUserEmail() {
    return $_SESSION['user_email'] ?? null;
}

function getUserRole() {
    return $_SESSION['user_role'] ?? null;
}

function requireLogin() {
    if (!isLoggedIn()) {
        setFlash('warning', 'Please login to access this page.');
        redirect(SITE_URL . 'login');
        exit;
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        setFlash('error', 'You do not have permission to access this page.');
        redirect(SITE_URL);
        exit;
    }
}

function requireCustomer() {
    requireLogin();
    if (!isCustomer()) {
        setFlash('error', 'You do not have permission to access this page.');
        redirect(SITE_URL);
        exit;
    }
}

// ============================================
// FILE FUNCTIONS - KEEP THESE (SINGLE DEFINITION)
// ============================================

function getFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' B';
    }
}

function getFileExtension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

function isAllowedFile($filename) {
    $ext = getFileExtension($filename);
    return in_array($ext, ALLOWED_EXTENSIONS);
}

function generateUniqueFileName($filename) {
    $ext = getFileExtension($filename);
    return uniqid() . '_' . time() . '.' . $ext;
}

function deleteFile($path) {
    if (file_exists($path) && is_file($path)) {
        return unlink($path);
    }
    return false;
}

// ============================================
// MISC FUNCTIONS
// ============================================

function getClientIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

function getUserAgent() {
    return $_SERVER['HTTP_USER_AGENT'] ?? '';
}

function isAjax() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

function formatPrice($amount) {
    return CURRENCY_SYMBOL . number_format($amount, 2);
}

function getActiveService($current) {
    $services = ['digitizing', 'vector', 'character', 'patches'];
    return in_array($current, $services) ? $current : '';
}

// ============================================
// DUMP FUNCTION FOR DEBUGGING
// ============================================

function dd($data) {
    echo '<pre style="background: #0D0F12; color: #00F0FF; padding: 20px; border-radius: 8px; font-family: monospace; font-size: 14px; overflow: auto; max-height: 80vh;">';
    var_dump($data);
    echo '</pre>';
    exit;
}
?>