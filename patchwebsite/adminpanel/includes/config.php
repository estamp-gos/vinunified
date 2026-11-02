<?php
// /patchwebsite/adminpanel/includes/config.php

// ============================================
// ADMIN PANEL CONFIGURATION
// ============================================

// Load main config (DO NOT redefine constants here)
require_once __DIR__ . '/../../includes/config.php';

// Load functions and auth (IMPORTANT - for getServiceName, getStatusBadge, etc.)
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';

// Admin specific settings - only if not already defined
if (!defined('ADMIN_PATH')) define('ADMIN_PATH', '/adminpanel/');
if (!defined('ADMIN_ASSETS')) define('ADMIN_ASSETS', SITE_URL . 'assets/');

// ============================================
// ADMIN SESSION FUNCTIONS
// ============================================

function isAdminLoggedIn() {
    return isset($_SESSION['user_id']) && 
           isset($_SESSION['user_role']) && 
           $_SESSION['user_role'] === 'admin';
}

function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header('Location: ' . SITE_URL . 'adminpanel/login.php');
        exit;
    }
}

function getAdminUser() {
    if (!isAdminLoggedIn()) return null;
    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    } catch (Exception $e) {
        return null;
    }
}

function getPendingOrderCount() {
    try {
        $db = getDB();
        $stmt = $db->query("SELECT COUNT(*) FROM orders WHERE status IN ('submitted', 'reviewing')");
        return $stmt->fetchColumn();
    } catch (Exception $e) {
        return 0;
    }
}
?>