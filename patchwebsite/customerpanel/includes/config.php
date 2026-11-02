<?php
// /patchwebsite/customerpanel/includes/config.php

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/auth.php';

if (!defined('CUSTOMER_PATH')) define('CUSTOMER_PATH', '/customerpanel/');

function isCustomerLoggedIn() {
    return isset($_SESSION['user_id']) && 
           isset($_SESSION['user_role']) && 
           $_SESSION['user_role'] === 'customer';
}

function requireCustomerLogin() {
    if (!isCustomerLoggedIn()) {
        header('Location: ' . SITE_URL . 'customerpanel/login.php');
        exit;
    }
}

function getCustomerUser() {
    if (!isCustomerLoggedIn()) return null;
    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    } catch (Exception $e) {
        return null;
    }
}

function getCustomerOrderCount() {
    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetchColumn();
    } catch (Exception $e) {
        return 0;
    }
}

function getCustomerPendingOrders() {
    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ? AND status IN ('submitted', 'reviewing', 'quoted')");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetchColumn();
    } catch (Exception $e) {
        return 0;
    }
}

function getCustomerCompletedOrders() {
    try {
        $db = getDB();
        $stmt = $db->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ? AND status = 'completed'");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetchColumn();
    } catch (Exception $e) {
        return 0;
    }
}
?>